<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // ── Catégories principales ───────────────────────────────

        $meubles = Category::create([
            'name'        => 'Meubles',
            'slug'        => 'meubles',
            'description' => 'Mobilier d\'intérieur : tables, chaises, canapés, rangements',
            'icon'        => 'sofa',
            'sort_order'  => 1,
        ]);

        $luminaires = Category::create([
            'name'        => 'Luminaires',
            'slug'        => 'luminaires',
            'description' => 'Éclairage intérieur : lampes, suspensions, appliques',
            'icon'        => 'lamp',
            'sort_order'  => 2,
        ]);

        $decoration = Category::create([
            'name'        => 'Décoration',
            'slug'        => 'decoration',
            'description' => 'Objets décoratifs : vases, plantes, cadres',
            'icon'        => 'palette',
            'sort_order'  => 3,
        ]);

        // ── Sous-catégories : Meubles ────────────────────────────

        Category::create([
            'name'        => 'Tables',
            'slug'        => 'tables',
            'description' => 'Tables à manger, tables basses, bureaux',
            'icon'        => 'table',
            'parent_id'   => $meubles->id,
            'sort_order'  => 1,
        ]);

        Category::create([
            'name'        => 'Assises',
            'slug'        => 'assises',
            'description' => 'Chaises, fauteuils, tabourets',
            'icon'        => 'chair',
            'parent_id'   => $meubles->id,
            'sort_order'  => 2,
        ]);

        Category::create([
            'name'        => 'Canapés',
            'slug'        => 'canapes',
            'description' => 'Canapés, méridiens, banquettes',
            'icon'        => 'sofa',
            'parent_id'   => $meubles->id,
            'sort_order'  => 3,
        ]);

        Category::create([
            'name'        => 'Rangements',
            'slug'        => 'rangements',
            'description' => 'Étagères, bibliothèques, commodes',
            'icon'        => 'bookshelf',
            'parent_id'   => $meubles->id,
            'sort_order'  => 4,
        ]);

        // ── Sous-catégories : Luminaires ─────────────────────────

        Category::create([
            'name'        => 'Lampes',
            'slug'        => 'lampes',
            'description' => 'Lampes de bureau, lampes de chevet, lampadaires',
            'icon'        => 'desk-lamp',
            'parent_id'   => $luminaires->id,
            'sort_order'  => 1,
        ]);

        Category::create([
            'name'        => 'Plafonniers',
            'slug'        => 'plafonniers',
            'description' => 'Plafonniers, suspensions, lustres',
            'icon'        => 'chandelier',
            'parent_id'   => $luminaires->id,
            'sort_order'  => 2,
        ]);

        Category::create([
            'name'        => 'Appliques',
            'slug'        => 'appliques',
            'description' => 'Appliques murales, spots',
            'icon'        => 'wall-lamp',
            'parent_id'   => $luminaires->id,
            'sort_order'  => 3,
        ]);

        // ── Sous-catégories : Décoration ─────────────────────────

        Category::create([
            'name'        => 'Plantes',
            'slug'        => 'plantes',
            'description' => 'Plantes d\'intérieur, pots, jardinières',
            'icon'        => 'plant',
            'parent_id'   => $decoration->id,
            'sort_order'  => 1,
        ]);

        Category::create([
            'name'        => 'Vases',
            'slug'        => 'vases',
            'description' => 'Vases, soliflores, cache-pots décoratifs',
            'icon'        => 'vase',
            'parent_id'   => $decoration->id,
            'sort_order'  => 2,
        ]);
    }
}
