<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMessageRequest;
use App\Http\Requests\UpdateMessageRequest;
use App\Models\Message;
use App\Models\Topic;
use Illuminate\Support\Facades\Log;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class MessageController extends Controller
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
            
            $topics = Topic::all();
            $topicSpecificMessages = [];
            
            foreach ($topics as $topic) {
                $topicId = $topic->id;
                $topicMessages = Message::where('topic_id', $topicId)
                    ->orderBy('created_at', 'desc')
                    ->get(['id','message_content', 'user_id', 'created_at']);
                $topicSpecificMessages[$topicId] = $topicMessages;
            }
            
            return response()->json($topicSpecificMessages,200);
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
    public function store(StoreMessageRequest $request)
    {
    try {
        $user = JWTAuth::parseToken()->authenticate();
        
        if ($user->is_admin) {
            return response()->json(['message' => 'Un administrateur ne peut pas envoyer de message.'], 400);
        }
        
        $request->validate([
            'message_content' => 'required|string',
            'topic_id' => self::ATTRIBUT,
        ]);
        
        Log::info(self::USER_AUTHENTICATED_MESSAGE . $user->id);
        
        $existingMessage = Message::where('message_content', $request->input('message_content'))
                                    ->where('user_id', $user->id)
                                    ->where('topic_id', $request->input('topic_id'))
                                    ->first();
    
        if ($existingMessage) {
            return response()->json(['message' =>
            'Vous avez déjà envoyé un message avec le même contenu dans ce sujet.'], 400);
        }
    
        $message = new Message();
        $message->message_content = $request->input('message_content');
        $message->user_id = $user->id;
        $message->topic_id = $request->input('topic_id');
        
        Log::info('Message object created with content: ' . $message->message_content);
        
        $message->save();

        $topic = Topic::find($request->input('topic_id'));
        $topic->message_received++;
        $topic->save();
        
        Log::info('Message saved');
        
        Log::info('Response: Message created with content: ' . $message->message_content);
        
        $response = ['message' => 'Votre message a bien été créé.'];
    } catch (\Exception $e) {
        Log::error('Exception occurred: ' . $e->getMessage());
        $response = ['error' => 'Une erreur est survenue lors de la création du message.', 500];
    }
    
    return response()->json($response);
    }

    /**
     * Display the specified resource.
     */
    public function show(Message $message)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Message $message)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMessageRequest $request, Message $message)
    {
        $user = JWTAuth::parseToken()->authenticate();
       
       if (!$user) {
           return response()->json(['message' => 'Unauthorized'], 401);
       }
       
       Log::info('User authenticated: ' . $user->id);
       
       if ($user->id !== $message->user_id) {
           return response()->json(['message' => 'Ce message ne vous appartient pas.'], 403);
       }
       
       
        try {
            $request->validate([
            'message_content' => 'required|string',
            'user_id' => self::ATTRIBUT,
            'topic_id' => self::ATTRIBUT,
            ], [
            'message_content.required' => 'Le contenu est requis.',
            'message_content.string' => 'Le contenu doit être une chaîne de caractères.',
            'user_id.required' => "L'ID de l'utilisateur est requis.",
            'user_id.integer' => "L'ID de l'utilisateur doit être un entier.",
            'topic_id.required' => "L'ID du sujet est requis.",
            'topic_id.integer' => "L'ID du sujet doit être un entier.",
            ]);
            $message->update($request->all());
            return response()->json(['message' => 'Modifications effectuées avec succès.','resultat' => $message],201);
        } catch (\Exception $e) {
               return response()->json(['message' => 'Erreur interne.
                Veuillez réessayer.', 'error' => $e->getMessage()], 500);
        }
       
       
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Message $message)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
    
            if (!$user) {
                return response()->json(['message' => 'User not found.'], 404);
            }
    
            Log::info('User authenticated: ' . $user->id);
    
            if ($user->is_admin) {
                $message->delete();
                return response()->json(['message' => 'Le message a été supprimé avec succès par l\'administrateur.']);
            } elseif ($user->id === $message->user_id) {
                $message->delete();
                return response()->json(['message' => 'Votre message a été supprimé avec succès.']);
            } else {
                return response()->json(['message' => 'Vous n\'êtes pas autorisé à supprimer ce message.'], 403);
            }
        } catch (\PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException $e) {
            Log::error('JWT Exception: ' . $e->getMessage());
            return response()->json(['message' => 'Une erreur est survenue lors de l\'authentification.'], 500);
        } catch (\Exception $e) {
            Log::error('Exception: ' . $e->getMessage());
            return response()->json(['message' => 'Une erreur est survenue.'], 500);
        }
        
        $message->delete();
        
        $topic = Topic::find($message->topic_id);
        if ($topic->message_received > 0) {
            $topic->message_received--;
        }
        $topic->save();
        
    }
}
