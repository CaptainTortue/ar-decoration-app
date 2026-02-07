<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CorsMiddleware
{
    /**
     * Gère les requêtes CORS pour les applications externes (front-end, VR, AR).
     *
     * Ce middleware permet aux applications externes d'accéder à l'API
     * tout en maintenant la sécurité via les tokens Sanctum pour les
     * routes protégées.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Gérer les requêtes OPTIONS (pré-vol CORS)
        if ($request->isMethod('OPTIONS')) {
            return response('', 200)
                ->header('Access-Control-Allow-Origin', $this->getAllowedOrigin($request))
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept, Origin')
                ->header('Access-Control-Allow-Credentials', 'true')
                ->header('Access-Control-Max-Age', '86400'); // Cache preflight 24h
        }

        $response = $next($request);

        // Ajouter les headers CORS à toutes les réponses API
        $response->headers->set('Access-Control-Allow-Origin', $this->getAllowedOrigin($request));
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept, Origin');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');
        $response->headers->set('Access-Control-Expose-Headers', 'Content-Length, Content-Range, Accept-Ranges');

        return $response;
    }

    /**
     * Détermine l'origine autorisée.
     *
     * En production, vous pouvez configurer une liste blanche d'origines
     * via la variable d'environnement CORS_ALLOWED_ORIGINS.
     */
    private function getAllowedOrigin(Request $request): string
    {
        // Liste des origines autorisées (configurable via .env)
        $allowedOrigins = array_filter(
            explode(',', env('CORS_ALLOWED_ORIGINS', '*'))
        );

        // Si '*' est dans la liste, autoriser toutes les origines
        if (in_array('*', $allowedOrigins)) {
            return '*';
        }

        // Sinon, vérifier si l'origine de la requête est autorisée
        $origin = $request->header('Origin', '');

        if (in_array($origin, $allowedOrigins)) {
            return $origin;
        }

        // Par défaut, retourner la première origine autorisée
        return $allowedOrigins[0] ?? '*';
    }
}
