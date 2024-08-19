<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WithdrawRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'account_name' => ['required', 'string'],
            'account_number' => ['required', 'regex:/^[0-9]+$/'],
            'amount' => ['required', 'numeric'],
            'password' => ['required'],
            'payment_type_id' => ['required', 'exists:payment_types,id'],
        ];
    }
}
