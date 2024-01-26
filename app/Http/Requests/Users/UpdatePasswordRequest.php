<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdatePasswordRequest extends FormRequest
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
            'email' => 'required|email|exists:users',
            'password' => 'required|same:confirm_password',
            'confirm_password' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'email.required' => __('messages.validation.user.email.required'),
            'email.email' => __('messages.validation.user.email.email'),
            'email.exists' =>  __('messages.validation.user.email.exists'),
            'password.required' => __('messages.validation.user.password.required'),
            'password.confirmed' => __('messages.validation.user.password.confirmed'),
            'password.regex' => __('messages.validation.user.password.regex'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}