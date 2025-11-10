<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderUpdateRequest extends FormRequest
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
            'items' => ['required', 'array', 'min:1'],
            'items.*.number' => ['required', 'string', 'regex:/^\d{1,3}$/'],
            'items.*.amount' => ['prohibited'],
            'notes' => ['nullable', 'string'],
            'status' => ['sometimes', 'in:PorPagar,Pagada,Cancelada'],
        ];
    }
}
