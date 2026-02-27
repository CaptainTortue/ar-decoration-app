@php
    $base = rtrim(config('app.url'), '/') . '/api';
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Documentation API — AR Décoration</title>
    <meta name="description" content="Documentation complète de l'API REST AR Décoration. Authentification, catalogue 3D, assets GLB, projets, pièces et objets de projet.">

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui'],
                        mono: ['JetBrains Mono', 'ui-monospace', 'monospace'],
                    },
                },
            },
        }
    </script>

    <style>
        /* Scrollbar sidebar */
        #sidebar::-webkit-scrollbar        { width: 4px; }
        #sidebar::-webkit-scrollbar-track  { background: transparent; }
        #sidebar::-webkit-scrollbar-thumb  { background: #334155; border-radius: 2px; }

        /* Smooth scroll */
        html { scroll-behavior: smooth; scroll-padding-top: 5rem; }

        /* Nav link active */
        .nav-link.active { color: #34d399; background-color: rgba(52, 211, 153, 0.08); }
        .nav-link { transition: color .15s, background-color .15s; }

        /* Endpoint card border-left color */
        .ep-get    { border-left-color: #10b981; }
        .ep-post   { border-left-color: #3b82f6; }
        .ep-put    { border-left-color: #f59e0b; }
        .ep-patch  { border-left-color: #8b5cf6; }
        .ep-delete { border-left-color: #ef4444; }

        /* Method badge */
        .badge-get    { background:#d1fae5; color:#065f46; }
        .badge-post   { background:#dbeafe; color:#1e40af; }
        .badge-put    { background:#fef3c7; color:#92400e; }
        .badge-patch  { background:#ede9fe; color:#5b21b6; }
        .badge-delete { background:#fee2e2; color:#991b1b; }

        /* Code block */
        .code-block { position:relative; }
        .copy-btn   { position:absolute; top:.6rem; right:.6rem; opacity:0; transition:opacity .15s; }
        .code-block:hover .copy-btn { opacity:1; }

        /* Table stripes */
        table tbody tr:nth-child(even) { background-color: #f8fafc; }

        /* Section fade-in anchor */
        .doc-section { padding-top: 1rem; }
    </style>
</head>

<body class="font-sans antialiased bg-slate-50 text-slate-800">

{{-- ─────────────────────────────────────────────────────────
     TOP NAVBAR
───────────────────────────────────────────────────────── --}}
<nav class="fixed top-0 left-0 right-0 z-50 bg-slate-900 border-b border-white/10" style="height:3.5rem">
    <div class="flex items-center justify-between h-full px-4 gap-3">

        <div class="flex items-center gap-3 min-w-0">
            <!-- Hamburger mobile -->
            <button id="sidebar-toggle"
                    class="lg:hidden flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-white hover:bg-white/10 transition-colors"
                    aria-label="Ouvrir le menu">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                </svg>
            </button>

            <!-- Logo -->
            <a href="/" class="flex items-center gap-2 flex-shrink-0 group">
                <div class="w-7 h-7 bg-emerald-500 group-hover:bg-emerald-400 rounded-lg flex items-center justify-center transition-colors">
                    <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"/>
                    </svg>
                </div>
                <span class="text-white font-semibold text-sm hidden sm:inline">AR Décoration</span>
            </a>

            <span class="text-slate-600 text-sm hidden sm:inline">/</span>
            <span class="text-slate-300 text-sm font-medium hidden sm:inline">Documentation API</span>
        </div>

        <div class="flex items-center gap-2 flex-shrink-0">
            <a href="/" class="text-slate-400 hover:text-white text-sm transition-colors hidden sm:inline">Accueil</a>
            <a href="/dashboard/login"
               class="bg-emerald-500 hover:bg-emerald-400 text-white text-sm font-medium px-4 py-1.5 rounded-lg transition-colors">
                Connexion
            </a>
        </div>
    </div>
</nav>

{{-- ─────────────────────────────────────────────────────────
     LAYOUT
───────────────────────────────────────────────────────── --}}
<div class="flex min-h-screen" style="padding-top:3.5rem">

    <!-- Overlay mobile -->
    <div id="sidebar-overlay" class="hidden fixed inset-0 bg-black/60 z-30 lg:hidden"></div>

    {{-- ─────────────────────────────────────────────────────────
         SIDEBAR
    ───────────────────────────────────────────────────────── --}}
    <aside id="sidebar"
           class="fixed left-0 bottom-0 w-64 bg-slate-900 border-r border-white/10 overflow-y-auto z-40 transition-transform duration-300 -translate-x-full lg:translate-x-0"
           style="top:3.5rem">
        <nav class="p-3 pb-8">

            <!-- Recherche rapide d'ancre (version simple) -->
            <div class="mb-4">
                <input id="nav-search" type="text" placeholder="Rechercher…"
                       class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-1.5 text-sm text-slate-300 placeholder-slate-600 focus:outline-none focus:border-emerald-500/50 focus:bg-white/8 transition-colors font-sans">
            </div>

            @php
            $navGroups = [
                'Introduction' => [
                    ['anchor' => 'intro',      'label' => 'Vue d\'ensemble'],
                    ['anchor' => 'auth-header','label' => 'Headers requis'],
                    ['anchor' => 'pagination', 'label' => 'Pagination'],
                    ['anchor' => 'errors',     'label' => 'Codes d\'erreur'],
                ],
                'Authentification' => [
                    ['anchor' => 'login',      'label' => 'POST /login',    'method' => 'post'],
                    ['anchor' => 'register',   'label' => 'POST /register', 'method' => 'post'],
                    ['anchor' => 'logout',     'label' => 'POST /logout',   'method' => 'post'],
                    ['anchor' => 'me',         'label' => 'GET /user',      'method' => 'get'],
                ],
                'Catégories' => [
                    ['anchor' => 'categories-list', 'label' => 'GET /categories',    'method' => 'get'],
                    ['anchor' => 'categories-show', 'label' => 'GET /categories/{id}','method' => 'get'],
                ],
                'Objets 3D' => [
                    ['anchor' => 'objects-list', 'label' => 'GET /furniture-objects',     'method' => 'get'],
                    ['anchor' => 'objects-show', 'label' => 'GET /furniture-objects/{id}','method' => 'get'],
                ],
                'Assets 3D' => [
                    ['anchor' => 'model-dl',     'label' => 'GET /{id}/model',        'method' => 'get'],
                    ['anchor' => 'model-stream', 'label' => 'GET /{id}/model/stream', 'method' => 'get'],
                    ['anchor' => 'thumbnail',    'label' => 'GET /{id}/thumbnail',    'method' => 'get'],
                ],
                'Projets' => [
                    ['anchor' => 'projects-list',    'label' => 'GET /projects',      'method' => 'get'],
                    ['anchor' => 'projects-create',  'label' => 'POST /projects',     'method' => 'post'],
                    ['anchor' => 'projects-show',    'label' => 'GET /projects/{id}', 'method' => 'get'],
                    ['anchor' => 'projects-update',  'label' => 'PUT /projects/{id}', 'method' => 'put'],
                    ['anchor' => 'projects-delete',  'label' => 'DELETE /projects/{id}','method' => 'delete'],
                ],
                'Pièce' => [
                    ['anchor' => 'room-show',   'label' => 'GET /projects/{id}/room',   'method' => 'get'],
                    ['anchor' => 'room-create', 'label' => 'POST /projects/{id}/room',  'method' => 'post'],
                    ['anchor' => 'room-update', 'label' => 'PUT /projects/{id}/room',   'method' => 'put'],
                    ['anchor' => 'room-delete', 'label' => 'DELETE /projects/{id}/room','method' => 'delete'],
                ],
                'Objets de projet' => [
                    ['anchor' => 'po-list',   'label' => 'GET /projects/{id}/objects',          'method' => 'get'],
                    ['anchor' => 'po-create', 'label' => 'POST /projects/{id}/objects',         'method' => 'post'],
                    ['anchor' => 'po-show',   'label' => 'GET …/objects/{objId}',               'method' => 'get'],
                    ['anchor' => 'po-update', 'label' => 'PUT …/objects/{objId}',               'method' => 'put'],
                    ['anchor' => 'po-delete', 'label' => 'DELETE …/objects/{objId}',            'method' => 'delete'],
                ],
            ];
            $methodDots = ['get'=>'bg-emerald-500','post'=>'bg-blue-500','put'=>'bg-amber-500','delete'=>'bg-red-500','patch'=>'bg-violet-500'];
            @endphp

            @foreach($navGroups as $group => $links)
            <div class="mb-1">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-widest px-3 py-2 mt-3">{{ $group }}</p>
                @foreach($links as $link)
                <a href="#{{ $link['anchor'] }}"
                   data-anchor="{{ $link['anchor'] }}"
                   class="nav-link flex items-center gap-2.5 px-3 py-1.5 rounded-lg text-slate-400 hover:text-white text-xs font-mono">
                    @if(isset($link['method']))
                    <span class="w-1.5 h-1.5 rounded-full flex-shrink-0 {{ $methodDots[$link['method']] ?? 'bg-slate-500' }}"></span>
                    @else
                    <span class="w-1.5 h-1.5 rounded-full flex-shrink-0 bg-slate-600"></span>
                    @endif
                    {{ $link['label'] }}
                </a>
                @endforeach
            </div>
            @endforeach
        </nav>
    </aside>

    {{-- ─────────────────────────────────────────────────────────
         MAIN CONTENT
    ───────────────────────────────────────────────────────── --}}
    <main class="flex-1 min-w-0 lg:ml-64">
        <div class="max-w-4xl mx-auto px-4 sm:px-8 py-10 space-y-20">

            {{-- ═══════════════════════════════════════════════
                 INTRODUCTION
            ═══════════════════════════════════════════════ --}}
            <section id="intro" class="doc-section">
                <div class="mb-8">
                    <span class="text-emerald-600 text-xs font-bold uppercase tracking-widest">Introduction</span>
                    <h1 class="text-4xl font-black text-slate-900 mt-2 mb-4">Documentation API</h1>
                    <p class="text-lg text-slate-500 leading-relaxed">
                        API REST JSON pour interagir avec la plateforme AR Décoration.
                        Utilisable par toute application front-end, VR ou AR via des appels HTTP standard.
                    </p>
                </div>

                <!-- URL de base -->
                <div class="bg-white border border-slate-200 rounded-2xl p-6 mb-6">
                    <h2 class="font-bold text-slate-800 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"/></svg>
                        URL de base
                    </h2>
                    <div class="code-block">
                        <pre class="bg-slate-900 text-emerald-400 font-mono text-sm rounded-xl px-5 py-4 overflow-x-auto">{{ $base }}</pre>
                        <button class="copy-btn bg-slate-700 hover:bg-slate-600 text-slate-300 text-xs px-2 py-1 rounded-md transition-colors" data-copy="{{ $base }}">Copier</button>
                    </div>
                    <p class="text-sm text-slate-500 mt-3">Toutes les routes décrites ci-dessous sont relatives à cette URL de base.</p>
                </div>

                <!-- Headers requis -->
                <div id="auth-header" class="bg-white border border-slate-200 rounded-2xl p-6 mb-6">
                    <h2 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.955 11.955 0 01.09 12c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-2.131-.544-4.136-1.5-5.892"/></svg>
                        Headers requis
                    </h2>
                    <table class="w-full text-sm border border-slate-100 rounded-xl overflow-hidden">
                        <thead class="bg-slate-50 text-slate-600 text-xs uppercase tracking-wide">
                            <tr>
                                <th class="text-left px-4 py-3 w-1/3">Header</th>
                                <th class="text-left px-4 py-3 w-1/3">Valeur</th>
                                <th class="text-left px-4 py-3">Requis</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr>
                                <td class="px-4 py-3 font-mono text-slate-700">Content-Type</td>
                                <td class="px-4 py-3 font-mono text-blue-600">application/json</td>
                                <td class="px-4 py-3"><span class="text-emerald-600 font-medium">Toujours</span></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 font-mono text-slate-700">Accept</td>
                                <td class="px-4 py-3 font-mono text-blue-600">application/json</td>
                                <td class="px-4 py-3"><span class="text-emerald-600 font-medium">Toujours</span></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 font-mono text-slate-700">Authorization</td>
                                <td class="px-4 py-3 font-mono text-blue-600">Bearer {token}</td>
                                <td class="px-4 py-3"><span class="text-amber-600 font-medium">Routes protégées</span></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 font-mono text-slate-700">Range</td>
                                <td class="px-4 py-3 font-mono text-blue-600">bytes=0-1048575</td>
                                <td class="px-4 py-3"><span class="text-slate-400 font-medium">Stream seulement</span></td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="mt-4 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 text-sm text-amber-800">
                        <strong>Token Sanctum :</strong> obtenez un token via <code class="font-mono bg-amber-100 px-1 rounded">POST /login</code>
                        puis passez-le dans chaque requête protégée sous la forme <code class="font-mono bg-amber-100 px-1 rounded">Authorization: Bearer {token}</code>.
                    </div>
                </div>

                <!-- Pagination -->
                <div id="pagination" class="bg-white border border-slate-200 rounded-2xl p-6 mb-6">
                    <h2 class="font-bold text-slate-800 mb-4">Pagination</h2>
                    <p class="text-sm text-slate-500 mb-4">Les routes de liste retournent un objet paginé Laravel standard. Utilisez le paramètre <code class="font-mono bg-slate-100 px-1 rounded">per_page</code> pour ajuster la taille de page.</p>
                    <div class="code-block">
                        <pre class="bg-slate-900 text-slate-300 font-mono text-xs rounded-xl px-5 py-4 overflow-x-auto leading-relaxed">{
  <span class="text-blue-300">"data"</span>: [ <span class="text-slate-500">/* objets */</span> ],
  <span class="text-blue-300">"links"</span>: {
    <span class="text-blue-300">"first"</span>: <span class="text-green-300">"{{ $base }}/furniture-objects?page=1"</span>,
    <span class="text-blue-300">"last"</span>:  <span class="text-green-300">"{{ $base }}/furniture-objects?page=4"</span>,
    <span class="text-blue-300">"prev"</span>:  <span class="text-amber-300">null</span>,
    <span class="text-blue-300">"next"</span>:  <span class="text-green-300">"{{ $base }}/furniture-objects?page=2"</span>
  },
  <span class="text-blue-300">"meta"</span>: {
    <span class="text-blue-300">"current_page"</span>: <span class="text-amber-300">1</span>,
    <span class="text-blue-300">"last_page"</span>:    <span class="text-amber-300">4</span>,
    <span class="text-blue-300">"per_page"</span>:     <span class="text-amber-300">20</span>,
    <span class="text-blue-300">"total"</span>:        <span class="text-amber-300">73</span>
  }
}</pre>
                    </div>
                </div>

                <!-- Codes d'erreur -->
                <div id="errors" class="bg-white border border-slate-200 rounded-2xl p-6">
                    <h2 class="font-bold text-slate-800 mb-4">Codes de réponse</h2>
                    <table class="w-full text-sm border border-slate-100 rounded-xl overflow-hidden">
                        <thead class="bg-slate-50 text-slate-600 text-xs uppercase tracking-wide">
                            <tr>
                                <th class="text-left px-4 py-3 w-24">Code</th>
                                <th class="text-left px-4 py-3">Signification</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach([
                                ['200','OK — requête réussie'],
                                ['201','Created — ressource créée'],
                                ['204','No Content — suppression réussie'],
                                ['206','Partial Content — réponse Range partielle'],
                                ['401','Unauthorized — token manquant ou invalide'],
                                ['403','Forbidden — accès non autorisé à cette ressource'],
                                ['404','Not Found — ressource introuvable'],
                                ['422','Unprocessable Entity — erreur de validation, corps JSON : {"message":"…","errors":{…}}'],
                                ['500','Server Error — erreur interne'],
                            ] as [$code, $desc])
                            <tr>
                                <td class="px-4 py-3 font-mono font-bold {{ str_starts_with($code,'2') ? 'text-emerald-600' : (str_starts_with($code,'4') ? 'text-red-500' : 'text-orange-500') }}">{{ $code }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $desc }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            {{-- ═══════════════════════════════════════════════
                 AUTHENTIFICATION
            ═══════════════════════════════════════════════ --}}
            <section class="doc-section">
                <div class="mb-6">
                    <span class="text-emerald-600 text-xs font-bold uppercase tracking-widest">Sécurité</span>
                    <h2 class="text-2xl font-black text-slate-900 mt-1">Authentification</h2>
                    <p class="text-slate-500 mt-1">Obtenez un token Sanctum via <code class="font-mono bg-slate-100 px-1 rounded text-sm">/login</code> ou <code class="font-mono bg-slate-100 px-1 rounded text-sm">/register</code>, puis passez-le dans le header <code class="font-mono bg-slate-100 px-1 rounded text-sm">Authorization: Bearer {token}</code>.</p>
                </div>
                <div class="space-y-6">

                    {{-- POST /login --}}
                    @include('docs._endpoint', [
                        'id'      => 'login',
                        'method'  => 'POST',
                        'path'    => '/login',
                        'public'  => true,
                        'summary' => 'Obtenir un token Sanctum. Ce token doit être inclus dans toutes les requêtes protégées.',
                        'body'    => [
                            ['name'=>'email',       'type'=>'string','req'=>true, 'desc'=>'Adresse email du compte'],
                            ['name'=>'password',    'type'=>'string','req'=>true, 'desc'=>'Mot de passe'],
                            ['name'=>'device_name', 'type'=>'string','req'=>true, 'desc'=>'Identifiant de l\'appareil, ex: "iPhone 15" ou "VR App"'],
                        ],
                        'response_code' => '200',
                        'response' => '{
  "token": "1|abc123xyz...",
  "user": {
    "id": 42,
    "name": "Alice Dupont",
    "email": "alice@example.com",
    "is_admin": false
  }
}',
                        'curl' => 'curl -X POST '.$base.'/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d \'{"email":"alice@example.com","password":"secret","device_name":"VR App"}\'',
                    ])

                    {{-- POST /register --}}
                    @include('docs._endpoint', [
                        'id'      => 'register',
                        'method'  => 'POST',
                        'path'    => '/register',
                        'public'  => true,
                        'summary' => 'Créer un nouveau compte utilisateur et retourner un token Sanctum.',
                        'body'    => [
                            ['name'=>'name',                  'type'=>'string','req'=>true, 'desc'=>'Nom complet, max 255 caractères'],
                            ['name'=>'email',                 'type'=>'string','req'=>true, 'desc'=>'Adresse email unique'],
                            ['name'=>'password',              'type'=>'string','req'=>true, 'desc'=>'Mot de passe, min 8 caractères'],
                            ['name'=>'password_confirmation', 'type'=>'string','req'=>true, 'desc'=>'Confirmation du mot de passe (identique à password)'],
                            ['name'=>'device_name',           'type'=>'string','req'=>true, 'desc'=>'Identifiant de l\'appareil'],
                        ],
                        'response_code' => '201',
                        'response' => '{
  "token": "2|def456uvw...",
  "user": {
    "id": 43,
    "name": "Bob Martin",
    "email": "bob@example.com",
    "is_admin": false
  }
}',
                        'curl' => 'curl -X POST '.$base.'/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d \'{"name":"Bob Martin","email":"bob@example.com","password":"secret123","password_confirmation":"secret123","device_name":"AR App"}\'',
                    ])

                    {{-- POST /logout --}}
                    @include('docs._endpoint', [
                        'id'      => 'logout',
                        'method'  => 'POST',
                        'path'    => '/logout',
                        'public'  => false,
                        'summary' => 'Révoquer le token courant. Le token ne sera plus utilisable après cet appel.',
                        'body'    => [],
                        'response_code' => '200',
                        'response' => '{"message": "Déconnexion réussie."}',
                        'curl' => 'curl -X POST '.$base.'/logout \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"',
                    ])

                    {{-- GET /user --}}
                    @include('docs._endpoint', [
                        'id'      => 'me',
                        'method'  => 'GET',
                        'path'    => '/user',
                        'public'  => false,
                        'summary' => 'Retourne les informations de l\'utilisateur propriétaire du token.',
                        'body'    => [],
                        'response_code' => '200',
                        'response' => '{
  "id": 42,
  "name": "Alice Dupont",
  "email": "alice@example.com",
  "is_admin": false
}',
                        'curl' => 'curl '.$base.'/user \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"',
                    ])
                </div>
            </section>

            {{-- ═══════════════════════════════════════════════
                 CATÉGORIES
            ═══════════════════════════════════════════════ --}}
            <section class="doc-section">
                <div class="mb-6">
                    <span class="text-emerald-600 text-xs font-bold uppercase tracking-widest">Catalogue</span>
                    <h2 class="text-2xl font-black text-slate-900 mt-1">Catégories</h2>
                    <p class="text-slate-500 mt-1">Routes publiques — aucun token requis.</p>
                </div>
                <div class="space-y-6">

                    @include('docs._endpoint', [
                        'id'      => 'categories-list',
                        'method'  => 'GET',
                        'path'    => '/categories',
                        'public'  => true,
                        'summary' => 'Liste toutes les catégories racines avec leurs sous-catégories et le nombre d\'objets.',
                        'body'    => [],
                        'response_code' => '200',
                        'response' => '{
  "data": [
    {
      "id": 1,
      "name": "Meubles",
      "slug": "meubles",
      "description": "Canapés, tables, chaises…",
      "parent_id": null,
      "furniture_objects_count": 42,
      "children": [
        { "id": 5, "name": "Canapés", "slug": "canapes", "parent_id": 1, "children": [] }
      ]
    }
  ]
}',
                        'curl' => 'curl '.$base.'/categories \
  -H "Accept: application/json"',
                    ])

                    @include('docs._endpoint', [
                        'id'      => 'categories-show',
                        'method'  => 'GET',
                        'path'    => '/categories/{id}',
                        'public'  => true,
                        'summary' => 'Retourne une catégorie avec ses sous-catégories, son parent et la liste de ses objets 3D.',
                        'params'  => [
                            ['name'=>'id','in'=>'path','req'=>true,'desc'=>'ID de la catégorie'],
                        ],
                        'body'    => [],
                        'response_code' => '200',
                        'response' => '{
  "data": {
    "id": 1,
    "name": "Meubles",
    "slug": "meubles",
    "parent_id": null,
    "furniture_objects_count": 42,
    "children": [ … ],
    "furniture_objects": [ … ]
  }
}',
                        'curl' => 'curl '.$base.'/categories/1 \
  -H "Accept: application/json"',
                    ])
                </div>
            </section>

            {{-- ═══════════════════════════════════════════════
                 OBJETS 3D
            ═══════════════════════════════════════════════ --}}
            <section class="doc-section">
                <div class="mb-6">
                    <span class="text-emerald-600 text-xs font-bold uppercase tracking-widest">Catalogue</span>
                    <h2 class="text-2xl font-black text-slate-900 mt-1">Objets 3D</h2>
                    <p class="text-slate-500 mt-1">Routes publiques — aucun token requis.</p>
                </div>
                <div class="space-y-6">

                    @include('docs._endpoint', [
                        'id'      => 'objects-list',
                        'method'  => 'GET',
                        'path'    => '/furniture-objects',
                        'public'  => true,
                        'summary' => 'Liste tous les objets 3D actifs. Supporte le filtrage et la recherche.',
                        'params'  => [
                            ['name'=>'category_id','in'=>'query','req'=>false,'desc'=>'Filtrer par ID de catégorie'],
                            ['name'=>'category',   'in'=>'query','req'=>false,'desc'=>'Filtrer par slug de catégorie (ex: canapes)'],
                            ['name'=>'search',     'in'=>'query','req'=>false,'desc'=>'Recherche par nom (LIKE)'],
                            ['name'=>'per_page',   'in'=>'query','req'=>false,'desc'=>'Nombre de résultats par page (défaut: 20)'],
                            ['name'=>'page',       'in'=>'query','req'=>false,'desc'=>'Numéro de page (défaut: 1)'],
                        ],
                        'body'    => [],
                        'response_code' => '200',
                        'response' => '{
  "data": [
    {
      "id": 1,
      "name": "Canapé Oslo",
      "slug": "canape-oslo",
      "description": "Canapé 3 places en tissu gris",
      "category_id": 5,
      "category": { "id": 5, "name": "Canapés", "slug": "canapes" },
      "model_url": "/storage/models/canape-oslo.glb",
      "thumbnail_url": "/storage/thumbnails/canape-oslo.webp",
      "assets": {
        "model":        "' . $base . '/furniture-objects/1/model",
        "model_stream": "' . $base . '/furniture-objects/1/model/stream",
        "thumbnail":    "' . $base . '/furniture-objects/1/thumbnail"
      },
      "dimensions": { "width": 2.2, "height": 0.85, "depth": 0.95 },
      "default_scale": 1.0,
      "price": 899.00,
      "is_active": true
    }
  ],
  "links": { … },
  "meta": { "current_page": 1, "total": 73, "per_page": 20 }
}',
                        'curl' => 'curl "'.$base.'/furniture-objects?category=canapes&per_page=10" \
  -H "Accept: application/json"',
                    ])

                    @include('docs._endpoint', [
                        'id'      => 'objects-show',
                        'method'  => 'GET',
                        'path'    => '/furniture-objects/{id}',
                        'public'  => true,
                        'summary' => 'Retourne un objet 3D avec sa catégorie et ses URLs d\'assets.',
                        'params'  => [
                            ['name'=>'id','in'=>'path','req'=>true,'desc'=>'ID de l\'objet 3D'],
                        ],
                        'body'    => [],
                        'response_code' => '200',
                        'response' => '{
  "data": {
    "id": 1,
    "name": "Canapé Oslo",
    "assets": {
      "model":        "' . $base . '/furniture-objects/1/model",
      "model_stream": "' . $base . '/furniture-objects/1/model/stream",
      "thumbnail":    "' . $base . '/furniture-objects/1/thumbnail"
    },
    "dimensions": { "width": 2.2, "height": 0.85, "depth": 0.95 },
    "available_colors": ["#FFFFFF","#808080","#1a1a1a"],
    "available_materials": ["tissu","cuir"],
    "price": 899.00
  }
}',
                        'curl' => 'curl '.$base.'/furniture-objects/1 \
  -H "Accept: application/json"',
                    ])
                </div>
            </section>

            {{-- ═══════════════════════════════════════════════
                 ASSETS 3D
            ═══════════════════════════════════════════════ --}}
            <section class="doc-section">
                <div class="mb-6">
                    <span class="text-emerald-600 text-xs font-bold uppercase tracking-widest">Assets</span>
                    <h2 class="text-2xl font-black text-slate-900 mt-1">Assets 3D</h2>
                    <p class="text-slate-500 mt-1">Routes publiques — aucun token requis. Retournent des fichiers binaires, pas du JSON.</p>
                </div>
                <div class="space-y-6">

                    @include('docs._endpoint', [
                        'id'      => 'model-dl',
                        'method'  => 'GET',
                        'path'    => '/furniture-objects/{id}/model',
                        'public'  => true,
                        'summary' => 'Télécharge le fichier GLB complet de l\'objet. Retourne le binaire avec Content-Type: model/gltf-binary.',
                        'params'  => [
                            ['name'=>'id','in'=>'path','req'=>true,'desc'=>'ID de l\'objet 3D'],
                        ],
                        'body'    => [],
                        'response_code' => '200',
                        'response' => '/* Binaire GLB */
Content-Type: model/gltf-binary
Content-Disposition: inline; filename="canape-oslo.glb"
Cache-Control: private, no-store',
                        'curl' => 'curl '.$base.'/furniture-objects/1/model \
  -H "Accept: application/json" \
  -o canape-oslo.glb',
                    ])

                    {{-- Stream --}}
                    <div id="model-stream" class="bg-white border border-l-4 ep-get border-slate-200 rounded-2xl overflow-hidden">
                        <div class="px-6 py-5 border-b border-slate-100">
                            <div class="flex flex-wrap items-center gap-3 mb-2">
                                <span class="badge-get font-mono text-xs font-bold px-2.5 py-1 rounded-lg">GET</span>
                                <code class="text-slate-800 font-mono text-sm font-semibold">/furniture-objects/{id}/model/stream</code>
                                <span class="text-xs bg-emerald-50 text-emerald-700 border border-emerald-200 px-2 py-0.5 rounded-full font-medium">Public</span>
                                <span class="text-xs bg-blue-50 text-blue-700 border border-blue-200 px-2 py-0.5 rounded-full font-medium">Range Requests</span>
                            </div>
                            <p class="text-slate-500 text-sm">Stream progressif du fichier GLB. Supporte les <strong>HTTP Range Requests</strong> (RFC 7233) pour le chargement par morceaux — idéal pour les gros fichiers dans les applications VR/AR.</p>
                        </div>
                        <div class="px-6 py-5 space-y-5">

                            <!-- Range expliqué -->
                            <div class="bg-blue-50 border border-blue-200 rounded-xl px-4 py-3 text-sm text-blue-900">
                                <strong class="block mb-1">Comment fonctionne le Range streaming ?</strong>
                                Envoyez le header <code class="font-mono bg-blue-100 px-1 rounded">Range: bytes=0-1048575</code> pour recevoir les premiers 1 Mo.
                                La réponse est <code class="font-mono bg-blue-100 px-1 rounded">206 Partial Content</code> avec le header
                                <code class="font-mono bg-blue-100 px-1 rounded">Content-Range: bytes 0-1048575/5242880</code>.
                                Répétez avec les octets suivants jusqu'à avoir tout le fichier.
                            </div>

                            <div>
                                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">Headers de réponse</p>
                                <table class="w-full text-sm border border-slate-100 rounded-xl overflow-hidden">
                                    <thead class="bg-slate-50 text-slate-600 text-xs uppercase tracking-wide">
                                        <tr>
                                            <th class="text-left px-4 py-2">Header</th>
                                            <th class="text-left px-4 py-2">Valeur</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        <tr><td class="px-4 py-2 font-mono text-slate-700">Content-Type</td><td class="px-4 py-2 font-mono text-blue-600">model/gltf-binary</td></tr>
                                        <tr><td class="px-4 py-2 font-mono text-slate-700">Accept-Ranges</td><td class="px-4 py-2 font-mono text-blue-600">bytes</td></tr>
                                        <tr><td class="px-4 py-2 font-mono text-slate-700">Content-Range</td><td class="px-4 py-2 font-mono text-blue-600">bytes 0-1048575/5242880</td></tr>
                                        <tr><td class="px-4 py-2 font-mono text-slate-700">Content-Length</td><td class="px-4 py-2 font-mono text-blue-600">1048576</td></tr>
                                    </tbody>
                                </table>
                            </div>

                            <div>
                                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">Exemple — premier chunk</p>
                                <div class="code-block">
                                    <pre class="bg-slate-900 text-slate-300 font-mono text-xs rounded-xl px-5 py-4 overflow-x-auto">curl {{ $base }}/furniture-objects/1/model/stream \
  -H "Range: bytes=0-1048575" \
  -H "Accept: application/json" \
  -o chunk_0.bin</pre>
                                    <button class="copy-btn bg-slate-700 hover:bg-slate-600 text-slate-300 text-xs px-2 py-1 rounded-md" data-copy="curl {{ $base }}/furniture-objects/1/model/stream -H 'Range: bytes=0-1048575' -H 'Accept: application/json' -o chunk_0.bin">Copier</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    @include('docs._endpoint', [
                        'id'      => 'thumbnail',
                        'method'  => 'GET',
                        'path'    => '/furniture-objects/{id}/thumbnail',
                        'public'  => true,
                        'summary' => 'Retourne l\'image de prévisualisation de l\'objet. Content-Type détecté automatiquement (image/webp, image/png, image/jpeg…).',
                        'params'  => [
                            ['name'=>'id','in'=>'path','req'=>true,'desc'=>'ID de l\'objet 3D'],
                        ],
                        'body'    => [],
                        'response_code' => '200',
                        'response' => '/* Binaire image */
Content-Type: image/webp
Content-Disposition: inline; filename="canape-oslo.webp"',
                        'curl' => 'curl '.$base.'/furniture-objects/1/thumbnail \
  -o thumbnail.webp',
                    ])
                </div>
            </section>

            {{-- ═══════════════════════════════════════════════
                 PROJETS
            ═══════════════════════════════════════════════ --}}
            <section class="doc-section">
                <div class="mb-6">
                    <span class="text-emerald-600 text-xs font-bold uppercase tracking-widest">Projets</span>
                    <h2 class="text-2xl font-black text-slate-900 mt-1">Projets</h2>
                    <p class="text-slate-500 mt-1">Routes protégées — token requis. Un utilisateur ne voit que ses propres projets. Un admin voit tous les projets.</p>
                </div>
                <div class="space-y-6">

                    @include('docs._endpoint', [
                        'id'      => 'projects-list',
                        'method'  => 'GET',
                        'path'    => '/projects',
                        'public'  => false,
                        'summary' => 'Liste les projets de l\'utilisateur connecté (ou tous les projets si admin).',
                        'params'  => [
                            ['name'=>'per_page','in'=>'query','req'=>false,'desc'=>'Résultats par page (défaut: 15)'],
                            ['name'=>'page',    'in'=>'query','req'=>false,'desc'=>'Numéro de page (défaut: 1)'],
                        ],
                        'body'    => [],
                        'response_code' => '200',
                        'response' => '{
  "data": [
    {
      "id": 1,
      "name": "Mon salon",
      "description": "Rénovation complète du salon",
      "status": "in_progress",
      "objects_count": 7,
      "user_id": 42,
      "created_at": "2025-01-15T10:30:00Z",
      "updated_at": "2025-02-01T14:22:00Z"
    }
  ],
  "meta": { "current_page": 1, "total": 3, "per_page": 15 }
}',
                        'curl' => 'curl '.$base.'/projects \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"',
                    ])

                    @include('docs._endpoint', [
                        'id'      => 'projects-create',
                        'method'  => 'POST',
                        'path'    => '/projects',
                        'public'  => false,
                        'summary' => 'Créer un nouveau projet de décoration.',
                        'body'    => [
                            ['name'=>'name',          'type'=>'string','req'=>true, 'desc'=>'Nom du projet, max 255 caractères'],
                            ['name'=>'description',   'type'=>'string','req'=>false,'desc'=>'Description, max 2000 caractères'],
                            ['name'=>'status',        'type'=>'string','req'=>false,'desc'=>'draft | in_progress | completed (défaut: draft)'],
                            ['name'=>'scene_settings','type'=>'object','req'=>false,'desc'=>'Paramètres de scène libres au format JSON'],
                        ],
                        'response_code' => '201',
                        'response' => '{
  "data": {
    "id": 12,
    "name": "Chambre parentale",
    "status": "draft",
    "room": null,
    "objects": [],
    "user_id": 42
  }
}',
                        'curl' => 'curl -X POST '.$base.'/projects \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d \'{"name":"Chambre parentale","description":"Projet de rénovation","status":"draft"}\'',
                    ])

                    @include('docs._endpoint', [
                        'id'      => 'projects-show',
                        'method'  => 'GET',
                        'path'    => '/projects/{id}',
                        'public'  => false,
                        'summary' => 'Retourne un projet avec sa pièce et la liste de ses objets placés (avec leurs assets 3D).',
                        'params'  => [
                            ['name'=>'id','in'=>'path','req'=>true,'desc'=>'ID du projet'],
                        ],
                        'body'    => [],
                        'response_code' => '200',
                        'response' => '{
  "data": {
    "id": 12,
    "name": "Chambre parentale",
    "status": "in_progress",
    "room": {
      "id": 3,
      "name": "Chambre",
      "dimensions": { "width": 4.5, "length": 5.0, "height": 2.7 },
      "floor": { "material": "wood", "color": "#C4A882" },
      "wall":  { "material": "paint", "color": "#FFFFFF" }
    },
    "objects": [
      {
        "id": 8,
        "furniture_object_id": 1,
        "position": { "x": 1.2, "y": 0.0, "z": 0.8 },
        "rotation": { "x": 0.0, "y": 90.0, "z": 0.0 },
        "scale":    { "x": 1.0, "y": 1.0, "z": 1.0 },
        "is_visible": true,
        "is_locked": false
      }
    ]
  }
}',
                        'curl' => 'curl '.$base.'/projects/12 \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"',
                    ])

                    @include('docs._endpoint', [
                        'id'      => 'projects-update',
                        'method'  => 'PUT',
                        'path'    => '/projects/{id}',
                        'public'  => false,
                        'summary' => 'Mettre à jour un projet. Seul le propriétaire (ou un admin) peut modifier.',
                        'params'  => [
                            ['name'=>'id','in'=>'path','req'=>true,'desc'=>'ID du projet'],
                        ],
                        'body'    => [
                            ['name'=>'name',          'type'=>'string','req'=>false,'desc'=>'Nouveau nom'],
                            ['name'=>'description',   'type'=>'string','req'=>false,'desc'=>'Nouvelle description'],
                            ['name'=>'status',        'type'=>'string','req'=>false,'desc'=>'draft | in_progress | completed'],
                            ['name'=>'scene_settings','type'=>'object','req'=>false,'desc'=>'Paramètres de scène JSON'],
                        ],
                        'response_code' => '200',
                        'response' => '{ "data": { "id": 12, "name": "Chambre parentale v2", "status": "in_progress", … } }',
                        'curl' => 'curl -X PUT '.$base.'/projects/12 \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d \'{"status":"completed"}\'',
                    ])

                    @include('docs._endpoint', [
                        'id'      => 'projects-delete',
                        'method'  => 'DELETE',
                        'path'    => '/projects/{id}',
                        'public'  => false,
                        'summary' => 'Supprimer un projet et toutes ses données (pièce + objets placés). Action irréversible.',
                        'params'  => [
                            ['name'=>'id','in'=>'path','req'=>true,'desc'=>'ID du projet'],
                        ],
                        'body'    => [],
                        'response_code' => '200',
                        'response' => '{"message": "Projet supprimé."}',
                        'curl' => 'curl -X DELETE '.$base.'/projects/12 \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"',
                    ])
                </div>
            </section>

            {{-- ═══════════════════════════════════════════════
                 PIÈCE
            ═══════════════════════════════════════════════ --}}
            <section class="doc-section">
                <div class="mb-6">
                    <span class="text-emerald-600 text-xs font-bold uppercase tracking-widest">Projets</span>
                    <h2 class="text-2xl font-black text-slate-900 mt-1">Pièce configurée</h2>
                    <p class="text-slate-500 mt-1">Routes protégées. Chaque projet dispose d'une pièce optionnelle définissant l'espace 3D.</p>
                </div>
                <div class="space-y-6">

                    @php
                    $roomBody = [
                        ['name'=>'name',              'type'=>'string', 'req'=>false,'desc'=>'Nom de la pièce, ex: "Salon"'],
                        ['name'=>'width',             'type'=>'number', 'req'=>true, 'desc'=>'Largeur en mètres (0.1–100)'],
                        ['name'=>'length',            'type'=>'number', 'req'=>true, 'desc'=>'Longueur en mètres (0.1–100)'],
                        ['name'=>'height',            'type'=>'number', 'req'=>true, 'desc'=>'Hauteur en mètres (0.1–20)'],
                        ['name'=>'floor_material',    'type'=>'string', 'req'=>false,'desc'=>'wood | laminate | tile | carpet | concrete | marble | vinyl'],
                        ['name'=>'floor_color',       'type'=>'string', 'req'=>false,'desc'=>'Couleur hexadécimale, ex: "#C4A882"'],
                        ['name'=>'wall_material',     'type'=>'string', 'req'=>false,'desc'=>'paint | wallpaper | brick | stone | wood_panel | concrete | plaster'],
                        ['name'=>'wall_color',        'type'=>'string', 'req'=>false,'desc'=>'Couleur hexadécimale, ex: "#FFFFFF"'],
                        ['name'=>'lighting_settings', 'type'=>'object', 'req'=>false,'desc'=>'Objet JSON libre, ex: {"ambient_intensity":"0.5"}'],
                    ];
                    $roomResponse = '{
  "data": {
    "id": 3,
    "project_id": 12,
    "name": "Chambre",
    "dimensions": { "width": 4.5, "length": 5.0, "height": 2.7 },
    "floor": { "material": "wood", "color": "#C4A882" },
    "wall":  { "material": "paint", "color": "#FFFFFF" },
    "lighting_settings": { "ambient_intensity": "0.5", "shadow_enabled": "true" }
  }
}';
                    @endphp

                    @include('docs._endpoint', [
                        'id'      => 'room-show',
                        'method'  => 'GET',
                        'path'    => '/projects/{id}/room',
                        'public'  => false,
                        'summary' => 'Récupère la configuration de la pièce d\'un projet.',
                        'params'  => [['name'=>'id','in'=>'path','req'=>true,'desc'=>'ID du projet']],
                        'body'    => [],
                        'response_code' => '200',
                        'response' => $roomResponse,
                        'curl' => 'curl '.$base.'/projects/12/room \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"',
                    ])

                    @include('docs._endpoint', [
                        'id'      => 'room-create',
                        'method'  => 'POST',
                        'path'    => '/projects/{id}/room',
                        'public'  => false,
                        'summary' => 'Crée (ou remplace) la pièce du projet. Si une pièce existe déjà, elle est supprimée et remplacée.',
                        'params'  => [['name'=>'id','in'=>'path','req'=>true,'desc'=>'ID du projet']],
                        'body'    => $roomBody,
                        'response_code' => '201',
                        'response' => $roomResponse,
                        'curl' => 'curl -X POST '.$base.'/projects/12/room \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d \'{"width":4.5,"length":5.0,"height":2.7,"floor_material":"wood","wall_material":"paint","wall_color":"#FFFFFF"}\'',
                    ])

                    @include('docs._endpoint', [
                        'id'      => 'room-update',
                        'method'  => 'PUT',
                        'path'    => '/projects/{id}/room',
                        'public'  => false,
                        'summary' => 'Met à jour partiellement la pièce existante. Seuls les champs envoyés sont modifiés.',
                        'params'  => [['name'=>'id','in'=>'path','req'=>true,'desc'=>'ID du projet']],
                        'body'    => array_map(fn($f) => array_merge($f, ['req'=>false]), $roomBody),
                        'response_code' => '200',
                        'response' => $roomResponse,
                        'curl' => 'curl -X PUT '.$base.'/projects/12/room \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d \'{"height":3.0,"floor_material":"marble"}\'',
                    ])

                    @include('docs._endpoint', [
                        'id'      => 'room-delete',
                        'method'  => 'DELETE',
                        'path'    => '/projects/{id}/room',
                        'public'  => false,
                        'summary' => 'Supprime la configuration de la pièce. Les objets placés dans le projet ne sont pas affectés.',
                        'params'  => [['name'=>'id','in'=>'path','req'=>true,'desc'=>'ID du projet']],
                        'body'    => [],
                        'response_code' => '200',
                        'response' => '{"message": "Pièce supprimée."}',
                        'curl' => 'curl -X DELETE '.$base.'/projects/12/room \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"',
                    ])
                </div>
            </section>

            {{-- ═══════════════════════════════════════════════
                 OBJETS DE PROJET
            ═══════════════════════════════════════════════ --}}
            <section class="doc-section">
                <div class="mb-6">
                    <span class="text-emerald-600 text-xs font-bold uppercase tracking-widest">Projets</span>
                    <h2 class="text-2xl font-black text-slate-900 mt-1">Objets de projet</h2>
                    <p class="text-slate-500 mt-1">Routes protégées. Représentent les objets 3D placés dans un projet avec leur position, rotation et échelle.</p>
                </div>
                <div class="space-y-6">

                    @php
                    $poBody = [
                        ['name'=>'furniture_object_id','type'=>'integer','req'=>true, 'desc'=>'ID de l\'objet 3D à placer (doit exister et être actif)'],
                        ['name'=>'position_x',         'type'=>'number', 'req'=>false,'desc'=>'Position X en mètres (défaut: 0)'],
                        ['name'=>'position_y',         'type'=>'number', 'req'=>false,'desc'=>'Position Y en mètres (défaut: 0)'],
                        ['name'=>'position_z',         'type'=>'number', 'req'=>false,'desc'=>'Position Z en mètres (défaut: 0)'],
                        ['name'=>'rotation_x',         'type'=>'number', 'req'=>false,'desc'=>'Rotation X en degrés (défaut: 0)'],
                        ['name'=>'rotation_y',         'type'=>'number', 'req'=>false,'desc'=>'Rotation Y en degrés (défaut: 0)'],
                        ['name'=>'rotation_z',         'type'=>'number', 'req'=>false,'desc'=>'Rotation Z en degrés (défaut: 0)'],
                        ['name'=>'scale_x',            'type'=>'number', 'req'=>false,'desc'=>'Échelle X, min 0.001 (défaut: 1)'],
                        ['name'=>'scale_y',            'type'=>'number', 'req'=>false,'desc'=>'Échelle Y, min 0.001 (défaut: 1)'],
                        ['name'=>'scale_z',            'type'=>'number', 'req'=>false,'desc'=>'Échelle Z, min 0.001 (défaut: 1)'],
                        ['name'=>'color',              'type'=>'string', 'req'=>false,'desc'=>'Couleur appliquée, max 50 caractères'],
                        ['name'=>'material',           'type'=>'string', 'req'=>false,'desc'=>'Matériau appliqué, max 100 caractères'],
                        ['name'=>'is_visible',         'type'=>'boolean','req'=>false,'desc'=>'Visibilité de l\'objet (défaut: true)'],
                        ['name'=>'is_locked',          'type'=>'boolean','req'=>false,'desc'=>'Verrouillage en édition (défaut: false)'],
                    ];
                    $poResponse = '{
  "data": {
    "id": 8,
    "project_id": 12,
    "furniture_object_id": 1,
    "furniture_object": {
      "id": 1,
      "name": "Canapé Oslo",
      "assets": {
        "model":     "' . $base . '/furniture-objects/1/model",
        "thumbnail": "' . $base . '/furniture-objects/1/thumbnail"
      },
      "dimensions": { "width": 2.2, "height": 0.85, "depth": 0.95 }
    },
    "position": { "x": 1.2, "y": 0.0, "z": 0.8 },
    "rotation": { "x": 0.0, "y": 90.0, "z": 0.0 },
    "scale":    { "x": 1.0, "y": 1.0, "z": 1.0 },
    "color": null,
    "material": null,
    "is_visible": true,
    "is_locked": false
  }
}';
                    @endphp

                    @include('docs._endpoint', [
                        'id'      => 'po-list',
                        'method'  => 'GET',
                        'path'    => '/projects/{id}/objects',
                        'public'  => false,
                        'summary' => 'Liste tous les objets placés dans un projet avec leurs métadonnées 3D et assets.',
                        'params'  => [['name'=>'id','in'=>'path','req'=>true,'desc'=>'ID du projet']],
                        'body'    => [],
                        'response_code' => '200',
                        'response' => '{ "data": [ … ] }',
                        'curl' => 'curl '.$base.'/projects/12/objects \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"',
                    ])

                    @include('docs._endpoint', [
                        'id'      => 'po-create',
                        'method'  => 'POST',
                        'path'    => '/projects/{id}/objects',
                        'public'  => false,
                        'summary' => 'Ajouter un objet 3D dans un projet avec sa position, rotation et échelle.',
                        'params'  => [['name'=>'id','in'=>'path','req'=>true,'desc'=>'ID du projet']],
                        'body'    => $poBody,
                        'response_code' => '201',
                        'response' => $poResponse,
                        'curl' => 'curl -X POST '.$base.'/projects/12/objects \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d \'{"furniture_object_id":1,"position_x":1.2,"position_y":0,"position_z":0.8,"rotation_y":90}\'',
                    ])

                    @include('docs._endpoint', [
                        'id'      => 'po-show',
                        'method'  => 'GET',
                        'path'    => '/projects/{id}/objects/{objectId}',
                        'public'  => false,
                        'summary' => 'Retourne un objet placé spécifique avec ses données complètes.',
                        'params'  => [
                            ['name'=>'id',      'in'=>'path','req'=>true,'desc'=>'ID du projet'],
                            ['name'=>'objectId','in'=>'path','req'=>true,'desc'=>'ID de l\'objet placé'],
                        ],
                        'body'    => [],
                        'response_code' => '200',
                        'response' => $poResponse,
                        'curl' => 'curl '.$base.'/projects/12/objects/8 \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"',
                    ])

                    @include('docs._endpoint', [
                        'id'      => 'po-update',
                        'method'  => 'PUT',
                        'path'    => '/projects/{id}/objects/{objectId}',
                        'public'  => false,
                        'summary' => 'Met à jour la transformation (position, rotation, échelle) ou les propriétés d\'un objet placé.',
                        'params'  => [
                            ['name'=>'id',      'in'=>'path','req'=>true,'desc'=>'ID du projet'],
                            ['name'=>'objectId','in'=>'path','req'=>true,'desc'=>'ID de l\'objet placé'],
                        ],
                        'body'    => array_filter($poBody, fn($f) => $f['name'] !== 'furniture_object_id'),
                        'response_code' => '200',
                        'response' => $poResponse,
                        'curl' => 'curl -X PUT '.$base.'/projects/12/objects/8 \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d \'{"position_x":2.5,"rotation_y":180,"is_locked":true}\'',
                    ])

                    @include('docs._endpoint', [
                        'id'      => 'po-delete',
                        'method'  => 'DELETE',
                        'path'    => '/projects/{id}/objects/{objectId}',
                        'public'  => false,
                        'summary' => 'Retirer un objet du projet. L\'objet 3D du catalogue n\'est pas supprimé.',
                        'params'  => [
                            ['name'=>'id',      'in'=>'path','req'=>true,'desc'=>'ID du projet'],
                            ['name'=>'objectId','in'=>'path','req'=>true,'desc'=>'ID de l\'objet placé'],
                        ],
                        'body'    => [],
                        'response_code' => '200',
                        'response' => '{"message": "Objet supprimé du projet."}',
                        'curl' => 'curl -X DELETE '.$base.'/projects/12/objects/8 \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"',
                    ])

                </div>
            </section>

            <!-- Footer docs -->
            <footer class="border-t border-slate-200 pt-10 pb-16 text-center">
                <p class="text-slate-400 text-sm">&copy; {{ date('Y') }} AR Décoration — <a href="/" class="hover:text-emerald-600 transition-colors">Retour à l'accueil</a></p>
            </footer>

        </div>
    </main>
