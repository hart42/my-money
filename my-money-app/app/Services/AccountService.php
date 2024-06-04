<?php

namespace App\Services;

use App\Models\Account;
use Exception;
use Illuminate\Support\Facades\DB;

class AccountService {
    
    public function createFirstAccount(int $newClientId, string $accountType) {
        try {
            DB::beginTransaction();
            $accountData = [
                "account_owner_id" => $newClientId,
                "account_type" => $accountType,
            ];
            $newAccount = Account::create($accountData);
            DB::commit();

            return $newAccount;
        } catch (Exception $ex) {
            DB::rollBack();
            return response()->json([
                'error_message' => $ex->getMessage()
            ], 400);
        }
    }
}