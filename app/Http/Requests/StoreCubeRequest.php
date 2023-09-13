<?php

namespace App\Http\Requests;

use App\Models\Category;
use App\Models\Cube;
use App\Models\CubeMetadata;
use App\Models\SiloFile;
use App\Models\SiloFolder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\Rule;

class StoreCubeRequest extends FormRequest
{
    protected function nestedAttributeKeyBack(string $attribute, int $count = 1): string
    {
        $holidayKeys = explode('.', $attribute);
        $keysToKeep = array_slice($holidayKeys, 0, -$count);
        return implode('.', $keysToKeep);
    }

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
                Rule::unique(Cube::class)->where('organization_id', $this->organization->id)->ignore(optional($this->cube)->id)->whereNull('deleted_at'),
            ],
            'description' => [
                'string',
                $this->isMethod('PUT') ? 'sometimes' : null,
            ],
            'is_dataflow' => [
                'boolean',
                $this->isMethod('PATCH') ? 'sometimes' : null,
            ],
            'category_id' => [
                'required',
                Rule::exists(Category::class, 'id'),
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
                $this->isMethod('PUT') ? 'sometimes' : null,
                'array',
            ],
            'metadata.*.field' => [
                $this->isMethod('PUT') ? 'sometimes' : null,
                'string',
                $this->isMethod('PUT') ? Rule::unique(CubeMetadata::class)->where('cube_id', $this->cube->id) : null,
            ],
            'metadata.*.value' => [
                $this->isMethod('PUT') ? 'sometimes' : null,
                'string',
            ],
            'folder' => [
                $this->isMethod('PUT') ? 'sometimes' : null,
                'integer',
                function ($attribute, $value, $fail) {
                    $folder = SiloFolder::find(App::make('fakeid')->decode($value));
                    if (!isset($folder)) {
                        $fail("Folder doens't exists");
                    }

                    if (isset($folder) && $folder->organization_id != $this->organization->id) {
                        $fail("Folder doens't belongs to the same organization");
                    }
                },
            ],
            'columns' => [
                'required_without:is_dataflow',
                'array',
                function ($attribute, $value, $fail) {
                    $keys = array_keys($value);
                    foreach ($keys as $key) {
                        $file = SiloFile::find(App::make('fakeid')->decode($key));
                        if (!isset($file)) {
                            $fail("File doens't exists");
                        }

                        if (isset($file) && $file->folder->organization_id != $this->organization->id) {
                            $fail("File doens't belongs to the same organization");
                        }
                    }
                },
            ],
            'columns.*' => [
                $this->isMethod('PUT') ? 'sometimes' : null,
                function ($attribute, $value, $fail) {
                    $attributePrefix = $this->nestedAttributeKeyBack($attribute);
                    $columns = $this->{"${attributePrefix}"};
                    foreach ($columns as $key => $column) {
                        $file = SiloFile::find(App::make('fakeid')->decode($key));
                        foreach ($column as $tableName => $values) {
                            // dd($file->attributes);
                            $attribute = $file->attributes->where('type', 'table')->where('name', $tableName)->first();
                            $attributeNames = $attribute->attributes->pluck('name');
                            $values = collect($values);

                            foreach ($values as $value) {
                                if (!in_array($value, $attributeNames->all())) {
                                    $fail("attribute '{$value}' for table '{$tableName}' in file '{$file->name}' doens't exists");
                                }
                            }
                        }
                    }
                },
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
