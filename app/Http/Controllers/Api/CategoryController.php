<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Liste toutes les catégories principales avec leurs sous-catégories.
     */
    public function index(Request $request)
    {
        $categories = Category::whereNull('parent_id')
            ->with('children')
            ->withCount('furnitureObjects')
            ->orderBy('sort_order')
            ->get();

        return CategoryResource::collection($categories);
    }

    /**
     * Affiche une catégorie avec ses enfants et ses objets.
     */
    public function show(Category $category)
    {
        $category->load(['children', 'furnitureObjects', 'parent']);
        $category->loadCount('furnitureObjects');

        return new CategoryResource($category);
    }
}
