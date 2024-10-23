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
use OpenApi\Annotations as OA;
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
@include('swagger_info');
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

 
Route::controller(AuthController::class)->group(function () {
    /**
     * 
 * @OA\Post(
 *     path="/login",
 *     tags={"Auth"},
 *     summary="Connectez-vous",
 *     @OA\Response(
 *         response=200,
 *         description="Succès de la connexion"
 *     )
 * )
 */
    Route::post('login', 'login');
/**
 * @OA\Post(
 *     path="/register",
 *     tags={"Auth"},
 *     summary="Inscrivez-vous",
 *     @OA\Response(
 *         response=200,
 *         description="Succès de l'inscription"
 *     )
 * )
 */
    Route::post('register', 'register');
/**
 * @OA\Post(
 *     path="/logout",
 *     tags={"Auth"},
 *     summary="Déconnectez-vous",
 *     @OA\Response(
 *         response=200,
 *         description="Succès de la déconnexion"
 *     )
 * )
 */
    Route::post('logout', 'logout');
/**
 * @OA\Post(
 *     path="/refresh",
 *     tags={"Auth"},
 *     summary="Actualisez le jeton d'authentification",
 *     @OA\Response(
 *         response=200,
 *         description="Succès de l'actualisation du jeton"
 *     )
 * )
 */
    Route::post('refresh', 'refresh');


});

Route::middleware(['isAdmin', 'auth:api'])->group(function () {
    /**
 * @OA\Post(
 *     path="/addfield",
 *     tags={"Fields"},
 *     summary="Ajoutez un champ",
 *     @OA\Response(
 *         response=200,
 *         description="Succès de l'ajout du champ"
 *     )
 * )
 */
    Route::post('/addfield', [FieldController::class, 'Store']);
/**
 * @OA\Delete(
 *     path="/deletefield/{field}",
 *     tags={"Fields"},
 *     summary="Supprimez un champ",
 *     @OA\Response(
 *         response=200,
 *         description="Succès de la suppression du champ"
 *     )
 * )
 */
    Route::delete('/deletefield/{field}',[FieldController::class, 'destroy']);


/**
 * @OA\Post(
 *     path="/addforum",
 *     tags={"Forums"},
 *     summary="Ajoutez un forum",
 *     @OA\Response(
 *         response=200,
 *         description="Succès de l'ajout du forum"
 *     )
 * )
 */
    Route::post('/addforum', [ForumController::class, 'Store']);

/**
 * @OA\Delete(
 *     path="/deleteforum/{forum}",
 *     tags={"Forums"},
 *     summary="Supprimez un forum",
 *     @OA\Response(
 *         response=200,
 *         description="Succès de la suppression du forum"
 *     )
 * )
 */
    Route::delete('/deleteforum/{forum}',[ForumController::class, 'destroy']);


});

