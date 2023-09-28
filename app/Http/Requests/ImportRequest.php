<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ImportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'file' => 'required',
            'file.*' => 'required|file|mimes:xlsx,csv|max:50000'
        ];
    }

    public function messages()
    {
        return [
            'file.required' => 'File is required',
            'file.*.file' => 'File is File',
            'file.*.mimes' => 'Type File must be CSV or XLSX',
            'file.*.max' => 'Max is 50MB'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }

}
