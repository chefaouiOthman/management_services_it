<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->nom_complet)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <!-- Champ Photo de Profil avec option Caméra -->
        <div class="mt-6 space-y-2">
            <x-input-label for="avatar" :value="__('Photo de profil')" />

            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                <div class="flex-shrink-0">
                    @if(auth()->user()->avatar)
                        <button type="button" onclick="document.getElementById('avatar_lightbox').showModal()" class="focus:outline-none">
                            <img id="avatar_preview" src="{{ asset('storage/' . auth()->user()->avatar) }}" class="w-16 h-16 rounded-full object-cover border border-gray-300 shadow-sm">
                        </button>
                    @else
                        <div id="avatar_placeholder" class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 font-bold border border-gray-300 shadow-sm">
                            {{ strtoupper(substr(auth()->user()->nom_complet, 0, 1)) }}
                        </div>
                        <img id="avatar_preview" class="w-16 h-16 rounded-full object-cover border border-gray-300 shadow-sm hidden">
                    @endif
                </div>

                <div class="space-y-2 w-full">
                    <input id="avatar" name="avatar" type="file" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition-all duration-200" accept="image/*" />

                    <button type="button" onclick="openCamera()" class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-semibold text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none transition-all duration-200">
                        <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Prendre une photo avec la caméra
                    </button>
                </div>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
        </div>

        <!-- MODALE DE LA CAMÉRA -->
        <dialog id="camera_modal" class="backdrop:bg-black/70 rounded-2xl p-0 max-w-md w-full overflow-hidden shadow-2xl border border-gray-200 bg-white">
            <div class="p-6 flex flex-col items-center">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Prendre une photo</h3>

                <div class="relative w-full aspect-video bg-black rounded-xl overflow-hidden shadow-inner mb-4">
                    <video id="webcam" autoplay playsinline class="w-full h-full object-cover"></video>
                    <canvas id="canvas" class="hidden"></canvas>
                </div>

                <div class="flex gap-3 w-full">
                    <button type="button" onclick="closeCamera()" class="flex-1 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium text-sm transition-colors">
                        Annuler
                    </button>
                    <button type="button" onclick="capturePhoto()" class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium text-sm shadow-sm transition-colors">
                        Capturer la photo
                    </button>
                </div>
            </div>
        </dialog>

        <!-- Fenêtre Modale de Zoom (Lightbox) -->
        @if(auth()->user()->avatar)
            <dialog id="avatar_lightbox" class="backdrop:bg-black/70 rounded-2xl p-0 max-w-lg w-full overflow-hidden shadow-2xl border border-gray-100 bg-white">
                <div class="relative p-6 flex flex-col items-center">
                    <button type="button" onclick="document.getElementById('avatar_lightbox').close()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-full p-2 transition-all duration-200 focus:outline-none">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <h3 class="text-md font-medium text-gray-700 mb-4">Aperçu de votre photo</h3>

                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}" class="max-w-full max-h-[70vh] rounded-xl object-contain shadow-inner">
                </div>
            </dialog>
        @endif

        <!-- SCRIPTS JAVASCRIPT DE GESTION CAMERA -->
        <script>
            let stream = null;
            const video = document.getElementById('webcam');
            const canvas = document.getElementById('canvas');
            const cameraModal = document.getElementById('camera_modal');

            async function openCamera() {
                try {
                    stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: "user" }, audio: false });
                    video.srcObject = stream;
                    cameraModal.showModal();
                } catch (err) {
                    alert("Impossible d'accéder à la caméra : " + err.message);
                }
            }

            function capturePhoto() {
                if (!stream) return;

                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;

                const context = canvas.getContext('2d');
                context.drawImage(video, 0, 0, canvas.width, canvas.height);

                canvas.toBlob((blob) => {
                    if (blob) {
                        const file = new File([blob], "capture_avatar.png", { type: "image/png" });

                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);
                        const fileInput = document.getElementById('avatar');
                        fileInput.files = dataTransfer.files;

                        const previewUrl = URL.createObjectURL(blob);
                        const previewImg = document.getElementById('avatar_preview');
                        const placeholder = document.getElementById('avatar_placeholder');

                        if (previewImg) {
                            previewImg.src = previewUrl;
                            previewImg.classList.remove('hidden');
                        }
                        if (placeholder) {
                            placeholder.classList.add('hidden');
                        }
                    }
                    closeCamera();
                }, 'image/png');
            }

            function closeCamera() {
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                }
                video.srcObject = null;
                cameraModal.close();
            }
        </script>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
