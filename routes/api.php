<?php

use App\Http\Controllers\Api\AssetController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\FurnitureObjectController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\ProjectObjectController;
use App\Http\Controllers\Api\RoomController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ─────────────────────────────────────────────────────────────
//  Routes publiques (pas d'authentification requise)
// ─────────────────────────────────────────────────────────────

// Authentification API
Route::post('/login', [AuthController::class, 'login']);

// Bibliothèque d'objets — lecture seule
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category}', [CategoryController::class, 'show']);
Route::get('/furniture-objects', [FurnitureObjectController::class, 'index']);
Route::get('/furniture-objects/{furnitureObject}', [FurnitureObjectController::class, 'show']);

// ─────────────────────────────────────────────────────────────
//  Assets 3D — Fichiers GLB et thumbnails (publics)
//  Accessibles par les applications front-end/VR/AR externes
// ─────────────────────────────────────────────────────────────

Route::prefix('furniture-objects/{furnitureObject}')->group(function () {
    // Télécharger le modèle GLB
    Route::get('/model', [AssetController::class, 'downloadModel'])
        ->name('api.furniture-objects.model');

    // Streamer le modèle GLB (support Range requests pour gros fichiers)
    Route::get('/model/stream', [AssetController::class, 'streamModel'])
        ->name('api.furniture-objects.model.stream');

    // Télécharger la thumbnail
    Route::get('/thumbnail', [AssetController::class, 'downloadThumbnail'])
        ->name('api.furniture-objects.thumbnail');

    // Requêtes OPTIONS pour CORS preflight
    Route::options('/model', fn () => response('', 204));
    Route::options('/model/stream', fn () => response('', 204));
    Route::options('/thumbnail', fn () => response('', 204));
});

// ─────────────────────────────────────────────────────────────
//  Routes protégées (authentification Sanctum requise)
// ─────────────────────────────────────────────────────────────

Route::middleware('auth:sanctum')->group(function () {

    // Utilisateur connecté
    Route::get('/user', function (Request $request) {
        $user = $request->user();
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'is_admin' => $user->is_admin,
        ]);
    });

    // Déconnexion API
    Route::post('/logout', [AuthController::class, 'logout']);

    // Projets — CRUD complet
    Route::apiResource('projects', ProjectController::class);

    // Pièce d'un projet — imbriquée
    Route::get('/projects/{project}/room', [RoomController::class, 'show']);
    Route::post('/projects/{project}/room', [RoomController::class, 'store']);
    Route::put('/projects/{project}/room', [RoomController::class, 'update']);
    Route::delete('/projects/{project}/room', [RoomController::class, 'destroy']);

    // Objets placés dans un projet — imbriquée
    Route::apiResource('projects.objects', ProjectObjectController::class)
        ->parameters(['objects' => 'object']);
});
