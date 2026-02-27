<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AR Décoration — Visualisez votre intérieur en réalité augmentée</title>
    <meta name="description" content="Planifiez et visualisez votre décoration intérieure en 3D. Placez des meubles virtuels, configurez votre pièce et exportez vers vos applications VR/AR.">

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (CDN — landing page uniquement) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                },
            },
        }
    </script>

    <style>
        .gradient-hero {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 60%, #064e3b 100%);
        }
        .gradient-text {
            background: linear-gradient(135deg, #6ee7b7, #10b981, #059669);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .card-hover {
            transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
        }
        .card-hover:hover {
            transform: translateY(-6px);
            box-shadow: 0 24px 48px rgba(16, 185, 129, 0.08);
        }
        .floating       { animation: float 7s  ease-in-out infinite; }
        .floating-slow  { animation: float 10s ease-in-out infinite 1.5s; }
        .floating-slower{ animation: float 12s ease-in-out infinite 3s; }
        @keyframes float {
            0%, 100% { transform: translateY(0px)   rotate(0deg); }
            33%       { transform: translateY(-14px) rotate(3deg); }
            66%       { transform: translateY(-7px)  rotate(-2deg); }
        }
        .pulse-dot { animation: pdot 2s ease-in-out infinite; }
        @keyframes pdot {
            0%, 100% { opacity: 1; }
            50%       { opacity: 0.35; }
        }
        ::-webkit-scrollbar       { width: 6px; }
        ::-webkit-scrollbar-track { background: #0f172a; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 3px; }
    </style>
</head>

<body class="font-sans antialiased bg-white text-gray-900 overflow-x-hidden">

    {{-- =========================================================
         NAVIGATION
    ========================================================== --}}
    <nav class="fixed top-0 left-0 right-0 z-50 bg-slate-900/90 backdrop-blur-md border-b border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                <!-- Logo -->
                <a href="/" class="flex items-center gap-2.5 group">
                    <div class="w-9 h-9 bg-emerald-500 group-hover:bg-emerald-400 rounded-xl flex items-center justify-center transition-colors shadow-lg shadow-emerald-500/30">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"/>
                        </svg>
                    </div>
                    <span class="text-white font-bold text-lg tracking-tight">AR Décoration</span>
                </a>

                <!-- Actions -->
                <div class="flex items-center gap-2">
                    <a href="/docs"
                       class="hidden md:inline-flex text-slate-300 hover:text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors hover:bg-white/5">
                        Documentation
                    </a>
                    <a href="/dashboard/login"
                       class="hidden sm:inline-flex text-slate-300 hover:text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors hover:bg-white/5">
                        Connexion
                    </a>
                    <a href="/dashboard/register"
                       class="bg-emerald-500 hover:bg-emerald-400 text-white text-sm font-semibold px-5 py-2 rounded-lg transition-all shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/40">
                        Commencer gratuitement
                    </a>
                </div>
            </div>
        </div>
    </nav>


    {{-- =========================================================
         HERO
    ========================================================== --}}
    <section class="gradient-hero min-h-screen flex items-center pt-16 relative overflow-hidden">

        <!-- Halos lumineux -->
        <div class="absolute top-1/3 right-0 w-[600px] h-[600px] bg-emerald-500/8 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-1/4 w-[500px] h-[500px] bg-emerald-700/8 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Cubes décoratifs flottants -->
        <div class="absolute right-16 top-1/4 hidden xl:block floating" aria-hidden="true">
            <svg width="130" height="150" viewBox="0 0 120 140" fill="none">
                <polygon points="60,10 110,37 60,64 10,37"  fill="rgba(52,211,153,0.10)" stroke="rgba(52,211,153,0.50)" stroke-width="1.5"/>
                <polygon points="10,37 60,64 60,124 10,97"  fill="rgba(52,211,153,0.06)" stroke="rgba(52,211,153,0.35)" stroke-width="1.5"/>
                <polygon points="110,37 60,64 60,124 110,97" fill="rgba(52,211,153,0.14)" stroke="rgba(52,211,153,0.50)" stroke-width="1.5"/>
            </svg>
        </div>
        <div class="absolute right-52 top-1/2 hidden xl:block floating-slow" aria-hidden="true">
            <svg width="55" height="65" viewBox="0 0 120 140" fill="none">
                <polygon points="60,10 110,37 60,64 10,37"  fill="rgba(52,211,153,0.08)" stroke="rgba(52,211,153,0.40)" stroke-width="1.5"/>
                <polygon points="10,37 60,64 60,124 10,97"  fill="rgba(52,211,153,0.04)" stroke="rgba(52,211,153,0.25)" stroke-width="1.5"/>
                <polygon points="110,37 60,64 60,124 110,97" fill="rgba(52,211,153,0.10)" stroke="rgba(52,211,153,0.40)" stroke-width="1.5"/>
            </svg>
        </div>
        <div class="absolute left-16 bottom-1/3 hidden xl:block floating-slower" aria-hidden="true">
            <svg width="70" height="82" viewBox="0 0 120 140" fill="none">
                <polygon points="60,10 110,37 60,64 10,37"  fill="rgba(52,211,153,0.07)" stroke="rgba(52,211,153,0.30)" stroke-width="1.5"/>
                <polygon points="10,37 60,64 60,124 10,97"  fill="rgba(52,211,153,0.04)" stroke="rgba(52,211,153,0.20)" stroke-width="1.5"/>
                <polygon points="110,37 60,64 60,124 110,97" fill="rgba(52,211,153,0.09)" stroke="rgba(52,211,153,0.30)" stroke-width="1.5"/>
            </svg>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 relative w-full">
            <div class="max-w-3xl">

                <!-- Badge -->
                <div class="inline-flex items-center gap-2.5 bg-emerald-500/10 border border-emerald-500/30 rounded-full px-4 py-2 mb-10">
                    <span class="w-2 h-2 bg-emerald-400 rounded-full pulse-dot"></span>
                    <span class="text-emerald-400 text-sm font-medium">Nouvelle expérience de décoration intérieure</span>
                </div>

                <!-- Titre principal -->
                <h1 class="text-5xl sm:text-6xl lg:text-7xl font-black text-white leading-[1.05] mb-7">
                    Visualisez votre<br>
                    intérieur en<br>
                    <span class="gradient-text">réalité augmentée</span>
                </h1>

                <!-- Sous-titre -->
                <p class="text-xl text-slate-400 leading-relaxed mb-12 max-w-2xl">
                    Placez virtuellement des meubles et objets 3D dans votre espace. Configurez chaque détail,
                    gérez vos projets de décoration et exportez vers vos applications VR/AR via notre API.
                </p>

                <!-- CTA -->
                <div class="flex flex-col sm:flex-row gap-4 mb-16">
                    <a href="/dashboard/register"
                       class="bg-emerald-500 hover:bg-emerald-400 text-white font-semibold px-9 py-4 rounded-xl text-lg transition-all shadow-xl shadow-emerald-500/20 hover:shadow-emerald-500/40 text-center">
                        Commencer gratuitement →
                    </a>
                    <a href="/dashboard/login"
                       class="border border-white/20 hover:border-white/40 text-white font-semibold px-9 py-4 rounded-xl text-lg transition-all hover:bg-white/5 text-center">
                        Se connecter
                    </a>
                </div>

                <!-- Stats rapides -->
                <div class="flex flex-wrap items-center gap-8 pt-8 border-t border-white/10">
                    <div>
                        <div class="text-3xl font-extrabold text-white">GLB</div>
                        <div class="text-slate-400 text-sm mt-0.5">Objets 3D natifs</div>
                    </div>
                    <div class="w-px h-10 bg-white/10 hidden sm:block"></div>
                    <div>
                        <div class="text-3xl font-extrabold text-white">API REST</div>
                        <div class="text-slate-400 text-sm mt-0.5">VR / AR ready</div>
                    </div>
                    <div class="w-px h-10 bg-white/10 hidden sm:block"></div>
                    <div>
                        <div class="text-3xl font-extrabold text-white">Gratuit</div>
                        <div class="text-slate-400 text-sm mt-0.5">Pour commencer</div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    {{-- =========================================================
         FONCTIONNALITÉS
    ========================================================== --}}
    <section id="fonctionnalites" class="py-28 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="text-center mb-20">
                <span class="text-emerald-600 font-semibold text-sm uppercase tracking-widest">Fonctionnalités</span>
                <h2 class="text-4xl sm:text-5xl font-black text-gray-900 mt-3 mb-5">
                    Tout pour votre projet de décoration
                </h2>
                <p class="text-xl text-gray-500 max-w-2xl mx-auto leading-relaxed">
                    Une suite complète d'outils pour planifier, visualiser et réaliser votre intérieur idéal.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-7">

                <!-- Catalogue 3D -->
                <div class="card-hover bg-gray-50 hover:bg-white border border-gray-100 hover:border-emerald-100 rounded-2xl p-7">
                    <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-5">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Catalogue 3D</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">
                        Bibliothèque d'objets 3D en format GLB haute qualité : meubles, luminaires, décoration murale et bien plus.
                    </p>
                </div>

                <!-- Configuration de pièce -->
                <div class="card-hover bg-gray-50 hover:bg-white border border-gray-100 hover:border-emerald-100 rounded-2xl p-7">
                    <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-5">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Configuration de pièce</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">
                        Définissez les dimensions exactes, choisissez matériaux du sol et des murs. Votre espace virtuel à l'identique.
                    </p>
                </div>

                <!-- Gestion de projets -->
                <div class="card-hover bg-gray-50 hover:bg-white border border-gray-100 hover:border-emerald-100 rounded-2xl p-7">
                    <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-5">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 00-1.883 2.542l.857 6a2.25 2.25 0 002.227 1.932H19.05a2.25 2.25 0 002.227-1.932l.857-6a2.25 2.25 0 00-1.883-2.542m-16.5 0V6A2.25 2.25 0 016 3.75h3.879a1.5 1.5 0 011.06.44l2.122 2.12a1.5 1.5 0 001.06.44H18A2.25 2.25 0 0120.25 9v.776"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Gestion de projets</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">
                        Créez, sauvegardez et gérez plusieurs projets en parallèle. Suivez l'avancement de chaque aménagement.
                    </p>
                </div>

                <!-- API VR/AR -->
                <div class="card-hover bg-gray-50 hover:bg-white border border-gray-100 hover:border-emerald-100 rounded-2xl p-7">
                    <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-5">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 7.5l3 2.25-3 2.25m4.5 0h3m-9 8.25h13.5A2.25 2.25 0 0021 18V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v12a2.25 2.25 0 002.25 2.25z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">API VR/AR Ready</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">
                        Accédez à vos données et fichiers GLB via une API REST sécurisée. Compatible avec toutes les applications AR/VR.
                    </p>
                </div>
            </div>
        </div>
    </section>


    {{-- =========================================================
         COMMENT ÇA MARCHE
    ========================================================== --}}
    <section class="py-28 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="text-center mb-20">
                <span class="text-emerald-600 font-semibold text-sm uppercase tracking-widest">Processus</span>
                <h2 class="text-4xl sm:text-5xl font-black text-gray-900 mt-3 mb-5">
                    Commencez en 3 étapes
                </h2>
                <p class="text-xl text-gray-500 max-w-2xl mx-auto leading-relaxed">
                    Simple et rapide. Votre premier projet de décoration AR en quelques minutes.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-10 relative">

                <!-- Ligne de connexion (desktop) -->
                <div class="hidden md:block absolute top-[2.75rem] left-[calc(16.66%+2.5rem)] right-[calc(16.66%+2.5rem)] h-px bg-gradient-to-r from-emerald-200 via-emerald-300 to-emerald-200"></div>

                <!-- Étape 1 -->
                <div class="flex flex-col items-center text-center">
                    <div class="w-[4.5rem] h-[4.5rem] bg-emerald-500 rounded-2xl flex items-center justify-center shadow-xl shadow-emerald-500/30 mb-8">
                        <span class="text-white text-2xl font-black">1</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Créez votre compte</h3>
                    <p class="text-gray-500 leading-relaxed max-w-xs">
                        Inscription gratuite et immédiate. Aucune carte bancaire requise pour commencer à explorer.
                    </p>
                </div>

                <!-- Étape 2 -->
                <div class="flex flex-col items-center text-center">
                    <div class="w-[4.5rem] h-[4.5rem] bg-emerald-500 rounded-2xl flex items-center justify-center shadow-xl shadow-emerald-500/30 mb-8">
                        <span class="text-white text-2xl font-black">2</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Configurez votre pièce</h3>
                    <p class="text-gray-500 leading-relaxed max-w-xs">
                        Renseignez les dimensions, sélectionnez les matériaux. Votre espace virtuel prend forme.
                    </p>
                </div>

                <!-- Étape 3 -->
                <div class="flex flex-col items-center text-center">
                    <div class="w-[4.5rem] h-[4.5rem] bg-emerald-500 rounded-2xl flex items-center justify-center shadow-xl shadow-emerald-500/30 mb-8">
                        <span class="text-white text-2xl font-black">3</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Placez vos objets 3D</h3>
                    <p class="text-gray-500 leading-relaxed max-w-xs">
                        Parcourez le catalogue, positionnez les meubles, ajustez position et échelle, puis visualisez en AR.
                    </p>
                </div>
            </div>
        </div>
    </section>


    {{-- =========================================================
         BLOC API DÉVELOPPEURS
    ========================================================== --}}
    <section class="py-20 bg-white border-y border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-slate-900 rounded-3xl overflow-hidden relative">
                <div class="absolute right-0 top-0 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="grid grid-cols-1 lg:grid-cols-2">

                    <!-- Texte -->
                    <div class="p-10 lg:p-14 relative">
                        <span class="inline-block bg-emerald-500/15 text-emerald-400 text-xs font-semibold uppercase tracking-widest px-3 py-1 rounded-full mb-6">
                            Pour les développeurs
                        </span>
                        <h2 class="text-3xl sm:text-4xl font-black text-white mb-5 leading-tight">
                            Une API REST sécurisée pour vos apps VR/AR
                        </h2>
                        <p class="text-slate-400 leading-relaxed mb-8">
                            Récupérez objets 3D, métadonnées et fichiers GLB directement via notre API.
                            Authentification Sanctum. Support des Range Requests pour le streaming progressif.
                        </p>
                        <div class="flex flex-wrap gap-3">
                            <span class="bg-white/5 border border-white/10 text-slate-300 text-xs px-3 py-1.5 rounded-lg font-mono">GET /api/furniture-objects</span>
                            <span class="bg-white/5 border border-white/10 text-slate-300 text-xs px-3 py-1.5 rounded-lg font-mono">GET /api/{id}/model</span>
                            <span class="bg-white/5 border border-white/10 text-slate-300 text-xs px-3 py-1.5 rounded-lg font-mono">GET /api/{id}/thumbnail</span>
                        </div>
                    </div>

                    <!-- Code snippet décoratif -->
                    <div class="p-10 lg:p-14 border-t lg:border-t-0 lg:border-l border-white/5 flex items-center">
                        <div class="w-full bg-black/40 rounded-2xl p-6 font-mono text-sm leading-loose overflow-x-auto">
                            <div class="flex items-center gap-2 mb-5">
                                <div class="w-3 h-3 bg-red-500/60 rounded-full"></div>
                                <div class="w-3 h-3 bg-yellow-500/60 rounded-full"></div>
                                <div class="w-3 h-3 bg-green-500/60 rounded-full"></div>
                            </div>
                            <p><span class="text-slate-500">// Récupérer un objet 3D</span></p>
                            <p><span class="text-emerald-400">GET</span> <span class="text-white">/api/furniture-objects/1</span></p>
                            <p class="text-slate-500">Authorization: Bearer <span class="text-yellow-400">{token}</span></p>
                            <br>
                            <p><span class="text-slate-500">// Réponse</span></p>
                            <p><span class="text-slate-400">{</span></p>
                            <p>&nbsp;&nbsp;<span class="text-blue-300">"name"</span><span class="text-slate-400">:</span> <span class="text-green-300">"Canapé moderne"</span><span class="text-slate-400">,</span></p>
                            <p>&nbsp;&nbsp;<span class="text-blue-300">"assets"</span><span class="text-slate-400">: {</span></p>
                            <p>&nbsp;&nbsp;&nbsp;&nbsp;<span class="text-blue-300">"model"</span><span class="text-slate-400">:</span> <span class="text-green-300">"…/model"</span><span class="text-slate-400">,</span></p>
                            <p>&nbsp;&nbsp;&nbsp;&nbsp;<span class="text-blue-300">"thumbnail"</span><span class="text-slate-400">:</span> <span class="text-green-300">"…/thumbnail"</span></p>
                            <p>&nbsp;&nbsp;<span class="text-slate-400">}</span></p>
                            <p><span class="text-slate-400">}</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    {{-- =========================================================
         CTA FINAL
    ========================================================== --}}
    <section class="py-28 gradient-hero relative overflow-hidden">
        <div class="absolute top-0 right-1/4 w-[500px] h-[500px] bg-emerald-500/8 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-1/4 w-[400px] h-[400px] bg-emerald-700/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative">
            <span class="inline-block bg-emerald-500/15 text-emerald-400 text-xs font-semibold uppercase tracking-widest px-3 py-1 rounded-full mb-8">
                Commencez dès maintenant
            </span>
            <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black text-white mb-6 leading-tight">
                Prêt à transformer<br>votre intérieur ?
            </h2>
            <p class="text-xl text-slate-400 mb-12 max-w-2xl mx-auto leading-relaxed">
                Créez votre compte gratuitement et commencez à visualiser votre décoration en réalité augmentée dès aujourd'hui.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="/dashboard/register"
                   class="bg-emerald-500 hover:bg-emerald-400 text-white font-semibold px-10 py-4 rounded-xl text-lg transition-all shadow-xl shadow-emerald-500/25 hover:shadow-emerald-500/50">
                    Créer mon compte gratuitement
                </a>
                <a href="/dashboard/login"
                   class="border border-white/25 hover:border-white/50 text-white font-semibold px-10 py-4 rounded-xl text-lg transition-all hover:bg-white/5">
                    J'ai déjà un compte
                </a>
            </div>
        </div>
    </section>


    {{-- =========================================================
         FOOTER
    ========================================================== --}}
    <footer class="bg-slate-950 text-slate-500 py-14 border-t border-white/5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-8">

                <!-- Logo + baseline -->
                <div class="flex flex-col items-center md:items-start gap-2">
                    <a href="/" class="flex items-center gap-2 group">
                        <div class="w-8 h-8 bg-emerald-500 group-hover:bg-emerald-400 rounded-lg flex items-center justify-center transition-colors">
                            <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"/>
                            </svg>
                        </div>
                        <span class="text-white font-bold">AR Décoration</span>
                    </a>
                    <p class="text-sm text-slate-600">Visualisation intérieure en réalité augmentée</p>
                </div>

                <!-- Liens -->
                <nav class="flex flex-wrap justify-center gap-x-8 gap-y-2 text-sm">
                    <a href="/dashboard/login"    class="hover:text-slate-300 transition-colors">Connexion</a>
                    <a href="/dashboard/register" class="hover:text-slate-300 transition-colors">Inscription</a>
                    <a href="/admin/login"         class="hover:text-slate-300 transition-colors">Espace admin</a>
                </nav>

                <!-- Copyright -->
                <p class="text-sm text-slate-600">
                    &copy; {{ date('Y') }} AR Décoration. Tous droits réservés.
                </p>
            </div>
        </div>
    </footer>

</body>
</html>
