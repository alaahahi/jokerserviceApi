<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Orion\Facades\Orion;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\CardTypeController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\UsersController;
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Orion::resource('category', CategoryController::class);
Orion::resource('card', CardController::class);
Orion::resource('cardType', CardTypeController::class);
Orion::morphToManyResource('posts', 'comments', UsersController::class);
//mobile
Route::get('categories/{categoryId?}/sub_categories/{lang?}', [CustomerController::class, 'categories_sub_categories']);
Route::get('sub_categories_employee/{subCategoriesId?}/{lang?}', [CustomerController::class, 'sub_categories_employee']);
Route::get('categories/{lang?}', [CustomerController::class, 'categories']);
Route::post('add_employee_info/{moblie?}', [CustomerController::class, 'add_employee_info']);
Route::get('employee_info/{moblie?}/{lang?}', [CustomerController::class, 'employee_info']);
Route::post('add_client_info/{moblie?}', [CustomerController::class, 'add_client_info']);
Route::post('add_order/{clientId?}/{sub_categories_id?}/{employeeId?}', [CustomerController::class, 'add_order']);
Route::get('get_order_client/{clientId?}/{lang?}', [CustomerController::class, 'get_order_client']);
Route::get('get_order_employee/{employeeId?}/{status?}/{lang?}', [CustomerController::class, 'get_order_employee']);
Route::post('employee_order_accept/{orderId?}', [CustomerController::class, 'employee_order_accept']);
Route::post('client_order_finish/{orderId?}', [CustomerController::class, 'client_order_finish']);
Route::post('employee_order_reject/{orderId?}', [CustomerController::class, 'employee_order_reject']);
Route::delete('client_order_remove/{orderId?}', [CustomerController::class, 'client_order_remove']);
Route::get('search/{q?}/{lang?}', [CustomerController::class, 'search']);
Route::get('app_page/{title?}/{lang?}', [CustomerController::class, 'app_page']);
Route::post('update_location/{moblie?}', [CustomerController::class, 'update_location']);
Route::post('update_token/{moblie?}', [CustomerController::class, 'update_token']);
Route::get('notification/{moblie?}', [CustomerController::class, 'notification']);
Route::get('slider/', [CustomerController::class, 'slider']);
Route::post('categories_sub_categories_array/{lang?}', [CustomerController::class, 'categories_sub_categories_array']);
Route::get('employee_is_block/{moblie?}', [CustomerController::class, 'employee_is_block']);