<?php

namespace Database\Seeders;

use App\Models\Currency;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Base currencies
        Currency::query()->upsert([
            ['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$', 'active' => true],
            ['code' => 'VES', 'name' => 'Bolívar Soberano', 'symbol' => 'Bs', 'active' => true],
        ], ['code'], ['name', 'symbol', 'active']);

        // Example user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
