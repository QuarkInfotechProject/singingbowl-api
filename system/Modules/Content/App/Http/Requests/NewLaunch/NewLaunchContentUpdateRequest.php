<?php

namespace Modules\Content\App\Http\Requests\NewLaunch;

use Illuminate\Foundation\Http\FormRequest;

class NewLaunchContentUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'id' => ['required', 'integer'],
            'files.desktopImage' => ['required', 'integer', 'exists:files,id'],
            'files.mobileImage' => ['required', 'integer', 'exists:files,id'],
            'link' => ['nullable', 'url'],
            'isBanner' => ['boolean', 'required'],
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
