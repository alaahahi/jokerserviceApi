<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\UsersController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => 'admin'], function () {
    Voyager ::routes();

});

Route::get('employees_accept', [UsersController::class, 'employees_accept'])->name('employees_accept');
Route::get('employees_payment', [UsersController::class, 'employees_payment'])->name('employees_payment');
Route::get('approval_employee/{employee_id?}', [UsersController::class, 'approval_employee'])->name('approval_employee');
Route::get('block_employee/{employee_id?}', [UsersController::class, 'block_employee'])->name('block_employee');
Route::get('un_block_employee/{employee_id?}', [UsersController::class, 'un_block_employee'])->name('un_block_employee');

Route::get('admin/my_company', [CustomerController::class, 'my_company']);
Route::get('admin/my_orders', [CustomerController::class, 'my_orders']);
Route::get('admin/my_products', [CustomerController::class, 'my_products'])->name('admin.my_products');;

Route::get('admin/rejection/{id?}', [CustomerController::class, 'rejection'])->name('admin.rejection');
Route::get('admin/edit_product/{id?}', [CustomerController::class, 'edit_product'])->name('admin.edit_product');
Route::put('admin/edit_products/{id?}', [CustomerController::class, 'edit_products'])->name('admin.edit_products');
Route::delete('admin/remove_products/{id?}', [CustomerController::class, 'remove_products'])->name('admin.remove_products');