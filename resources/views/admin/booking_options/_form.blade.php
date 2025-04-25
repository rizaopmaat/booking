@csrf

<div class="mb-4">
    <label for="name_nl" class="block text-sm font-medium text-gray-700">{{ __('Name (NL)') }}</label>
    <input type="text" name="name[nl]" id="name_nl" value="{{ old('name.nl', $option->getTranslation('name', 'nl', false) ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm" required>
    @error('name.nl') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
</div>

<div class="mb-4">
    <label for="name_en" class="block text-sm font-medium text-gray-700">{{ __('Name (EN)') }}</label>
    <input type="text" name="name[en]" id="name_en" value="{{ old('name.en', $option->getTranslation('name', 'en', false) ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm" required>
    @error('name.en') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
</div>

<div class="mb-4">
    <label for="description_nl" class="block text-sm font-medium text-gray-700">{{ __('Description (NL)') }}</label>
    <textarea name="description[nl]" id="description_nl" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">{{ old('description.nl', $option->getTranslation('description', 'nl', false) ?? '') }}</textarea>
    @error('description.nl') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
</div>

<div class="mb-4">
    <label for="description_en" class="block text-sm font-medium text-gray-700">{{ __('Description (EN)') }}</label>
    <textarea name="description[en]" id="description_en" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">{{ old('description.en', $option->getTranslation('description', 'en', false) ?? '') }}</textarea>
    @error('description.en') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
    <div>
        <label for="price" class="block text-sm font-medium text-gray-700">{{ __('Price') }} (€)</label>
        <input type="number" step="0.01" name="price" id="price" value="{{ old('price', $option->price ?? '0.00') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm" required>
        @error('price') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="price_type" class="block text-sm font-medium text-gray-700">{{ __('Price Type') }}</label>
        <select name="price_type" id="price_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm" required>
            <option value="fixed" {{ old('price_type', $option->price_type ?? 'fixed') == 'fixed' ? 'selected' : '' }}>{{ __('Fixed') }}</option>
            <option value="per_person" {{ old('price_type', $option->price_type ?? '') == 'per_person' ? 'selected' : '' }}>{{ __('Per Person') }}</option>
        </select>
        @error('price_type') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
    </div>
</div>

<div class="flex items-start mb-4">
    <div class="flex items-center h-5">
        <input id="is_cancellation_option" name="is_cancellation_option" type="checkbox" value="1" {{ old('is_cancellation_option', $option->is_cancellation_option ?? false) ? 'checked' : '' }} class="focus:ring-red-500 h-4 w-4 text-red-600 border-gray-300 rounded">
    </div>
    <div class="ml-3 text-sm">
        <label for="is_cancellation_option" class="font-medium text-gray-700">{{ __('Is Cancellation Option?') }}</label>
        <p class="text-gray-500">{{ __('Check if this package allows special cancellation.') }}</p>
    </div>
</div>

<div class="flex items-start mb-6">
    <div class="flex items-center h-5">
        <input id="is_active" name="is_active" type="checkbox" value="1" {{ old('is_active', $option->is_active ?? true) ? 'checked' : '' }} class="focus:ring-red-500 h-4 w-4 text-red-600 border-gray-300 rounded">
    </div>
    <div class="ml-3 text-sm">
        <label for="is_active" class="font-medium text-gray-700">{{ __('Active?') }}</label>
        <p class="text-gray-500">{{ __('Uncheck to hide this option from users.') }}</p>
    </div>
</div>

<div class="flex justify-end">
    <a href="{{ route('admin.booking-options.index') }}" class="mr-4 inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
        {{ __('Cancel') }}
    </a>
    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
        {{ $submitText ?? __('Save') }}
    </button>
</div> 