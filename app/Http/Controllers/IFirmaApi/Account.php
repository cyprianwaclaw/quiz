<?php

namespace App\Http\Controllers\IFirmaApi;

class Account extends IFirmaApi {

    public function __construct($login = null, $key = null) {
        if ($login === null)
            $login = config('app.IFIRMA_API_LOGIN');
        if ($key === null)
            $key = config('app.IFIRMA_API_ABONENT_KEY');
        $this->apiKeyName = 'abonent';
        parent::__construct($login, $key);
    }

    public function getAccountancyMonth() {

        return $this->request( $this->apiKeyName . '/miesiacksiegowy' );
    }

    public function changeAccountancyMonth($next = true, $importDataFromPrevYear = true) {

        return $this->request( $this->apiKeyName . '/miesiacksiegowy', self::REQUEST_TYPE_PUT, [
            'MiesiacKsiegowy' => ( $next ? 'NAST' : 'POPRZ' ),
            'PrzeniesDaneZPoprzedniegoRoku' => $importDataFromPrevYear
        ] );
    }

    public function getLimit() {
        return $this->request( $this->apiKeyName . '/limit' );
    }

}
