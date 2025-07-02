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
use Rinvex\Subscriptions\Models\Plan;

class PaymentController extends APIController
{
    private Transfers24 $transfers24;

    // private function giveUserPremium(User $user)
    // {
    //     // Sprawdź, czy użytkownik już ma aktywną subskrypcję
    //     $existingSubscription = $user->planSubscriptions()
    //         ->whereNull('canceled_at')
    //         ->where(function ($query) {
    //             $query->whereNull('ends_at')->orWhere('ends_at', '>', now());
    //         })->first();

    //     if ($existingSubscription) {
    //         // Jeśli użytkownik ma aktywną subskrypcję, przedłuż ją o miesiąc
    //         $existingSubscription->update([
    //             'ends_at' => now()->addMonth()
    //         ]);
    //     } else {
    //         // Jeśli użytkownik nie ma subskrypcji, twórz nową
    //         $user->planSubscriptions()->create([
    //             'plan_id' => 1, // ID planu premium w bazie danych
    //             'starts_at' => now(),
    //             'ends_at' => now()->addMonth(),
    //         ]);
    //     }
    // }

    // private function giveUserPremium(User $user)
    // {
    //     // Sprawdź, czy użytkownik nie ma już aktywnej subskrypcji
    //     if ($user->hasPremium()) {
    //         return;
    //     }

    //     // Dodaj subskrypcję na 30 dni
    //     $user->newPlanSubscription('premium', 3) // 1 = ID planu premium
    //         ->startsAt(now())
    //         ->endsAt(now()->addMonth())
    //         ->save();
    // }

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

    // public function status(Request $request)
    // {
    //     \Log::info('Webhook received', $request->all());

    //     $response = $this->transfers24->receive($request);
    //     \Log::info('Response from Transfers24', [
    //         'session_id' => $response->getSessionId(),
    //         'status' => $response->isSuccess()
    //     ]);

    //     $payment = Payment::where('session_id', $response->getSessionId())->first();

    //     if (!$payment) {
    //         \Log::error('Payment not found for session_id', ['session_id' => $response->getSessionId()]);
    //         return response()->json(['error' => 'Payment not found'], 404);
    //     }

    //     Invoice::generate($payment);

    //     $payment->status = PaymentStatus::SUCCESS;
    //     $subscription = PlanSubscription::findOrFail($payment->plan_subscription_id);
    //     $subscription->renew();
    //     Invoice::generate($payment);
    //     event(new Subscribed(User::findOrFail($subscription->subscriber_id), $subscription->plan));

    //     $payment->save();
    //     \Log::info('Payment status updated to SUCCESS', ['payment_id' => $payment->id]);

    //     return response()->json(['message' => 'Payment status updated']);
    // }



    /**
     * Trying change payment status to SUCCESS,
     */

// public function status(Request $request)
// {
//     // Pobierz ID płatności z requestu (zakładam, że masz weryfikację Przelewy24)
//     $paymentId = $request->input('payment_id');

//     // Znajdź płatność w bazie danych
//     $payment = Payment::where('transaction_id', $paymentId)->first();

//     // Sprawdź, czy płatność istnieje i jest już oznaczona jako SUKCES
//     if (!$payment || $payment->status === 'SUCCESS') {
//         return response()->json(['message' => 'Płatność już przetworzona'], 200);
//     }

//     // Zmień status płatności na sukces
//     $payment->update(['status' => 'SUCCESS']);

//     // Pobierz użytkownika, który dokonał płatności
//     $user = $payment->user; // Jeśli w `Payment` masz relację `user()`

//     if (!$user) {
//         return response()->json(['error' => 'Nie znaleziono użytkownika'], 404);
//     }

//     // Aktywuj subskrypcję premium
//     $this->giveUserPremium($user);

//     return response()->json(['message' => 'Płatność zaakceptowana, subskrypcja aktywowana']);
// }

    /**
     * Trying change payment status to SUCCESS,
     */

