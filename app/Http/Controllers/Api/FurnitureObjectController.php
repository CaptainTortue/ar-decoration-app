<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FurnitureObjectResource;
use App\Models\FurnitureObject;
use Illuminate\Http\Request;

class FurnitureObjectController extends Controller
{
    /**
     * Liste tous les objets actifs de la bibliothèque.
     * Filtrage par catégorie et recherche par nom possible.
     */
    public function index(Request $request)
    {
        $query = FurnitureObject::with('category')
            ->where('is_active', true);

        // Filtrage par catégorie
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filtrage par slug de catégorie
        if ($request->has('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Recherche par nom
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $objects = $query->orderBy('name')->paginate(
            $request->get('per_page', 20)
        );

        return FurnitureObjectResource::collection($objects);
    }

    /**
     * Affiche un objet 3D avec sa catégorie.
     */
    public function show(FurnitureObject $furnitureObject)
    {
        $furnitureObject->load('category');

        return new FurnitureObjectResource($furnitureObject);
    }
}
