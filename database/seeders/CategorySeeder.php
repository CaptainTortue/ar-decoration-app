<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // ── Catégories principales ───────────────────────────────

        $meubles = Category::firstOrCreate(
            ['slug' => 'meubles'],
            [
                'name'        => 'Meubles',
                'description' => 'Mobilier d\'intérieur : tables, chaises, canapés, rangements',
                'icon'        => 'sofa',
                'sort_order'  => 1,
            ]
        );

        $luminaires = Category::firstOrCreate(
            ['slug' => 'luminaires'],
            [
                'name'        => 'Luminaires',
                'description' => 'Éclairage intérieur : lampes, suspensions, appliques',
                'icon'        => 'lamp',
                'sort_order'  => 2,
            ]
        );

        $decoration = Category::firstOrCreate(
            ['slug' => 'decoration'],
            [
                'name'        => 'Décoration',
                'description' => 'Objets décoratifs : vases, plantes, cadres',
                'icon'        => 'palette',
                'sort_order'  => 3,
            ]
        );

        // ── Sous-catégories : Meubles ────────────────────────────

        Category::firstOrCreate(
            ['slug' => 'tables'],
            [
                'name'        => 'Tables',
                'description' => 'Tables à manger, tables basses, bureaux',
                'icon'        => 'table',
                'parent_id'   => $meubles->id,
                'sort_order'  => 1,
            ]
        );

        Category::firstOrCreate(
            ['slug' => 'assises'],
            [
                'name'        => 'Assises',
                'description' => 'Chaises, fauteuils, tabourets',
                'icon'        => 'chair',
                'parent_id'   => $meubles->id,
                'sort_order'  => 2,
            ]
        );

        Category::firstOrCreate(
            ['slug' => 'canapes'],
            [
                'name'        => 'Canapés',
                'description' => 'Canapés, méridiens, banquettes',
                'icon'        => 'sofa',
                'parent_id'   => $meubles->id,
                'sort_order'  => 3,
            ]
        );

        Category::firstOrCreate(
            ['slug' => 'rangements'],
            [
                'name'        => 'Rangements',
                'description' => 'Étagères, bibliothèques, commodes',
                'icon'        => 'bookshelf',
                'parent_id'   => $meubles->id,
                'sort_order'  => 4,
            ]
        );

        // ── Sous-catégories : Luminaires ─────────────────────────

        Category::firstOrCreate(
            ['slug' => 'lampes'],
            [
                'name'        => 'Lampes',
                'description' => 'Lampes de bureau, lampes de chevet, lampadaires',
                'icon'        => 'desk-lamp',
                'parent_id'   => $luminaires->id,
                'sort_order'  => 1,
            ]
        );

        Category::firstOrCreate(
            ['slug' => 'plafonniers'],
            [
                'name'        => 'Plafonniers',
                'description' => 'Plafonniers, suspensions, lustres',
                'icon'        => 'chandelier',
                'parent_id'   => $luminaires->id,
                'sort_order'  => 2,
            ]
        );

        Category::firstOrCreate(
            ['slug' => 'appliques'],
            [
                'name'        => 'Appliques',
                'description' => 'Appliques murales, spots',
                'icon'        => 'wall-lamp',
                'parent_id'   => $luminaires->id,
                'sort_order'  => 3,
            ]
        );

        // ── Sous-catégories : Décoration ─────────────────────────

        Category::firstOrCreate(
            ['slug' => 'plantes'],
            [
                'name'        => 'Plantes',
                'description' => 'Plantes d\'intérieur, pots, jardinières',
                'icon'        => 'plant',
                'parent_id'   => $decoration->id,
                'sort_order'  => 1,
            ]
        );

        Category::firstOrCreate(
            ['slug' => 'vases'],
            [
                'name'        => 'Vases',
                'description' => 'Vases, soliflores, cache-pots décoratifs',
                'icon'        => 'vase',
                'parent_id'   => $decoration->id,
                'sort_order'  => 2,
            ]
        );
    }
}
