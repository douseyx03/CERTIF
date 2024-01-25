<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTopicRequest extends FormRequest
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
    public function rules()
    {
        return [
            'content' => 'required|string',
            'message_received' => 'integer',
            'user_id' => 'required|integer',
            'forum_id' => 'required|integer',
        ];
    }

    public function messages()
    {
        return [
            'content.required' => 'Le contenu est requis.',
            'content.string' => 'Le contenu doit être une chaîne de caractères.',
            'message_received.integer' => 'Le message reçu doit être un entier.',
            'user_id.required' => "L'ID de l'utilisateur est requis.",
            'user_id.integer' => "L'ID de l'utilisateur doit être un entier.",
            'forum_id.required' => "L'ID du forum est requis.",
            'forum_id.integer' => "L'ID du forum doit être un entier.",
        ];
    }
}
