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
    public function store(StoreFieldRequest $request): JsonResponse
    {
        $user = JWTAuth::parseToken()->authenticate();
        // $user = Auth::user();
        
        $field = new Field([
            'fieldname' => $request->input('fieldname'),
            'description' => $request->input('description'),
            'picture' => $request->file('picture')->store('pictures/field', 'public'),
            'user_id' => (int) $user->id,
        ]);
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
        //
    }
}
