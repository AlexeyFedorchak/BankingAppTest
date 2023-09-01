<?php

namespace App\Http\Requests\API\Money;

use Illuminate\Foundation\Http\FormRequest;

class StatementRequest extends FormRequest
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
            'page' => [
                'integer',
                'min:1'
            ],
            'per_page' => [
                'integer',
                'min:1'
            ],
        ];
    }
}
