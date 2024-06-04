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
            'email' => 'test@email.com',
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
            'cpf' => '12345678911',
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
            'email' => 'test@email.com',
            'password' => '@123test',
            'cpf' => '12345678911',
            'cnpj' => '12345678911234',
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
            'email' => 'test@email.com',
            'cnpj' => '12345678911234',
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
            'email' => 'test@email.com',
            'cnpj' => '12345678911234',
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
     * Test create a client successfully 
     */
    public function test_create_client_without_account_type_successfully() {
        $parameters = [
            'full_name' => 'test',
            'email' => 'test@email.com',
            'password' => '@123test',
            'cpf' => '12345678911',
            'cnpj' => '12345678911234',
        ];
        $response = $this->json('POST', '/create-client', $parameters);

        dd($response->json());
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
                    "full_name",
                    "email"
                ]
            ]);
        $responseData = $response->json();
        $this->assertContainsEquals("Account created successfully!", $responseData["message"]);
    }
}
