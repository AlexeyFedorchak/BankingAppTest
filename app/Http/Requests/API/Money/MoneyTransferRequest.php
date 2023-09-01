<?php

namespace App\Http\Requests\API\Money;

use Illuminate\Foundation\Http\FormRequest;

class MoneyTransferRequest extends FormRequest
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
     * @return array
     */
    public function rules(): array
    {
        return [
            'amount' => [
                'required',
                'numeric',
                'min:1',
                'max:1000000'
            ],
            'email' => [
                'required',
                'email',
                'exists:users,email'
            ],
        ];
    }
}