</div>

{{-- ─────────────────────────────────────────────────────────
     SCRIPTS
───────────────────────────────────────────────────────── --}}
<script>
// ── Sidebar mobile toggle ──────────────────────────────────
const sidebar  = document.getElementById('sidebar');
const overlay  = document.getElementById('sidebar-overlay');
const toggle   = document.getElementById('sidebar-toggle');

function openSidebar()  { sidebar.classList.remove('-translate-x-full'); overlay.classList.remove('hidden'); }
function closeSidebar() { sidebar.classList.add('-translate-x-full');    overlay.classList.add('hidden'); }

toggle?.addEventListener('click',  openSidebar);
overlay?.addEventListener('click',  closeSidebar);

// ── Sidebar nav search ────────────────────────────────────
document.getElementById('nav-search')?.addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.nav-link').forEach(el => {
        el.style.display = el.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
    document.querySelectorAll('#sidebar nav > div').forEach(group => {
        const visible = [...group.querySelectorAll('.nav-link')].some(l => l.style.display !== 'none');
        group.style.display = visible ? '' : 'none';
    });
});

// ── Copy to clipboard ─────────────────────────────────────
document.querySelectorAll('.copy-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const text = btn.dataset.copy || btn.closest('.code-block')?.querySelector('pre')?.textContent || '';
        navigator.clipboard.writeText(text.trim()).then(() => {
            const original = btn.textContent;
            btn.textContent = '✓ Copié';
            btn.classList.add('text-emerald-400');
            setTimeout(() => { btn.textContent = original; btn.classList.remove('text-emerald-400'); }, 1500);
        });
    });
});

