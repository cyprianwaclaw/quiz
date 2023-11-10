<?php

namespace App\Http\Requests;

use App\Rules\Bic;
use App\Rules\Iban;
use App\Rules\Nip;
use App\Rules\Postalcode;
use App\Rules\Regon;
use Illuminate\Foundation\Http\FormRequest;

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
//            inputów bedzie wiecej
//            tak jak na figmie
            'name' => 'sometimes|string',
            'surname' => 'sometimes|string',
            'email' => 'sometimes|email',
            'phone' => 'sometimes|digits:9',

            'company_name' => ['string','required_with:nip,regon,city,postcode,street,building_number,house_number'],
            'nip' => ['required_with:company_name,regon,city,postcode,street,building_number,house_number', new Nip()],
            'regon' => ['required_with:nip,company_name,city,postcode,street,building_number,house_number', new Regon()],
            'city' => ['required_with:company_name,nip,regon,postcode,street,building_number,house_number','min:3'],
            'postcode' => ['required_with:company_name,nip,regon,city,street,building_number,house_number', new Postalcode('PL')],
            'street' => ['required_with:company_name,nip,regon,city,postcode,building_number,house_number', 'min:3'],
            'building_number' => ['required_with:company_name,nip,regon,city,postcode,street,house_number', 'string', 'max:5'],
            'house_number' => ['required_with:company_name,nip,regon,city,postcode,street,building_number', 'string', 'max:5'],

            'iban' => ['required_with:bank_name,swift', new Iban()],
            'bank_name' => ['required_with:iban,swift', 'string'],
            'swift' => ['required_with:iban,bank_name', new Bic()],
        ];
    }
    protected function prepareForValidation()
    {
        $this->merge([
            'phone' => preg_replace('/[^0-9]+/', '', $this->phone),
        ]);

    }
}
