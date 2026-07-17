<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'MSI') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen flex">
        <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-gradient-to-br from-indigo-600 via-indigo-700 to-violet-800 items-center justify-center p-12">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute -top-24 -right-24 w-96 h-96 bg-white rounded-full"></div>
                <div class="absolute -bottom-32 -left-32 w-80 h-80 bg-violet-300 rounded-full"></div>
                <div class="absolute top-1/3 left-1/4 w-64 h-64 bg-indigo-400 rounded-full blur-3xl"></div>
            </div>
            <div class="relative z-10 text-white max-w-lg">
                <div class="mb-8">
                    <svg viewBox="0 0 500 380" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto drop-shadow-2xl">
                        <defs>
                            <linearGradient id="sg1" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#818cf8"/><stop offset="100%" stop-color="#c4b5fd"/>
                            </linearGradient>
                            <linearGradient id="sg2" x1="0%" y1="0%" x2="0%" y2="100%">
                                <stop offset="0%" stop-color="rgba(255,255,255,0.12)"/><stop offset="100%" stop-color="rgba(255,255,255,0.04)"/>
                            </linearGradient>
                            <linearGradient id="sg3" x1="0%" y1="0%" x2="100%" y2="0%">
                                <stop offset="0%" stop-color="#6366f1"/><stop offset="100%" stop-color="#8b5cf6"/>
                            </linearGradient>
                            <filter id="glow">
                                <feGaussianBlur stdDeviation="3" result="blur"/>
                                <feMerge><feMergeNode in="blur"/><feMergeNode in="SourceGraphic"/></feMerge>
                            </filter>
                        </defs>

                        <!-- Server Rack Background -->
                        <rect x="30" y="30" width="200" height="300" rx="12" fill="rgba(255,255,255,0.06)" stroke="rgba(255,255,255,0.12)" stroke-width="1.5"/>
                        
                        <!-- Server Units -->
                        <rect x="45" y="45" width="170" height="45" rx="6" fill="url(#sg2)" stroke="rgba(255,255,255,0.08)" stroke-width="1"/>
                        <rect x="55" y="55" width="60" height="6" rx="3" fill="rgba(255,255,255,0.3)"/>
                        <rect x="55" y="67" width="80" height="4" rx="2" fill="rgba(255,255,255,0.12)"/>
                        <!-- Server 1 LEDs -->
                        <circle cx="195" cy="58" r="4" fill="#34d399" filter="url(#glow)">
                            <animate attributeName="opacity" values="1;0.3;1" dur="1.5s" repeatCount="indefinite"/>
                        </circle>
                        <circle cx="195" cy="72" r="4" fill="#34d399" filter="url(#glow)">
                            <animate attributeName="opacity" values="0.3;1;0.3" dur="2s" repeatCount="indefinite"/>
                        </circle>

                        <rect x="45" y="100" width="170" height="45" rx="6" fill="url(#sg2)" stroke="rgba(255,255,255,0.08)" stroke-width="1"/>
                        <rect x="55" y="110" width="70" height="6" rx="3" fill="rgba(255,255,255,0.3)"/>
                        <rect x="55" y="122" width="40" height="4" rx="2" fill="rgba(255,255,255,0.12)"/>
                        <rect x="100" y="122" width="50" height="4" rx="2" fill="rgba(255,255,255,0.12)"/>
                        <circle cx="195" cy="113" r="4" fill="#f59e0b" filter="url(#glow)">
                            <animate attributeName="opacity" values="1;0.2;1" dur="2.5s" repeatCount="indefinite"/>
                        </circle>
                        <circle cx="195" cy="127" r="4" fill="#34d399" filter="url(#glow)">
                            <animate attributeName="opacity" values="0.5;1;0.5" dur="1.8s" repeatCount="indefinite"/>
                        </circle>

                        <rect x="45" y="155" width="170" height="45" rx="6" fill="url(#sg2)" stroke="rgba(255,255,255,0.08)" stroke-width="1"/>
                        <rect x="55" y="165" width="90" height="6" rx="3" fill="rgba(255,255,255,0.3)"/>
                        <rect x="55" y="177" width="65" height="4" rx="2" fill="rgba(255,255,255,0.12)"/>
                        <circle cx="195" cy="168" r="4" fill="#34d399" filter="url(#glow)">
                            <animate attributeName="opacity" values="0.8;0.2;0.8" dur="3s" repeatCount="indefinite"/>
                        </circle>
                        <circle cx="195" cy="182" r="4" fill="#f59e0b" filter="url(#glow)">
                            <animate attributeName="opacity" values="1;0.3;1" dur="1.2s" repeatCount="indefinite"/>
                        </circle>

                        <rect x="45" y="210" width="170" height="45" rx="6" fill="url(#sg2)" stroke="rgba(255,255,255,0.08)" stroke-width="1"/>
                        <rect x="55" y="220" width="100" height="6" rx="3" fill="rgba(255,255,255,0.3)"/>
                        <rect x="55" y="232" width="55" height="4" rx="2" fill="rgba(255,255,255,0.12)"/>
                        <circle cx="195" cy="223" r="4" fill="#34d399" filter="url(#glow)">
                            <animate attributeName="opacity" values="1;0.4;1" dur="2.2s" repeatCount="indefinite"/>
                        </circle>
                        <circle cx="195" cy="237" r="4" fill="#34d399" filter="url(#glow)">
                            <animate attributeName="opacity" values="0.6;1;0.6" dur="1.6s" repeatCount="indefinite"/>
                        </circle>

                        <rect x="45" y="265" width="170" height="45" rx="6" fill="url(#sg2)" stroke="rgba(255,255,255,0.08)" stroke-width="1"/>
                        <rect x="55" y="275" width="45" height="6" rx="3" fill="rgba(255,255,255,0.3)"/>
                        <rect x="55" y="287" width="30" height="4" rx="2" fill="rgba(255,255,255,0.12)"/>
                        <circle cx="195" cy="278" r="4" fill="#34d399" filter="url(#glow)">
                            <animate attributeName="opacity" values="0.3;1;0.3" dur="2.8s" repeatCount="indefinite"/>
                        </circle>
                        <circle cx="195" cy="292" r="4" fill="#f59e0b" filter="url(#glow)">
                            <animate attributeName="opacity" values="1;0.2;1" dur="1.4s" repeatCount="indefinite"/>
                        </circle>

                        <!-- Data lines from server to laptop -->
                        <path d="M230 120 C 280 120, 320 80, 380 80" stroke="rgba(129,140,248,0.4)" stroke-width="2" stroke-dasharray="6 4" fill="none">
                            <animate attributeName="stroke-dashoffset" values="0;-20" dur="2s" repeatCount="indefinite"/>
                        </path>
                        <path d="M230 180 C 290 180, 310 140, 380 140" stroke="rgba(129,140,248,0.3)" stroke-width="1.5" stroke-dasharray="4 4" fill="none">
                            <animate attributeName="stroke-dashoffset" values="0;-16" dur="2.5s" repeatCount="indefinite"/>
                        </path>

                        <!-- Laptop -->
                        <rect x="340" y="50" width="120" height="100" rx="8" fill="rgba(255,255,255,0.08)" stroke="rgba(255,255,255,0.15)" stroke-width="1.5"/>
                        <!-- Screen content -->
                        <rect x="350" y="60" width="100" height="65" rx="4" fill="rgba(255,255,255,0.04)"/>
                        <rect x="358" y="72" width="55" height="4" rx="2" fill="rgba(129,140,248,0.5)"/>
                        <rect x="358" y="82" width="75" height="3" rx="1.5" fill="rgba(255,255,255,0.15)"/>
                        <rect x="358" y="90" width="60" height="3" rx="1.5" fill="rgba(255,255,255,0.1)"/>
                        <rect x="358" y="98" width="70" height="3" rx="1.5" fill="rgba(255,255,255,0.12)"/>
                        <!-- Glowing dot on screen -->
                        <circle cx="430" cy="75" r="3" fill="#34d399" filter="url(#glow)">
                            <animate attributeName="opacity" values="1;0.2;1" dur="1.2s" repeatCount="indefinite"/>
                        </circle>
                        <!-- Laptop base -->
                        <rect x="345" y="140" width="110" height="6" rx="3" fill="rgba(255,255,255,0.15)"/>
                        <!-- Laptop stand -->
                        <path d="M370 146 L 380 155 L 420 155 L 430 146" stroke="rgba(255,255,255,0.1)" stroke-width="1.5" fill="none"/>

                        <!-- Decorative floating data nodes -->
                        <circle cx="300" cy="50" r="3" fill="#a5b4fc" opacity="0.6">
                            <animate attributeName="cy" values="50;45;50" dur="4s" repeatCount="indefinite"/>
                            <animate attributeName="opacity" values="0.6;1;0.6" dur="4s" repeatCount="indefinite"/>
                        </circle>
                        <circle cx="320" cy="230" r="2.5" fill="#c4b5fd" opacity="0.5">
                            <animate attributeName="cy" values="230;225;230" dur="3.5s" repeatCount="indefinite"/>
                        </circle>
                        <circle cx="270" cy="280" r="3" fill="#a5b4fc" opacity="0.4">
                            <animate attributeName="cy" values="280;275;280" dur="5s" repeatCount="indefinite"/>
                            <animate attributeName="opacity" values="0.4;0.8;0.4" dur="5s" repeatCount="indefinite"/>
                        </circle>

                        <!-- Shield/Lock icon at bottom -->
                        <path d="M110 310 L110 325 C110 335, 125 345, 125 345 C125 345, 140 335, 140 325 L140 310 Z" fill="rgba(52,211,153,0.2)" stroke="rgba(52,211,153,0.4)" stroke-width="1.5"/>
                        <path d="M119 325 L123 330 L131 320" stroke="#34d399" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" opacity="0.7"/>

                        <text x="250" y="360" text-anchor="middle" fill="rgba(255,255,255,0.4)" font-size="12" font-family="figtree, sans-serif" letter-spacing="1">MANAGEMENT SERVICES IT</text>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold tracking-tight mb-3">Management Services IT</h1>
                <p class="text-indigo-200/80 text-lg leading-relaxed">Plateforme intégrée de gestion des ressources, projets, formations et finances IT.</p>
            </div>
        </div>
        <div class="flex-1 flex items-center justify-center px-6 py-12 lg:px-12">
            <div class="w-full max-w-md">
                <div class="text-center lg:text-left mb-8">
                    <a href="/" class="inline-flex items-center gap-2">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-600 to-violet-600 flex items-center justify-center text-white font-bold text-lg shadow-md">M</div>
                        <span class="text-2xl font-extrabold text-gray-900 tracking-tight">MSI</span>
                    </a>
                </div>
                <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 p-8">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>
