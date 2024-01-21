<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFieldRequest;
use App\Http\Requests\UpdateFieldRequest;
use App\Models\Field;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class FieldController extends Controller
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
     * ***Store a newly created resource in storage(It'll be used to store in our database the newly created fields).
     */
    public function store(StoreFieldRequest $request)
    {
         
            $user = JWTAuth::parseToken()->authenticate();
            // $user = Auth::user();

            $field = new Field();
            $field->fieldname = $request->input('fieldname');
            $field->description = $request->input('description');
            $picturePath = $request->file('picture')->store('pictures/field', 'public');
            $field->picture = $picturePath;
            $field->user_id = $user->id;
            $field->save();
            return response()->json(['message' => 'Field created successfully'], 201);
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
        //
    }
}
