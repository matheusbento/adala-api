<?php

namespace App\Http\Requests;

use App\Models\Cube;
use App\Models\CubeMetadata;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCubeRequest extends FormRequest
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
                $this->isMethod('PATCH') ? 'sometimes' : null,
                Rule::unique(Cube::class)->where('organization_id', $this->organization->id)->ignore(optional($this->cube)->id)->whereNull('deleted_at'),
            ],
            'description' => [
                'string',
                $this->isMethod('PATCH') ? 'sometimes' : null,
            ],
            'start_date' => [
                'sometimes',
                'nullable',
                'date',
                'after_or_equal:today',
            ],
            'end_date' => [
                'sometimes',
                'nullable',
                'date',
                'required_without:contract_length_id',
                'after_or_equal:start_date',
            ],
            'metadata' => [
                $this->isMethod('PATCH') ? 'sometimes' : null,
                'array',
            ],
            'metadata.*.field' => [
                $this->isMethod('PATCH') ? 'sometimes' : null,
                'string',
                // Rule::unique(CubeMetadata::class)->where('cube_id', $this->cube->id),
            ],
            'metadata.*.value' => [
                $this->isMethod('PATCH') ? 'sometimes' : null,
                'string',
            ],
            'model' => [
                $this->isMethod('PATCH') ? 'sometimes' : null,
                'array',
            ],
            'model.dimensions' => [
                $this->isMethod('PATCH') ? 'sometimes' : null,
                'array',
            ],
            'model.dimensions.*.name' => [
                $this->isMethod('PATCH') ? 'sometimes' : null,
                'string',
            ],
            'model.dimensions.*.role' => [
                'sometimes',
                'string',
            ],
            'model.dimensions.*.levels.*' => [
                $this->isMethod('PATCH') ? 'sometimes' : null,
                'array',
            ],
            'model.dimensions.*.levels.*.name' => [
                $this->isMethod('PATCH') ? 'sometimes' : null,
                'string',
            ],
            'model.dimensions.*.levels.*.label' => [
                $this->isMethod('PATCH') ? 'sometimes' : null,
                'string',
            ],
            'model.dimensions.*.levels.*.attributes' => [
                $this->isMethod('PATCH') ? 'sometimes' : null,
                'array',
            ],
            'model.cubes' => [
                $this->isMethod('PATCH') ? 'sometimes' : null,
                'array',
            ],
            'model.cubes.*.id' => [
                $this->isMethod('PATCH') ? 'sometimes' : null,
            ],
            'model.cubes.*.name' => [
                $this->isMethod('PATCH') ? 'sometimes' : null,
                'string',
            ],
            'model.cubes.*.dimensions.*' => [
                $this->isMethod('PATCH') ? 'sometimes' : null,
                'string',
            ],
            'model.cubes.*.measures' => [
                $this->isMethod('PATCH') ? 'sometimes' : null,
                'array',
            ],
            'model.cubes.*.measures.*.name' => [
                $this->isMethod('PATCH') ? 'sometimes' : null,
                'string',
            ],
            'model.cubes.*.measures.*.label' => [
                $this->isMethod('PATCH') ? 'sometimes' : null,
                'string',
            ],
            'model.cubes.*.aggregates' => [
                $this->isMethod('PATCH') ? 'sometimes' : null,
                'array',
            ],
            'model.cubes.*.aggregates.*.name' => [
                $this->isMethod('PATCH') ? 'sometimes' : null,
                'string',
            ],
            'model.cubes.*.aggregates.*.measure' => [
                'sometimes',
                'string',
            ],
            'model.cubes.*.aggregates.*.function' => [
                $this->isMethod('PATCH') ? 'sometimes' : null,
                'string',
                Rule::in(['sum', 'count']),
            ],
            'model.cubes.*.mappings' => [
                $this->isMethod('PATCH') ? 'sometimes' : null,
            ],
            'model.cubes.*.info' => [
                $this->isMethod('PATCH') ? 'sometimes' : null,
            ],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
        ];
    }
}
