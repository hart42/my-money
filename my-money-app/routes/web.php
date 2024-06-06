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
$router->patch('/deposit',                   "App\Http\Controllers\TransactionController@deposit");


// Route::post('/create-client', [ClientController::class, 'createNewClient']);
// Route::patch('/deposit', [TransactionController::class, 'deposit']); 
