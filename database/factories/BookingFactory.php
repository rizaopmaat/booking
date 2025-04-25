<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Booking::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $checkInDate = $this->faker->dateTimeBetween('+1 week', '+4 weeks');
        $checkOutDate = (clone $checkInDate)->modify('+' . rand(1, 7) . ' days');
        $numGuests = $this->faker->numberBetween(1, 4);
        $pricePerNight = $this->faker->randomFloat(2, 80, 300);
        $nights = (int) $checkInDate->diff($checkOutDate)->format('%a');
        $totalPrice = $pricePerNight * $nights;
        
        return [
            'user_id' => User::factory(),
            'room_id' => Room::factory(),
            'check_in_date' => $checkInDate,
            'check_out_date' => $checkOutDate,
            'num_guests' => $numGuests,
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'cancelled']),
            'total_price' => $totalPrice,
            'options_total' => 0,
            'payment_method' => 'at_accommodation',
            'reference' => $this->faker->uuid(),
            'notes' => $this->faker->optional()->paragraph(),
            'guest_first_name' => $this->faker->firstName(),
            'guest_last_name' => $this->faker->lastName(),
            'guest_email' => $this->faker->email(),
            'guest_phone' => $this->faker->phoneNumber(),
            'street' => $this->faker->streetName(),
            'house_number' => $this->faker->buildingNumber(),
            'postal_code' => $this->faker->postcode(),
            'city' => $this->faker->city(),
            'country' => $this->faker->country(),
        ];
    }

    /**
     * Indicate the booking is confirmed.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function confirmed()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'confirmed',
            ];
        });
    }

    /**
     * Indicate the booking is pending.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function pending()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'pending',
            ];
        });
    }

    /**
     * Indicate the booking is cancelled.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function cancelled()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'cancelled',
            ];
        });
    }
} 