<?php

namespace App\Http\Requests;

use App\Rules\Bic;
use App\Rules\Iban;
use App\Rules\Nip;
use App\Rules\Postalcode;
use App\Rules\Regon;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
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
            'phone' => 'sometimes|array|size:3',
            'phone.*' => 'sometimes|digits:3',

            'company_name' => 'sometimes|string',
            'nip' => ['sometimes', new Nip()],
            'regon' => ['sometimes', new Regon()],
            'city' => 'sometimes|min:3',
            'postcode' => ['sometimes', new Postalcode('PL')],
            'street' => 'sometimes|min:3',
            'building_number' => 'sometimes|string|max:5',
            'house_number' => 'sometimes|string|max:5',

            'iban' => ['sometimes', new Iban()],
            'bank_name' => 'sometimes|string',
            'swift' => ['sometimes', new Bic()],


        ];
    }
}
