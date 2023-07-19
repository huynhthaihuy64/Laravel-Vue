<?php

namespace App\Http\Requests\Users;

use App\Enum\Mail\Selection;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SendMailAllRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'subject' => 'required',
            'content' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'subject.required' => "Subject is required",
            'content.required' => 'Content is required',
        ];
    }
}