// ── Active section highlighting ───────────────────────────
const sections = document.querySelectorAll('.doc-section, [id]');
const navLinks  = document.querySelectorAll('.nav-link[data-anchor]');

const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            navLinks.forEach(link => link.classList.remove('active'));
            const anchor = entry.target.id;
            const active = document.querySelector(`.nav-link[data-anchor="${anchor}"]`);
            if (active) {
                active.classList.add('active');
                active.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
            }
        }
    });
}, { rootMargin: '-20% 0px -70% 0px' });

document.querySelectorAll('[id]').forEach(el => observer.observe(el));

// ── Add copy buttons to all code blocks without one ──────
document.querySelectorAll('.code-block').forEach(block => {
    if (!block.querySelector('.copy-btn')) {
        const btn = document.createElement('button');
        btn.className = 'copy-btn bg-slate-700 hover:bg-slate-600 text-slate-300 text-xs px-2 py-1 rounded-md transition-colors';
        btn.textContent = 'Copier';
        block.appendChild(btn);
        btn.addEventListener('click', () => {
            const text = block.querySelector('pre')?.textContent || '';
            navigator.clipboard.writeText(text.trim()).then(() => {
                btn.textContent = '✓ Copié';
                btn.classList.add('text-emerald-400');
                setTimeout(() => { btn.textContent = 'Copier'; btn.classList.remove('text-emerald-400'); }, 1500);
            });
        });
    }
});
</script>

</body>
</html>
