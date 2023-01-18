<?php

namespace App\Http\Requests;

use App\Models\SiloFile;
use App\Models\Tag;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSiloFileRequest extends FormRequest
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
                Rule::unique(SiloFile::class, 'name')
                    ->where('folder_id', $this->folder->id)
                    ->whereNull('deleted_at')
                    ->ignore(optional($this->file)->id),
            ],
            'description' => [
                'string',
            ],
            'tags' => [
                'array',
            ],
            'tags.*' => [
                'required',
                Rule::exists(Tag::class, 'id'),
            ],
            'file' => [
                $this->id ? 'sometimes' : null,
                'required',
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'file.required' => 'The file is required',
        ];
    }
}
