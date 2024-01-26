<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMessageRequest extends FormRequest
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
            'message_content' => 'required|string',
            'user_id' => 'required|integer',
            'topic_id' => 'required|integer',
        ];
    }

    public function messages()
    {
        return [
            'message_content.required' => 'Le contenu du message est requis.',
            'message_content.string' => 'Le contenu du message doit être une chaîne de caractères.',
            'user_id.required' => "L'ID de l'utilisateur est requis.",
            'user_id.integer' => "L'ID de l'utilisateur doit être un entier.",
            'topic_id.required' => "L'ID du sujet est requis.",
            'topic_id.integer' => "L'ID du sujet doit être un entier.",
        ];
    }
}
