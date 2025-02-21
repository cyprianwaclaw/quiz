<?php

namespace App\Http\Controllers\API;

use App\Enums\PaymentStatus;
use App\Events\Subscribed;
use App\Http\Resources\PaymentResource;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PlanSubscription;
use App\Models\User;
use Devpark\Transfers24\Requests\Transfers24;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use function event;
use Illuminate\Support\Facades\Log;
use App\Enums\PayoutStatus;

class PaymentController extends APIController
{
    private Transfers24 $transfers24;

    public function __construct(Transfers24 $transfers24)
    {
        $this->transfers24 = $transfers24;
    }

    // public function status(Request $request)
    // {
    //     $response = $this->transfers24->receive($request);
    //     $payment = Payment::where('session_id', $response->getSessionId())->firstOrFail();
    //     Invoice::generate($payment);

    //     // if ($response->isSuccess()) {
    //     $payment->status = PaymentStatus::SUCCESS;
    //     $subscription = PlanSubscription::findOrFail($payment->plan_subscription_id);
    //     $subscription->renew();
    //     Invoice::generate($payment);
    //     event(new Subscribed(User::findOrFail($subscription->subscriber_id), $subscription->plan));
    //     // } else {
    //     //     $payment->status = PaymentStatus::FAIL;
    //     //     $payment->error_code = $response->getErrorCode();
    //     //     $payment->error_description = json_encode($response->getErrorDescription());
    //     // }
    //     $payment->save();
    // }

    public function status(Request $request)
    {
        \Log::info('Webhook received', $request->all());

        $response = $this->transfers24->receive($request);
        \Log::info('Response from Transfers24', [
            'session_id' => $response->getSessionId(),
            'status' => $response->isSuccess()
        ]);

        $payment = Payment::where('session_id', $response->getSessionId())->first();

        if (!$payment) {
            \Log::error('Payment not found for session_id', ['session_id' => $response->getSessionId()]);
            return response()->json(['error' => 'Payment not found'], 404);
        }

        Invoice::generate($payment);

        $payment->status = PaymentStatus::SUCCESS;
        $subscription = PlanSubscription::findOrFail($payment->plan_subscription_id);
        $subscription->renew();
        Invoice::generate($payment);
        event(new Subscribed(User::findOrFail($subscription->subscriber_id), $subscription->plan));

        $payment->save();
        \Log::info('Payment status updated to SUCCESS', ['payment_id' => $payment->id]);

        return response()->json(['message' => 'Payment status updated']);
    }

    /**
     * Download invoice by payment ID
     *
     * Download invoice as PDF
     *
     * @group Payments
     * @urlParam payment integer required The ID of the invoice. Example: 1
     * @response 200 scenario="Invoice fetched" [Content PDF]
     * @responseFile 404 scenario="Invoice not found" storage/api-docs/responses/resource.404.json
     *
     * @param $id
     * @return \App\Models\IFirmaApi\Response|bool|string|void
     */
    public function downloadInvoice(int $invoice_id)
    {
        $payment = Payment::where('ifirma_invoice_id', $invoice_id)->firstOrFail();
        $this->authorize('download', $payment);
        return $payment->downloadInvoice();
    }


    /**
     * Get list of payment objects
     *
     * Also return header response `X-Total-Count` containing the number of fetched objects.
     *
     * @group Payments
     * @responseFile status=200 scenario="Invoice fetched" storage/api-docs/responses/payments/index.200.json
     * @responseFile status=401 scenario="Unauthenticated" storage/api-docs/responses/401.json
     */
    //     public function index(Request $request)
    //     {
    // $user= auth()->user();
    // $perPage = 15;
    // $page = $request->input('page', 1);
    // $payments = $user->payments()->with(['planSubscription.plan'])->orderBy('created_at', 'desc')->paginate($perPage, ['*'], 'page', $page);;
    //         // $query = $user->payouts()->orderBy('created_at', 'desc')->paginate($perPage, ['*'], 'page', $page);
    //         // $collection = auth()->user()->payments()->with(['planSubscription.plan'])->get();

