<x-guest-layout>

    {{-- Logo --}}
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-red-600 text-center">{{ __('Register for Hotel ROC') }}</h1>
    </div>

    <x-validation-errors class="mb-4" />

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div>
            <x-label for="name" value="{{ __('Name') }}" />
            <x-input id="name" class="block mt-1 w-full focus:ring-red-500 focus:border-red-500" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
        </div>

        <div class="mt-4">
            <x-label for="email" value="{{ __('Email') }}" />
            <x-input id="email" class="block mt-1 w-full focus:ring-red-500 focus:border-red-500" type="email" name="email" :value="old('email')" required autocomplete="username" />
        </div>

        <div class="mt-4">
            <x-label for="password" value="{{ __('Password') }}" />
            <x-input id="password" class="block mt-1 w-full focus:ring-red-500 focus:border-red-500" type="password" name="password" required autocomplete="new-password" />
        </div>

        <div class="mt-4">
            <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
            <x-input id="password_confirmation" class="block mt-1 w-full focus:ring-red-500 focus:border-red-500" type="password" name="password_confirmation" required autocomplete="new-password" />
        </div>

        {{-- Language Selection --}}
        <div class="mt-4">
            <x-label for="language" value="{{ __('Language') }}" />
            <select name="language" id="language" class="block mt-1 w-full border-gray-300 focus:border-red-500 focus:ring-red-500 rounded-md shadow-sm">
                <option value="nl" {{ old('language', app()->getLocale()) == 'nl' ? 'selected' : '' }}>{{ __('Nederlands') }}</option>
                <option value="en" {{ old('language', app()->getLocale()) == 'en' ? 'selected' : '' }}>{{ __('Engels') }}</option>
            </select>
        </div>

        <div class="flex items-center justify-end mt-6">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-button class="ml-4 bg-red-600 hover:bg-red-700">
                {{ __('Register') }}
            </x-button>
        </div>
    </form>

</x-guest-layout>
