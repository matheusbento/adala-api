<?php

namespace App\Http\Requests;

use App\Models\SiloFolder;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSiloFolderRequest extends FormRequest
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
                $this->isMethod('PATCH') ? 'sometimes' : null,
                'required',
                'string',
                Rule::unique(SiloFolder::class, 'name')
                    ->where('organization_id', $this->organizationId)
                    ->whereNull('deleted_at')
                    ->ignore(optional($this->folder)->id),
            ],
            'description' => [
                'string',
                $this->isMethod('PATCH') ? 'sometimes' : null,
            ],
        ];
    }
}
