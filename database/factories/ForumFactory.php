<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Forum>
 */
class ForumFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'forumname' => $this->faker->word,
            'description' => $this->faker->sentence,
            'field_id' => function () {
                return \App\Models\Field::factory();
            },
            'user_id' => function () {
                return \App\Models\User::factory();
            },
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now')
        ];
    }
}
