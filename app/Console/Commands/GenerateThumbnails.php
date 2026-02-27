<?php

namespace App\Console\Commands;

use App\Models\FurnitureObject;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GenerateThumbnails extends Command
{
    protected $signature = 'thumbnails:generate
                            {--force : Régénère même si la thumbnail existe déjà}
                            {--id= : Génère uniquement pour un objet spécifique (ID)}';

    protected $description = 'Génère les thumbnails manquantes pour les objets 3D via PHP GD';

    // Palette de couleurs par catégorie (fond, accent)
    private array $categoryColors = [
        'tables'     => [[139, 90, 43],   [205, 133, 63]],
        'assises'    => [[101, 67, 33],   [160, 120, 60]],
        'canapes'    => [[80,  60, 100],  [130, 100, 160]],
        'rangements' => [[60,  80, 60],   [100, 130, 100]],
        'lampes'     => [[160, 120, 20],  [220, 180, 40]],
        'plafonniers'=> [[140, 100, 20],  [200, 160, 40]],
        'appliques'  => [[150, 110, 30],  [210, 170, 60]],
        'plantes'    => [[40,  100, 40],  [80,  160, 80]],
        'vases'      => [[60,  100, 120], [100, 160, 180]],
    ];

    // Couleur par défaut si la catégorie est inconnue
    private array $defaultColors = [[70, 70, 90], [110, 110, 140]];

    public function handle(): int
    {
        if (!extension_loaded('gd')) {
            $this->error('L\'extension PHP GD est requise. Activez-la dans php.ini.');
            return self::FAILURE;
        }

        $query = FurnitureObject::with('category');

        if ($id = $this->option('id')) {
            $query->where('id', $id);
        }

        $objects = $query->get();

        if ($objects->isEmpty()) {
            $this->warn('Aucun objet trouvé.');
            return self::SUCCESS;
        }

        $generated = 0;
        $skipped   = 0;
        $errors    = 0;

        $this->info("Génération des thumbnails pour {$objects->count()} objet(s)...");
        $bar = $this->output->createProgressBar($objects->count());
        $bar->start();

        foreach ($objects as $object) {
            // Déterminer le chemin de la thumbnail
            $thumbnailPath = $object->thumbnail_path;

            if (empty($thumbnailPath)) {
                // Construire un chemin par défaut basé sur le slug
                $category = $object->category;
                $subdir = $this->getCategorySubdir($category?->slug ?? 'furniture');
                $thumbnailPath = "thumbnails/{$subdir}/{$object->slug}.webp";
            }

            // Vérifier si la thumbnail existe déjà
            if (!$this->option('force') && Storage::disk('public')->exists($thumbnailPath)) {
                $skipped++;
                $bar->advance();
                continue;
            }

            try {
                $imageData = $this->generateThumbnail($object);

                // Créer les répertoires si nécessaire
                $dir = dirname($thumbnailPath);
                if (!Storage::disk('public')->exists($dir)) {
                    Storage::disk('public')->makeDirectory($dir);
                }

                Storage::disk('public')->put($thumbnailPath, $imageData);

                // Mettre à jour le chemin en base si nécessaire
                if ($object->thumbnail_path !== $thumbnailPath) {
                    $object->update(['thumbnail_path' => $thumbnailPath]);
                }

                $generated++;
            } catch (\Throwable $e) {
                $errors++;
                $this->newLine();
                $this->error("Erreur pour \"{$object->name}\": {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Terminé !");
        $this->table(
            ['Statut', 'Nombre'],
            [
                ['✓ Générées',  $generated],
                ['→ Ignorées',  $skipped],
                ['✗ Erreurs',   $errors],
            ]
        );

        return $errors > 0 ? self::FAILURE : self::SUCCESS;
    }

    /**
     * Génère une image PNG en mémoire pour l'objet donné.
     * Retourne le contenu binaire de l'image (PNG ou WebP selon support).
     */
    private function generateThumbnail(FurnitureObject $object): string
    {
        $width  = 400;
        $height = 400;

        $img = imagecreatetruecolor($width, $height);
        imagesavealpha($img, true);

        // ── Couleurs ─────────────────────────────────────────────
        $categorySlug = $object->category?->slug ?? '';
        [$bgRgb, $accentRgb] = $this->categoryColors[$categorySlug] ?? $this->defaultColors;

        $bgColor     = imagecolorallocate($img, $bgRgb[0], $bgRgb[1], $bgRgb[2]);
        $accentColor = imagecolorallocate($img, $accentRgb[0], $accentRgb[1], $accentRgb[2]);
        $white       = imagecolorallocate($img, 255, 255, 255);
        $lightGray   = imagecolorallocate($img, 200, 200, 200);
        $darkOverlay = imagecolorallocatealpha($img, 0, 0, 0, 60);

        // ── Fond dégradé simulé (bandes horizontales) ─────────────
        for ($y = 0; $y < $height; $y++) {
            $ratio = $y / $height;
            $r = (int) ($bgRgb[0] * (1 - $ratio * 0.4));
            $g = (int) ($bgRgb[1] * (1 - $ratio * 0.4));
            $b = (int) ($bgRgb[2] * (1 - $ratio * 0.4));
            $lineColor = imagecolorallocate($img, $r, $g, $b);
            imageline($img, 0, $y, $width, $y, $lineColor);
        }

        // ── Icône 3D (cube isométrique) centré ───────────────────
        $this->drawIsometricCube($img, $width / 2, $height / 2 - 20, 80, $accentRgb);

        // ── Bande inférieure ──────────────────────────────────────
        $bandHeight = 110;
        imagefilledrectangle($img, 0, $height - $bandHeight, $width, $height, $darkOverlay);

        // ── Nom de l'objet ────────────────────────────────────────
        $name = $object->name;
        // Tronquer si trop long
        if (strlen($name) > 22) {
            $name = substr($name, 0, 20) . '…';
        }
        $this->drawCenteredText($img, $name, $width / 2, $height - $bandHeight + 22, 4, $white);

        // ── Catégorie ─────────────────────────────────────────────
        $categoryName = $object->category?->name ?? 'Sans catégorie';
        $this->drawCenteredText($img, $categoryName, $width / 2, $height - $bandHeight + 48, 2, $lightGray);

        // ── Dimensions ────────────────────────────────────────────
        $dims = sprintf(
            '%sm × %sm × %sm',
            rtrim(rtrim((string) $object->width, '0'), '.'),
            rtrim(rtrim((string) $object->depth, '0'), '.'),
            rtrim(rtrim((string) $object->height, '0'), '.')
        );
        $this->drawCenteredText($img, $dims, $width / 2, $height - $bandHeight + 70, 2, $lightGray);

        // ── Prix ──────────────────────────────────────────────────
        if ($object->price) {
            $price = number_format((float) $object->price, 2, ',', ' ') . ' €';
            $this->drawCenteredText($img, $price, $width / 2, $height - $bandHeight + 90, 2, $accentColor);
        }

        // ── Bordure accent en bas ─────────────────────────────────
        $accentLine = imagecolorallocate($img, $accentRgb[0], $accentRgb[1], $accentRgb[2]);
        imagefilledrectangle($img, 0, $height - 4, $width, $height, $accentLine);

        // ── Capture en mémoire ────────────────────────────────────
        ob_start();

        if (function_exists('imagewebp')) {
            imagewebp($img, null, 85);
        } else {
            imagepng($img, null, 8);
        }

        $data = ob_get_clean();
        imagedestroy($img);

        return $data;
    }

    /**
     * Dessine un cube isométrique simplifié.
     */
    private function drawIsometricCube(\GdImage $img, float $cx, float $cy, int $size, array $accentRgb): void
    {
        $s = $size;
        $h = (int) ($s * 0.5);   // demi-hauteur isométrique

        // Face du dessus (plus claire)
        $topR = min(255, $accentRgb[0] + 60);
        $topG = min(255, $accentRgb[1] + 60);
        $topB = min(255, $accentRgb[2] + 60);
        $topColor = imagecolorallocate($img, $topR, $topG, $topB);

        // Face gauche (accentuée)
        $leftColor = imagecolorallocate($img, $accentRgb[0], $accentRgb[1], $accentRgb[2]);

        // Face droite (plus sombre)
        $rightR = max(0, $accentRgb[0] - 50);
        $rightG = max(0, $accentRgb[1] - 50);
        $rightB = max(0, $accentRgb[2] - 50);
        $rightColor = imagecolorallocate($img, $rightR, $rightG, $rightB);

        // Points du cube isométrique
        // Sommet haut
        $top    = [(int)$cx,      (int)($cy - $h)];
        $left   = [(int)($cx - $s), (int)$cy];
        $right  = [(int)($cx + $s), (int)$cy];
        $bottom = [(int)$cx,      (int)($cy + $h)];
        $topL   = [(int)($cx - $s), (int)($cy - $h)];
        $topR   = [(int)($cx + $s), (int)($cy - $h)];

        // Face du dessus (losange)
        imagefilledpolygon($img, [
            $top[0], $top[1],
            $topL[0], $topL[1],
            $left[0], $left[1],
            $cx, $cy,
        ], $topColor);

        // Face gauche
        imagefilledpolygon($img, [
            $topL[0], $topL[1],
            $left[0], $left[1],
            $bottom[0], $bottom[1],
            (int)($cx - $s), (int)($cy + $h / 2 + $h / 2),
        ], $leftColor);

        // Face droite
        imagefilledpolygon($img, [
            (int)$cx, (int)$cy,
            $right[0], $right[1],
            (int)($cx + $s), (int)($cy + $h),
            $bottom[0], $bottom[1],
        ], $rightColor);

        // Contours
        $outline = imagecolorallocatealpha($img, 0, 0, 0, 80);
        imagepolygon($img, [
            $top[0], $top[1],
            $topL[0], $topL[1],
            $left[0], $left[1],
            (int)$cx, (int)$cy,
        ], $outline);
        imagepolygon($img, [
            $top[0], $top[1],
            $topR[0], $topR[1],
            $right[0], $right[1],
            (int)$cx, (int)$cy,
        ], $outline);
    }

    /**
     * Écrit du texte centré horizontalement.
     */
    private function drawCenteredText(\GdImage $img, string $text, float $cx, float $cy, int $fontSize, int $color): void
    {
        $textWidth = imagefontwidth($fontSize) * strlen($text);
        $x = (int) ($cx - $textWidth / 2);
        $y = (int) ($cy - imagefontheight($fontSize) / 2);
        imagestring($img, $fontSize, $x, $y, $text, $color);
    }

    /**
     * Retourne le sous-dossier correspondant au slug de catégorie.
     */
    private function getCategorySubdir(string $slug): string
    {
        return match ($slug) {
            'lampes', 'plafonniers', 'appliques' => 'lighting',
            'plantes', 'vases'                   => 'decoration',
            default                              => 'furniture',
        };
    }
}
