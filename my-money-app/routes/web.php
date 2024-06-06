<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\TransactionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/


$router = app('router');
$router->post('/create-client',              "App\Http\Controllers\ClientController@createNewClient");
$router->post('/deposit',                   "App\Http\Controllers\TransactionController@deposit");
$router->post('/withdraw',                   "App\Http\Controllers\TransactionController@withdraw");
$router->post('/transfer',                   "App\Http\Controllers\TransactionController@transfer");


// Route::post('/create-client', [ClientController::class, 'createNewClient']);
// Route::patch('/deposit', [TransactionController::class, 'deposit']); 
