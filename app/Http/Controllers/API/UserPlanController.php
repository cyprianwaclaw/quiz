<?php

namespace App\Http\Controllers\API;

use App\Enums\PaymentStatus;
use App\Events\Subscribed;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\PlanSubscription;
use App\Models\User;
use Carbon\Carbon;
use Devpark\Transfers24\Exceptions\RequestException;
use Devpark\Transfers24\Exceptions\RequestExecutionException;
use Devpark\Transfers24\Requests\Transfers24;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserPlanController extends APIController
{
    private Transfers24 $transfers24;

    public function __construct(Transfers24 $transfers24)
    {
        $this->transfers24 = $transfers24;
    }

    /**
     * Subscribe premium
     * @group Payments
     *
     * @bodyParam plan integer required The ID of the plan. Example: 2
     * @responseFile 200 scenario="Success" storage/api-docs/responses/payments/subscribe.200.json
     * @responseFile 404 scenario="Plan not available" storage/api-docs/responses/payments/subscribe.404.json
     * @responseFile 404 scenario="Plan not found" storage/api-docs/responses/payments/subscribe.422.json
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function buyPlan(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate(['plan' => 'required|integer|exists:App\Models\Plan,id']);
        $plan = Plan::findOrFail($request->input('plan'));
        if ($plan->price > 0) {
            $planSubscription = auth()->user()->newPlanSubscription('mertrtretain', $plan);

            // $planSubscription = 3;

            // $planSubscription->ends_at = now();
            // $planSubscription->save();
            // // return $this->$planSubscription;
            // Log::error('Błąd transakcji', ['error' =>   "payment"]);

            return $this->paymentTransaction($planSubscription, $plan);
        } else {
            return $this->sendError('Ten plan jest niedostępny');
        }
    }



    public function setUserPlan($user, $plan)
    {
    }

    /**
     * Return the user has a premium
     * @group Operation about user
     *
     * @queryParam user_id int Specify user id to get his plans Example: null
     *
     * @response status=200 scenario="Success" true or false
     *
     * @param Request $request
     * @return bool
     */
    public function userHasPremium(Request $request)
    {
        if (!$request->input('user_id'))
            $user = auth()->user();
        else
            $user = User::findOrFail($request->input('user_id'));
        return json_encode([
            'has_premium' => (bool)$user->hasPremium(),
            'premium_end' => $user->activePlanSubscriptions()->pluck('ends_at')->first()
        ]);
    }

    /**
     * Get user plan
     * @group Operation about user
     *
     * @queryParam user_id int Specify user id to get his plans Example: null
     *
     * @responseFile status=200 scenario="Success" storage/api-docs/responses/users/getPlan.200.json
     * @responseFile status=401 scenario="Unauthenticated" storage/api-docs/responses/401.json
     *
     * @param Request $request
     * @return array|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Relations\MorphMany|object
     */
    public function getUserPlan(Request $request)
    {
        if (!$request->input('user_id'))
            $user = auth()->user();
        else
            $user = User::findOrFail($request->input('user_id'));
        return response()->json((bool)$user->activePlanSubscriptions()->pluck('name')->count());
    }

    private function paymentTransaction(PlanSubscription $planSubscription, Plan $plan)
    {
        $payment = new Payment();
        $payment->plan_subscription_id = $planSubscription->id;
        // $payment->plan_subscription_id = $planSubscription;
        Log::error('$payment', ['error' => $payment]);

        try {
            $response = $this->transfers24
                ->setEmail(\Auth::user()->email)
                ->setAmount($planSubscription->plan->price)
                ->init();
            if ($response->isSuccess()) {
                $payment->status = PaymentStatus::IN_PROGRESS;
                $payment->session_id = $response->getSessionId();
                $payment->save();
                // save registration parameters in payment object
                return $this->sendResponse($this->transfers24->execute($response->getToken()));
            } else {
                $payment->status = PaymentStatus::FAIL;
                $payment->error_code = $response->getErrorCode();
                $payment->error_description = json_encode($response->getErrorDescription());
                $payment->save();
                return $this->sendError('Błąd__');
                Log::error('Błąd transakcji', ['error' =>   $payment]);

            }
        } catch (RequestException | RequestExecutionException $e) {
            Log::error('Błąd transakcji', ['error' => $e]);
            return $this->sendError('Błąd transakcji');
        }
    }

    // private function paymentTransaction(PlanSubscription $planSubscription, Plan $plan)
    // {
    //     $payment = new Payment();
    //     $payment->plan_subscription_id = $planSubscription->id;  // Użyj ID subskrypcji

    //     try {
    //         $response = $this->transfers24
    //         ->setEmail(\Auth::user()->email)
    //         ->setAmount($planSubscription->plan->price)
    //         ->init();

    //         if ($response->isSuccess()) {
    //             // Płatność zakończona sukcesem, przydziel plan premium
    //             $payment->status = PaymentStatus::SUCCESS;
    //             $payment->session_id = $response->getSessionId();
    //             $payment->save();

    //             // Przydziel plan premium
    //             $this->setUserPlan(auth()->user(), $plan);

    //             return $this->sendResponse($this->transfers24->execute($response->getToken()));
    //         } else {
    //             $payment->status = PaymentStatus::FAIL;
    //             $payment->error_code = $response->getErrorCode();
    //             $payment->error_description = json_encode($response->getErrorDescription());
    //             $payment->save();
    //             return $this->sendError('Błąd__');
    //             Log::error('Błąd transakcji', ['error' =>   $payment]);
    //         }
    //     } catch (RequestException | RequestExecutionException $e) {
    //         Log::error('Błąd transakcji', ['error' => $e]);
    //         return $this->sendError('Błąd transakcji');
    //     }
    // }

    public function givePremium(Request $request)
    {
        $days = (int)$request->input('days');
        $user = User::findOrFail($request->input('user_id'));

        $plan = Plan::findOrFail(2);
        if ($plan->price > 0) {
            $planSubscription = $user->newPlanSubscription('maEWWEWQEWQEWin', $plan);
            // $planSubscription->ends_at = Carbon::now()->addMonth(); // Dodanie miesiąca
            $planSubscription->ends_at = Carbon::now()->addDays($days);
            $planSubscription->save();

            return $this->sendResponse('Premium ważne do: ' . Carbon::now()->addDays($days));
        } else {
            return $this->sendError('Ten plan jest niedostępny');
        }
    }
}
