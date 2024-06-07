<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ClientFeatureTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test missing parameters when create a client : cpf of cnpj
     */
    public function test_missing_cpf_cnpj_parameters_when_create_client() {
        $parameters = [
            'full_name' => 'test',
            'email' => 'testClientFeture@email.com',
            'password' => '@123test',
        ];
        $response = $this->json('POST', '/create-client', $parameters);
        $response
            ->assertStatus(400)
            ->assertJson([
                "error_message" => "The cpf field is required when cnpj is not present. (and 1 more error)"
            ]);
    }

    /**
     * Test missing parameters when create a client : email
     */
    public function test_missing_email_parameters_when_create_client() {
        $parameters = [
            'full_name' => 'test',
            'password' => '@123test',
            'cpf' => '98765432132',
        ];
        $response = $this->json('POST', '/create-client', $parameters);

        $response
            ->assertStatus(400)
            ->assertJson([
                "error_message" => "The email field is required."
            ]);
    }

    /**
     * Test missing parameters when create a client : full_name
     */
    public function test_missing_full_name_parameters_when_create_client() {
        $parameters = [
            'email' => 'testClientFeture@email.com',
            'password' => '@123test',
            'cpf' => '98765432132',
            'cnpj' => '99999999999999',
        ];
        $response = $this->json('POST', '/create-client', $parameters);

        $response
            ->assertStatus(400)
            ->assertJson([
                "error_message" => "The full name field is required."
            ]);
    }

    /**
     * Test missing parameters when create a client : password
     */
    public function test_missing_password_parameters_when_create_client() {
        $parameters = [
            'full_name' => 'test',
            'email' => 'testClientFeture@email.com',
            'cnpj' => '99999999999999',
        ];
        $response = $this->json('POST', '/create-client', $parameters);

        $response
            ->assertStatus(400)
            ->assertJson([
                "error_message" => "The password field is required."
            ]);
    }

    /**
     * Test wrong parameter value when create a client : account_type
     */
    public function test_wrong_parameter_value_when_create_client() {
        $parameters = [
            'full_name' => 'test',
            'email' => 'testClientFeture@email.com',
            'cnpj' => '99999999999999',
            "password" => '@123test',
            'account_type' => 'wrong',
        ];
        $response = $this->json('POST', '/create-client', $parameters);

        $response
            ->assertStatus(400)
            ->assertJson([
                "error_message" => "The selected account type is invalid."
            ]);
    }

    /**
     * Test create a client successfully without an account_type and became a client account
     */
    public function test_create_client_without_account_type_successfully() {
        $parameters = [
            'full_name' => 'test',
            'email' => 'testClientFeture@email.com',
            'password' => '@123test',
            'cpf' => '98765432132',
            'cnpj' => '99999999999999',
        ];
        $response = $this->json('POST', '/create-client', $parameters);

        $response
            ->assertStatus(201)
            ->assertJsonStructure([
                "message",
                "account" => [
                    "account_id",
                    "balance",
                    "account_type",
                ],
                "client" => [
                    "client_id",
                    "full_name",
                    "email"
                ]
            ])
            ->assertJson([
                'message' => 'Account created successfully!',
            ]);

        $this->assertDatabaseHas('accounts', [
            'id' => $response->json('account.account_id'),
            'balance' => 0.00,
            'account_type' => 'client',
        ]);

        $this->assertDatabaseHas('clients', [
            'id' => $response->json('client.client_id'),
            'email' => $response->json('client.email'),
        ]);
    }

    /**
     * Test create a client successfully without an account_type and became a shop account
     */
    public function test_create_client_without_account_type_became_shop_account_successfully() {
        $parameters = [
            'full_name' => 'test',
            'email' => 'testClientFeture@email.com',
            'password' => '@123test',
            'cnpj' => '99999999999999',
        ];
        $response = $this->json('POST', '/create-client', $parameters);

        $response
            ->assertStatus(201)
            ->assertJsonStructure([
                "message",
                "account" => [
                    "account_id",
                    "balance",
                    "account_type",
                ],
                "client" => [
                    "client_id",
                    "full_name",
                    "email"
                ]
            ])
            ->assertJson([
                'message' => 'Account created successfully!',
            ]);

        $this->assertDatabaseHas('accounts', [
            'id' => $response->json('account.account_id'),
            'balance' => 0.00,
            'account_type' => 'shop',
        ]);

        $this->assertDatabaseHas('clients', [
            'id' => $response->json('client.client_id'),
            'email' => $response->json('client.email'),
        ]);
    }

    /**
     * Test create a client successfully with account_type client
     */
    public function test_create_client_with_account_type_client_successfully() {
        $parameters = [
            'full_name' => 'test',
            'email' => 'testClientFeture@email.com',
            'password' => '@123test',
            'cpf' => '98765432132',
            'account_type' => 'client',
        ];
        $response = $this->json('POST', '/create-client', $parameters);

        $response
            ->assertStatus(201)
            ->assertJsonStructure([
                "message",
                "account" => [
                    "account_id",
                    "balance",
                    "account_type",
                ],
                "client" => [
                    "client_id",
                    "full_name",
                    "email"
                ]
            ])
            ->assertJson([
                'message' => 'Account created successfully!',
            ]);

        $this->assertDatabaseHas('accounts', [
            'id' => $response->json('account.account_id'),
            'balance' => 0.00,
            'account_type' => 'client',
        ]);

        $this->assertDatabaseHas('clients', [
            'id' => $response->json('client.client_id'),
            'email' => $response->json('client.email'),
        ]);
    }

    /**
     * Test create a client successfully with account_type shop
     */
    public function test_create_client_with_account_type_shop_successfully() {
        $parameters = [
            'full_name' => 'test',
            'email' => 'testClientFeture@email.com',
            'password' => '@123test',
            'cnpj' => '99999999999999',
            'account_type' => 'shop',
        ];
        $response = $this->json('POST', '/create-client', $parameters);

        $response
            ->assertStatus(201)
            ->assertJsonStructure([
                "message",
                "account" => [
                    "account_id",
                    "balance",
                    "account_type",
                ],
                "client" => [
                    "client_id",
                    "full_name",
                    "email"
                ]
            ])
            ->assertJson([
                'message' => 'Account created successfully!',
            ]);

        $this->assertDatabaseHas('accounts', [
            'id' => $response->json('account.account_id'),
            'balance' => 0.00,
            'account_type' => 'shop',
        ]);

        $this->assertDatabaseHas('clients', [
            'id' => $response->json('client.client_id'),
            'email' => $response->json('client.email'),
        ]);
    }
}
