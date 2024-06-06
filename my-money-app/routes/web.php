<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/


// Route::post('/create-client', [ClientController::class, 'createNewClient']);
$router = app('router');
$router->post('/create-client',              "App\Http\Controllers\ClientController@createNewClient");
// $router->get('/create-client',              "App\Http\Controllers\ClientController@createNewClient");

// Route::post('/test-route', function() {
//     return response()->json(['message' => 'Rota de teste funcionando']);
// });
// Route::get('/test-route', function() {
//     return response()->json(['message' => 'Rota de teste funcionando']);
// });