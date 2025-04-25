<?php

namespace Database\Factories;

use App\Models\Room;
use App\Models\RoomImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RoomImage>
 */
class RoomImageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = RoomImage::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        static $order = 0;
        
        return [
            'room_id' => Room::factory(),
            'path' => 'images/rooms/test_' . $this->faker->uuid() . '.jpg',
            'order' => $order++,
            'alt_text' => $this->faker->optional()->sentence(3),
        ];
    }
} 