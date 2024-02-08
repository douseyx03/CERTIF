<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\FieldController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ReplyController;
use App\Http\Controllers\TopicController;
use App\Models\Reply;

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

    Route::post('profile', 'profile');
    Route::post('updateprofile', 'updateProfile');
});

Route::middleware(['isAdmin', 'auth:api'])->group(function () {

    Route::post('/addfield', [FieldController::class, 'Store']);
    Route::post('/updatespecificfield/{field}', [FieldController::class, 'update']);
    Route::post('/deletefield/{field}', [FieldController::class, 'destroy']);

    Route::post('/addforum', [ForumController::class, 'Store']);
    Route::post('/deleteforum/{forum}', [ForumController::class, 'destroy']);
});

Route::middleware('auth:api')->group(function () {

    Route::post('/displayforum', [ForumController::class, 'show']);

    Route::post('/addtopic', [TopicController::class, 'store']);
    Route::post('/displayspecifictopic/{topic}', [TopicController::class, 'show']);
    Route::post('/displaytopic', [TopicController::class, 'index']);
    Route::post('/updatespecifictopic/{topic}', [TopicController::class, 'update']);
    Route::post('/deletespecifictopic/{topic}', [TopicController::class, 'destroy']);

    Route::post('/sendmessage', [MessageController::class, 'store']);
    Route::post('/displaymessage', [MessageController::class, 'index']);
    Route::post('/deletespecificmessage/{message}', [MessageController::class, 'destroy']);
    Route::post('/updatespecificmessage/{message}', [MessageController::class, 'update']);

    Route::post('/sendreply', [ReplyController::class, 'store']);
    Route::post('/displayreply', [ReplyController::class, 'index']);
    Route::post('/deletespecificreply/{reply}', [ReplyController::class, 'destroy']);
    Route::post('/updatespecificreply/{reply}', [ReplyController::class, 'update']);
});

Route::post('/displayfield', [FieldController::class, 'index']);