Route::middleware('auth:api')->group(function () {
/**
 * @OA\Get(
 *     path="/displayforum",
 *     tags={"Forums"},
 *     summary="Affichez un forum",
 *     @OA\Response(
 *         response=200,
 *         description="Succès de l'affichage du forum"
 *     )
 * )
 */
    Route::get('/displayforum', [ForumController::class, 'show']);


/**
 * @OA\Post(
 *     path="/addtopic",
 *     tags={"Topics"},
 *     summary="Ajoutez un sujet",
 *     @OA\Response(
 *         response=200,
 *         description="Succès de l'ajout du sujet"
 *     )
 * )
 */
    Route::post('/addtopic',[TopicController::class,'store']);
/**
 * @OA\Get(
 *     path="/displayspecifictopic/{topic}",
 *     tags={"Topics"},
 *     summary="Affichez un sujet spécifique",
 *     @OA\Response(
 *         response=200,
 *         description="Succès de l'affichage du sujet spécifique"
 *     )
 * )
 */
    Route::get('/displayspecifictopic/{topic}',[TopicController::class,'show']);
/**
 * @OA\Get(
 *     path="/displaytopic",
 *     tags={"Topics"},
 *     summary="Affichez les sujets",
 *     @OA\Response(
 *         response=200,
 *         description="Succès de l'affichage des sujets"
 *     )
 * )
 */
    Route::get('/displaytopic',[TopicController::class,'index']);
/**
 * @OA\Put(
 *     path="/updatespecifictopic/{topic}",
 *     tags={"Topics"},
 *     summary="Mettre à jour un sujet spécifique",
 *     @OA\Response(
 *         response=200,
 *         description="Succès de la mise à jour du sujet spécifique"
 *     )
 * )
 */
    Route::put('/updatespecifictopic/{topic}',[TopicController::class,'update']);
/**
 * @OA\Delete(
 *     path="/deletespecifictopic/{topic}",
 *     tags={"Topics"},
 *     summary="Supprimez un sujet spécifique",
 *     @OA\Response(
 *         response=200,
 *         description="Succès de la suppression du sujet spécifique"
 *     )
 * )
 */

    Route::delete('/deletespecifictopic/{topic}',[TopicController::class,'destroy']);

/**
 * @OA\Post(
 *     path="/sendmessage",
 *     tags={"Messages"},
 *     summary="Envoyez un message",
 *     @OA\Response(
 *         response=200,
 *         description="Succès de l'envoi du message"
 *     )
 * )
 */
    Route::post('/sendmessage',[MessageController::class,'store']);

/**
 * @OA\Get(
 *     path="/displaymessage",
 *     tags={"Messages"},
 *     summary="Affichez les messages",
 *     @OA\Response(
 *         response=200,
 *         description="Succès de l'affichage des messages"
 *     )
 * )
 */
    Route::get('/displaymessage',[MessageController::class,'index']);
/**
 * @OA\Delete(
 *     path="/deletespecificmessage/{message}",
 *     tags={"Messages"},
 *     summary="Supprimez un message spécifique",
 *     @OA\Response(
 *         response=200,
 *         description="Succès de la suppression du message spécifique"
 *     )
 * )
 */
    Route::delete('/deletespecificmessage/{message}',[MessageController::class,'destroy']);
/**
 *  @OA\Put(
 *     path="/updatespecificmessage/{message}",
 *     tags={"Messages"},
 *     summary="Mettre à jour un message spécifique",
 *     @OA\Response(
 *         response=200,
 *         description="Succès de la mise à jour du message spécifique"
 *     )
 * )
 *
 */
    Route::put('/updatespecificmessage/{message}',[MessageController::class,'update']);

/*
* @OA\Post(
 *     path="/sendreply",
 *     tags={"Replies"},
 *     summary="Envoyez une réponse",
 *     @OA\Response(
 *         response=200,
 *         description="Succès de l'envoi de la réponse"
 *     )
 * )
 */
    Route::post('/sendreply',[ReplyController::class,'store']);
/*
* @OA\Get(
 *     path="/displayreply",
 *     tags={"Replies"},
 *     summary="Affichez les réponses",
 *     @OA\Response(
 *         response=200,
 *         description="Succès de l'affichage des réponses"
 *     )
 * )
 *
*/
    Route::get('/displayreply',[ReplyController::class,'index']);
/*
*
 * @OA\Delete(
 *     path="/deletespecificreply/{reply}",
 *     tags={"Replies"},
 *     summary="Supprimez une réponse spécifique",
 *     @OA\Response(
 *         response=200,
 *         description="Succès de la suppression de la réponse spécifique"
 *     )
 * )
*/
    Route::delete('/deletespecificreply/{reply}',[ReplyController::class,'destroy']);
/*
*
* @OA\Put(
 *     path="/updatespecificreply/{reply}",
 *     tags={"Replies"},
 *     summary="Mettre à jour une réponse spécifique",
 *     @OA\Response(
 *         response=200,
 *         description="Succès de la mise à jour de la réponse spécifique"
 *     )
 * )
*/
    Route::put('/updatespecificreply/{reply}',[ReplyController::class,'update']);


    

    Route::post('/sendreply', [ReplyController::class, 'store']);
    Route::post('/displayreply', [ReplyController::class, 'index']);
    Route::post('/deletespecificreply/{reply}', [ReplyController::class, 'destroy']);
    Route::post('/updatespecificreply/{reply}', [ReplyController::class, 'update']);
    Route::get('/displayrepliesformessage/{message}', [ReplyController::class,'getRepliesByMessageId']);
});


/**
 * @OA\Get(
 *     path="/displayfield",
 *     tags={"Fields"},
 *     summary="Affichez les champs",
 *     @OA\Response(
 *         response=200,
 *         description="Succès de l'affichage des champs"
 *     )
 * )
 * 
 */
Route::get('/displayfield', [FieldController::class, 'index']);

