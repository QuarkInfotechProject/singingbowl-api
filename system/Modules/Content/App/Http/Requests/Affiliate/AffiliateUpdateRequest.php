<?php

namespace Modules\Content\App\Http\Requests\Affiliate;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Content\App\Models\Affiliate;

class AffiliateUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'id' => 'required|integer',
            'files.desktopLogo' => 'required|integer|exists:files,id',
            'files.mobileLogo' => 'required|integer|exists:files,id',
            'title' => $this->getTitleRules(),
            'description' => $this->getDescriptionRules(),
        ];
    }

    private function getTitleRules() {
        return $this->isRequiredIfIsNotPartner('string|min:2|max:255');
    }

    private function getDescriptionRules() {
        return $this->isRequiredIfIsNotPartner('string|min:2|max:255');
    }

    private function isRequiredIfIsNotPartner($rules) {
        $affiliate = Affiliate::find(request()->id);

        return $affiliate->is_partner ? 'nullable|'.$rules : 'required|'.$rules;
    }

    public function messages(): array
    {
        return [
            'files.desktopLogo.required' => 'Please upload a logo for the entity.',
            'files.desktopLogo.integer' => 'The logo must be a valid integer.',
            'files.desktopLogo.exists' => 'The selected logo does not exist in our records. Please upload a valid logo file.',
            'files.mobileLogo.required' => 'Please upload a logo for the entity.',
            'files.mobileLogo.integer' => 'The logo must be a valid integer.',
            'files.mobileLogo.exists' => 'The selected logo does not exist in our records. Please upload a valid logo file.',
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
