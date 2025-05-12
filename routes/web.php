<?php

use App\Http\Controllers\KuantazController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/benefits', [KuantazController::class, 'benefits']);