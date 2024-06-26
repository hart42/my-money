<?php

namespace App\Http\Controllers;

use App\Services\TransactionService;
use Exception;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function deposit(Request $request) {
        try {
            $rules = [
                'payee' => 'int|required',
                'value' => 'numeric|min:0.01|required',
            ];
            $validatedData = $this->validate($request, $rules);

            return $this->transactionService->deposit($validatedData);
        } catch (Exception $ex) {
            return response()->json([
                'error_message' => $ex->getMessage()
            ], 400);
        }
    }

    public function withdraw(Request $request) {
        try {
            $rules = [
                'payer' => 'int|required',
                'value' => 'numeric|min:0.01|required',
            ];
            $validatedData = $this->validate($request, $rules);

            return $this->transactionService->withdraw($validatedData);
        } catch (Exception $ex) {
            return response()->json([
                'error_message' => $ex->getMessage()
            ], 400);
        }
    }

    public function transfer(Request $request) {
        try {
            $rules = [
                'value' => 'numeric|min:0.01|required',
                'payer' => 'int|required',
                'payee' => 'int|required',
            ];
            $validatedData = $this->validate($request, $rules);

            return $this->transactionService->transfer($validatedData);
        } catch (Exception $ex) {
            return response()->json([
                'error_message' => $ex->getMessage()
            ], 400);
        }
    }
}
