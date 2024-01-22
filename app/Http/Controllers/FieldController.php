<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFieldRequest;
use App\Http\Requests\UpdateFieldRequest;
use App\Models\Field;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class FieldController extends Controller
{
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
        //  dd($field);
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
        //
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
