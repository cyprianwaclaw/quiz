<?php

namespace App\Http\Controllers\IFirmaApi;

use App\Models\IFirmaApi\InvoiceBase;
use App\Models\IFirmaApi\InvoiceDomestic;

class Contractor extends IFirmaApi {

    public \App\Models\IFirmaApi\Contractor $contractor;
    public function __construct($login = null, $key = null) {
        if ($login === null)
            $login = config('app.IFIRMA_API_LOGIN');
        if ($key === null)
            $key = config('app.IFIRMA_API_FAKTURA_KEY');
        $this->apiKeyName = 'faktura';
        parent::__construct($login, $key);

    }

    public function addContractor(): \App\Models\IFirmaApi\Response|bool|string
    {
        return $this->request('kontrahenci', self::REQUEST_TYPE_POST, $this->contractor->toJSON());
    }


}
