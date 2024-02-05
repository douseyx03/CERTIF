<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Field>
 */

class FieldFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        try {
            $user = Auth::user();
            $userId = $user ? $user->id : null;
        } catch (\Exception $e) {
            Log::error('Error accessing user ID in FieldFactory: ' . $e->getMessage());
            $userId = null;
        }

        return [
            'fieldname' => $this->faker->word,
            'description' => $this->faker->sentence,
            'picture' => $this->faker->imageUrl(),
            'is_archived' => $this->faker->boolean,
            'user_id' => $userId,
        ];
    }
}
