<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TransactionFeatureTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test missing parameters when deposit value
     */
    public function test_missing_payee_when_deposit() {
        $parameters = [
            // 'payee' => 12,
            'value' => 42.0,
        ];
        $response = $this->json('POST', '/create-client', $parameters);
        $response
            ->assertStatus(400)
            ->assertJson([
                "error_message" => "The cpf field is required when cnpj is not present. (and 1 more error)"
            ]);
    }
}