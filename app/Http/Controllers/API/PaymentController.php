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

    public function status(Request $request)
    {
        try {
            $response = $this->transfers24->receive($request);
            Log::info('PaymentController: Transfers24 response', ['response' => $response]);
            $payment = Payment::where('session_id', $response->getSessionId())->firstOrFail();
        } catch (\Exception $e) {
            Log::error('PaymentController: Transfers24 receive error', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Błąd płatności: ' . $e->getMessage()], 500);
        }

        try {
            Invoice::generate($payment);
            Log::info('PaymentController: Faktura wygenerowana', ['payment_id' => $payment->id]);
        } catch (\Exception $e) {
            Log::error('PaymentController: Błąd generowania faktury', ['error' => $e->getMessage(), 'payment_id' => $payment->id]);
        }

        try {
            $payment->status = PaymentStatus::SUCCESS;
            Log::info('PaymentController: plan_subscription_id przed pobraniem subskrypcji', [
                'payment_id' => $payment->id,
                'plan_subscription_id' => $payment->plan_subscription_id
            ]);
            $subscription = PlanSubscription::findOrFail($payment->plan_subscription_id);
            $subscription->renew();
            Log::info('PaymentController: Subskrypcja odnowiona', ['subscription_id' => $subscription->id]);
            Invoice::generate($payment);
            event(new Subscribed(User::findOrFail($subscription->subscriber_id), $subscription->plan));
            $payment->save();
            Log::info('PaymentController: Payment zapisany jako SUCCESS', ['payment_id' => $payment->id]);
        } catch (\Exception $e) {
            Log::error('PaymentController: Błąd zmiany pakietu/subskrypcji', ['error' => $e->getMessage(), 'payment_id' => $payment->id]);
            return response()->json(['error' => 'Błąd subskrypcji: ' . $e->getMessage()], 500);
        }
    }

    public function status(Request $request)
    {
        try {
            $response = $this->transfers24->receive($request);
            Log::info('PaymentController: Transfers24 response', ['response' => $response]);
            $payment = Payment::where('session_id', $response->getSessionId())->firstOrFail();

            try {
                Invoice::generate($payment);
                Log::info('PaymentController: Faktura wygenerowana', ['payment_id' => $payment->id]);
            } catch (\Exception $e) {
                Log::error('PaymentController: Błąd generowania faktury', ['error' => $e->getMessage(), 'payment_id' => $payment->id]);
            }

            $payment->status = PaymentStatus::SUCCESS;
            Log::info('PaymentController: plan_subscription_id przed pobraniem subskrypcji', [
                'payment_id' => $payment->id,
                'plan_subscription_id' => $payment->plan_subscription_id
            ]);
            $subscription = PlanSubscription::findOrFail($payment->plan_subscription_id);
            $subscription->renew();
            Log::info('PaymentController: Subskrypcja odnowiona', ['subscription_id' => $subscription->id]);
            Invoice::generate($payment);
            event(new Subscribed(User::findOrFail($subscription->subscriber_id), $subscription->plan));
            $payment->save();
            Log::info('PaymentController: Payment zapisany jako SUCCESS', ['payment_id' => $payment->id]);
            return response()->json(['message' => 'Payment status updated']);
        } catch (\Exception $e) {
            Log::error('PaymentController: Błąd statusu płatności', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Błąd statusu płatności: ' . $e->getMessage()], 500);
        }
    }


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


    // ...usunięto nieprawidłowe komentarze i zakomentowane duplikaty...
    //     }
    public function index(Request $request)
    {
        $user = auth()->user();
        $perPage = 15;
        $page = $request->input('page', 1);
        $payments = $user->payments()->with(['planSubscription.plan'])->orderBy('created_at', 'desc')->paginate($perPage, ['status'], 'page', $page);

        return response([
            'success' => true,
            // 'data' => PaymentResource::collection($payments),
            'data'=>$payments,
            'message' => 'Objects fetched',
        ], 200, [
            'X-Total-Count' => $payments->total(),
        ]);
    }


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
    //