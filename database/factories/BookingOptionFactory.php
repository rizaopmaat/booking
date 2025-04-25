<?php

namespace Database\Factories;

use App\Models\BookingOption;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BookingOption>
 */
class BookingOptionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BookingOption::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $options = [
            ['Breakfast', 'Ontbijt'],
            ['Late check-out', 'Laat uitchecken'],
            ['Airport transfer', 'Luchthaventransfer'],
            ['Spa access', 'Toegang tot spa'],
            ['Welcome package', 'Welkomstpakket']
        ];
        
        $randomOption = $this->faker->randomElement($options);
        
        return [
            'name' => [
                'en' => $randomOption[0],
                'nl' => $randomOption[1]
            ],
            'description' => [
                'en' => $this->faker->paragraph(),
                'nl' => $this->faker->paragraph(),
            ],
            'price' => $this->faker->randomFloat(2, 10, 100),
            'price_type' => $this->faker->randomElement(['fixed', 'per_guest', 'per_night']),
            'is_active' => true,
            'is_cancellation_option' => false,
            'order' => $this->faker->numberBetween(1, 10),
        ];
    }

    /**
     * Indicate the option is for cancellation protection.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function cancellationOption()
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => [
                    'en' => 'Cancellation Protection',
                    'nl' => 'Annuleringsbescherming'
                ],
                'description' => [
                    'en' => 'Allows free cancellation up to 24 hours before check-in',
                    'nl' => 'Gratis annulering tot 24 uur voor inchecken'
                ],
                'price' => 15.00,
                'price_type' => 'fixed',
                'is_cancellation_option' => true,
            ];
        });
    }
} 