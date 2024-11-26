<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UploadUserAvatarRequest extends FormRequest
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
     * @return array
     */
    public function rules(): array
    {
        return [
            'avatar' => [
                'required',
                'image',
                Rule::dimensions()
                    ->minWidth(100)
                    ->minHeight(100)
                    ->maxWidth(600)
                    ->maxHeight(600),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'avatar.dimensions' => [
                "min_width" => "The :attribute width cannot be less than :min_widthpx",
                "min_height" => "The :attribute height cannot be less than :min_heightpx",
                "max_width" => "The :attribute width cannot be greater than :max_widthpx",
                "max_height" => "The :attribute height cannot be greater than :max_heightpx",
            ]
        ];
    }
}