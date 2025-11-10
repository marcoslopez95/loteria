<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderStoreRequest extends FormRequest
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
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'quick_user' => ['nullable', 'array'],
            'quick_user.name' => ['required_without:user_id', 'string', 'min:2', 'max:255'],
            'quick_user.email' => ['required_without:user_id', 'email', 'max:255', 'unique:users,email'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.number' => ['required', 'string', 'regex:/^\\d{1,3}$/', 'distinct'],
            'items.*.amount' => ['prohibited'],

            'notes' => ['nullable', 'string'],
        ];
    }
}
