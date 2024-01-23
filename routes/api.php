<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\FieldController;
use App\Http\Controllers\ForumController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
});

Route::middleware(['isAdmin', 'auth:api'])->group(function () {
    
    Route::post('/addfield', [FieldController::class, 'Store']);
    Route::post('/deletefield/{field}',[FieldController::class, 'destroy']);

    Route::post('/addforum', [ForumController::class, 'Store']);
    Route::post('/deleteforum/{forum}',[ForumController::class, 'destroy']);

});

Route::middleware('auth:api')->group(function () {
    Route::get('/user/dashboard',function(){
        return 't frr';
    });
    Route::post('/displayforum', [ForumController::class, 'show']);
    
});

Route::post('/displayfield', [FieldController::class, 'index']);
