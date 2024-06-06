<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccountFactory extends Factory
{
    protected $model = Account::class;

    public function definition()
    {
        return [
            'account_owner_id' => Client::factory(),
            'balance' => $this->faker->randomFloat(2, 0, 1000),
            'account_type' => $this->faker->randomElement(['shop', 'client']),
        ];
    }
}