<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCubeDashboardItemRequest extends FormRequest
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
            ],
            'chart' => [
                'string',
                $this->isMethod('PUT') ? 'sometimes' : null,
            ],
            'processing_method' => [
                'string',
            ],
            'select' => [
                'array',
                $this->isMethod('PUT') ? 'sometimes' : null,
            ],
            'filter' => [
                'array',
                $this->isMethod('PUT') ? 'sometimes' : null,
            ],
            'layout' => [
                'array',
                $this->isMethod('PUT') ? 'sometimes' : null,
            ],
        ];
    }
}
