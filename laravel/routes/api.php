<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoriasController;
use App\Http\Controllers\PublicacionesController;
use App\Http\Controllers\PublicacionesCategoriaController;

Route::resource('categorias', CategoriasController::class);
Route::resource('publicaciones', PublicacionesController::class);
Route::resource('publicaciones-categorias', PublicacionesCategoriaController::class);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');