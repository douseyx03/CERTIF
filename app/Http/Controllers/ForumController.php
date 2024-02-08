<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreForumRequest;
use App\Http\Requests\UpdateForumRequest;
use App\Models\Forum;
use Illuminate\Support\Facades\Log;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class ForumController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(StoreForumRequest $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        
        Log::info('User authenticated: ' . $user->id);
        
        $existingForum = Forum::where('field_id', $request->input('field_id'))->first();
        
        if ($existingForum) {
            Log::info('Forum with field_id ' . $request->input('field_id') . ' already exists');
            return response()->json(['message' => 'Il existe déjà un forum pour ce domaine'], 422);
        }
        
        $forum = new Forum();
        $forum->forumname = $request->input('forumname');
        $forum->description = $request->input('description');
        $forum->field_id = $request->input('field_id');
        $forum->user_id = $user->id;
        
        Log::info('Forum object created with name: ' . $forum->forumname);
        
        $forum->save();
        
        Log::info('Forum saved');
        
        Log::info('Response: Forum created with name: ' . $forum->forumname);
        
        return response()->json(['message' => 'Votre forum a bien été créé.'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Forum $forum)
    {
        $ourforum = Forum::all();
        return response()->json($ourforum);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Forum $forum)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateForumRequest $request, Forum $forum)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            
            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
            
            Log::info('User authenticated: ' . $user->id);
            
            if ($user->id !== $forum->user_id) {
                return response()->json(['message' => 'Ce forum n\'a pas été créé par vous. Ce domaine ne vous appartient pas.'], 403);
            }
            
            $data = $request->only(['forumname', 'description']);
    
            
            $forum->update($data);
            
            return response()->json($forum);
        } catch (\Exception $e) {
             Log::error('Error updating field: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while updating the field.',
                'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Forum::destroy($id);
        return response()->json(['message' => 'Le forum a été supprimé avec succès.']);
        
    }
}
