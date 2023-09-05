<?php

namespace App\Http\Requests;

use App\Models\Category;
use App\Models\SiloFolder;
use Illuminate\Validation\Rule;

class StoreSiloFolderRequest extends BaseRequest
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
            'is_dataflow' => [
                'boolean',
                $this->isMethod('PATCH') ? 'sometimes' : null,
            ],
            'category_id' => [
                'required',
                Rule::exists(Category::class, 'id'),
            ],
        ];
    }
}
