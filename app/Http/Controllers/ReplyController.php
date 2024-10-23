<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReplyRequest;
use App\Http\Requests\UpdateReplyRequest;
use App\Models\Message;
use App\Models\Reply;
use Illuminate\Support\Facades\Log;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use OpenApi\Annotations as OA;
// @include('swagger_info');
class ReplyController extends Controller
{
    
    const ATTRIBUT = 'required|integer';
    const USER_AUTHENTICATED_MESSAGE = 'User authenticated:';

    /**
 * Récupère tous les messages et leurs réponses.
 *
 * @OA\Get(
 *     path="/api/displayreply",
 *     tags={"Replies"},
 *     summary="Récupère tous les messages et leurs réponses",
 *     @OA\Response(
 *         response=200,
 *         description="Liste de messages et leurs réponses",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\AdditionalProperties(
 *                 type="array",
 *                 @OA\Items(ref="#/components/schemas/Reply")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erreur interne",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="error", type="string", example="Une erreur inattendue est survenue")
 *         )
 *     )
 * )
 *
 * @return JsonResponse
 */
    public function index()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            Log::info(self::USER_AUTHENTICATED_MESSAGE . ' ' . $user->id);
            
            $messages = Message::all();
            $MessagesSpecificTopic = [];
            
            foreach ($messages as $message) {
                $messageId = $message->id;
                $messageReplies = Reply::where('message_id', $messageId)
                    ->orderBy('created_at', 'desc')
                    ->get(['id','reply_content', 'user_id', 'created_at']);
                $MessagesSpecificTopic[$messageId] = $messageReplies;
            }
            
