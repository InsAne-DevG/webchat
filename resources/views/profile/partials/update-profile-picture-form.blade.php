<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Profile Picture') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update profile picture.") }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.update-picture') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div>
            @auth
                <img src="{{ asset('profile-pictures') . '/' . auth()->user()->profile_picture }}" style="border-radius: 50%;margin: auto;width: 200px;height: 200px;object-fit: cover;" id="profile_preview"  alt="">
            @else
                <img src="{{ asset('profile-pictures/default.png') }}" style="border-radius: 50%;margin: auto;width: 200px;height: 200px;object-fit: cover;" id="profile_preview"  alt="">
            @endauth
            <x-input-label for="profile_picture" :value="__('Profile Picture')" />
            <x-text-input id="profile_picture" class="block mt-1 w-full" type="file" name="profile_picture" :value="old('profile_picture')" required autofocus accept="image/*" />
            <x-input-error :messages="$errors->get('profile_picture')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-picture-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
<script>
    document.getElementById('profile_picture').addEventListener('change', (e)=>{
        document.getElementById('profile_preview').src = URL.createObjectURL(e.target.files[0]);
    });
</script>
