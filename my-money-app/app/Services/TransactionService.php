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

    public function transfer(array $data) {
        $transaction = DB::transaction(function () use ($data) {
            $payer = Account::lockForUpdate()->find($data['payer']);
            $payee = Account::lockForUpdate()->find($data['payee']);
            if(!$payer || !$payee) {
                return null;
            }

            if($payer->account_type === 'shop') {
                return ['message' => 'payer cannot be a shopkeeper'];
            }
            if(($payer->balance - $data['value']) <= 0.00) {
                return ['message' => 'insufficient funds!'];
            }

            $payer->balance -= $data['value'];
            $payer->save();
            $payee->balance += $data['value'];
            $payee->save();

            $transaction = Transaction::create([
                'account_id' => $payer->id,
                'transaction_type' => 'transfer',
                'amount' => $data['value'],
                'to_account_id' => $payee->id,
            ]);
            return $transaction;
        });

        if(is_null($transaction)) {
            return response()->json([
                'message' => 'Account not found!',
            ], 404);
        }

        if(isset($transaction['message'])) {
            return response()->json([
                'message' => $transaction['message'],
            ], 400);
        }

        return response()->json([
            'message' => 'successfully transfer!',
            'transfer' => [
                'payer' => $transaction->account_id,
                'payee' => $transaction->to_account_id,
                'amount' => $transaction->amount,
            ],
        ], 200);
    }
}