    public function status(Request $request)
    {
        Log::info('Webhook received - full request', $request->all());

        try {
            // Odbierz i zweryfikuj webhook (implementacja w transfers24)
            $response = $this->transfers24->receive($request);

            $sessionId = $response->getSessionId();
            $paymentStatusFromWebhook = $response->getStatus(); // zakładam, że jest metoda getStatus()

            Log::info('Parsed sessionId and status from webhook', [
                'session_id' => $sessionId,
                'status' => $paymentStatusFromWebhook,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to parse webhook data', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid webhook data'], 400);
        }

        // Szukamy płatności po session_id
        $payment = Payment::where('session_id', $sessionId)->first();

        if (!$payment) {
            Log::error('Payment not found for session_id', ['session_id' => $sessionId]);
            return response()->json(['error' => 'Payment not found'], 404);
        }

        Log::info('Payment found', ['payment_id' => $payment->id, 'current_status' => $payment->status]);

        // Sprawdzamy status płatności z webhooka — jeśli jest sukces, zmieniamy status
        if ($paymentStatusFromWebhook === 'success' || $paymentStatusFromWebhook === PaymentStatus::SUCCESS) {
            if ($payment->status === PaymentStatus::SUCCESS) {
                Log::warning('Payment already processed as SUCCESS', ['payment_id' => $payment->id]);
                return response()->json(['message' => 'Payment already processed']);
            }

            try {
                Invoice::generate($payment);
                Log::info('Invoice generated', ['payment_id' => $payment->id]);
            } catch (\Exception $e) {
                Log::error('Invoice generation failed', ['payment_id' => $payment->id, 'error' => $e->getMessage()]);
                // Można tu zdecydować, czy przerywać działanie, czy iść dalej
            }

            $payment->status = PaymentStatus::SUCCESS;
            $payment->save();

            Log::info('Payment status updated to SUCCESS', ['payment_id' => $payment->id]);

            $user = $payment->user;
            $plan = Plan::find(3);

            if (!$plan) {
                Log::error('Plan ID 3 not found');
                return response()->json(['error' => 'Plan not found'], 500);
            }

            try {
                $subscription = $user
                    ->newPlanSubscription('premium', $plan)
                    ->create([
                        'starts_at' => now(),
                        'ends_at' => now()->addMonth(),
                    ]);
                Log::info('Subscription created', [
                    'subscription_id' => $subscription->id,
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                ]);
            } catch (\Exception $e) {
                Log::error('Subscription creation failed', ['error' => $e->getMessage()]);
                return response()->json(['error' => 'Subscription creation failed'], 500);
            }

            return response()->json(['message' => 'Plan activated', 'plan_id' => $plan->id]);
        }

        // Jeśli status płatności z webhooka to coś innego niż sukces:
        Log::info('Payment status from webhook is not success, no changes made', [
            'payment_id' => $payment->id,
            'webhook_status' => $paymentStatusFromWebhook,
        ]);

        return response()->json(['message' => 'Payment status not updated, current status: ' . $payment->status]);
    }


    public function statusOld(Request $request)
    {
        Log::info('Webhook received', $request->all());

        $response = $this->transfers24->receive($request);
        $sessionId = $response->getSessionId();

        $payment = Payment::where('session_id', $sessionId)->first();

        if (!$payment) {
            Log::error('Payment not found', ['session_id' => $sessionId]);
            return response()->json(['error' => 'Payment not found'], 404);
        }

        // Unikamy wielokrotnego przetwarzania
        if ($payment->status === PaymentStatus::SUCCESS) {
            return response()->json(['message' => 'Payment already processed']);
        }
        Invoice::generate($payment);
        $payment->status = PaymentStatus::SUCCESS;
        $payment->save();

        $user = $payment->user;

        // Wymuszony plan o ID = 3
        $plan = Plan::find(3);

        if (!$plan) {
            Log::error('Plan ID 3 not found');
            return response()->json(['error' => 'Plan not found'], 500);
        }

        $subscription = $user
            ->newPlanSubscription('premium', $plan)
            ->create([
                'starts_at' => now(),
                'ends_at' => now()->addMonth(),
            ]);

        Log::info('Subscription created', [
            'subscription_id' => $subscription->id,
            'user_id' => $user->id,
            'plan_id' => $plan->id,
        ]);

        return response()->json(['message' => 'Plan activated', 'plan_id' => $plan->id]);
    }

    // public function status1(Request $request)
    // {
    //     \Log::info('Webhook received', $request->all());

