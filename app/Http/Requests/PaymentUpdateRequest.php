<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentUpdateRequest extends FormRequest
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
        $paymentId = $this->route('payment')?->id ?? null;

        return [
            'amount' => ['required', 'decimal:0,2', 'min:0.01'],
            'currency_id' => ['required', 'integer', 'exists:currencies,id'],
            'exchange_rate' => ['required', 'decimal:0,8', 'min:0.00000001'],
            'reference' => [
                'required', 'string', 'max:255',
                Rule::unique('payments', 'reference')->ignore($paymentId),
            ],
            'paid_at' => ['nullable', 'date'],
        ];
    }
}
