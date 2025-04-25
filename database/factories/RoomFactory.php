<?php

namespace Database\Factories;

use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Room>
 */
class RoomFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Room::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => [
                'nl' => $this->faker->words(3, true) . ' NL',
                'en' => $this->faker->words(3, true) . ' EN',
            ],
            'description' => [
                'nl' => $this->faker->paragraphs(3, true),
                'en' => $this->faker->paragraphs(3, true),
            ],
            'price' => $this->faker->randomFloat(2, 50, 500),
            'capacity' => $this->faker->numberBetween(1, 6),
            'total_inventory' => $this->faker->numberBetween(1, 10),
            'is_available' => true,
            'image' => null,
        ];
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function withImages($count = 1)
    {
        return $this->afterCreating(function (Room $room) use ($count) {
            for ($i = 0; $i < $count; $i++) {
                $room->images()->create([
                    'path' => 'test/image_' . $i . '.jpg',
                    'order' => $i,
                ]);
            }
        });
    }
} 