<?php

namespace App\Http\Requests;

use App\Enums\QuizDifficulty;
use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\In;

class StoreCompetitionRequest extends FormRequest
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
            'category_id' => 'required|exists:App\Models\Category,id',
            'title' => 'required|min:3',
            'first_points' => 'required',
            'second_points' => 'required',
            'third_points' => 'required',
            'time_start' => 'required',
            'time_end' => 'required',
            'description' => 'nullable',
            'image' => [
                'sometimes',
                'image',
                /*Rule::dimensions()
                    ->minWidth(100)
                    ->minHeight(100)
                    ->maxWidth(1000)
                    ->maxHeight(1000),*/
            ],
            // 'time' => 'required',
            'difficulty' => [new In(QuizDifficulty::TYPES)]
        ];
    }

    public function messages(): array
    {
        return [
            'image.dimensions' => [
                "min_width" => "The :attribute width cannot be less than :min_widthpx",
                "min_height" => "The :attribute height cannot be less than :min_heightpx",
                "max_width" => "The :attribute width cannot be greater than :max_widthpx",
                "max_height" => "The :attribute height cannot be greater than :max_heightpx",
            ],
            'in' => 'The :attribute must be one of types: ' . implode(', ', QuizDifficulty::TYPES) . '.',
        ];
    }

    public function bodyParameters()
    {
        return [
            'category_id' => [
                'description' => 'The id of the quiz category.',
                'example' => 2,
            ],
            'title' => [
                'description' => 'The title of the quiz category.',
                'example' => 'Quiz title',
            ],
            'description' => [
                'description' => 'The description of the quiz.',
                'example' => 'Quiz description',
            ],
            'image' => [
                'description' => 'The image of the quiz.',
            ],
            'time' => [
                'description' => 'The time of the quiz.',
            ],
            'difficulty' => [
                'description' => 'The difficulty of the quiz.',
            ],
        ];
    }
}
