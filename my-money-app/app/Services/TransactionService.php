<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class TransactionService {

    public function deposit(array $data) {
        $account = DB::transaction(function () use ($data) {
            $account = Account::lockForUpdate()->find($data['payee']);
            if(!$account) {
                return null;
            }

            $account->balance += $data['value'];
            $account->save();

            Transaction::create([
                'account_id' => $account->id,
                'transaction_type' => 'deposit',
                'amount' => $data['value'],
            ]);
            return $account;
        });

        if(is_null($account)) {
            return response()->json([
                'message' => 'Account not found!',
            ], 404);
        }

        return response()->json([
            'message' => 'successfully deposited!',
            'account' => [
                'account_id' => $account->id,
                'balance' => $account->balance,
                'account_type' => $account->account_type,
            ],
        ], 200);
    }
}