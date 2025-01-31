<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

use Illuminate\Support\Facades\Route;


Route::get('/test', function (){
    return 'test deu boa';
});
Route::post('/test2', function (){
    return 'test deu boa post';
});