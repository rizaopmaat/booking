<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BookingOption;

class BookingOptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BookingOption::create([
            'name' => ['nl' => 'Annuleringspakket', 'en' => 'Cancellation Package'],
            'description' => ['nl' => 'Mogelijkheid om flexibel te annuleren volgens voorwaarden.', 'en' => 'Option to cancel flexibly according to conditions.'],
            'price' => 10.00, // Example price, adjust as needed
            'price_type' => 'fixed',
            'is_cancellation_option' => true,
            'is_active' => true,
            'order' => 1,
        ]);

        BookingOption::create([
            'name' => ['nl' => 'Onbeperkt Ontbijt', 'en' => 'Unlimited Breakfast'],
            'description' => ['nl' => 'Geniet elke ochtend van een uitgebreid ontbijtbuffet.', 'en' => 'Enjoy an extensive breakfast buffet every morning.'],
            'price' => 15.00,
            'price_type' => 'per_guest',
            'is_active' => true,
            'order' => 2,
        ]);

        BookingOption::create([
            'name' => ['nl' => 'Verwenpakket', 'en' => 'Indulgence Package'],
            'description' => ['nl' => 'Een uur vroeger inchecken, een uur later uitchecken en een fles bubbels.', 'en' => 'Check in one hour earlier, check out one hour later, and a bottle of bubbly.'],
            'price' => 40.00,
            'price_type' => 'fixed',
            'is_active' => true,
            'order' => 3,
        ]);

        BookingOption::create([
            'name' => ['nl' => 'Ontbijt op Bed', 'en' => 'Breakfast in Bed'],
            'description' => ['nl' => 'Laat een luxe ontbijt bezorgen op uw kamer.', 'en' => 'Have a luxurious breakfast delivered to your room.'],
            'price' => 45.00,
            'price_type' => 'fixed',
            'is_active' => true,
            'order' => 4,
        ]);

        BookingOption::create([
            'name' => ['nl' => 'Love Pakket', 'en' => 'Love Package'],
            'description' => ['nl' => 'Romantische verrassingen voorbereid op de kamer.', 'en' => 'Romantic surprises prepared in the room.'],
            'price' => 50.00, // Example price
            'price_type' => 'fixed',
            'is_active' => true,
            'order' => 5,
        ]);

        BookingOption::create([
            'name' => ['nl' => 'Party Pakket Deluxe', 'en' => 'Party Package Deluxe'],
            'description' => ['nl' => 'Ballonnen, fles bubbels, felicitatiekaart en slingers voor een feestelijke aankomst.', 'en' => 'Balloons, bottle of bubbly, congratulatory card, and garlands for a festive arrival.'],
            'price' => 75.00, // Example price
            'price_type' => 'fixed',
            'is_active' => true,
            'order' => 6,
        ]);
    }
}
