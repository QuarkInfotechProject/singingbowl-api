<?php
namespace Modules\Color\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ColorUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:colors,id',
            'name' => 'required|string|max:255',
            'hex_code' => 'required|string|max:7',
            'status' => 'required|boolean',
        ];
    }
}
