<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FurnitureObject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AssetController extends Controller
{
    public function downloadModel(FurnitureObject $furnitureObject): StreamedResponse
    {
        if (! $furnitureObject->is_active) {
            abort(404, 'Objet non disponible.');
        }

        if (empty($furnitureObject->model_path)) {
            abort(404, 'Modèle 3D non configuré pour cet objet.');
        }

        $filePath = $furnitureObject->model_path;
        $fileName = basename($filePath);
        $disk = Storage::disk('s3');

        $stream = $disk->readStream($filePath);

        if ($stream === false) {
            // Pas de exists() : on renvoie 404 si impossible à lire
            abort(404, 'Fichier modèle 3D introuvable.');
        }

        return response()->stream(function () use ($stream) {
            fpassthru($stream);
            if (is_resource($stream)) fclose($stream);
        }, 200, [
            'Content-Type' => 'model/gltf-binary',
            'Content-Disposition' => 'inline; filename="'.$fileName.'"',

            // Cache (mets "public, max-age=..." si fichiers immutables + versionnés)
            'Cache-Control' => 'private, no-store, no-cache, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',

            // CORS
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization, Range',
            'Access-Control-Expose-Headers' => 'Content-Length, Content-Range, Accept-Ranges',
        ]);
    }

    public function downloadThumbnail(FurnitureObject $furnitureObject): StreamedResponse
    {
        if (! $furnitureObject->is_active) {
            abort(404, 'Objet non disponible.');
        }

        if (empty($furnitureObject->thumbnail_path)) {
            abort(404, 'Thumbnail non configurée pour cet objet.');
        }

        $filePath = $furnitureObject->thumbnail_path;
        $fileName = basename($filePath);

        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $contentType = match ($extension) {
            'webp' => 'image/webp',
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            default => 'application/octet-stream',
        };

        $disk = Storage::disk('s3');
        $stream = $disk->readStream($filePath);

        if ($stream === false) {
            abort(404, 'Thumbnail introuvable.');
        }

        return response()->stream(function () use ($stream) {
            fpassthru($stream);
            if (is_resource($stream)) fclose($stream);
        }, 200, [
            'Content-Type' => $contentType,
            'Content-Disposition' => 'inline; filename="'.$fileName.'"',

            'Cache-Control' => 'private, no-store, no-cache, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',

            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization',
        ]);
    }

    /**
     * Stream "best effort" avec Range:
     * - Si on peut déterminer fileSize => on répond 206 + Content-Range
     * - Sinon on ignore Range et on stream en 200 (ça marche partout)
     *
     * NOTE: pour un Range parfait (lecture partielle côté R2), il faut AWS SDK getObject avec Range.
     */
    public function streamModel(Request $request, FurnitureObject $furnitureObject): StreamedResponse
    {
        if (! $furnitureObject->is_active) {
            abort(404, 'Objet non disponible.');
        }

        if (empty($furnitureObject->model_path)) {
            abort(404, 'Modèle 3D non configuré pour cet objet.');
        }

        $disk = Storage::disk('s3');
        $filePath = $furnitureObject->model_path;
        $fileName = basename($filePath);

        // On tente de récupérer la taille (peut échouer sur R2 selon config/permissions)
        $size = null;
        try {
            $size = $disk->size($filePath); // alias de fileSize selon version
        } catch (\Throwable $e) {
            // Pas bloquant: on stream quand même en 200
            Log::warning('Unable to get model size (Range disabled)', [
                'path' => $filePath,
                'error' => $e->getMessage(),
            ]);
        }

        $start = 0;
        $end = $size !== null ? $size - 1 : null;
        $statusCode = 200;

        if ($size !== null && $request->hasHeader('Range')) {
            $range = $request->header('Range');

            if (preg_match('/bytes=(\d+)-(\d*)/', $range, $matches)) {
                $start = (int) $matches[1];
                $end = ($matches[2] !== '') ? (int) $matches[2] : ($size - 1);

                // sécurise les bornes
                $start = max(0, min($start, $size - 1));
                $end = max($start, min($end, $size - 1));

                $statusCode = 206;
            }
        }

        // IMPORTANT:
        // Flysystem readStream() ne permet pas de demander une plage distante.
        // Donc: si Range demandé, on "saute" localement en lisant et jetant, ce qui n'est pas optimal.
        // Pour gros fichiers, préfère la version AWS SDK.
        $headers = [
            'Content-Type' => 'model/gltf-binary',
            'Content-Disposition' => 'inline; filename="'.$fileName.'"',
            'Accept-Ranges' => 'bytes',

            'Cache-Control' => 'private, no-store, no-cache, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',

            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization, Range',
            'Access-Control-Expose-Headers' => 'Content-Length, Content-Range, Accept-Ranges',
        ];

        if ($size !== null) {
            $length = ($statusCode === 206)
                ? ($end - $start + 1)
                : $size;

            $headers['Content-Length'] = $length;

            if ($statusCode === 206) {
                $headers['Content-Range'] = "bytes {$start}-{$end}/{$size}";
            }
        }

        $stream = $disk->readStream($filePath);

        if ($stream === false) {
            abort(404, 'Fichier modèle 3D introuvable.');
        }

        return response()->stream(function () use ($stream, $start, $end, $size) {
            // Si Range demandé et qu'on a la taille, on "skip" jusqu'à start
            // (pas optimal mais fonctionnel)
            if ($size !== null && $start > 0) {
                $toSkip = $start;
                $buffer = 8192;

                while ($toSkip > 0 && !feof($stream)) {
                    $read = fread($stream, min($buffer, $toSkip));
                    if ($read === false || $read === '') break;
                    $toSkip -= strlen($read);
                }
            }

            $bufferSize = 8192;

            if ($size !== null && $end !== null) {
                $remaining = ($end - $start + 1);

                while ($remaining > 0 && !feof($stream)) {
                    $chunk = fread($stream, min($bufferSize, $remaining));
                    if ($chunk === false || $chunk === '') break;

                    echo $chunk;
                    $remaining -= strlen($chunk);
                    flush();
                }
            } else {
                // Pas de Range: stream complet
                while (!feof($stream)) {
                    $chunk = fread($stream, $bufferSize);
                    if ($chunk === false) break;

                    echo $chunk;
                    flush();
                }
            }

            if (is_resource($stream)) fclose($stream);
        }, $statusCode, $headers);
    }
}
