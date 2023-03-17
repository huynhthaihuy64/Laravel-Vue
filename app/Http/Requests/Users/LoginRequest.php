<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            //
            'email' => 'required|exists:users',
            'password' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'email.required' => 'Please enter your email',
            'email.exists' => 'Email is not exist',
            'password.required' => 'Please enter your password',
            'password.regiex' => 'Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters',
        ];
    }
}