    //     $response = $this->transfers24->receive($request);
    //     \Log::info('Response from Transfers24', [
    //         'session_id' => $response->getSessionId(),
    //         'status' => $response->isSuccess()
    //     ]);

    //     $payment = Payment::where('session_id', $response->getSessionId())->first();

    //     if (!$payment) {
    //         \Log::error('Payment not found for session_id', ['session_id' => $response->getSessionId()]);
    //         return response()->json(['error' => 'Payment not found'], 404);
    //     }

    //     \Log::info('Payment found', ['payment_id' => $payment->id]);

    //     try {
    //         \Log::info('Generating invoice before status change');
    //         Invoice::generate($payment);
    //         \Log::info('Invoice generated successfully');
    //     } catch (\Exception $e) {
    //         \Log::error('Error generating invoice', ['message' => $e->getMessage(), 'payment_id' => $payment->id]);
    //     }

    //     // 🔴 Sprawdź, czy kod dochodzi do tego miejsca
    //     \Log::info('Updating payment status', ['payment_id' => $payment->id]);
    //     $payment->status = PaymentStatus::SUCCESS;
    //     $payment->save();

    //     \Log::info('Payment status updated successfully', ['payment_id' => $payment->id, 'status' => $payment->status]);
    //     // Pobierz użytkownika, który dokonał płatności
    //     // $user = $payment->user;
    //     // \Log::info('Payment usery', ['user' => $user]);

    //     // $this->giveUserPremium($user);

    //     return response()->json(['message' => 'Payment status updated']);
    // }
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


    // public function indexTest(Request $request)
    // {
    //     $user = auth()->user();
    //     $perPage = 4;

    //     // Pobierz wszystkie ID subskrypcji użytkownika
    //     $subscriptionIds = $user->planSubscriptions()->pluck('id');

    //     // Pobierz płatności powiązane z tymi subskrypcjami
    //     $payments = Payment::whereIn('plan_subscription_id', $subscriptionIds)
    //     ->select(
    //         'payments.id as payment_id',
    //         'payments.status',
    //         'payments.error_code',
    //         'payments.error_description',
    //         'payments.session_id',
    //         // 'payments.plan_subscription_id',
    //         'payments.ifirma_invoice_id',
    //         'payments.created_at',
    //         'payments.updated_at'
    //     )
    //         ->orderBy('payments.created_at', 'desc')
    //         ->paginate($perPage);

    //     // Mapowanie danych
    //     $payments->getCollection()->transform(function ($payment) {
    //         return [
    //             'payment_id' => $payment->payment_id,
    //             'status' => $payment->status,
    //             'status_text' => PayoutStatus::TYPES_WITH_TEXT_PAYMENTS[$payment->status] ?? 'Nieznany status',
    //             'error_code' => $payment->error_code,
    //             'error_description' => $payment->error_description,
    //             // 'plan_subscription_id' => $payment->plan_subscription_id,
    //             'ifirma_invoice_id' => $payment->ifirma_invoice_id,
    //             'date' => optional($payment->created_at)->format('d.m.Y'),
    //             // 'price' => 40,
    //         ];
    //     });

    //     return response()->json([
    //         'data' => $payments,
    //         'pagination' => [
    //             'total' => $payments->total(),
    //             'per_page' => $payments->perPage(),
    //             'current_page' => $payments->currentPage(),
    //             'last_page' => $payments->lastPage()
    //         ],
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
                // 'payments.plan_subscription_id',
                'payments.ifirma_invoice_id',
                'payments.created_at',
                'payments.updated_at'
            )
            // ->with(['planSubscription' => function ($query) {
            //     $query->select('id as plan_subscription_id', 'subscriber_id as laravel_through_key');
            // }])
            // ->whereHas('planSubscription', function ($query) use ($user) {
            //     $query->where('subscriber_id', $user->id)->whereNull('deleted_at');
            // })
            // ->orderBy('payments.created_at', 'asc')
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
                // 'plan_subscription_id' => $payment->plan_subscription_id,
                'ifirma_invoice_id' => $payment->ifirma_invoice_id,
                'date' => optional($payment->created_at)->format('d.m.Y'),
                // 'updated_at' => optional($payment->updated_at)->format('d.m.Y'),
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
