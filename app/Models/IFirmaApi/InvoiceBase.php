<?php

namespace App\Models\IFirmaApi;

abstract class InvoiceBase extends Base {

    const PATH = '';
    const PAYMENT_PATH = '';

    public function addItem(Item $item) {

        if( ! isset($this->data['Pozycje']) ) {
            $this->data['Pozycje'] = [];
        }

        $this->data['Pozycje'][] = $item;
    }

    public function getItems()
    {
        return $this->data['Pozycje'];
    }

    public function setContractor(Contractor $contractor) {

        $this->data['Kontrahent'] = $contractor;
    }

}
