@php
    $methodClass = match(strtolower($method)) {
        'get'    => 'badge-get ep-get',
        'post'   => 'badge-post ep-post',
        'put'    => 'badge-put ep-put',
        'patch'  => 'badge-patch ep-patch',
        'delete' => 'badge-delete ep-delete',
        default  => 'bg-slate-100 text-slate-700',
    };
    $badgeClass = 'badge-' . strtolower($method);
    $borderClass = 'ep-' . strtolower($method);
    $params ??= [];
@endphp

<div id="{{ $id }}" class="bg-white border border-l-4 {{ $borderClass }} border-slate-200 rounded-2xl overflow-hidden">

    {{-- En-tête --}}
    <div class="px-6 py-5 border-b border-slate-100">
        <div class="flex flex-wrap items-center gap-3 mb-2">
            <span class="{{ $badgeClass }} font-mono text-xs font-bold px-2.5 py-1 rounded-lg uppercase">{{ $method }}</span>
            <code class="text-slate-800 font-mono text-sm font-semibold break-all">{{ $path }}</code>
            @if($public)
                <span class="text-xs bg-emerald-50 text-emerald-700 border border-emerald-200 px-2 py-0.5 rounded-full font-medium">Public</span>
            @else
                <span class="text-xs bg-amber-50 text-amber-700 border border-amber-200 px-2 py-0.5 rounded-full font-medium flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                    Token requis
                </span>
            @endif
        </div>
        <p class="text-slate-500 text-sm leading-relaxed">{{ $summary }}</p>
    </div>

    <div class="px-6 py-5 space-y-6">

        {{-- Paramètres de chemin/query --}}
        @if(!empty($params))
        <div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">Paramètres</p>
            <table class="w-full text-sm border border-slate-100 rounded-xl overflow-hidden">
                <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wide">
                    <tr>
                        <th class="text-left px-4 py-2.5 w-1/4">Nom</th>
                        <th class="text-left px-4 py-2.5 w-16">Dans</th>
                        <th class="text-left px-4 py-2.5 w-20">Requis</th>
                        <th class="text-left px-4 py-2.5">Description</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($params as $p)
                    <tr>
                        <td class="px-4 py-2.5 font-mono text-slate-700 text-xs">{{ $p['name'] }}</td>
                        <td class="px-4 py-2.5">
                            <span class="text-xs {{ $p['in'] === 'path' ? 'bg-violet-50 text-violet-700 border border-violet-200' : 'bg-slate-100 text-slate-600' }} px-1.5 py-0.5 rounded font-mono">{{ $p['in'] }}</span>
                        </td>
                        <td class="px-4 py-2.5 text-xs {{ $p['req'] ? 'text-red-500 font-semibold' : 'text-slate-400' }}">
                            {{ $p['req'] ? 'Oui' : 'Non' }}
                        </td>
                        <td class="px-4 py-2.5 text-slate-600 text-xs">{{ $p['desc'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        {{-- Body / champs de formulaire --}}
        @if(!empty($body))
        <div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">Corps de la requête <span class="text-slate-400 font-normal normal-case">(application/json)</span></p>
            <table class="w-full text-sm border border-slate-100 rounded-xl overflow-hidden">
                <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wide">
                    <tr>
                        <th class="text-left px-4 py-2.5 w-1/4">Champ</th>
                        <th class="text-left px-4 py-2.5 w-20">Type</th>
                        <th class="text-left px-4 py-2.5 w-20">Requis</th>
                        <th class="text-left px-4 py-2.5">Description</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($body as $field)
                    <tr>
                        <td class="px-4 py-2.5 font-mono text-slate-700 text-xs">
                            {{ $field['name'] }}
                            @if($field['req'] ?? false)<span class="text-red-400 ml-0.5">*</span>@endif
                        </td>
                        <td class="px-4 py-2.5">
                            <span class="text-xs bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded font-mono">{{ $field['type'] }}</span>
                        </td>
                        <td class="px-4 py-2.5 text-xs {{ ($field['req'] ?? false) ? 'text-red-500 font-semibold' : 'text-slate-400' }}">
                            {{ ($field['req'] ?? false) ? 'Oui' : 'Non' }}
                        </td>
                        <td class="px-4 py-2.5 text-slate-600 text-xs">{{ $field['desc'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        {{-- Exemple cURL --}}
        <div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">Exemple cURL</p>
            <div class="code-block">
                <pre class="bg-slate-900 text-slate-300 font-mono text-xs rounded-xl px-5 py-4 overflow-x-auto leading-relaxed">{{ $curl }}</pre>
                <button class="copy-btn bg-slate-700 hover:bg-slate-600 text-slate-300 text-xs px-2 py-1 rounded-md transition-colors" data-copy="{{ $curl }}">Copier</button>
            </div>
        </div>

        {{-- Réponse --}}
        <div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">
                Réponse
                <span class="ml-2 font-mono font-normal text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded">{{ $response_code }}</span>
            </p>
            <div class="code-block">
                <pre class="bg-slate-900 text-slate-300 font-mono text-xs rounded-xl px-5 py-4 overflow-x-auto leading-relaxed">{{ $response }}</pre>
                <button class="copy-btn bg-slate-700 hover:bg-slate-600 text-slate-300 text-xs px-2 py-1 rounded-md transition-colors">Copier</button>
            </div>
        </div>

    </div>
</div>
