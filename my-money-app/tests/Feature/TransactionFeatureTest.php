<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Client;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TransactionFeatureTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void {
        parent::setUp();
        $this->withoutMiddleware();
    }

    /**
     * Test missing parameters when deposit value
     */
    public function test_missing_payee_when_deposit() {
        $parameters = [
            'value' => 42.0,
        ];
        $response = $this->json('POST', '/deposit', $parameters);

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
        $response = $this->json('POST', '/deposit', $parameters);

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
        $response = $this->json('POST', '/deposit', $parameters);

        $response
            ->assertStatus(400)
            ->assertJson([
                "error_message" => "The value field must be at least 0.01."
            ]);
    }

    /**
     * Test deposit in non-existent account
     */
    public function test_deposit_non_existent_account() {
        $parameters = [
            'payee' => 2,
            'value' => 12,
        ];
        $response = $this->json('POST', '/deposit', $parameters);

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
        $response = $this->json('POST', '/deposit', $parameters);

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

    /**
     * Test missing parameters when withdraw value
     */
    public function test_missing_payer_when_withdraw() {
        $parameters = [
            'value' => 42.0,
        ];
        $response = $this->json('POST', '/withdraw', $parameters);

        $response
            ->assertStatus(400)
            ->assertJson([
                "error_message" => "The payer field is required."
            ]);
    }

    /**
     * Test missing parameters when withdraw value
     */
    public function test_missing_value_when_withdraw() {
        $parameters = [
            'payer' => 12,
        ];
        $response = $this->json('POST', '/withdraw', $parameters);

        $response
            ->assertStatus(400)
            ->assertJson([
                "error_message" => "The value field is required."
            ]);
    }

    /**
     * Test send negative value
     */
    public function test_negative_value_when_withdraw() {
        $parameters = [
            'payer' => 12,
            'value' => -12,
        ];
        $response = $this->json('POST', '/withdraw', $parameters);

        $response
            ->assertStatus(400)
            ->assertJson([
                "error_message" => "The value field must be at least 0.01."
            ]);
    }

    /**
     * Test withdraw from non-existent-account
     */
    public function test_withdraw_non_existent_account() {
        $parameters = [
            'payer' => 2,
            'value' => 12,
        ];
        $response = $this->json('POST', '/withdraw', $parameters);

        $response
            ->assertStatus(404)
            ->assertJson([
                "message" => "Account not found!"
            ]);
    }

    /**
     * Test withdraw with insufficient funds
     */
    public function test_withdraw_insufficient_funds() {
        $client = Client::factory()->create();
        $account = Account::factory()->create([
            'account_owner_id' => $client->id,
            'balance' => 10.00,
            'account_type' => 'client',
        ]);

        $parameters = [
            'payer' => $account->id,
            'value' => 42.0,
        ];
        $response = $this->json('POST', '/withdraw', $parameters);

        $response
            ->assertStatus(400)
            ->assertJsonStructure([
                "message",
            ])
            ->assertJson([
                'message' => 'insufficient funds!',
            ]);

        $this->assertDatabaseHas('accounts', [
            'id' => $account->id,
            'balance' => 10.00,
            'account_type' => 'client',
        ]);
    }

    /**
     * Test withdraw a value successfully
     */
    public function test_withdraw_successfully() {
        $client = Client::factory()->create();
        $account = Account::factory()->create([
            'account_owner_id' => $client->id,
            'balance' => 100.00,
            'account_type' => 'client',
        ]);

        $parameters = [
            'payer' => $account->id,
            'value' => 42.0,
        ];
        $response = $this->json('POST', '/withdraw', $parameters);

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
                'message' => 'successfully withdraw!',
            ]);

        $this->assertDatabaseHas('accounts', [
            'id' => $response->json('account.account_id'),
            'balance' => (100.00 - 42.0),
            'account_type' => 'client',
        ]);
    }

    /**
     * Test missing parameters when transfer payer
     */
    public function test_missing_payer_when_transfer() {
        $parameters = [
            'payee' => 12,
            'value' => 42.0,
        ];
        $response = $this->json('POST', '/transfer', $parameters);

        $response
            ->assertStatus(400)
            ->assertJson([
                "error_message" => "The payer field is required."
            ]);
    }

    /**
     * Test missing parameters when transfer payee
     */
    public function test_missing_payee_when_transfer() {
        $parameters = [
            'payer' => 11,
            'value' => 42.0,
        ];
        $response = $this->json('POST', '/transfer', $parameters);

        $response
            ->assertStatus(400)
            ->assertJson([
                "error_message" => "The payee field is required."
            ]);
    }

    /**
     * Test missing parameters when transfer value
     */
    public function test_missing_value_when_transfer() {
        $parameters = [
            'payer' => 11,
            'payee' => 12,
        ];
        $response = $this->json('POST', '/transfer', $parameters);

        $response
            ->assertStatus(400)
            ->assertJson([
                "error_message" => "The value field is required."
            ]);
    }

    /**
     * Test send negative value
     */
    public function test_negative_value_when_transfer() {
        $parameters = [
            'payer' => 12,
            'payee' => 12,
            'value' => -12,
        ];
        $response = $this->json('POST', '/transfer', $parameters);

        $response
            ->assertStatus(400)
            ->assertJson([
                "error_message" => "The value field must be at least 0.01."
            ]);
    }

    /**
     * Test transfer from non-existent account
    */
    public function test_transfer_non_existent_account() {
        $parameters = [
            'payer' => 1,
            'payee' => 2,
            'value' => 12,
        ];
        $response = $this->json('POST', '/transfer', $parameters);

        $response
            ->assertStatus(404)
            ->assertJson([
                "message" => "Account not found!"
            ]);
    }

    /**
     * Test transfer with insufficient funds
     */
    public function test_transfer_insufficient_funds() {
        $payer = Client::factory()->create();
        $payee = Client::factory()->create();
        $accountClient = Account::factory()->create([
            'account_owner_id' => $payer->id,
            'balance' => 10.00,
            'account_type' => 'client',
        ]);
        $accountShop = Account::factory()->create([
            'account_owner_id' => $payee->id,
            'balance' => 10.00,
            'account_type' => 'shop',
        ]);

        $parameters = [
            'payer' => $accountClient->id,
            'payee' => $accountShop->id,
            'value' => 42.0,
        ];
        $response = $this->json('POST', '/transfer', $parameters);

        $response
            ->assertStatus(400)
            ->assertJsonStructure([
                "message",
            ])
            ->assertJson([
                'message' => 'insufficient funds!',
            ]);

        $this->assertDatabaseHas('accounts', [
            'id' => $accountClient->id,
            'balance' => 10.00,
            'account_type' => 'client',
        ]);
        $this->assertDatabaseHas('accounts', [
            'id' => $accountShop->id,
            'balance' => 10.00,
            'account_type' => 'shop',
        ]);
    }

    /**
     * Test transfer from a shopkeeper to a client 
     */
    public function test_transfer_from_shopkeeper_to_client() {
        $payer = Client::factory()->create();
        $payee = Client::factory()->create();
        $accountShop = Account::factory()->create([
            'account_owner_id' => $payer->id,
            'balance' => 100.00,
            'account_type' => 'shop',
        ]);
        $accountClient = Account::factory()->create([
            'account_owner_id' => $payee->id,
            'balance' => 100.00,
            'account_type' => 'client',
        ]);

        $parameters = [
            'payer' => $accountShop->id,
            'payee' => $accountClient->id,
            'value' => 42.0,
        ];
        $response = $this->json('POST', '/transfer', $parameters);

        $response
            ->assertStatus(400)
            ->assertJsonStructure([
                "message",
            ])
            ->assertJson([
                'message' => 'payer cannot be a shopkeeper',
            ]);

        $this->assertDatabaseHas('accounts', [
            'id' => $accountClient->id,
            'balance' => 100.00,
            'account_type' => 'client',
        ]);
        $this->assertDatabaseHas('accounts', [
            'id' => $accountShop->id,
            'balance' => 100.00,
            'account_type' => 'shop',
        ]);
    }

    /**
     * Test transfer successfully
     */
    public function test_transfer_between_client_and_shopkeeper_successfully() {
        $payer = Client::factory()->create();
        $payee = Client::factory()->create();
        $accountShop = Account::factory()->create([
            'account_owner_id' => $payer->id,
            'balance' => 100.00,
            'account_type' => 'shop',
        ]);
        $accountClient = Account::factory()->create([
            'account_owner_id' => $payee->id,
            'balance' => 100.00,
            'account_type' => 'client',
        ]);

        $parameters = [
            'payer' => $accountClient->id,
            'payee' => $accountShop->id,
            'value' => 42.0,
        ];
        $response = $this->json('POST', '/transfer', $parameters);

        $response
        ->assertStatus(200)
        ->assertJsonStructure([
            "message",
            "transfer" => [
                "payer",
                "payee",
                "amount",
            ],
        ])
        ->assertJson([
            'message' => 'successfully transfer!',
        ]);

        $this->assertDatabaseHas('accounts', [
            'id' => $accountClient->id,
            'balance' => 58.00,
            'account_type' => 'client',
        ]);
        $this->assertDatabaseHas('accounts', [
            'id' => $accountShop->id,
            'balance' => 142.00,
            'account_type' => 'shop',
        ]);
    }

    /**
     * Test transfer successfully
     */
    public function test_transfer_between_client_and_client_successfully() {
        $payer = Client::factory()->create();
        $payee = Client::factory()->create();
        $accountClient = Account::factory()->create([
            'account_owner_id' => $payee->id,
            'balance' => 100.00,
            'account_type' => 'client',
        ]);
        $accountCLient2 = Account::factory()->create([
            'account_owner_id' => $payer->id,
            'balance' => 100.00,
            'account_type' => 'client',
        ]);
        
        $parameters = [
            'payer' => $accountClient->id,
            'payee' => $accountCLient2->id,
            'value' => 42.0,
        ];
        $response = $this->json('POST', '/transfer', $parameters);

        $response
        ->assertStatus(200)
        ->assertJsonStructure([
            "message",
            "transfer" => [
                "payer",
                "payee",
                "amount",
            ],
        ])
        ->assertJson([
            'message' => 'successfully transfer!',
        ]);

        $this->assertDatabaseHas('accounts', [
            'id' => $accountClient->id,
            'balance' => 58.00,
            'account_type' => 'client',
        ]);
        $this->assertDatabaseHas('accounts', [
            'id' => $accountCLient2->id,
            'balance' => 142.00,
            'account_type' => 'client',
        ]);
    }
}