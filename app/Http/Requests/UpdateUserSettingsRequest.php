<?php

namespace App\Http\Requests;

use App\Rules\Bic;
use App\Rules\Nip;
use App\Rules\Postalcode;
use App\Rules\Regon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateUserSettingsRequest extends FormRequest
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
            // dane podstawowe
            'name' => 'sometimes|string',
            'surname' => 'sometimes|string',
            'email' => 'sometimes|email',
            'phone' => 'sometimes',

            // dane firmy
            'company_name' => ['string', 'required_with:nip,regon,city,postcode,street,building_number,house_number'],
            'nip' => ['required_with:company_name,regon,city,postcode,street,building_number,house_number', new Nip()],
            'regon' => ['required_with:nip,company_name,city,postcode,street,building_number,house_number', new Regon()],
            'city' => ['required_with:company_name,nip,regon,postcode,street,building_number,house_number', 'min:3'],
            'postcode' => ['required_with:company_name,nip,regon,city,street,building_number,house_number', new Postalcode('PL')],
            'street' => ['required_with:company_name,nip,regon,city,postcode,building_number,house_number', 'min:3'],
            'building_number' => ['required_with:company_name,nip,regon,city,postcode,street,house_number', 'string', 'max:5'],
            'house_number' => [],

            // dane bankowe (konto 26 cyfr zamiast IBAN)
            'iban' => [
                // 'required_with:bank_name,swift',
                'regex:/^\d{26}$/'
            ],
            // 'bank_name' => ['required_with:account_number,swift', 'string'],
            // 'swift' => ['required_with:account_number,bank_name', new Bic()],
        ];
    }

    /**
     * Przygotowanie danych do walidacji
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'phone' => $this->phone ? preg_replace('/[^0-9]+/', '', $this->phone) : null,
            'account_number' => $this->account_number ? preg_replace('/[^0-9]+/', '', $this->account_number) : null,
        ]);
    }

    /**
     * Format błędów w JSON
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->toArray();

        $formattedErrors = [];
        foreach ($errors as $field => $messages) {
            $formattedErrors[$field] = ['message' => $messages[0]];
        }

        throw new HttpResponseException(response()->json([
            'errors' => $formattedErrors
        ], 422));
    }
}