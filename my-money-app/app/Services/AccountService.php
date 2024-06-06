<?php

namespace App\Services;

use App\Models\Account;
use Exception;
use Illuminate\Database\DatabaseManager;

class AccountService {

    protected $dbm;
    protected $account;

    public function __construct(DatabaseManager $dbm, Account $account) {
        $this->dbm = $dbm;
        $this->account = $account;
    }

    public function createFirstAccount(int $newClientId, string $accountType) {
        try {
            $this->dbm->beginTransaction();
            $accountData = [
                "account_owner_id" => $newClientId,
                "account_type" => $accountType,
            ];
            $newAccount = $this->account::create($accountData);
            $newAccount->refresh();
            $this->dbm->commit();

            return $newAccount;
        } catch (Exception $ex) {
            $this->dbm->rollBack();
            return response()->json([
                'error_message' => $ex->getMessage()
            ], 400);
        }
    }
}