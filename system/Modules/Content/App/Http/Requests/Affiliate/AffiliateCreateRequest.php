<?php

namespace Modules\Content\App\Http\Requests\Affiliate;

use Illuminate\Foundation\Http\FormRequest;

class AffiliateCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'isPartner' => 'required|boolean',
            'files.desktopLogo' => 'required|integer|exists:files,id',
            'files.mobileLogo' => 'required|integer|exists:files,id',
            'title' => $this->getTitleRules(),
            'description' => $this->getDescriptionRules(),
            'link' => 'nullable|url',
        ];
    }

    private function getTitleRules() {
        return $this->isRequiredIfIsNotPartner('string|min:2|max:255');
    }

    private function getDescriptionRules() {
        return $this->isRequiredIfIsNotPartner('string|min:2|max:255');
    }

    private function isRequiredIfIsNotPartner($rules) {
        return request()->isPartner ? 'nullable|'.$rules : 'required|'.$rules;
    }

    public function messages(): array
    {
        return [
            'isPartner.required' => 'Please specify whether the entity is a partner or not.',
            'isPartner.boolean' => 'The partner status must be either true or false.',

            'files.desktopLogo.required' => 'Please upload a logo for the entity.',
            'files.desktopLogo.integer' => 'The logo must be a valid integer.',
            'files.desktopLogo.exists' => 'The selected logo does not exist in our records. Please upload a valid logo file.',
            'files.mobileLogo.required' => 'Please upload a logo for the entity.',
            'files.mobileLogo.integer' => 'The logo must be a valid integer.',
            'files.mobileLogo.exists' => 'The selected logo does not exist in our records. Please upload a valid logo file.',

            'link.url' => 'The link format is invalid.',
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
