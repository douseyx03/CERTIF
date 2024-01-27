<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReplyRequest;
use App\Http\Requests\UpdateReplyRequest;
use App\Models\Message;
use App\Models\Reply;
use Illuminate\Support\Facades\Log;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class ReplyController extends Controller
{
    const ATTRIBUT = 'required|integer';
    const USER_AUTHENTICATED_MESSAGE = 'User authenticated:';

    /**
     * Display a listing of the resource.
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
     * Store a newly created resource in storage.
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
     * Update the specified resource in storage.
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
     * Remove the specified resource from storage.
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
}
