<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookStoreRequest extends FormRequest
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
            'title' => 'required|string',
            'description' => 'nullable|string',
            'pages' => 'nullable|integer',
            'year' => 'nullable|integer',
            'authors' => 'array|required',
            'categories' => 'array|required',
            'book_id' => 'required|integer|unique:books',
            'authors.*' => 'exists:authors,id',
            'categories.*' => 'exists:categories,id',
        ];
    }
}
