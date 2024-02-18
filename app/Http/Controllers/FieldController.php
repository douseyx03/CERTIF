<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFieldRequest;
use App\Http\Requests\UpdateFieldRequest;
use App\Models\Field;
use Dotenv\Exception\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class FieldController extends Controller
{
    const ATTRIBUT = 'required|integer';

    /**
     * Display a listing of the resource.
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
     * ***Store a newly created resource in storage(It'll be used to store in our database the newly created fields).
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
           $user = Auth::user();
   
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
               $file->storeAs('pictures/field', $filename, 'public');
               $data['picture'] = $filename;
           }
           
           $field->update($data);
   
           return response()->json(['message' => 'Domaine mis à jour avec succès.', 'Nouvelles infos' => $field], 200);
       } catch (ModelNotFoundException $e) {
           Log::error('Error updating field: ' . $e->getMessage());
           return response()->json(['message' => 'Field not found.', 'error' => $e->getMessage()], 404);
       } catch (ValidationException $e) {
           Log::error('Error updating field: ' . $e->getMessage());
           return response()->json(['message' => 'Validation error.', 'error' => $e->getMessage()], 422);
       } catch (\Exception $e) {
           Log::error('Error updating field: ' . $e->getMessage());
           return response()->json(['message' => 'An error occurred while updating the field.', 'error' => $e->getMessage()], 500);
       }
   }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Field $field)
    {
        $field->is_archived = true;
        $field->update();

        return response()->json($field);
    }
}
