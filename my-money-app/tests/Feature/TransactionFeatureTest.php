<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Client;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TransactionFeatureTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test missing parameters when deposit value
     */
    public function test_missing_payee_when_deposit() {
        $parameters = [
            'value' => 42.0,
        ];
        $response = $this->json('PATCH', '/deposit', $parameters);

        $response
            ->assertStatus(400)
            ->assertJson([
                "error_message" => "The payee field is required."
            ]);
    }

    /**
     * Test missing parameters when deposit value
     */
    public function test_missing_value_when_deposit() {
        $parameters = [
            'payee' => 12,
        ];
        $response = $this->json('PATCH', '/deposit', $parameters);

        $response
            ->assertStatus(400)
            ->assertJson([
                "error_message" => "The value field is required."
            ]);
    }

    /**
     * Test send negative value
     */
    public function test_negative_value_when_deposit() {
        $parameters = [
            'payee' => 12,
            'value' => -12,
        ];
        $response = $this->json('PATCH', '/deposit', $parameters);

        $response
            ->assertStatus(400)
            ->assertJson([
                "error_message" => "The value field must be at least 0.01."
            ]);
    }

    /**
     * Test send negative value
     */
    public function test_deposit_non_existent_account() {
        $parameters = [
            'payee' => 2,
            'value' => 12,
        ];
        $response = $this->json('PATCH', '/deposit', $parameters);

        $response
            ->assertStatus(404)
            ->assertJson([
                "message" => "Account not found!"
            ]);
    }

    /**
     * Test deposit a value successfully
     */
    public function test_deposit_successfully() {
        $client = Client::factory()->create();
        $account = Account::factory()->create([
            'account_owner_id' => $client->id,
            'balance' => 0.00,
            'account_type' => 'client',
        ]);

        $parameters = [
            'payee' => $account->id,
            'value' => 42.0,
        ];
        $response = $this->json('PATCH', '/deposit', $parameters);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                "message",
                "account" => [
                    "account_id",
                    "balance",
                    "account_type",
                ],
            ])
            ->assertJson([
                'message' => 'successfully deposited!',
            ]);

        $this->assertDatabaseHas('accounts', [
            'id' => $response->json('account.account_id'),
            'balance' => 42.0,
            'account_type' => 'client',
        ]);
    }
}