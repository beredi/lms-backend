<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookUpdateRequest extends FormRequest
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
            'title' => 'sometimes|string',
            'description' => 'sometimes|string',
            'pages' => 'sometimes|integer',
            'year' => 'sometimes|integer',
            'authors' => 'array|sometimes',
            'book_id' => 'sometimes|integer|unique:books',
            'authors.*' => 'exists:authors,id', // Assuming authors table has an 'id' column
        ];
    }
}
