<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FurnitureObject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AssetController extends Controller
{
    /**
     * Télécharge le fichier GLB d'un objet 3D.
     *
     * Route: GET /api/furniture-objects/{furnitureObject}/model
     *
     * Sécurité:
     * - Vérifie que l'objet existe et est actif
     * - Vérifie que le fichier existe sur le disque
     * - Headers appropriés pour le streaming 3D
     * - Support CORS pour applications front-end externes
     */
    public function downloadModel(FurnitureObject $furnitureObject): StreamedResponse
    {
        // Vérifier que l'objet est actif
        if (!$furnitureObject->is_active) {
            abort(404, 'Objet non disponible.');
        }

        // Vérifier que le chemin du modèle existe
        if (empty($furnitureObject->model_path)) {
            abort(404, 'Modèle 3D non configuré pour cet objet.');
        }

        // Vérifier que le fichier existe sur le disque public
        if (!Storage::disk('public')->exists($furnitureObject->model_path)) {
            abort(404, 'Fichier modèle 3D introuvable.');
        }

        $filePath = $furnitureObject->model_path;
        $fileName = basename($filePath);

        return Storage::disk('public')->download($filePath, $fileName, [
            'Content-Type' => 'model/gltf-binary',
            'Content-Disposition' => 'inline; filename="' . $fileName . '"',
            'Cache-Control' => 'public, max-age=31536000', // Cache 1 an (fichiers immutables)
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization',
        ]);
    }

    /**
     * Télécharge la thumbnail d'un objet 3D.
     *
     * Route: GET /api/furniture-objects/{furnitureObject}/thumbnail
     *
     * Sécurité:
     * - Vérifie que l'objet existe et est actif
     * - Vérifie que le fichier existe sur le disque
     * - Headers optimisés pour les images
     */
    public function downloadThumbnail(FurnitureObject $furnitureObject): StreamedResponse
    {
        // Vérifier que l'objet est actif
        if (!$furnitureObject->is_active) {
            abort(404, 'Objet non disponible.');
        }

        // Vérifier que le chemin du thumbnail existe
        if (empty($furnitureObject->thumbnail_path)) {
            abort(404, 'Thumbnail non configurée pour cet objet.');
        }

        // Vérifier que le fichier existe sur le disque public
        if (!Storage::disk('public')->exists($furnitureObject->thumbnail_path)) {
            abort(404, 'Fichier thumbnail introuvable.');
        }

        $filePath = $furnitureObject->thumbnail_path;
        $fileName = basename($filePath);

        // Déterminer le content-type basé sur l'extension
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $contentType = match ($extension) {
            'webp' => 'image/webp',
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            default => 'application/octet-stream',
        };

        return Storage::disk('public')->download($filePath, $fileName, [
            'Content-Type' => $contentType,
            'Content-Disposition' => 'inline; filename="' . $fileName . '"',
            'Cache-Control' => 'public, max-age=31536000', // Cache 1 an
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization',
        ]);
    }

    /**
     * Stream le fichier GLB (pour les applications AR/VR).
     *
     * Route: GET /api/furniture-objects/{furnitureObject}/model/stream
     *
     * Optimisé pour le streaming avec support des requêtes Range
     * pour les gros fichiers GLB.
     */
    public function streamModel(Request $request, FurnitureObject $furnitureObject): StreamedResponse
    {
        // Vérifier que l'objet est actif
        if (!$furnitureObject->is_active) {
            abort(404, 'Objet non disponible.');
        }

        if (empty($furnitureObject->model_path)) {
            abort(404, 'Modèle 3D non configuré pour cet objet.');
        }

        if (!Storage::disk('public')->exists($furnitureObject->model_path)) {
            abort(404, 'Fichier modèle 3D introuvable.');
        }

        $path = Storage::disk('public')->path($furnitureObject->model_path);
        $size = filesize($path);
        $fileName = basename($furnitureObject->model_path);

        // Support des requêtes Range pour le streaming partiel
        $start = 0;
        $end = $size - 1;
        $statusCode = 200;

        if ($request->hasHeader('Range')) {
            $range = $request->header('Range');
            if (preg_match('/bytes=(\d+)-(\d*)/', $range, $matches)) {
                $start = intval($matches[1]);
                $end = !empty($matches[2]) ? intval($matches[2]) : $size - 1;
                $statusCode = 206; // Partial Content
            }
        }

        $length = $end - $start + 1;

        $headers = [
            'Content-Type' => 'model/gltf-binary',
            'Content-Length' => $length,
            'Content-Disposition' => 'inline; filename="' . $fileName . '"',
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'public, max-age=31536000',
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization, Range',
            'Access-Control-Expose-Headers' => 'Content-Length, Content-Range, Accept-Ranges',
        ];

        if ($statusCode === 206) {
            $headers['Content-Range'] = "bytes {$start}-{$end}/{$size}";
        }

        return response()->stream(function () use ($path, $start, $length) {
            $handle = fopen($path, 'rb');
            fseek($handle, $start);

            $bufferSize = 8192; // 8KB chunks
            $remaining = $length;

            while ($remaining > 0 && !feof($handle)) {
                $readSize = min($bufferSize, $remaining);
                echo fread($handle, $readSize);
                $remaining -= $readSize;
                flush();
            }

            fclose($handle);
        }, $statusCode, $headers);
    }
}
