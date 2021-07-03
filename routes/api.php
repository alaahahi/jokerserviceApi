<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Orion\Facades\Orion;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\CardTypeController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\UsersController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Orion::resource('category', CategoryController::class);
Orion::resource('card', CardController::class);
Orion::resource('cardType', CardTypeController::class);
Orion::morphToManyResource('posts', 'comments', UsersController::class);
//mobile


Route::get('categories/{categoryId?}/sub_categories/{lang?}', [CustomerController::class, 'categories_sub_categories']);
Route::get('sub_categories_employee/{subCategoriesId?}', [CustomerController::class, 'sub_categories_employee']);
Route::get('categories/{lang?}', [CustomerController::class, 'categories']);
Route::post('add_employee_info/{moblie?}', [CustomerController::class, 'add_employee_info']);
Route::get('employee_info/{moblie?}', [CustomerController::class, 'employee_info']);
Route::post('add_client_info/{moblie?}', [CustomerController::class, 'add_client_info']);
Route::post('add_order/{moblie_client?}/{sub_categories_id?}/{moblie_employee?}', [CustomerController::class, 'add_order']);
