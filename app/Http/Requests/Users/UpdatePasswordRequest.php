<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

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
            'email.required' => 'Email is required',
            'email.email' => 'The attribute must be a valid email address',
            'email.exists' => 'Email is not exist',
            'password.required' => 'Password is required',
            'password.confirmed' => 'Password confirm does not match',
            'password.regex' => 'Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters',
        ];
    }
}