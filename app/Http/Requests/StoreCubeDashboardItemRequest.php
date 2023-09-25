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
                'string',
                $this->isMethod('PUT') ? 'sometimes' : null,
            ],
            'filter' => [
                'string',
                $this->isMethod('PUT') ? 'sometimes' : null,
            ],
            'layout' => [
                'string',
                $this->isMethod('PUT') ? 'sometimes' : null,
            ],
        ];
    }
}
