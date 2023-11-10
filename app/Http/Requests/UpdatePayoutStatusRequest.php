<?php

namespace App\Http\Requests;

use App\Enums\PayoutStatus;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Dimensions;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\In;

class UpdatePayoutStatusRequest extends FormRequest
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
            'status' => ['required', new In(PayoutStatus::TYPES)],
        ];
    }

    public function messages()
    {
        return ['in' => 'The status must be one of types: '. implode(', ', PayoutStatus::TYPES) .'.'];
    }
}
