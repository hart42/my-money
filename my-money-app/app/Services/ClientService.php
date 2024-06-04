<?php

namespace App\Services;

use App\Models\Client;
use Exception;
use Illuminate\Support\Facades\DB;

class ClientService {
    
    public function createNewClient(array $data) {
        $query = Client::select('id')
            ->where('email', $data['email']);

        if(isset($data['cpf'])) {
            $query->orWhere('cpf', $data['cpf']);
        }
        if(isset($data['cnpj'])) {
            $query->orWhere('cnpj', $data['cnpj']);
        }
        $client_exist = $query->first();
        if(!is_null($client_exist)) {
            return response()->json([
                'message' => 'client could not be created, inconsistent data!'
            ], 401);
        }
        $data['password'] = bcrypt($data['password']);
        
        try {
            DB::beginTransaction();
            $newClient = Client::create($data);
            // validate account_type
            if(!isset($data['account_type'])) {
                $data['account_type'] = isset($data['cpf']) ? 'client' : 'shop';
            }
            $newAccount = (new AccountService)->createFirstAccount($newClient->id, $data['account_type']);
            DB::commit();

            return [
                "client_id" => $newClient->id,
                "account_id" => $newAccount->id,
                "balance" => $newAccount->balance,
                "account_type" => $newAccount->account_type,
            ];
        } catch (Exception $ex) {
            DB::rollBack();
            return response()->json([
                'error_message' => $ex->getMessage()
            ], 400);
        }
    }
}