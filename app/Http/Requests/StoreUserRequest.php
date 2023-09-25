<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;

class StoreUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'direct_permissions.*' => [
                'string',
                Rule::exists(Permission::class, 'name'),
            ],
            'direct_permissions' => [
                $this->isMethod('PATCH') ? 'sometimes' : null,
                'present',
                'array',
            ],
            'email' => [
                $this->isMethod('PATCH') ? 'sometimes' : null,
                'required',
                'email',
                Rule::unique(User::class, 'email')->ignore($this->id),
            ],
            'name' => [
                $this->isMethod('PATCH') ? 'sometimes' : null,
                'required',
                'string',
            ],
        ];
    }
}
