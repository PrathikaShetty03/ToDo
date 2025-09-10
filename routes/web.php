<?php

use App\Http\Controllers\TodoController;
use App\Models\Todo;
use Illuminate\Support\Facades\Route;

Route::get('/', [TodoController::class, 'index']);
//Route::get('todos/{todo}/edit','TodoController@edit');

