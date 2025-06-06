<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // return false;
        return Auth::user()->hasPermissionTo('post-create') || Auth::user()->hasPermissionTo('post-edit');
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
            'body' => 'nullable',
            'slug' => 'nullable|string|unique:posts,slug,',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required',
            'image'=>'nullable'

        ];
    }
}
