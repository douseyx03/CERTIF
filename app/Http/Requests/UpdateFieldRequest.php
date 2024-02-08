<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFieldRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
   public function rules(): array
    {
        return [
            'fieldname' => 'required|string|regex:/^[A-Z][a-zA-Z\s]*$/',
            'description' => 'required|string',
            'picture' => 'required|image|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'fieldname.required' => 'Le nom de domaine est requit',
            'fieldname.regex' => 'Le nom de domaine commence obligatoirement par une majuscule
             et ne peut contenir que des lettres ',
            'description.required' => 'La description du domaine est obligatoire',
            'description.string' => 'Le domaine peut contenir des lettres des chiffres et des symboles',
            'picture.required' => 'L\'image ou le logo du domaine est obligatoire',
            'picture.image' => 'Le fichier doit être sous le format image ',
            'picture.max' => 'la taille maximale permise pour l\'image est de 2Mo ',
        ];
    }
}
