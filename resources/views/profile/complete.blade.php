<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">{{ __('messages.complete_profile_title') }}</h2>
                    
                    @if ($new_registration)
                        <div class="mb-8 p-4 bg-green-100 text-green-700 rounded-md">
                            <p>{{ __('messages.welcome_after_registration') }}</p>
                        </div>
                    @endif
                    
                    <p class="mb-6 text-gray-600">{{ __('messages.complete_profile_description') }}</p>
                    
                    <form method="POST" action="{{ route('profile.complete.save') }}" class="space-y-6">
                        @csrf
                        
                        {{-- Personal Information --}}
                        <div class="bg-gray-50 p-4 rounded-md mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Personal Information') }}</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                {{-- First Name --}}
                                <div>
                                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">{{ __('First Name') }}</label>
                                    <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $user->first_name) }}" 
                                        class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-red-200 focus:border-red-500 @error('first_name') border-red-500 @enderror">
                                    @error('first_name')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Last Name --}}
                                <div>
                                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Last Name') }}</label>
                                    <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $user->last_name) }}" 
                                        class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-red-200 focus:border-red-500 @error('last_name') border-red-500 @enderror">
                                    @error('last_name')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Phone Number --}}
                                <div>
                                    <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Phone Number') }}</label>
                                    <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number', $user->phone_number) }}" 
                                        class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-red-200 focus:border-red-500 @error('phone_number') border-red-500 @enderror">
                                    @error('phone_number')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        {{-- Address Information --}}
                        <div class="bg-gray-50 p-4 rounded-md mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Address Information') }}</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                                {{-- Street --}}
                                <div class="md:col-span-2">
                                    <label for="street" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Street') }}</label>
                                    <input type="text" name="street" id="street" value="{{ old('street', $user->street) }}" 
                                        class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-red-200 focus:border-red-500 @error('street') border-red-500 @enderror">
                                    @error('street')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- House Number --}}
                                <div>
                                    <label for="house_number" class="block text-sm font-medium text-gray-700 mb-1">{{ __('House Number') }}</label>
                                    <input type="text" name="house_number" id="house_number" value="{{ old('house_number', $user->house_number) }}" 
                                        class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-red-200 focus:border-red-500 @error('house_number') border-red-500 @enderror">
                                    @error('house_number')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                {{-- Postal Code --}}
                                <div>
                                    <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Postal Code') }}</label>
                                    <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', $user->postal_code) }}" 
                                        class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-red-200 focus:border-red-500 @error('postal_code') border-red-500 @enderror">
                                    @error('postal_code')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- City --}}
                                <div>
                                    <label for="city" class="block text-sm font-medium text-gray-700 mb-1">{{ __('City') }}</label>
                                    <input type="text" name="city" id="city" value="{{ old('city', $user->city) }}" 
                                        class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-red-200 focus:border-red-500 @error('city') border-red-500 @enderror">
                                    @error('city')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Country --}}
                                <div>
                                    <label for="country" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Country') }}</label>
                                    <input type="text" name="country" id="country" value="{{ old('country', $user->country) }}" 
                                        class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-red-200 focus:border-red-500 @error('country') border-red-500 @enderror">
                                    @error('country')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between mt-8">
                            <button type="submit" name="skip" value="true" class="bg-gray-200 text-gray-800 hover:bg-gray-300 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition">
                                {{ __('messages.complete_profile_skip') }}
                            </button>

                            <button type="submit" class="bg-red-600 text-white hover:bg-red-700 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition">
                                {{ __('messages.complete_profile_save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 