<?php

namespace Modules\Content\App\Http\Requests\FlashOffer;

use Illuminate\Foundation\Http\FormRequest;

class FlashOfferUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'id' => ['required', 'integer'],
            'name' => ['required', 'string', 'min:2', 'max:50'],
            'files.desktopFile' => ['required', 'integer', 'exists:files,id'],
            'files.mobileFile' => ['required', 'integer', 'exists:files,id'],
            'link' => ['nullable', 'url'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
