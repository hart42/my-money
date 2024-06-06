<?php

namespace App\Services;

use App\Models\Client;
use Exception;
use App\Services\AccountService;
use Illuminate\Database\DatabaseManager;

class ClientService {

    protected $dbm;
    protected $client;
    protected $accountService;

    public function __construct(DatabaseManager $dbm, Client $client, AccountService $accountService) {
        $this->dbm = $dbm;
        $this->client = $client;
        $this->accountService = $accountService;
    }

    public function createNewClient(array $data) {
        $query = $this->client::select('id')
            ->where('email', $data['email']);

        if(isset($data['cpf'])) {
            $query->orWhere('cpf', $data['cpf']);
        }
        if(isset($data['cnpj'])) {
            $query->orWhere('cnpj', $data['cnpj']);
        }
        $clientExist = $query->first();
        if(!is_null($clientExist)) {
            return response()->json([
                'message' => 'client could not be created, inconsistent data!'
            ], 401);
        }
        $data['password'] = bcrypt($data['password']);
        
        try {
            $this->dbm->beginTransaction();
            $newClient = $this->client::create($data);

            if(!isset($data['account_type'])) {
                $data['account_type'] = isset($data['cpf']) ? 'client' : 'shop';
            }
            $newAccount = $this->accountService->createFirstAccount($newClient->id, $data['account_type']);
            $this->dbm->commit();

            return response()->json(
                [
                    "message" => "Account created successfully!",
                    "account" => [
                        "account_id" => $newAccount->id,
                        "balance" => $newAccount->balance,
                        "account_type" => $newAccount->account_type,
                    ],
                    "client" => [
                        "client_id" => $newClient->id,
                        "full_name" => $newClient->full_name,
                        "email" => $newClient->email,
                    ],
                ], 201) ;
        } catch (Exception $ex) {
            $this->dbm->rollBack();
            return response()->json([
                'error_message' => $ex->getMessage()
            ], 400);
        }
    }
}