<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Currency>
 */
class CurrencyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $code = strtoupper(fake()->unique()->lexify('???'));

        return [
            'code' => $code,
            'name' => $code,
            'symbol' => '$',
            'active' => true,
        ];
    }

    public function usd(): static
    {
        return $this->state(fn () => [
            'code' => 'USD',
            'name' => 'US Dollar',
            'symbol' => '$',
            'active' => true,
        ]);
    }

    public function ves(): static
    {
        return $this->state(fn () => [
            'code' => 'VES',
            'name' => 'Bolívar Soberano',
            'symbol' => 'Bs',
            'active' => true,
        ]);
    }
}
