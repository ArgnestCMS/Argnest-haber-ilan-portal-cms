<section>
    <header>
        <h2 class="text-lg font-black text-slate-900">
            Profil Bilgileri
        </h2>

        <p class="mt-1 text-sm text-slate-600">
            Hesap bilgilerinizi, avatarınızı, biyografinizi ve sosyal medya linklerinizi güncelleyin.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form
        method="post"
        action="{{ route('profile.update') }}"
        enctype="multipart/form-data"
        class="mt-6 space-y-6"
    >
        @csrf
        @method('patch')

        {{-- AVATAR --}}
        <div>
            <x-input-label for="avatar" value="Avatar" />

            <input
                id="avatar"
                name="avatar"
                type="file"
                accept="image/*"
                class="mt-2 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 shadow-sm file:mr-4 file:rounded-lg file:border-0 file:bg-blue-700 file:px-4 file:py-2 file:text-sm file:font-bold file:text-white hover:file:bg-blue-800"
            >

            <x-input-error class="mt-2" :messages="$errors->get('avatar')" />

            @if($user->avatar)
                <div class="mt-4">
                    <img
                        src="{{ asset('storage/' . $user->avatar) }}"
                        class="w-24 h-24 rounded-2xl object-cover border shadow"
                    >
                </div>
            @endif
        </div>

        {{-- AD --}}
        <div>
            <x-input-label for="name" value="Ad Soyad" />

            <x-text-input
                id="name"
                name="name"
                type="text"
                class="mt-1 block w-full"
                :value="old('name', $user->name)"
                required
                autofocus
                autocomplete="name"
            />

            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        {{-- EMAIL --}}
        <div>
            <x-input-label for="email" value="E-posta" />

            <x-text-input
                id="email"
                name="email"
                type="email"
                class="mt-1 block w-full"
                :value="old('email', $user->email)"
                required
                autocomplete="username"
            />

            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-slate-800">
                        E-posta adresiniz doğrulanmamış.

                        <button
                            form="send-verification"
                            class="underline text-sm text-slate-600 hover:text-slate-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            Doğrulama e-postasını tekrar gönder.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            Yeni doğrulama bağlantısı e-posta adresinize gönderildi.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        {{-- BIO --}}
        <div>
            <x-input-label for="bio" value="Biyografi" />

            <textarea
                id="bio"
                name="bio"
                rows="5"
                class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                placeholder="Kendiniz hakkında kısa bir açıklama yazın..."
            >{{ old('bio', $user->bio) }}</textarea>

            <x-input-error class="mt-2" :messages="$errors->get('bio')" />
        </div>

        {{-- SOSYAL MEDYA --}}
        <div class="grid md:grid-cols-2 gap-5">

            <div>
                <x-input-label for="facebook" value="Facebook URL" />

                <x-text-input
                    id="facebook"
                    name="facebook"
                    type="url"
                    class="mt-1 block w-full"
                    :value="old('facebook', $user->facebook)"
                    placeholder="https://facebook.com/..."
                />

                <x-input-error class="mt-2" :messages="$errors->get('facebook')" />
            </div>

            <div>
                <x-input-label for="twitter" value="X / Twitter URL" />

                <x-text-input
                    id="twitter"
                    name="twitter"
                    type="url"
                    class="mt-1 block w-full"
                    :value="old('twitter', $user->twitter)"
                    placeholder="https://x.com/..."
                />

                <x-input-error class="mt-2" :messages="$errors->get('twitter')" />
            </div>

            <div>
                <x-input-label for="instagram" value="Instagram URL" />

                <x-text-input
                    id="instagram"
                    name="instagram"
                    type="url"
                    class="mt-1 block w-full"
                    :value="old('instagram', $user->instagram)"
                    placeholder="https://instagram.com/..."
                />

                <x-input-error class="mt-2" :messages="$errors->get('instagram')" />
            </div>

            <div>
                <x-input-label for="youtube" value="YouTube URL" />

                <x-text-input
                    id="youtube"
                    name="youtube"
                    type="url"
                    class="mt-1 block w-full"
                    :value="old('youtube', $user->youtube)"
                    placeholder="https://youtube.com/..."
                />

                <x-input-error class="mt-2" :messages="$errors->get('youtube')" />
            </div>

        </div>

        {{-- KAYDET --}}
        <div class="flex items-center gap-4">
            <x-primary-button>
                Profili Kaydet
            </x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-green-600 font-bold"
                >
                    Profil güncellendi.
                </p>
            @endif
        </div>
    </form>
</section>