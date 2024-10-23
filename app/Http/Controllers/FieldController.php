<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFieldRequest;
use App\Http\Requests\UpdateFieldRequest;
use App\Models\Field;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use OpenApi\Annotations as OA;
// @include('swagger_info');
class FieldController extends Controller
{
<<<<<<< apiPlatform
    
    
=======
    const ATTRIBUT = 'required|integer';

>>>>>>> main
    /**
 * Affiche une liste de domaines.
 * 
 * @OA\Get(
 *     path="/api/displayfield",
 *     tags={"Fields"},
 *     summary="Affiche une liste des domaines",
 *     @OA\Response(
 *         response=200,
 *         description="Opération réussie",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/Field")
 *         )
 *     )
 * )
 *
 * @return JsonResponse
 */
    public function index()
    {
        return response()->json(Field::where('is_archived', false)->get());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

  /**
 * Stocke une nouvelle ressource créée dans le stockage (Il sera utilisé pour stocker dans notre base de données les champs nouvellement créés).
 * 
 * @OA\Post(
 *     path="/api/addfield",
 *     tags={"Fields"},
 *     summary="Stocke une nouvelle ressource créée",
 *     @OA\RequestBody(
 *         @OA\JsonContent(ref="#/components/schemas/StoreFieldRequest")
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Opération réussie"
 *     )
 * )
 *
 * @param StoreFieldRequest $request
 * @return JsonResponse
 */
    public function store(StoreFieldRequest $request): JsonResponse
    {
        $user = JWTAuth::parseToken()->authenticate();
        
        $field = new Field();
        $field->fieldname = $request->input('fieldname');
        $field->description = $request->input('description');
        $field->picture = $request->input('picture');
        $field->user_id = $user->id;
        if($request->file('picture')){
            $file= $request->file('picture');
            $filename= date('YmdHi').$file->getClientOriginalName();
            $file-> move(public_path('pictures/field'), $filename);
            $field['picture']= $filename;
        }
        $field->save();
        return response()->json(['message' => 'Votre domaine a bien été créé.'], 201);
    }
    /**
     * Display the specified resource.
     */
    public function show(Field $field)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Field $field)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
   public function update(UpdateFieldRequest $request, Field $field)
   {
       try {
           $user = JWTAuth::parseToken()->authenticate();
           
           if (!$user) {
               return response()->json(['message' => 'Unauthorized'], 401);
           }
           
           Log::info('User authenticated: ' . $user->id);
           
           if ($user->id !== $field->user_id) {
               return response()->json(['message' => 'Ce domaine n\'a pas été créé par vous. Ce domaine ne vous appartient pas.'], 403);
           }
           
           $data = $request->only(['fieldname', 'description']);
           if ($request->hasFile('picture')) {
               $file = $request->file('picture');
               $filename = date('YmdHi') . $file->getClientOriginalName();
               $file->move(public_path('pictures/field'), $filename);
               $data['picture'] = $filename;
           }
           
           $field->update($data);
           
           return response()->json($field);
       } catch (\Exception $e) {
            Log::error('Error updating field: ' . $e->getMessage());
           return response()->json(['message' => 'An error occurred while updating the field.'], 500);
       }
   }

    /**
 * Supprime la ressource spécifiée.
 * 
 * @OA\Delete(
 *     path="/api/deletefield/{field}",
 *     tags={"Fields"},
 *     summary="Supprime la ressource spécifiée",
 *     @OA\Parameter(
 *         name="field",
 *         in="path",
 *         description="ID de la ressource",
 *         required=true,
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Opération réussie",
 *         @OA\JsonContent(ref="#/components/schemas/Field")
 *     )
 * )
 *
 * @param Field $field
 * @return JsonResponse
 */
    public function destroy(Field $field)
    {
        $field->is_archived = true;
        $field->update();

        return response()->json($field);
    }
}
