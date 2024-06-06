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

    public function withdraw(array $data) {
        $account = DB::transaction(function () use ($data) {
            $account = Account::lockForUpdate()->find($data['payer']);
            if(!$account) {
                return null;
            }

            if(($account->balance - $data['value']) <= 0.00) {
                return ['message' => 'insufficient funds!'];
            }
            $account->balance -= $data['value'];
            $account->save();

            Transaction::create([
                'account_id' => $account->id,
                'transaction_type' => 'withdraw',
                'amount' => $data['value'],
            ]);
            return $account;
        });

        if(is_null($account)) {
            return response()->json([
                'message' => 'Account not found!',
            ], 404);
        }

        if(isset($account['message'])) {
            return response()->json([
                'message' => $account['message'],
            ], 400);
        }

        return response()->json([
            'message' => 'successfully withdraw!',
            'account' => [
                'account_id' => $account->id,
                'balance' => $account->balance,
                'account_type' => $account->account_type,
            ],
        ], 200);
    }
}