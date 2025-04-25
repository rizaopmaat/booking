@extends('layouts.app')

@section('title', $room->name)

@section('content')
<div class="bg-gradient-to-br from-red-50 to-red-100 py-12 px-4">
    <div class="max-w-7xl mx-auto">
        <div class="mb-8">
            <a href="{{ route('rooms.index', request()->only(['check_in', 'check_out', 'guests'])) }}" class="inline-flex items-center text-red-600 hover:text-red-800 font-semibold">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                {{ __('Back to Rooms') }}
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-xl overflow-hidden">
            <div class="mb-8">
                <img src="{{ $room->image_url }}" alt="{{ $room->name }}" class="w-full h-80 object-cover">
                @if($room->images->isNotEmpty())
                    <div class="grid grid-cols-5 gap-2 p-4 bg-gray-50">
                        @foreach($room->images->take(5) as $image)
                            <img src="{{ $image->image_url }}" alt="Gallery image for {{ $room->name }}" class="w-full h-24 object-cover rounded cursor-pointer hover:opacity-75 transition duration-300">
                        @endforeach
                    </div>
                @endif
            </div>
            
            <div class="p-8">
                <h1 class="text-3xl md:text-4xl font-bold font-playfair text-gray-900 mb-3">{{ $room->name }}</h1>
                <div class="flex items-center text-gray-600 mb-4 text-sm">
                    <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <p class="text-gray-700 mb-6 leading-relaxed">{{ $room->description }}</p>

                <div class="mb-6 border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">{{ __('Features') }}</h3>
                    <div class="grid grid-cols-2 gap-4 text-gray-600">
                        <span class="flex items-center"><svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20V10m0 10c-3.072 0-5.564-2.493-5.564-5.564 0-3.072 2.492-5.564 5.564-5.564 3.072 0 5.564 2.492 5.564 5.564 0 3.071-2.492 5.564-5.564 5.564z"></path></svg>{{ __('Free WiFi') }}</span>
                        <span class="flex items-center"><svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>{{ __('Air Conditioning') }}</span>
                        <span class="flex items-center"><svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-14l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>{{ __('Luxury Bathroom') }}</span>
                    </div>
                </div>

                <form action="{{ route('booking.confirmation.show') }}" method="POST" x-data="bookingForm({{ $room->price }}, {{ $isReturningCustomer ? 'true' : 'false' }}, {{ $bookingOptions->toJson() }})" @submit.prevent="submitForm($event)" x-init="init(); calculatePrice()">
                    @csrf
                    <input type="hidden" name="room_id" value="{{ $room->id }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="check_in_date" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Check In') }}</label>
                            <input type="date" id="check_in_date" name="check_in_date" value="{{ request('check_in', old('check_in_date')) }}" x-model="checkInDate" @change="calculatePrice" required class="block w-full bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="check_out_date" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Check Out') }}</label>
                            <input type="date" id="check_out_date" name="check_out_date" value="{{ request('check_out', old('check_out_date')) }}" x-model="checkOutDate" @change="calculatePrice" required class="block w-full bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                        </div>
                    </div>
                     <div>
                        <label for="num_guests" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Guests') }}</label>
                        <select id="num_guests" name="num_guests" x-model.number="numGuests" @change="calculatePrice" required class="block w-full bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm mb-4">
                            @for ($i = 1; $i <= $room->capacity; $i++)
                                <option value="{{ $i }}">{{ $i }} {{ $i == 1 ? __('Guest') : __('Guests') }}</option>
                            @endfor
                        </select>
                    </div>

                    <div class="mb-6 border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">{{ __('Optional Packages') }}</h3>
                        <div class="space-y-3">
                            <template x-for="option in bookingOptions" :key="option.id">
                                <label class="flex items-start p-3 border border-gray-200 rounded-md hover:bg-gray-50 transition duration-150">
                                    <input type="checkbox" 
                                           :value="option.id" 
                                           :checked="selectedOptions.includes(option.id)"
                                           @click="toggleOption(option.id)"
                                           class="mt-1 h-4 w-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                                    <div class="ml-3 text-sm">
                                        <span class="font-medium text-gray-900" x-text="option.name"></span>
                                        <span class="text-gray-500 ml-2">
                                            (€<span x-text="option.price"></span> <span x-show="option.price_type === 'per_guest'">/ {{ __('per person') }}</span>)
                                        </span>
                                        <p class="text-gray-500" x-text="option.description"></p>
                                    </div>
                                </label>
                            </template>
                        </div>
                    </div>

                    <div class="bg-red-50 p-4 rounded-md mb-6">
                        <h4 class="text-md font-semibold text-gray-800 mb-3">{{ __('Price Summary') }}</h4>

                        <div class="flex justify-between items-center mb-1 text-sm">
                            <span class="text-gray-700">{{ __('Price per night') }}:</span>
                            <span class="font-semibold">€{{ number_format($room->price, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center mb-2 text-sm" x-show="numberOfNights > 0">
                            <span class="text-gray-700" x-text="'x ' + numberOfNights + ' ' + (numberOfNights === 1 ? '{{ __('night') }}' : '{{ __('nights') }}')"></span>
                            <span class="font-semibold" x-text="'€' + subtotal.toFixed(2)"></span>
                        </div>

                        <div class="flex justify-between items-center text-xs text-green-700 mb-1 pt-2 border-t border-red-200" x-show="durationDiscountAmount > 0">
                            <span>{{ __('Stay Discount') }} (15%):</span>
                            <span class="font-semibold" x-text="'- €' + durationDiscountAmount.toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between items-center text-xs text-blue-700 mb-1" x-show="loyaltyDiscountAmount > 0">
                            <span>{{ __('Loyalty Discount') }}:</span>
                            <span class="font-semibold" x-text="'- €' + loyaltyDiscountAmount.toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between items-center text-sm font-semibold mb-2 text-gray-800" x-show="(durationDiscountAmount > 0 || loyaltyDiscountAmount > 0)">
                            <span>{{ __('Base Total') }}:</span>
                            <span x-text="'€' + baseTotal.toFixed(2)"></span>
                        </div>

                        <div class="pt-2 border-t border-red-200" x-show="selectedOptions.length > 0">
                            <h5 class="text-sm font-semibold text-gray-700 mb-1">{{ __('Selected Packages') }}:</h5>
                            <template x-for="optionId in [...new Set(selectedOptions)]" :key="optionId">
                                <div class="flex justify-between items-center text-xs text-gray-600 mb-1 ml-2">
                                    <span x-text="
                                        (() => {
                                            const option = getOptionDetails(optionId);
                                            const isBreakfast = option.name && option.name.toLowerCase().includes('breakfast');
                                            const quantity = optionQuantities[optionId] || 1;
                                            let displayName = option.name;
                                            
                                            if (isBreakfast && quantity > 1) {
                                                displayName = option.name + ' (' + quantity + 'x)';
                                            }
                                            
                                            return displayName + (option.price_type === 'per_guest' ? ' (x' + numGuests + ')' : '');
                                        })()
                                    "></span>
                                    <span class="font-semibold" x-text="'+ €' + calculateSingleOptionPrice(optionId).toFixed(2)"></span>
                                </div>
                            </template>
                            <div class="flex justify-between items-center text-sm font-semibold mt-1 text-gray-800">
                                <span>{{ __('Packages Subtotal') }}:</span>
                                <span x-text="'€' + optionsSubtotal.toFixed(2)"></span>
                            </div>
                        </div>

                        <div class="flex justify-between items-center border-t border-red-300 pt-2 mt-2">
                            <span class="text-lg font-bold text-gray-900">{{ __('Total Price') }}</span>
                            <del class="text-sm text-gray-500 mr-2" x-show="optionsSubtotal > 0 || durationDiscountAmount > 0 || loyaltyDiscountAmount > 0" x-text="'€' + originalTotal.toFixed(2)"></del>
                            <span class="text-lg font-bold text-red-700" x-text="'€' + total.toFixed(2)"></span>
                        </div>
                        <p x-show="error" x-text="error" class="text-red-600 text-sm mt-2"></p>
                    </div>

                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-md transition duration-300 text-lg flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        {{ __('Book Now') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function bookingForm(pricePerNight, isReturningCustomer, bookingOptionsJson) {
        return {
            checkInDate: '{{ request("check_in", old("check_in_date")) }}',
            checkOutDate: '{{ request("check_out", old("check_out_date")) }}',
            numGuests: {{ old('num_guests', 1) }},
            pricePerNight: pricePerNight,
            isReturningCustomer: isReturningCustomer,
            bookingOptions: bookingOptionsJson,
            selectedOptions: [],
            optionQuantities: {},

            numberOfNights: 0,
            subtotal: 0,
            durationDiscountAmount: 0,
            loyaltyDiscountAmount: 0,
            loyaltyDiscountValue: 5,
            baseTotal: 0,
            optionsSubtotal: 0,
            originalTotal: 0,
            total: 0,
            error: '',
            minNightsForDiscount: 3,
            discountRate: 0.15,

            init() {
                console.log("Booking Options in Alpine:", this.bookingOptions); // <-- DEBUG LOG

                const initialGuests = parseInt('{{ request("num_guests", old("num_guests", 1)) }}');
                const roomCapacity = {{ $room->capacity }};
                if (!isNaN(initialGuests) && initialGuests > 0 && initialGuests <= roomCapacity) {
                     this.numGuests = initialGuests;
                } else {
                     this.numGuests = 1; // Default to 1 if invalid or not provided
                }
                // Wacht tot DOM updates klaar zijn, zet dan de select waarde EN bereken de prijs
                this.$nextTick(() => { 
                     const guestSelect = document.getElementById('num_guests');
                     if (guestSelect) guestSelect.value = this.numGuests; // Zorg dat select klopt
                     
                     this.calculatePrice(); // Bereken prijs NU pas
                });
            },

            getOptionDetails(optionId) {
                const option = this.bookingOptions.find(opt => opt.id == optionId) || {};
                console.log(`Optie details opgevraagd voor ID ${optionId}: ${JSON.stringify(option)}`);
                return option;
            },

            toggleOption(optionId) {
                const option = this.getOptionDetails(optionId);
                const isBreakfastOption = option.name && option.name.toLowerCase().includes('breakfast');
                
                if (this.selectedOptions.includes(optionId)) {
                    this.selectedOptions = this.selectedOptions.filter(id => id !== optionId);
                    if (this.optionQuantities[optionId]) {
                        delete this.optionQuantities[optionId];
                    }
                } else {
                    if (isBreakfastOption) {
                        this.selectedOptions.push(optionId);
                        if (!this.optionQuantities[optionId]) {
                            this.optionQuantities[optionId] = 0;
                        }
                        this.optionQuantities[optionId]++;
                    } else {
                        if (!this.selectedOptions.includes(optionId)) {
                            this.selectedOptions.push(optionId);
                            this.optionQuantities[optionId] = 1;
                        }
                    }
                }
                
                this.calculatePrice();
                return true;
            },

            calculateSingleOptionPrice(optionId) {
                 const option = this.getOptionDetails(optionId);
                 if (!option.id) return 0;
                 let price = parseFloat(option.price);
                 if (option.price_type === 'per_guest') {
                     const oldPrice = price;
                     price *= this.numGuests;
                     console.log(`Berekening per persoon optie ${option.name}: €${oldPrice} x ${this.numGuests} = €${price}`);
                 } else {
                     console.log(`Vaste prijs optie ${option.name}: €${price}`);
                 }
                 
                 const quantity = this.optionQuantities[optionId] || 1;
                 if (quantity > 1) {
                     console.log(`Optie ${option.name} x${quantity}: €${price} x ${quantity} = €${price * quantity}`);
                     return price * quantity;
                 }
                 
                 return price;
            },

            calculatePrice() {
                console.log(`Prijsberekening gestart. Aantal gasten: ${this.numGuests}`);
                // Reset prijsgerelateerde variabelen
                this.durationDiscountAmount = 0;
                this.loyaltyDiscountAmount = 0;
                this.originalTotal = 0;
                this.total = 0;
                this.subtotal = 0;
                this.numberOfNights = 0;
                this.optionsSubtotal = 0;
                this.baseTotal = 0;
                this.error = ''; // Reset error hier

                if (!this.checkInDate || !this.checkOutDate) {
                    // Geen datums, knop blijft disabled via de :disabled binding
                    console.log("Geen datums geselecteerd, prijsberekening gestopt");
                    return; 
                }

                const start = new Date(this.checkInDate);
                const end = new Date(this.checkOutDate);
                const todayMidnight = new Date();
                todayMidnight.setHours(0, 0, 0, 0); 

                 // Check if dates were parsed correctly
                if (isNaN(start.getTime()) || isNaN(end.getTime())) {
                     this.error = 'Invalid date format provided.'; // Set a specific error
                     // Reset prices
                     this.total = 0; this.subtotal = 0; this.numberOfNights = 0; this.optionsSubtotal = 0; this.baseTotal = 0;
                     return; // Stop calculation
                }

                if (start < todayMidnight) {
                    this.error = '{{ __("Check-in date cannot be in the past.") }}';
                    // Reset prices
                    this.total = 0; this.subtotal = 0; this.numberOfNights = 0; this.optionsSubtotal = 0; this.baseTotal = 0;
                    return;
                }
                if (end <= start) {
                    this.error = '{{ __("Check-out date must be after check-in date.") }}';
                    // Reset prices
                    this.total = 0; this.subtotal = 0; this.numberOfNights = 0; this.optionsSubtotal = 0; this.baseTotal = 0;
                    return;
                }

                // --- Als we hier komen, is error leeg en zijn datums geldig ---
                // Voer de normale prijsberekening uit...
                const diffTime = Math.abs(end - start);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                this.numberOfNights = diffDays;
                this.subtotal = this.numberOfNights * this.pricePerNight;
                this.originalTotal = this.subtotal; 
                this.baseTotal = this.subtotal;

                if (this.numberOfNights >= this.minNightsForDiscount) {
                    this.durationDiscountAmount = this.subtotal * this.discountRate;
                    this.baseTotal -= this.durationDiscountAmount;
                }

                if (this.isReturningCustomer) {
                    this.loyaltyDiscountAmount = this.loyaltyDiscountValue;
                    this.baseTotal -= this.loyaltyDiscountAmount;
                }
                this.baseTotal = Math.max(0, this.baseTotal); 

                this.optionsSubtotal = 0; 
                console.log(`Geselecteerde opties: ${JSON.stringify(this.selectedOptions)}`);
                console.log(`Beschikbare opties: ${JSON.stringify(this.bookingOptions)}`);
                console.log(`Optie hoeveelheden: ${JSON.stringify(this.optionQuantities)}`);
                
                const uniqueOptions = [...new Set(this.selectedOptions)];
                uniqueOptions.forEach(optionId => {
                    const optionPrice = this.calculateSingleOptionPrice(optionId);
                    this.optionsSubtotal += optionPrice;
                    console.log(`Optie ${optionId} toegevoegd: €${optionPrice.toFixed(2)}, totaal opties nu: €${this.optionsSubtotal.toFixed(2)}`);
                });

                this.total = this.baseTotal + this.optionsSubtotal;
                this.total = Math.max(0, this.total); 
                console.log(`Eindprijs berekend: €${this.total.toFixed(2)}`);
            },

            submitForm(event) {
                if (this.error) {
                    alert(this.error);
                } else if (!this.checkInDate || !this.checkOutDate) {
                    alert('{{ __("Please select check-in and check-out dates.") }}');
                } else {
                     // Zorg ervoor dat geselecteerde opties als hidden inputs worden toegevoegd
                     const form = event.target;
                     
                     // Verwijder eerst alle bestaande checkboxes voor opties
                     form.querySelectorAll('input[name^="options"]').forEach(el => el.remove());
                     
                     // Voeg elke optie toe met de juiste hoeveelheid
                     const uniqueOptions = [...new Set(this.selectedOptions)];
                     uniqueOptions.forEach(optionId => {
                         const option = this.getOptionDetails(optionId);
                         const isBreakfastOption = option.name && option.name.toLowerCase().includes('breakfast');
                         const quantity = isBreakfastOption && this.optionQuantities[optionId] ? 
                             this.optionQuantities[optionId] : 1;
                         
                         // Voeg de optie toe als hidden input met juiste quantity
                         const hiddenInput = document.createElement('input');
                         hiddenInput.type = 'hidden';
                         hiddenInput.name = `options[${optionId}]`;
                         hiddenInput.value = quantity;
                         form.appendChild(hiddenInput);
                         
                         console.log(`Optie ${optionId} verzonden met hoeveelheid: ${quantity}`);
                     });
                     
                     // Debug log voor opties
                     console.log(`Verzenden met geselecteerde opties: ${JSON.stringify(this.selectedOptions)}`);
                     console.log(`Verzenden met hoeveelheden: ${JSON.stringify(this.optionQuantities)}`);
                     
                     // Geen fouten, verstuur het formulier expliciet
                     form.submit();
                }
            }
        }
    }
</script>
@endsection 