<?php

namespace Modules\CorporateOrder\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CorporateOrderCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'firstName' => 'required|string|min:2|max:255',
            'lastName' => 'required|string|min:2|max:255',
            'companyName' => 'required|string|min:2|max:255',
            'email' => 'required|email|min:2|max:255|unique:corporate_orders,email',
            'phone' => 'required|integer',
            'quantity' => 'required|numeric|min:0',
            'requirement' => 'nullable|string|min:2|max:255'
        ];
    }
}
