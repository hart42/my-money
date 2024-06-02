<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

$router = app('router');

$router->get('/create-client',              "App\Http\Controllers\ClientController@createNewClient");
