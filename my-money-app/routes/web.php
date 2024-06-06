<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/


$router = app('router');

$router->post('/create-client',              "ClientController@createNewClient");

$router->group(['middleware' => 'auth.transaction'], function () use ($router) {
    $router->post('/deposit',                   "TransactionController@deposit");
    $router->post('/withdraw',                   "TransactionController@withdraw");
    $router->post('/transfer',                   "TransactionController@transfer");
});


