<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class RegisterUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
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
            'name' => 'required|string|max:40',
            'surname' => 'required|string|max:40',
            'email' => 'required|email|unique:users,email',
            'confirmEmail' => 'required|string|same:email',
            'password' => 'required|string|min:6|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+])[A-Za-z\d!@#$%^&*()_+]{6,}$/',
            'confirmPassword' => 'required|string|same:password',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Imie jest wymagane.',
            'name.string' => 'Nazwa musi być ciągiem znaków.',
            'name.max' => 'Imie nie może przekraczać 40 znaków.',

            'surname.required' => 'Nazwisko jest wymagane.',
            'surname.string' => 'Nazwa musi być ciągiem znaków.',
            'surname.max' => 'Nazwa nie może przekraczać 40 znaków.',

            'email.required' => 'Pole adresu e-mail jest wymagane',
            'email.email' => 'Podany adres e-mail nie jest prawidłowy',
            'email.unique' => 'Podany adres e-mail jest już używany',
            'confirmEmail.same' => 'Hasła muszą być identyczne',

            'password.required' => 'Pole hasła jest wymagane.',
            'password.string' => 'Hasło musi być ciągiem znaków.',
            'password.min' => 'Hasło musi mieć co najmniej 6 znaków.',
            'password.regex' => 'Hasło musi zawierać co najmniej jedną małą literę, jedną wielką literę, jedną cyfrę oraz jeden znak specjalny.',
            'confirmPassword.same' => 'Hasła muszą być identyczne',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     */
    protected function failedValidation(Validator $validator)
    {
        $response = response()->json(['errors' => $validator->errors()], 422);
        throw new ValidationException($validator, $response);
    }
}