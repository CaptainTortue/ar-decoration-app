<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\FurnitureObject;
use Illuminate\Database\Seeder;

class FurnitureObjectSeeder extends Seeder
{
    public function run(): void
    {
        // ── Récupération des sous-catégories ─────────────────────

        $tables     = Category::where('slug', 'tables')->first();
        $assises    = Category::where('slug', 'assises')->first();
        $canapes    = Category::where('slug', 'canapes')->first();
        $rangements = Category::where('slug', 'rangements')->first();
        $lampes     = Category::where('slug', 'lampes')->first();
        $plafonniers = Category::where('slug', 'plafonniers')->first();
        $appliques  = Category::where('slug', 'appliques')->first();
        $plantes    = Category::where('slug', 'plantes')->first();
        $vases      = Category::where('slug', 'vases')->first();

        // ══════════════════════════════════════════════════════════
        //  MEUBLES — Tables
        // ══════════════════════════════════════════════════════════

        FurnitureObject::create([
            'name'                => 'Table à manger',
            'slug'                => 'table-a-manger',
            'description'         => 'Table à manger classique en bois, 4 à 6 personnes.',
            'category_id'         => $tables->id,
            'model_path'          => 'models/furniture/table.glb',
            'thumbnail_path'      => 'thumbnails/furniture/table.webp',
            'width'               => 1.500,
            'height'              => 0.750,
            'depth'               => 0.900,
            'default_scale'       => 1.000,
            'available_colors'    => ['Chêne naturel', 'Noyer', 'Blanc'],
            'available_materials' => ['Bois massif', 'MDF laqué'],
            'price'               => 349.99,
            'is_active'           => true,
        ]);

        FurnitureObject::create([
            'name'                => 'Table basse',
            'slug'                => 'table-basse',
            'description'         => 'Table basse ronde de salon, plateau en verre et pieds en métal.',
            'category_id'         => $tables->id,
            'model_path'          => 'models/furniture/table-basse.glb',
            'thumbnail_path'      => 'thumbnails/furniture/table-basse.webp',
            'width'               => 0.800,
            'height'              => 0.400,
            'depth'               => 0.800,
            'default_scale'       => 1.000,
            'available_colors'    => ['Noir', 'Doré', 'Chrome'],
            'available_materials' => ['Verre trempé', 'Métal'],
            'price'               => 189.99,
            'is_active'           => true,
        ]);

        // ══════════════════════════════════════════════════════════
        //  MEUBLES — Assises
        // ══════════════════════════════════════════════════════════

        FurnitureObject::create([
            'name'                => 'Chaise',
            'slug'                => 'chaise',
            'description'         => 'Chaise de salle à manger au design simple et moderne.',
            'category_id'         => $assises->id,
            'model_path'          => 'models/furniture/chaise.glb',
            'thumbnail_path'      => 'thumbnails/furniture/chaise.webp',
            'width'               => 0.450,
            'height'              => 0.850,
            'depth'               => 0.500,
            'default_scale'       => 1.000,
            'available_colors'    => ['Blanc', 'Noir', 'Gris'],
            'available_materials' => ['Plastique', 'Bois'],
            'price'               => 79.99,
            'is_active'           => true,
        ]);

        FurnitureObject::create([
            'name'                => 'Chaise Eames',
            'slug'                => 'chaise-eames',
            'description'         => 'Chaise design inspirée du style Eames, coque et pieds en bois.',
            'category_id'         => $assises->id,
            'model_path'          => 'models/furniture/chaise-eames.glb',
            'thumbnail_path'      => 'thumbnails/furniture/chaise-eames.webp',
            'width'               => 0.470,
            'height'              => 0.810,
            'depth'               => 0.550,
            'default_scale'       => 1.000,
            'available_colors'    => ['Blanc', 'Noir', 'Jaune moutarde', 'Bleu pétrole'],
            'available_materials' => ['Polypropylène', 'Bois de hêtre'],
            'price'               => 129.99,
            'is_active'           => true,
        ]);

        // ══════════════════════════════════════════════════════════
        //  MEUBLES — Canapés
        // ══════════════════════════════════════════════════════════

        FurnitureObject::create([
            'name'                => 'Canapé 3 places',
            'slug'                => 'canape-3-places',
            'description'         => 'Canapé confortable 3 places, revêtement en tissu doux.',
            'category_id'         => $canapes->id,
            'model_path'          => 'models/furniture/canape.glb',
            'thumbnail_path'      => 'thumbnails/furniture/canape.webp',
            'width'               => 2.100,
            'height'              => 0.850,
            'depth'               => 0.900,
            'default_scale'       => 1.000,
            'available_colors'    => ['Gris anthracite', 'Beige', 'Bleu nuit', 'Vert sauge'],
            'available_materials' => ['Tissu', 'Velours', 'Lin'],
            'price'               => 899.99,
            'is_active'           => true,
        ]);

        // ══════════════════════════════════════════════════════════
        //  MEUBLES — Rangements
        // ══════════════════════════════════════════════════════════

        FurnitureObject::create([
            'name'                => 'Étagère 5 niveaux',
            'slug'                => 'etagere-5-niveaux',
            'description'         => 'Étagère ouverte à 5 niveaux, idéale pour le salon ou le bureau.',
            'category_id'         => $rangements->id,
            'model_path'          => 'models/furniture/etagere.glb',
            'thumbnail_path'      => 'thumbnails/furniture/etagere.webp',
            'width'               => 0.800,
            'height'              => 1.800,
            'depth'               => 0.300,
            'default_scale'       => 1.000,
            'available_colors'    => ['Chêne', 'Noir', 'Blanc'],
            'available_materials' => ['Bois', 'Métal'],
            'price'               => 149.99,
            'is_active'           => true,
        ]);

        FurnitureObject::create([
            'name'                => 'Étagère murale',
            'slug'                => 'etagere-murale',
            'description'         => 'Étagère murale flottante, fixation invisible.',
            'category_id'         => $rangements->id,
            'model_path'          => 'models/furniture/etagere-murale.glb',
            'thumbnail_path'      => 'thumbnails/furniture/etagere-murale.webp',
            'width'               => 0.600,
            'height'              => 0.200,
            'depth'               => 0.250,
            'default_scale'       => 1.000,
            'available_colors'    => ['Chêne clair', 'Noir mat', 'Blanc'],
            'available_materials' => ['MDF laqué', 'Bois massif'],
            'price'               => 39.99,
            'is_active'           => true,
        ]);

        FurnitureObject::create([
            'name'                => 'Bibliothèque',
            'slug'                => 'bibliotheque',
            'description'         => 'Grande bibliothèque en bois avec étagères ajustables.',
            'category_id'         => $rangements->id,
            'model_path'          => 'models/furniture/bibliotheque.glb',
            'thumbnail_path'      => 'thumbnails/furniture/bibliotheque.webp',
            'width'               => 1.200,
            'height'              => 2.000,
            'depth'               => 0.350,
            'default_scale'       => 1.000,
            'available_colors'    => ['Noyer', 'Chêne foncé', 'Blanc'],
            'available_materials' => ['Bois massif', 'Panneau mélaminé'],
            'price'               => 449.99,
            'is_active'           => true,
        ]);

        // ══════════════════════════════════════════════════════════
        //  LUMINAIRES — Lampes
        // ══════════════════════════════════════════════════════════

        FurnitureObject::create([
            'name'                => 'Lampe de bureau',
            'slug'                => 'lampe-bureau',
            'description'         => 'Lampe de bureau articulée avec bras réglable.',
            'category_id'         => $lampes->id,
            'model_path'          => 'models/lighting/lampe-bureau.glb',
            'thumbnail_path'      => 'thumbnails/lighting/lampe-bureau.webp',
            'width'               => 0.200,
            'height'              => 0.500,
            'depth'               => 0.200,
            'default_scale'       => 1.000,
            'available_colors'    => ['Noir', 'Blanc', 'Laiton'],
            'available_materials' => ['Métal', 'Aluminium'],
            'price'               => 69.99,
            'is_active'           => true,
        ]);

        // ══════════════════════════════════════════════════════════
        //  LUMINAIRES — Plafonniers
        // ══════════════════════════════════════════════════════════

        FurnitureObject::create([
            'name'                => 'Plafonnier',
            'slug'                => 'plafonnier',
            'description'         => 'Plafonnier rond moderne avec diffuseur en verre dépoli.',
            'category_id'         => $plafonniers->id,
            'model_path'          => 'models/lighting/plafonnier.glb',
            'thumbnail_path'      => 'thumbnails/lighting/plafonnier.webp',
            'width'               => 0.400,
            'height'              => 0.150,
            'depth'               => 0.400,
            'default_scale'       => 1.000,
            'available_colors'    => ['Chrome', 'Noir', 'Or brossé'],
            'available_materials' => ['Verre', 'Métal'],
            'price'               => 119.99,
            'is_active'           => true,
        ]);

        FurnitureObject::create([
            'name'                => 'Lustre',
            'slug'                => 'lustre',
            'description'         => 'Lustre suspendu élégant à plusieurs branches.',
            'category_id'         => $plafonniers->id,
            'model_path'          => 'models/lighting/lustre.glb',
            'thumbnail_path'      => 'thumbnails/lighting/lustre.webp',
            'width'               => 0.600,
            'height'              => 0.500,
            'depth'               => 0.600,
            'default_scale'       => 1.000,
            'available_colors'    => ['Noir mat', 'Laiton antique', 'Chrome'],
            'available_materials' => ['Métal forgé', 'Cristal'],
            'price'               => 279.99,
            'is_active'           => true,
        ]);

        // ══════════════════════════════════════════════════════════
        //  LUMINAIRES — Appliques
        // ══════════════════════════════════════════════════════════

        FurnitureObject::create([
            'name'                => 'Applique murale',
            'slug'                => 'applique-murale',
            'description'         => 'Applique murale à éclairage indirect, ambiance chaleureuse.',
            'category_id'         => $appliques->id,
            'model_path'          => 'models/lighting/applique-murale.glb',
            'thumbnail_path'      => 'thumbnails/lighting/applique-murale.webp',
            'width'               => 0.120,
            'height'              => 0.250,
            'depth'               => 0.100,
            'default_scale'       => 1.000,
            'available_colors'    => ['Blanc', 'Noir', 'Laiton'],
            'available_materials' => ['Métal', 'Plâtre'],
            'price'               => 49.99,
            'is_active'           => true,
        ]);

        // ══════════════════════════════════════════════════════════
        //  DÉCORATION — Plantes
        // ══════════════════════════════════════════════════════════

        FurnitureObject::create([
            'name'                => 'Pot de plante',
            'slug'                => 'pot-plante',
            'description'         => 'Pot en céramique avec plante verte d\'intérieur.',
            'category_id'         => $plantes->id,
            'model_path'          => 'models/decoration/pot-plante.glb',
            'thumbnail_path'      => 'thumbnails/decoration/pot-plante.webp',
            'width'               => 0.200,
            'height'              => 0.450,
            'depth'               => 0.200,
            'default_scale'       => 1.000,
            'available_colors'    => ['Terracotta', 'Blanc', 'Gris béton'],
            'available_materials' => ['Céramique', 'Grès'],
            'price'               => 34.99,
            'is_active'           => true,
        ]);

        FurnitureObject::create([
            'name'                => 'Vase avec plante',
            'slug'                => 'vase-plante',
            'description'         => 'Vase décoratif avec plante artificielle, idéal pour une étagère.',
            'category_id'         => $plantes->id,
            'model_path'          => 'models/decoration/vase-plante.glb',
            'thumbnail_path'      => 'thumbnails/decoration/vase-plante.webp',
            'width'               => 0.150,
            'height'              => 0.350,
            'depth'               => 0.150,
            'default_scale'       => 1.000,
            'available_colors'    => ['Vert olive', 'Blanc cassé', 'Noir'],
            'available_materials' => ['Céramique', 'Verre'],
            'price'               => 29.99,
            'is_active'           => true,
        ]);

        // ══════════════════════════════════════════════════════════
        //  DÉCORATION — Vases
        // ══════════════════════════════════════════════════════════

        FurnitureObject::create([
            'name'                => 'Vase à fleurs',
            'slug'                => 'vase-fleurs',
            'description'         => 'Vase en verre soufflé pour bouquet de fleurs fraîches.',
            'category_id'         => $vases->id,
            'model_path'          => 'models/decoration/vase-fleurs.glb',
            'thumbnail_path'      => 'thumbnails/decoration/vase-fleurs.webp',
            'width'               => 0.120,
            'height'              => 0.300,
            'depth'               => 0.120,
            'default_scale'       => 1.000,
            'available_colors'    => ['Transparent', 'Ambre', 'Bleu cobalt'],
            'available_materials' => ['Verre soufflé'],
            'price'               => 44.99,
            'is_active'           => true,
        ]);
    }
}
