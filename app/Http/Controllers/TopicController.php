<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTopicRequest;
use App\Http\Requests\UpdateTopicRequest;
use App\Models\Forum;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class TopicController extends Controller
{

    const USER_AUTHENTICATED_MESSAGE = 'User authenticated:';


    /**
     * Display a listing of the resource.
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
     * Store a newly created resource in storage.
     */
    public function store(StoreTopicRequest $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        
        $request->validate([
            'content' => 'required|string',
            'message_received' => 'integer',
            'forum_id' => 'required|integer',
        ]);
        
        Log::info('User authenticated: ' . $user->id);
        
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
     * Display the specified resource.
     */
    public function show(Topic $topic)
    {
        $user = JWTAuth::parseToken()->authenticate();
        
        Log::info('User authenticated: ' . $user->id);
        
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
     * Update the specified resource in storage.
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
                    'user_id' => 'required|integer',
                    'forum_id' => 'required|integer',
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
     * Remove the specified resource from storage.
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
