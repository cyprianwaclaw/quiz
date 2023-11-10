<?php

namespace App\Http\Requests;

use App\Enums\QuizDifficulty;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\In;

/**
 * @queryParam time integer The quiz time. No-example
 */
class IndexQuizRequest extends FormRequest
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
     * Query parameters
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'difficulty' => ['nullable', new In(QuizDifficulty::TYPES)],
            'time' => 'integer|nullable',
            'per_page' => 'integer|nullable'
        ];
    }

    public function queryParameters()
    {
        return [
            'difficulty' => [
                'description' => 'The quiz difficulty',
                'example' => 'easy'
            ],
            'per_page' => [
                'description' => 'Limit objects per page',
                'example' => 15
            ],
        ];
    }
}
