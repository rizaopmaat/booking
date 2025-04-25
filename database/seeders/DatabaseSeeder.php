<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Room;
use App\Models\Booking;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'is_admin' => true,
        ]);
        
        // Create normal user
        User::create([
            'name' => 'Test User',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
            'is_admin' => false,
        ]);
        
        // Create sample rooms with translations
        $rooms = [
            [
                'name' => ['nl' => 'Luxe Suite', 'en' => 'Luxury Suite'],
                'description' => ['nl' => 'Onze meest luxueuze suite met prachtig uitzicht over de stad.', 'en' => 'Our most luxurious suite with beautiful city views.'],
                'price' => 299.99,
                'capacity' => 2,
                'total_inventory' => 5,
                'image' => '/images/rooms/luxe-suite.jpg',
                'is_available' => true,
            ],
            [
                'name' => ['nl' => 'Deluxe Kamer', 'en' => 'Deluxe Room'],
                'description' => ['nl' => 'Ruime kamer met kingsize bed en eigen balkon.', 'en' => 'Spacious room with king-size bed and private balcony.'],
                'price' => 199.99,
                'capacity' => 2,
                'total_inventory' => 10,
                'image' => '/images/rooms/deluxe-room.jpg',
                'is_available' => true,
            ],
            [
                'name' => ['nl' => 'Familiekamer', 'en' => 'Family Room'],
                'description' => ['nl' => 'Perfecte kamer voor gezinnen met twee aparte slaapgedeelten.', 'en' => 'Perfect room for families with two separate sleeping areas.'],
                'price' => 249.99,
                'capacity' => 4,
                'total_inventory' => 3,
                'image' => '/images/rooms/family-room.jpg',
                'is_available' => true,
            ],
            // Add more rooms as needed with translations
        ];
        
        foreach ($rooms as $roomData) {
            Room::create($roomData);
        }
        
        // Create sample bookings (user_id and room_id might need adjustment if you change the users/rooms)
        Booking::create([
            'user_id' => 2, // Test user
            'room_id' => 1, // Luxe Suite
            'check_in_date' => now()->addDays(5),
            'check_out_date' => now()->addDays(10),
            'num_guests' => 2,
            'status' => 'confirmed',
            // Calculate base price + discounts (similar logic to BookingController might be needed here for accuracy)
            // For simplicity, using a hardcoded example, adjust if needed.
            'total_price' => (299.99 * 5) - (299.99 * 5 * 0.15), // Price * nights - duration discount (assuming returning status doesn't apply here)
            'options_total' => 0, // No options in this seed example
            // 'reference' is handled by the model
            // 'notes' can be added if needed
        ]);

        // Call other seeders
        $this->call([
            BookingOptionSeeder::class,
            // Add other seeders here if you have them
        ]);

        // You might want to seed Bookings after Rooms and Options if needed
        // $this->call(BookingSeeder::class);
    }
}
