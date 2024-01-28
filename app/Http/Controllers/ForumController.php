<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreForumRequest;
use App\Http\Requests\UpdateForumRequest;
use App\Models\Forum;
use Illuminate\Support\Facades\Log;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use OpenApi\Annotations as OA;
// @include('swagger_info');
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
 * Stocke une nouvelle ressource créée dans le stockage (Il sera utilisé pour stocker dans notre base de données les forums nouvellement créés).
 *
 * @OA\Post(
 *     path="/api/addforum",
 *     tags={"Forums"},
 *     summary="Stocke une nouvelle ressource créée",
 *     @OA\RequestBody(
 *         @OA\JsonContent(ref="#/components/schemas/StoreForumRequest")
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Opération réussie",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Votre forum a bien été créé."
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Entité non traitable",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Il existe déjà un forum pour ce domaine"
 *             )
 *         )
 *     )
 * )
 *
 * @param StoreForumRequest $request
 * @return JsonResponse
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
 * Affiche toutes les ressources.
 *
 * @OA\Get(
 *     path="/api/displayforum",
 *     tags={"Forums"},
 *     summary="Affiche toutes les ressources",
 *     @OA\Response(
 *         response=200,
 *         description="Opération réussie",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/Forum")
 *         )
 *     )
 * )
 *
 * @return JsonResponse
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
        //
    }

   /**
 * Supprime la ressource spécifiée.
 * 
 * @OA\Delete(
 *     path="/api/deleteforum/{forum}",
 *     tags={"Forums"},
 *     summary="Supprime la ressource spécifiée",
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="id",
 *                 type="integer",
 *                 description="ID de la ressource à supprimer"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Opération réussie",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Le forum a été supprimé avec succès."
 *             )
 *         )
 *     )
 * )
 *
 * @param int $id
 * @return JsonResponse
 */
    public function destroy($id)
    {
        Forum::destroy($id);
        return response()->json(['message' => 'Le forum a été supprimé avec succès.']);
        
    }
}
