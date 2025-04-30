@extends('layouts.admin')

@section('title', __('Gebruiker bewerken'))

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">{{ __('Gebruiker bewerken') }}</h1>
        <a href="{{ route('admin.users.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded-md transition">
            {{ __('Terug naar lijst') }}
        </a>
    </div>

    {{-- Foutmeldingen --}}
    @if($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
        <ul class="list-disc pl-5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Succesmeldingen --}}
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    {{-- Formulier --}}
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <form action="{{ route('admin.users.update', $user) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                {{-- Voornaam --}}
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Voornaam') }}</label>
                    <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $user->first_name) }}" 
                        class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-red-200 focus:border-red-500 @error('first_name') border-red-500 @enderror">
                </div>

                {{-- Achternaam --}}
                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Achternaam') }}</label>
                    <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $user->last_name) }}" 
                        class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-red-200 focus:border-red-500 @error('last_name') border-red-500 @enderror">
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Email') }}</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" 
                        class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-red-200 focus:border-red-500 @error('email') border-red-500 @enderror">
                </div>

                {{-- Telefoonnummer --}}
                <div>
                    <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Telefoonnummer') }}</label>
                    <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number', $user->phone_number) }}" 
                        class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-red-200 focus:border-red-500 @error('phone_number') border-red-500 @enderror">
                </div>
            </div>

            {{-- Admin status --}}
            <div class="mb-6">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="is_admin" class="form-checkbox h-5 w-5 text-red-600" {{ $user->is_admin ? 'checked' : '' }}>
                    <span class="ml-2 text-gray-700">{{ __('Admin rechten') }}</span>
                </label>
            </div>

            <div class="flex justify-end gap-4 border-t pt-6">
                <a href="{{ route('admin.users.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-6 rounded-md transition">
                    {{ __('Annuleren') }}
                </a>
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-6 rounded-md transition">
                    {{ __('Opslaan') }}
                </button>
            </div>
        </form>

        {{-- Wachtwoord Reset Link Sturen --}}
        <div class="mb-6 border-t p-6">
            <h3 class="text-lg font-medium text-gray-700 mb-4">{{ __('admin.users.send_reset_link_title') }}</h3>
            <form action="{{ route('admin.users.sendResetLink', $user) }}" method="POST">
                @csrf
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-md transition">
                    {{ __('admin.users.send_reset_link_button') }}
                </button>
            </form>
        </div>
    </div>
</div>
@endsection 