            return response()->json($MessagesSpecificTopic);
        } catch (\Exception $e) {

            Log::error('An error occurred: ' . $e->getMessage());
            
            return response()->json(['error' => 'An unexpected error occurred'], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

   /**
 * Crée une nouvelle réponse à un message.
 *
 * @OA\Post(
 *     path="/api/sendreply",
 *     tags={"Replies"},
 *     summary="Crée une nouvelle réponse à un message",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"reply_content", "message_id"},
 *             @OA\Property(property="reply_content", type="string", example="Contenu de la réponse"),
 *             @OA\Property(property="message_id", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Réponse créée avec succès",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Votre reponse a bien été créé.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Requête incorrecte",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Un administrateur ne peut pas envoyer de reponse.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erreur interne",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="error", type="string", example="Une erreur est survenue lors de la création de la reponse.")
 *         )
 *     )
 * )
 *
 * @param StoreReplyRequest $request
 * @return JsonResponse
 */
    public function store(StoreReplyRequest $request)
    {
    
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if ($user->is_admin) {
                return response()->json(['message' => 'Un administrateur ne peut pas envoyer de reponse.'], 400);
            }
            // dd($user);
            $request->validate([
                'reply_content' => 'required|string',
                'message_id' => self::ATTRIBUT,
            ]);
            
            Log::info(self::USER_AUTHENTICATED_MESSAGE . $user->id);
            
            $existingReply = Reply::where('reply_content', $request->input('reply_content'))
                                        ->where('user_id', $user->id)
                                        ->where('message_id', $request->input('message_id'))
                                        ->first();
        
            if ($existingReply) {
                return response()->json(['message' =>
                'Vous avez déjà envoyé une reponse avec le même contenu pour ce message.'], 400);
            }
        
            $reply = new Reply();
            $reply->reply_content = $request->input('reply_content');
            $reply->user_id = $user->id;
            $reply->message_id = $request->input('message_id');

            
            Log::info('Message object created with content: ' . $reply->reply_content);
            
            $reply->save();
            
            Log::info('Response: Reply created with content: ' . $reply->reply_content);
            
            $response = ['message' => 'Votre reponse a bien été créé.'];
      } catch (\Exception $e) {
          Log::error('Exception occurred: ' . $e->getMessage());
          Log::error($e);
          $response = ['error' => 'Une erreur est survenue lors de la création de la reponse.', 500];
      }
        
        return response()->json($response);
    }

    /**
     * Display the specified resource.
     */
    public function show(Reply $reply)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Reply $reply)
    {
        //
    }

    /**
 * Met à jour une réponse existante.
 *
 * @OA\Put(
 *     path="/api/replies/{reply}",
 *     tags={"Replies"},
 *     summary="Met à jour une réponse existante",
 *     @OA\Parameter(
 *         name="reply",
 *         in="path",
 *         required=true,
 *         description="ID de la réponse à mettre à jour",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"reply_content", "user_id", "message_id"},
 *             @OA\Property(property="reply_content", type="string", example="Nouveau contenu de la réponse"),
 *             @OA\Property(property="user_id", type="integer", example=1),
 *             @OA\Property(property="message_id", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Modifications effectuées avec succès",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Modifications effectuées avec succès."),
 *             @OA\Property(property="resultat", ref="#/components/schemas/Reply")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Requête incorrecte",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Le contenu est requis.")
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
 * @param UpdateReplyRequest $request
 * @param Reply $reply
 * @return JsonResponse
 *//**
 * Met à jour une réponse existante.
 *
 * @OA\Put(
 *     path="/api/updatespecificreply/{reply}",
 *     tags={"Replies"},
 *     summary="Met à jour une réponse existante",
 *     @OA\Parameter(
 *         name="reply",
 *         in="path",
 *         required=true,
 *         description="ID de la réponse à mettre à jour",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"reply_content", "user_id", "message_id"},
 *             @OA\Property(property="reply_content", type="string", example="Nouveau contenu de la réponse"),
 *             @OA\Property(property="user_id", type="integer", example=1),
 *             @OA\Property(property="message_id", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Modifications effectuées avec succès",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Modifications effectuées avec succès."),
 *             @OA\Property(property="resultat", ref="#/components/schemas/Reply")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Requête incorrecte",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Le contenu est requis.")
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
 * @param UpdateReplyRequest $request
 * @param Reply $reply
 * @return JsonResponse
 */
    public function update(UpdateReplyRequest $request, Reply $reply)
    {
        $user = JWTAuth::parseToken()->authenticate();
       
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        
        Log::info('User authenticated: ' . $user->id);
        
        if ($user->id !== $reply->user_id) {
            return response()->json(['message' => 'Cette reponse ne vous appartient pas.'], 403);
        }
        
        
         try {
             $request->validate([
             'reply_content' => 'required|string',
             'user_id' => self::ATTRIBUT,
             'message_id' => self::ATTRIBUT,
             ], [
             'reply_content.required' => 'Le contenu est requis.',
             'reply_content.string' => 'Le contenu doit être une chaîne de caractères.',
             'user_id.required' => "L'ID de l'utilisateur est requis.",
             'user_id.integer' => "L'ID de l'utilisateur doit être un entier.",
             'message_id.required' => "L'ID du message est requis.",
             'message_id.integer' => "L'ID du message doit être un entier.",
             ]);
             $reply->update($request->all());
             return response()->json(['message' => 'Modifications effectuées avec succès.','resultat' => $reply],201);
         } catch (\Exception $e) {
                return response()->json(['message' => 'Erreur interne.
                 Veuillez réessayer.', 'error' => $e->getMessage()], 500);
         }
    }

   /**
 * Supprime une réponse existante.
 *
 * @OA\Delete(
 *     path="/api/deletespecificreply/{reply}",
 *     tags={"Replies"},
 *     summary="Supprime une réponse existante",
 *     @OA\Parameter(
 *         name="reply",
 *         in="path",
 *         required=true,
 *         description="ID de la réponse à supprimer",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Suppression réussie",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Le réponse a été supprimé avec succès par l'administrateur."),
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Non autorisé",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Vous n'êtes pas autorisé à supprimer cette réponse.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Non trouvé",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="User not found.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erreur interne",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Une erreur est survenue.")
 *         )
 *     )
 * )
 *
 * @param Reply $reply
 * @return JsonResponse
 */
    public function destroy(Reply $reply)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
    
            if (!$user) {
                return response()->json(['message' => 'User not found.'], 404);
            }
    
            Log::info('User authenticated: ' . $user->id);

            if ($user->is_admin) {
                $reply->delete();
                return response()->json(['message' => 'Le réponse a été supprimé avec succès par l\'administrateur.']);
            } elseif ($user->id === $reply->user_id) {
                $reply->delete();
                return response()->json(['message' => 'Votre réponse a été supprimé avec succès.']);
            } else {
                return response()->json(['message' => 'Vous n\'êtes pas autorisé à supprimer cette réponse.'], 403);
            }
        } catch (\PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException $e) {
            Log::error('JWT Exception: ' . $e->getMessage());
            return response()->json(['message' => 'Une erreur est survenue lors de l\'authentification.'], 500);
        } catch (\Exception $e) {
            Log::error('Exception: ' . $e->getMessage());
            return response()->json(['message' => 'Une erreur est survenue.'], 500);
        }
        
        $reply->delete();
    }

    public function getRepliesByMessageId($messageId)
    {
       $replies = Reply::where('message_id', $messageId)
                   ->latest('created_at')
                   ->pluck('id', 'reply_content', 'user_id', 'created_at');
               
       return response()->json(['réponses' => $replies], 200);
    }
}
