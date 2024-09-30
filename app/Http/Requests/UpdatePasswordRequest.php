<?php

namespace App\Http\Requests;

// use Illuminate\Contracts\Validation\Validator;
// use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordRequest extends FormRequest
{
    public function __construct()
    {
    }


    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'current_password' => 'required',
            'new_password' => 'required|min:8',
            'confirm_password' => 'required|string|same:new_password',
        ];
    }
    public function messages()
    {
        return [
            'current_password.required' => 'Hasło jest wymagane',
            'new_password.required' => 'Wpisz nowe hasło',
            'new_password.min' => 'Hasło musi zawiera min. 8 znaków',
            'confirm_password.same' => 'Hasła nie sa takie same',
            'confirm_password.required' => 'Potwierdź hasło'
        ];
    }
    // protected function failedValidation(Validator $validator)
    // {
    //     $response = response()->json(['errors' => $validator->errors()->toArray()], 422);
    //     throw new ValidationException($validator, $response);
    // }
}