    //         // $collection = auth()->user()->payments()->successful()->with(['planSubscription.plan'])->get();
    //         return response([
    //             'success' => true,
    //             'data' => PaymentResource::collection( $payments),
    //             'message' => 'Objects fetched',
    //             // 'count' => $collection->count()
    //         ],
    //             200, [
    //                 'X-Total-Count' => $payments ->count()
    //             ]
    //         );
    //     }
    // public function index(Request $request)
    // {
    //     $user = auth()->user();
    //     $perPage = 15;
    //     $page = $request->input('page', 1);
    //     $payments = $user->payments()->with(['planSubscription.plan'])->orderBy('created_at', 'desc')->paginate($perPage, ['status'], 'page', $page);

    //     return response([
    //         'success' => true,
    //         // 'data' => PaymentResource::collection($payments),
    //         'data'=>$payments,
    //         'message' => 'Objects fetched',
    //     ], 200, [
    //         'X-Total-Count' => $payments->total(),
    //     ]);
    // }

    public function index(Request $request)
    {
        $user = auth()->user();
        $perPage = 4;
        $page = $request->input('page', 1);

        // Pobranie płatności
        $payments = $user->payments()
            ->select(
                'payments.id as payment_id',
                'payments.status',
                'payments.error_code',
                'payments.error_description',
                'payments.session_id',
                'payments.plan_subscription_id',
                'payments.ifirma_invoice_id',
                'payments.created_at',
                'payments.updated_at'
            )
            ->with(['planSubscription' => function ($query) {
                $query->select('id as plan_subscription_id', 'subscriber_id as laravel_through_key');
            }])
            ->whereHas('planSubscription', function ($query) use ($user) {
                $query->where('subscriber_id', $user->id)->whereNull('deleted_at');
            })
            ->orderBy('payments.created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        // Konwersja kolekcji na zmodyfikowane dane przed przypisaniem do paginacji
        $modifiedPayments = $payments->getCollection()->map(function ($payment) {
            return [
                'payment_id' => $payment->payment_id,
                'status' => $payment->status,
                'status_text' => PayoutStatus::TYPES_WITH_TEXT_PAYMENTS[$payment->status] ?? 'Nieznany status',
                'error_code' => $payment->error_code,
                'error_description' => $payment->error_description,
                // 'session_id' => $payment->session_id,
                'plan_subscription_id' => $payment->plan_subscription_id,
                'ifirma_invoice_id' => $payment->ifirma_invoice_id,
                'date' => optional($payment->created_at)->format('d.m.Y'),
                'updated_at' => optional($payment->updated_at)->format('d.m.Y'),
                'price' => 40,
            ];
        });

        // Zamiana zmodyfikowanej kolekcji w paginatorze
        $payments->setCollection($modifiedPayments);

        return response()->json([
            // 'success' => true,
            'data' => $payments,
            'pagination' => [
                'total' => $payments->total(),
                'per_page' => $payments->perPage(),
                'current_page' => $payments->currentPage(),
                'last_page' => $payments->lastPage()
            ],
            // 'message' => 'Objects fetched',
        ], 200, [
            'X-Total-Count' => $payments->total(),
        ]);
    }

    // public function index(Request $request)
    // {
    //     $user = auth()->user();
    //     $perPage = 15;
    //     $page = $request->input('page', 1);

    //     $payments = $user->payments()
    //     ->select('payments.id as payment_id', 'payments.status', 'payments.error_code', 'payments.error_description', 'payments.session_id', 'payments.plan_subscription_id', 'payments.ifirma_invoice_id', 'payments.created_at', 'payments.updated_at')
    //     ->with(['planSubscription' => function ($query) {
    //         $query->select('plan_subscriptions.id as plan_subscription_id', 'plan_subscriptions.subscriber_id as laravel_through_key');
    //     }])
    //         ->join('plan_subscriptions as ps1', 'ps1.id', '=', 'payments.plan_subscription_id')
    //         ->join('plan_subscriptions as ps2', 'ps2.id', '=', 'payments.plan_subscription_id')
    //         ->where('ps1.subscriber_id', $user->id)
    //         ->whereNull('ps1.deleted_at')
    //         ->orderBy('payments.created_at', 'desc')
    //         ->paginate($perPage, ['*'], 'page', $page);


    //     return response([
    //         'success' => true,
    //         'data' => $payments,
    //         'message' => 'Objects fetched',
    //     ], 200, [
    //         'X-Total-Count' => $payments->total(),
    //     ]);
    // }

}
