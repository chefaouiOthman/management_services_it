<x-guest-layout>
    <div x-data="{ loading: false }">
        <div class="text-center mb-6">
            <div class="mx-auto mb-6">
                <svg viewBox="0 0 180 140" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-40 h-auto mx-auto">
                    <defs>
                        <linearGradient id="fg1" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#818cf8"/><stop offset="100%" stop-color="#c4b5fd"/>
                        </linearGradient>
                        <linearGradient id="fg2" x1="0%" y1="0%" x2="100%" y2="0%">
                            <stop offset="0%" stop-color="#a78bfa"/><stop offset="100%" stop-color="#818cf8"/>
                        </linearGradient>
                        <linearGradient id="fg3" x1="0%" y1="0%" x2="0%" y2="100%">
                            <stop offset="0%" stop-color="#e0e7ff"/><stop offset="100%" stop-color="#c7d2fe"/>
                        </linearGradient>
                        <filter id="softglow">
                            <feGaussianBlur stdDeviation="2" result="blur"/>
                            <feMerge><feMergeNode in="blur"/><feMergeNode in="SourceGraphic"/></feMerge>
                        </filter>
                    </defs>

                    <!-- Security Key / Shield -->
                    <path d="M90 15 C90 15, 60 30, 60 50 L60 70 C60 85, 90 100, 90 100 C90 100, 120 85, 120 70 L120 50 C120 30, 90 15, 90 15 Z" fill="rgba(129,140,248,0.12)" stroke="rgba(129,140,248,0.3)" stroke-width="2"/>
                    <path d="M90 20 C90 20, 66 33, 66 50 L66 68 C66 80, 90 93, 90 93 C90 93, 114 80, 114 68 L114 50 C114 33, 90 20, 90 20 Z" fill="rgba(255,255,255,0.06)" stroke="rgba(129,140,248,0.15)" stroke-width="1"/>
                    <!-- Check inside shield -->
                    <path d="M80 58 L87 66 L100 50" stroke="#818cf8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" opacity="0.7"/>

                    <!-- Floating Question Marks -->
                    <text x="145" y="30" text-anchor="middle" fill="#a78bfa" font-size="22" font-family="figtree, sans-serif" font-weight="bold" opacity="0.6">
                        ?
                        <animate attributeName="y" values="30;25;30" dur="3s" repeatCount="indefinite"/>
                        <animate attributeName="opacity" values="0.6;1;0.6" dur="3s" repeatCount="indefinite"/>
                    </text>
                    <text x="160" y="55" text-anchor="middle" fill="#c4b5fd" font-size="16" font-family="figtree, sans-serif" font-weight="bold" opacity="0.4">
                        ?
                        <animate attributeName="y" values="55;50;55" dur="3.5s" repeatCount="indefinite"/>
                        <animate attributeName="opacity" values="0.4;0.8;0.4" dur="3.5s" repeatCount="indefinite"/>
                    </text>
                    <text x="30" y="35" text-anchor="middle" fill="#a5b4fc" font-size="18" font-family="figtree, sans-serif" font-weight="bold" opacity="0.5">
                        ?
                        <animate attributeName="y" values="35;28;35" dur="4s" repeatCount="indefinite"/>
                        <animate attributeName="opacity" values="0.5;1;0.5" dur="4s" repeatCount="indefinite"/>
                    </text>
                    <text x="20" y="65" text-anchor="middle" fill="#c4b5fd" font-size="13" font-family="figtree, sans-serif" font-weight="bold" opacity="0.35">
                        ?
                        <animate attributeName="y" values="65;60;65" dur="2.8s" repeatCount="indefinite"/>
                    </text>

                    <!-- Cloud underneath -->
                    <ellipse cx="90" cy="115" rx="55" ry="14" fill="rgba(255,255,255,0.05)" stroke="rgba(255,255,255,0.08)" stroke-width="1"/>
                    <ellipse cx="70" cy="110" rx="25" ry="12" fill="rgba(255,255,255,0.04)" stroke="rgba(255,255,255,0.06)" stroke-width="1"/>
                    <ellipse cx="112" cy="110" rx="22" ry="10" fill="rgba(255,255,255,0.04)" stroke="rgba(255,255,255,0.06)" stroke-width="1"/>

                    <!-- Small decorative dots -->
                    <circle cx="50" cy="90" r="2" fill="#a5b4fc" opacity="0.3">
                        <animate attributeName="opacity" values="0.3;0.7;0.3" dur="5s" repeatCount="indefinite"/>
                    </circle>
                    <circle cx="135" cy="95" r="2" fill="#c4b5fd" opacity="0.25">
                        <animate attributeName="opacity" values="0.25;0.6;0.25" dur="4s" repeatCount="indefinite"/>
                    </circle>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900">Mot de passe oublié ?</h2>
            <p class="text-sm text-gray-500 mt-1 mb-3">Pas de panique ! Même les meilleurs d'entre nous oublient parfois.</p>
            <p class="text-sm text-gray-400">Entrez votre adresse e-mail et nous nous occupons du reste.</p>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" @submit="loading = true">
            @csrf

            <div class="space-y-5">
                <div>
                    <x-input-label for="email" value="Email" />
                    <div class="relative mt-1.5">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="block w-full pl-11 pr-4 py-3 border-gray-200 bg-gray-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 rounded-xl text-sm transition-all duration-200 placeholder:text-gray-400" placeholder="vous@exemple.com">
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-500 bg-red-50 rounded-lg px-3 py-1.5 border border-red-100">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" :disabled="loading" class="w-full flex items-center justify-center gap-2 px-5 py-3 bg-gradient-to-r from-indigo-600 to-violet-600 text-white font-semibold rounded-xl hover:from-indigo-500 hover:to-violet-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:ring-offset-2 active:scale-[0.98] transition-all duration-200 shadow-md shadow-indigo-200 disabled:opacity-70 disabled:cursor-not-allowed">
                    <span x-show="!loading">Envoyer le lien</span>
                    <span x-show="loading" class="flex items-center gap-2" x-cloak>
                        <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        Envoi...
                    </span>
                </button>

                <p class="text-center text-sm text-gray-500">
                    <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500 transition-colors">← Retour à la connexion</a>
                </p>
            </div>
        </form>
    </div>
</x-guest-layout>
