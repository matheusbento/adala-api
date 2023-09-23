<?php

namespace App\Http\Requests;

use App\Models\Organization;
use Illuminate\Validation\Rule;

class StoreOrganizationRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => [
                'string',
                $this->isMethod('PUT') ? 'sometimes' : null,
                Rule::unique(Organization::class, 'name')->ignore($this->organization),
            ],
            'description' => [
                'string',
                $this->isMethod('PUT') ? 'sometimes' : null,
            ],
        ];
    }
}
