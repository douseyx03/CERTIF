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

    Route::get('profile', 'profile');
    Route::put('updateprofile', 'updateProfile')->middleware('blocked');
});

Route::middleware(['isAdmin', 'auth:api'])->group(function () {
    Route::get('/getAllUsers',[AuthController::class, 'getAllUsers']);

    Route::post('/addfield', [FieldController::class, 'Store']);
    Route::put('/updatespecificfield/{field}', [FieldController::class, 'update']);
    Route::delete('/deletefield/{field}',[FieldController::class, 'destroy']);

    Route::post('/addforum', [ForumController::class, 'Store']);
    Route::put('/updatespecificforum/{forum}', [ForumController::class, 'update']);
    Route::delete('/deleteforum/{forum}',[ForumController::class, 'destroy']);

    Route::patch('/blockuser/{user}', [AuthController::class,'blockUser']);
    Route::patch('/unblockuser/{user}', [AuthController::class,'unBlockUser']);
    Route::delete('/deleteuser/{user}', [AuthController::class,'deleteUser']);
    Route::get('/getblockedusers', [AuthController::class,'listOfBlockedUsers']);

});

Route::middleware(['auth:api','blocked'])->group(function () {

    Route::get('/displayforum', [ForumController::class, 'show']);

    Route::post('/addtopic',[TopicController::class,'store']);
    Route::get('/displayspecifictopic/{topic}',[TopicController::class,'show']);
    Route::get('/displaytopic',[TopicController::class,'index']);
    Route::put('/updatespecifictopic/{topic}',[TopicController::class,'update']);
    Route::delete('/deletespecifictopic/{topic}',[TopicController::class,'destroy']);

    Route::post('/sendmessage',[MessageController::class,'store']);
    Route::get('/displaymessage',[MessageController::class,'index']);
    Route::delete('/deletespecificmessage/{message}',[MessageController::class,'destroy']);
    Route::put('/updatespecificmessage/{message}',[MessageController::class,'update']);

    Route::post('/sendreply', [ReplyController::class, 'store']);
    Route::get('/displayreply', [ReplyController::class, 'index']);
    Route::delete('/deletespecificreply/{reply}', [ReplyController::class, 'destroy']);
    Route::put('/updatespecificreply/{reply}', [ReplyController::class, 'update']);
    Route::get('/displayrepliesformessage/{message}', [ReplyController::class,'getRepliesByMessageId']);
    
    Route::get('/getUsersInfos', [AuthController::class,'getUsersInfos']);
});

Route::get('/displayfield', [FieldController::class, 'index']);
