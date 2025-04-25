@extends('layouts.admin') {{-- Assuming you have an admin layout --}}

@section('title', __('Nieuwe Kamer Toevoegen'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">{{ __('Nieuwe Kamer Toevoegen') }}</h1>

    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <form action="{{ route('admin.rooms.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Tabs for Languages (Optional but recommended for UX) --}}
            <div x-data="{ tab: 'nl' }" class="mb-6">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <button type="button" @click="tab = 'nl'" :class="{ 'border-blue-500 text-blue-600': tab === 'nl', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'nl' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            {{ __('Nederlands') }}
                        </button>
                        <button type="button" @click="tab = 'en'" :class="{ 'border-blue-500 text-blue-600': tab === 'en', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'en' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            {{ __('Engels') }}
                        </button>
                    </nav>
                </div>

                {{-- Dutch Fields --}}
                <div x-show="tab === 'nl'" class="pt-6">
                    <div class="mb-4">
                        <label for="name_nl" class="block text-gray-700 text-sm font-bold mb-2">{{ __('Naam (NL)') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="name[nl]" id="name_nl" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('name.nl') border-red-500 @enderror" value="{{ old('name.nl') }}" required>
                        @error('name.nl')
                            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="description_nl" class="block text-gray-700 text-sm font-bold mb-2">{{ __('Beschrijving (NL)') }} <span class="text-red-500">*</span></label>
                        <textarea name="description[nl]" id="description_nl" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('description.nl') border-red-500 @enderror" required>{{ old('description.nl') }}</textarea>
                        @error('description.nl')
                            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- English Fields --}}
                <div x-show="tab === 'en'" class="pt-6" style="display: none;"> {{-- Hide initially --}}
                    <div class="mb-4">
                        <label for="name_en" class="block text-gray-700 text-sm font-bold mb-2">{{ __('Name (EN)') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="name[en]" id="name_en" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('name.en') border-red-500 @enderror" value="{{ old('name.en') }}" required>
                        @error('name.en')
                            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="description_en" class="block text-gray-700 text-sm font-bold mb-2">{{ __('Description (EN)') }} <span class="text-red-500">*</span></label>
                        <textarea name="description[en]" id="description_en" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('description.en') border-red-500 @enderror" required>{{ old('description.en') }}</textarea>
                        @error('description.en')
                            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Other Room Fields --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="price" class="block text-gray-700 text-sm font-bold mb-2">{{ __('Prijs per nacht (€)') }} <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" min="0" name="price" id="price" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('price') border-red-500 @enderror" value="{{ old('price') }}" required>
                    @error('price')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="capacity" class="block text-gray-700 text-sm font-bold mb-2">{{ __('Capaciteit (personen)') }} <span class="text-red-500">*</span></label>
                    <input type="number" min="1" name="capacity" id="capacity" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('capacity') border-red-500 @enderror" value="{{ old('capacity') }}" required>
                    @error('capacity')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Main Image --}}
            <div class="mb-6">
                <label for="image" class="block text-gray-700 text-sm font-bold mb-2">{{ __('Hoofdafbeelding (Optioneel)') }}</label>
                <input type="file" name="image" id="image" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('image') border-red-500 @enderror">
                 @error('image')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-600 mt-1">{{ __('Deze afbeelding wordt getoond in de kamerlijst.') }}</p>
            </div>

            {{-- Gallery Images --}}
             <div class="mb-6">
                <label for="images" class="block text-gray-700 text-sm font-bold mb-2">{{ __('Galerij Afbeeldingen (Selecteer meerdere)') }}</label>
                <input type="file" name="images[]" id="images" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('images.*') border-red-500 @enderror" multiple>
                 @error('images.*')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
                 <p class="text-xs text-gray-600 mt-1">{{ __('Deze afbeeldingen worden getoond op de kamerdetailpagina.') }}</p>
            </div>

            <div class="mb-6">
                 <label class="block text-gray-700 text-sm font-bold mb-2">{{ __('Beschikbaar?') }}</label>
                 <label class="inline-flex items-center">
                    <input type="hidden" name="is_available" value="0"> {{-- Send 0 if checkbox is unchecked --}}
                    <input type="checkbox" name="is_available" value="1" class="form-checkbox h-5 w-5 text-blue-600" {{ old('is_available', true) ? 'checked' : '' }}>
                    <span class="ml-2 text-gray-700">{{ __('Ja, deze kamer is beschikbaar voor boeking') }}</span>
                 </label>
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    {{ __('Kamer Opslaan') }}
                </button>
                <a href="{{ route('admin.rooms.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                    {{ __('Annuleren') }}
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Include Alpine.js if not already included in your layout --}}
{{-- <script src="//unpkg.com/alpinejs" defer></script> --}}
@endsection 