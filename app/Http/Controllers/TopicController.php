<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTopicRequest;
use App\Http\Requests\UpdateTopicRequest;
use App\Models\Forum;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use OpenApi\Annotations as OA;
// @include('swagger_info');
class TopicController extends Controller
{
    const ATTRIBUT = 'required|integer';
    const USER_AUTHENTICATED_MESSAGE = 'User authenticated:';


/**
 * Affiche une liste des sujets pour chaque forum.
 *
 * @OA\Get(
 *     path="/api/displaytopic",
 *     tags={"Topics"},
 *     summary="Affiche une liste des sujets pour chaque forum",
 *     @OA\Response(
 *         response=200,
 *         description="Opération réussie",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\AdditionalProperties(
 *                 type="array",
 *                 items=@OA\Items(
 *                     type="object",
 *                     @OA\Property(property="id", type="integer"),
 *                     @OA\Property(property="content", type="string"),
 *                     @OA\Property(property="message_received", type="integer"),
 *                     @OA\Property(property="user_id", type="integer"),
 *                     @OA\Property(property="created_at", type="string", format="date-time")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Non authentifié",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Unauthenticated.")
 *         )
 *     )
 * )
 *
 * @return JsonResponse
 */
    public function index()
    {
        $user = JWTAuth::parseToken()->authenticate();
        
        Log::info(self::USER_AUTHENTICATED_MESSAGE . ' ' . $user->id);
        
        $forums = Forum::all();
       $forumSpecificTopics = [];
       foreach ($forums as $forum) {
           $forumId = $forum->id;
           $forumTopics = Topic::where('forum_id', $forumId)
               ->orderBy('created_at', 'desc')
               ->get(['id','content', 'message_received', 'user_id', 'created_at']);
           $forumSpecificTopics[$forumId] = $forumTopics;
       }
       
       return response()->json($forumSpecificTopics);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

   /**
 * Crée un nouveau sujet dans un forum.
 * 
 * @OA\Post(
 *     path="/api/addtopic",
 *     tags={"Topics"},
 *     summary="Crée un nouveau sujet dans un forum",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"content", "message_received", "forum_id"},
 *             @OA\Property(property="content", type="string", description="Contenu du sujet"),
 *             @OA\Property(property="message_received", type="integer", description="Message reçu"),
 *             @OA\Property(property="forum_id", type="integer", description="ID du forum")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Sujet créé avec succès",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Votre sujet a bien été créé.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Requête incorrecte",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Vous avez déjà créé un sujet avec le même contenu dans ce forum.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Non authentifié",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Unauthenticated.")
 *         )
 *     )
 * )
 *
 * @param StoreTopicRequest $request
 * @return JsonResponse
 */
    public function store(StoreTopicRequest $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        
        $request->validate([
            'content' => self::ATTRIBUT,
            'message_received' => 'integer',
            'forum_id' => self::ATTRIBUT,
        ]);
        
        Log::info(self::USER_AUTHENTICATED_MESSAGE . $user->id);
        
        $existingTopic = Topic::where('content', $request->input('content'))
                                ->where('user_id', $user->id)
                                ->where('forum_id', $request->input('forum_id'))
                                ->first();
        
        if ($existingTopic) {
            return response()->json(['message' => 'Vous avez déjà créé un
            sujet avec le même contenu dans ce forum.'], 400);
        }
        
        $topic = new Topic();
        $topic->content = $request->input('content');
        $topic->message_received = $request->input('message_received');
        $topic->user_id = $user->id;
        $topic->forum_id = $request->input('forum_id');
        
        Log::info('Topic object created with content: ' . $topic->content);
        
        $topic->save();
        
        Log::info('Topic saved');
        
        Log::info('Response: Topic created with content: ' . $topic->content);
        
        return response()->json(['message' => 'Votre sujet a bien été créé.'], 201);
    }

   /**
 * Affiche le sujet spécifié.
 * 
 * @OA\Get(
 *     path="/api/displayspecifictopic/{topic}",
 *     tags={"Topics"},
 *     summary="Affiche le sujet spécifié",
 *     @OA\Parameter(
 *         name="topic",
 *         in="path",
 *         description="ID du sujet",
 *         required=true,
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Opération réussie",
 *         @OA\JsonContent(ref="#/components/schemas/Topic")
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Non authentifié",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Unauthenticated.")
 *         )
 *     )
 * )
 *
 * @param Topic $topic
 * @return JsonResponse
 */
    public function show(Topic $topic)
    {
        $user = JWTAuth::parseToken()->authenticate();
        
        Log::info(self::USER_AUTHENTICATED_MESSAGE . $user->id);
        
        return response()->json($topic);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Topic $topic)
    {
        //
    }

  /**
 * Met à jour le sujet spécifié.
 * 
 * @OA\Put(
 *     path="/api/updatespecifictopic/{topic}",
 *     tags={"Topics"},
 *     summary="Met à jour le sujet spécifié",
 *     @OA\Parameter(
 *         name="topic",
 *         in="path",
 *         description="ID du sujet",
 *         required=true,
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/UpdateTopicRequest")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Opération réussie",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Modifications effectuées avec succès."),
 *             @OA\Property(property="topic", ref="#/components/schemas/Topic")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Non authentifié",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Unauthorized")
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Accès interdit",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Vous ne pouvez pas modifier un sujet avec un message reçu.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erreur interne",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Erreur interne. Veuillez réessayer."),
 *             @OA\Property(property="error", type="string", example="Message d'erreur spécifique")
 *         )
 *     )
 * )
 *
 * @param UpdateTopicRequest $request
 * @param Topic $topic
 * @return JsonResponse
 */
    public function update(UpdateTopicRequest $request, Topic $topic)
    {
       $user = JWTAuth::parseToken()->authenticate();
       
       if (!$user) {
           return response()->json(['message' => 'Unauthorized'], 401);
       }
       
       Log::info('User authenticated: ' . $user->id);
       
       if ($user->id !== $topic->user_id) {
           return response()->json(['message' => 'Ce sujet ne vous appartient pas.'], 403);
       }
       
       if ($topic->message_received == 0) {
           try {
                $request->validate([
                    'content' => 'required|string',
                    'message_received' => 'integer',
                    'user_id' => self::ATTRIBUT,
                    'forum_id' => self::ATTRIBUT,
                ], [
                    'content.required' => 'Le contenu est requis.',
                    'content.string' => 'Le contenu doit être une chaîne de caractères.',
                    'message_received.integer' => 'Le message reçu doit être un entier.',
                    'user_id.required' => "L'ID de l'utilisateur est requis.",
                    'user_id.integer' => "L'ID de l'utilisateur doit être un entier.",
                    'forum_id.required' => "L'ID du forum est requis.",
                    'forum_id.integer' => "L'ID du forum doit être un entier.",
                ]);
                $topic->update($request->all());
               return response()->json(['message' => 'Modifications effectuées avec succès.', $topic],201);
           } catch (\Exception $e) {
               return response()->json(['message' => 'Erreur interne.
                Veuillez réessayer.', 'error' => $e->getMessage()], 500);
           }
       } else {
           return response()->json(['message' => 'Vous ne pouvez pas modifier un sujet avec un message reçu.'], 403);
       }
    }

    /**
 * Supprime le sujet spécifié.
 * 
 * @OA\Delete(
 *     path="/api/deletespecifictopic/{topic}",
 *     tags={"Topics"},
 *     summary="Supprime le sujet spécifié",
 *     @OA\Parameter(
 *         name="topic",
 *         in="path",
 *         description="ID du sujet",
 *         required=true,
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Opération réussie",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Le sujet a été supprimé avec succès par l'administrateur."),
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Non authentifié",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="User not found.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Accès interdit",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Vous n'êtes pas autorisé à supprimer ce sujet.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erreur interne",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Une erreur est survenue lors de l'authentification.")
 *         )
 *     )
 * )
 *
 * @param Topic $topic
 * @return JsonResponse
 */
    public function destroy(Topic $topic)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
    
            if (!$user) {
                return response()->json(['message' => 'User not found.'], 404);
            }
    
            Log::info('User authenticated: ' . $user->id);
    
            if ($user->is_admin) {
                $topic->delete();
                return response()->json(['message' => 'Le sujet a été supprimé avec succès par l\'administrateur.']);
            } elseif ($user->id === $topic->user_id) {
                $topic->delete();
                return response()->json(['message' => 'Votre sujet a été supprimé avec succès.']);
            } else {
                return response()->json(['message' => 'Vous n\'êtes pas autorisé à supprimer ce sujet.'], 403);
            }
        } catch (\PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException $e) {
            Log::error('JWT Exception: ' . $e->getMessage());
            return response()->json(['message' => 'Une erreur est survenue lors de l\'authentification.'], 500);
        } catch (\Exception $e) {
            Log::error('Exception: ' . $e->getMessage());
            return response()->json(['message' => 'Une erreur est survenue.'], 500);
        }
    }
}
