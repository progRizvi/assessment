<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
 */

Route::get('/', function () {
    return view('welcome');
});

// POST /users: Create a new user
Route::post('/users', [UserController::class, 'store']);

// POST /login: Login a user
Route::post('/login', [UserController::class, 'login'])->name("do.login");

// protected middleware
Route::group(['middleware' => ['auth']], function () {

// GET /transactions: Show all the transactions
    Route::get('/transactions', [TransactionController::class, 'getAllTransactions']);

// GET /deposit: Show all the transactions
    Route::get('/deposit', [TransactionController::class, 'getAllDeposits']);

// POST /deposit:
    Route::post('/deposit', [TransactionController::class, 'deposit']);

// GET /withdrawal: Show all the transactions

    Route::get('/withdrawal', [TransactionController::class, 'getAllWithdrawn']);

// POST /withdrawal:
    Route::post('/withdrawal', [TransactionController::class, 'withdrawal']);
});
