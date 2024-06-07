<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ClientService;
use Exception;

class ClientController extends Controller
{
    protected $clientService;

    public function __construct(ClientService $clientService) {
        $this->clientService = $clientService;
    }

    public function createNewClient(Request $request) {
        try {
            $rules = [
                'full_name' => 'required|string|max:280',
                'cpf' => 'required_without:cnpj|string|digits:11|unique:clients,cpf|regex:/^[0-9]+$/',
                'cnpj' => 'required_without:cpf|string|digits:14|unique:clients,cnpj|regex:/^[0-9]+$/',
                'email' => 'required|email',
                'password' => 'required|string',
                'shop_name' => 'string|max:120',
                'account_type' => 'string|in:client,shop',
            ];
            $validatedData = $this->validate($request, $rules);
            
            return $this->clientService->createNewClient($validatedData);
        } catch (Exception $ex) {
            return response()->json([
                'error_message' => $ex->getMessage()
            ], 400);
        }
    }
}
