<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Constants\FileConstants;
use App\Enums\UploadHistory\FileType;
use App\Rules\UploadFile;
use Illuminate\Support\Str;


class UploadExcelRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            FileConstants::INPUT_FILE => ['required', 'max:' . config('filesystems.size_file_max'), 'file'],
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    public function prepareForValidation(): void
    {
        $inputs = $this->all();
        if ($this->hasFile(FileConstants::INPUT_FILE)) {
            $file = $this->file(FileConstants::INPUT_FILE);
            $inputs[FileConstants::INPUT_FILE] = $file;
            $inputs[FileConstants::INPUT_FILE_EXTENSION] = Str::lower($file->getClientOriginalExtension());
        }
        $this->replace($inputs);
    }

}
