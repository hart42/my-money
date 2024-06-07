<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition()
    {
        return [
            'full_name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'cpf' => $this->faker->numerify('###########'),
            'cnpj' => $this->faker->numerify('##############'),
            'password' => bcrypt('password'),
            'shop_name' => $this->faker->company,
        ];
    }
}