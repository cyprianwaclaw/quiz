<?php

namespace App\Models;

use App\Models\IFirmaApi\Contractor;
use App\Models\IFirmaApi\InvoiceDomestic;
use App\Models\IFirmaApi\Item;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    public static function generate(Payment $payment): void
    {
        $planSubscription = $payment->planSubscription()->with(['subscriber','subscriber.company'])->firstOrFail();
        $user = $planSubscription->subscriber;
        $plan = $planSubscription->plan;
        $invoice = new \App\Http\Controllers\IFirmaApi\Invoice();
        $userName = $user->name . ' ' . $user->surname;
        $userNip = $user->company->nip;
        $userAddress = $user->company->address;
        $contractor = new Contractor($userName,$userNip,$userAddress->street,$userAddress->postcode,$userAddress->city,$user->email);
        $invoiceDomestic = new InvoiceDomestic($userNip, Carbon::now()->format("Y-m-d"), 7);
        $invoiceDomestic->addItem( new Item($plan->name, $plan->price,1));
        $invoiceDomestic->setContractor($contractor);
        $response = $invoice->add( $invoiceDomestic );
        $payment->ifirma_invoice_id = $response->get('Identyfikator');
        $payment->save();
    }

    public static function getAsPDF(Payment $payment)
    {
        $invoice = new \App\Http\Controllers\IFirmaApi\Invoice();
        return $invoice->getAsPdf($payment->ifirma_invoice_id);
    }
}
