<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reply>
 */
class ReplyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reply_content' => $this->faker->paragraph,
            'user_id' => function () {
                return \App\Models\User::factory();
            },
            'message_id' => function () {
                return \App\Models\Message::factory();
            },
            'is_archived' => $this->faker->boolean,
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now')
        ];
    }
}
