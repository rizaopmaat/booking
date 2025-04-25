<x-guest-layout>

    {{-- Logo --}}
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-red-600 text-center">{{ __('Hotel ROC Login') }}</h1>
    </div>

    <x-validation-errors class="mb-4" />

    @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <x-label for="email" value="{{ __('Email') }}" />
            <x-input id="email" class="block mt-1 w-full focus:ring-red-500 focus:border-red-500" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
        </div>

        <div class="mt-4">
            <x-label for="password" value="{{ __('Password') }}" />
            <x-input id="password" class="block mt-1 w-full focus:ring-red-500 focus:border-red-500" type="password" name="password" required autocomplete="current-password" />
        </div>

        {{-- Grouped bottom actions --}}
        <div class="mt-6">
            <div class="flex justify-between items-center mb-4">
                <label for="remember_me" class="flex items-center">
                    <x-checkbox id="remember_me" name="remember" class="text-red-600 focus:ring-red-500"/>
                    <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
            </div>

            <x-button class="w-full justify-center bg-red-600 hover:bg-red-700">
                {{ __('Log in') }}
            </x-button>

            <div class="text-center mt-4">
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" href="{{ route('register') }}">
                       {{ __('Don\'t have an account? Register') }}
                </a>
           </div>
        </div>
    </form>

</x-guest-layout>
