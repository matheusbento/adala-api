<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

abstract class BaseRequest extends FormRequest
{
    // To force Laravel to always return a JsonResponse instead of a RedirectResponse
    public function wantsJson()
    {
        return true;
    }

    // // To force Laravel thow a ValidationException instead a HttpResponseException
    // // for request class validations
    // public function failedValidation(Validator $validator)
    // {
    //     throw new ValidationException($validator);
    // }

    public function getInt($key, $defaultValue = null)
    {
        return (int) $this->get($key, $defaultValue);
    }

    public function getArray($key, $defaultValue = null)
    {
        $v = $this->get($key, $defaultValue);
        if (!is_array($v)) {
            return [$v];
        }
        return $v;
    }

    public function getDate($key, $defaultValue = null)
    {
        return Carbon::createFromFormat('Y-m-d', $this->get($key, $defaultValue));
    }

    public function getParsedFields($fieldsSettings)
    {
        $mapTypes = [
            'array' => 'getArray',
            'int' => 'getInt',
            'date' => 'getDate',
        ];
        $fields = [];
        foreach (array_keys($this->toArray()) as $key) {
            if (isset($fieldsSettings[$key])) {
                $fields[$key] = $this->get($key);
                if (isset($mapTypes[$fieldsSettings[$key]])) {
                    $fnName = $mapTypes[$fieldsSettings[$key]];
                    $fields[$key] = $this->{$fnName}($key);
                }
            }
        }
        return $fields;
    }

    protected function failedValidation(Validator $validator): void
    {
        $jsonResponse = response()->json(['errors' => $validator->errors()], 422);

        throw new HttpResponseException($jsonResponse);
    }
}
