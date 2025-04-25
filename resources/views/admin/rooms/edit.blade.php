@extends('layouts.admin')

@section('title', __('Kamer Bewerken') . ': ' . $room->getTranslation('name', 'nl')) {{-- Show current Dutch name --}}

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">{{ __('Kamer Bewerken') }}</h1>
        <a href="{{ route('admin.rooms.index') }}" class="text-blue-600 hover:text-blue-800">&larr; {{ __('Terug naar overzicht') }}</a>
    </div>

    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        {{-- !!! We need to update the updateRoom method to handle image deletion/updates !!! --}}
        <form action="{{ route('admin.rooms.update', $room->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Tabs for Languages --}}
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
                        <input type="text" name="name[nl]" id="name_nl" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('name.nl') border-red-500 @enderror" value="{{ old('name.nl', $room->getTranslation('name', 'nl')) }}" required>
                        @error('name.nl') <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p> @enderror
                    </div>
                    <div class="mb-4">
                        <label for="description_nl" class="block text-gray-700 text-sm font-bold mb-2">{{ __('Beschrijving (NL)') }} <span class="text-red-500">*</span></label>
                        <textarea name="description[nl]" id="description_nl" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('description.nl') border-red-500 @enderror" required>{{ old('description.nl', $room->getTranslation('description', 'nl')) }}</textarea>
                        @error('description.nl') <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- English Fields --}}
                <div x-show="tab === 'en'" class="pt-6" style="display: none;">
                    <div class="mb-4">
                        <label for="name_en" class="block text-gray-700 text-sm font-bold mb-2">{{ __('Name (EN)') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="name[en]" id="name_en" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('name.en') border-red-500 @enderror" value="{{ old('name.en', $room->getTranslation('name', 'en')) }}" required>
                        @error('name.en') <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p> @enderror
                    </div>
                    <div class="mb-4">
                        <label for="description_en" class="block text-gray-700 text-sm font-bold mb-2">{{ __('Description (EN)') }} <span class="text-red-500">*</span></label>
                        <textarea name="description[en]" id="description_en" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('description.en') border-red-500 @enderror" required>{{ old('description.en', $room->getTranslation('description', 'en')) }}</textarea>
                        @error('description.en') <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Other Room Fields --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                 <div>
                    <label for="price" class="block text-gray-700 text-sm font-bold mb-2">{{ __('Prijs per nacht (€)') }} <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" min="0" name="price" id="price" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('price') border-red-500 @enderror" value="{{ old('price', $room->price) }}" required>
                    @error('price') <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="capacity" class="block text-gray-700 text-sm font-bold mb-2">{{ __('Capaciteit (personen)') }} <span class="text-red-500">*</span></label>
                    <input type="number" min="1" name="capacity" id="capacity" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('capacity') border-red-500 @enderror" value="{{ old('capacity', $room->capacity) }}" required>
                    @error('capacity') <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Main Image Management --}}
            <div class="mb-6 border p-4 rounded space-y-3">
                 <label class="block text-gray-700 text-sm font-bold">{{ __('Huidige Hoofdafbeelding') }}</label>
                 @if($room->image)
                    <img src="{{ $room->image_url }}" alt="{{ __('Hoofdafbeelding') }}" class="h-24 w-auto rounded mb-2 object-cover">
                 @else
                    <p class="text-sm text-gray-500 italic">{{ __('Geen hoofdafbeelding ingesteld.') }}</p>
                 @endif
                 
                 <div>
                    <label for="image" class="cursor-pointer inline-flex items-center px-4 py-2 bg-gray-200 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        {{ __('Nieuwe Hoofdafbeelding Uploaden (Optioneel)') }}
                    </label>
                    <input type="file" name="image" id="image" class="hidden @error('image') border-red-500 @enderror">
                    @error('image') <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p> @enderror
                    <p class="text-xs text-gray-600 mt-1">{{ __('Als u een nieuwe uploadt, wordt de oude vervangen.') }}</p>
                 </div>
            </div>

            {{-- Gallery Image Management --}}
            <div class="mb-6 border p-4 rounded space-y-3">
                <label class="block text-gray-700 text-sm font-bold">{{ __('Huidige Galerij Afbeeldingen') }}</label>
                <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-4 mb-2">
                    @forelse ($room->images as $img)
                        <div class="relative group aspect-square">
                            <img src="{{ $img->image_url }}" alt="{{ __('Galerij afbeelding') }} {{ $loop->iteration }}" class="h-full w-full object-cover rounded shadow-md">
                            <label class="absolute top-1 right-1 cursor-pointer opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                <input type="checkbox" name="delete_images[]" value="{{ $img->id }}" class="absolute -top-1 -right-1 h-5 w-5 text-red-600 border-gray-300 rounded focus:ring-red-500">
                                <span class="inline-block bg-white/80 rounded-full p-0.5 text-red-600 hover:text-red-800">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                                </span>
                                <span class="sr-only">{{ __('Verwijder afbeelding') }} {{ $img->id }}</span>
                            </label>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 italic col-span-full">{{ __('Geen galerij afbeeldingen gevonden.') }}</p>
                    @endforelse
                </div>
                 <p class="text-xs text-gray-600">{{ __('Vink aan om afbeeldingen te verwijderen bij opslaan.') }}</p>

                <div>
                    <label for="images" class="cursor-pointer inline-flex items-center px-4 py-2 bg-gray-200 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                         <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        {{ __('Nieuwe Galerij Afbeeldingen Toevoegen') }}
                    </label>
                     <input type="file" name="images[]" id="images" class="hidden @error('images.*') border-red-500 @enderror" multiple>
                    @error('images.*') <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p> @enderror
                    <p class="text-xs text-gray-600 mt-1">{{ __('Selecteer meerdere bestanden tegelijk.') }}</p> {{-- Added clarification --}}
                </div>
            </div>

            <div class="mb-6">
                 <label class="block text-gray-700 text-sm font-bold mb-2">{{ __('Beschikbaar?') }}</label>
                 <label class="inline-flex items-center">
                    <input type="hidden" name="is_available" value="0">
                    <input type="checkbox" name="is_available" value="1" class="form-checkbox h-5 w-5 text-blue-600" {{ old('is_available', $room->is_available) ? 'checked' : '' }}>
                    <span class="ml-2 text-gray-700">{{ __('Ja, deze kamer is beschikbaar voor boeking') }}</span>
                 </label>
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    {{ __('Wijzigingen Opslaan') }}
                </button>
                <a href="{{ route('admin.rooms.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                    {{ __('Annuleren') }}
                </a>
            </div>
        </form>
    </div>
</div>
@endsection 