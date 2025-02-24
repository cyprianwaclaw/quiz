<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Events\Subscribed;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\PlanSubscription;
use Devpark\Transfers24\Exceptions\RequestException;
use Devpark\Transfers24\Exceptions\RequestExecutionException;
use Devpark\Transfers24\Requests\Transfers24;
use Illuminate\Http\Request;

class UserPlanController extends Controller
{
    private Transfers24 $transfers24;

    public function __construct(Transfers24 $transfers24)
    {
        $this->transfers24 = $transfers24;
    }


    public function buyPlan1(Request $request)
    {
        $plan = Plan::find($request->input('plan'));
        $subscription = auth()->user()->newPlanSubscription('main', $plan);
        $subscription->cancel(true);

        return $this->paymentTransaction($subscription, $plan);
//
//        return redirect(route('dashboard'));
    }
    public function buyPlan(Request $request)
    {
        $plan = Plan::findOrFail($request->input('plan'));

        // Tworzymy rekord płatności, ale jeszcze nie przypisujemy planu
        $payment = new Payment();
        $payment->user_id = auth()->id();
        $payment->plan_id = $plan->id;
        $payment->status = PaymentStatus::IN_PROGRESS;
        $payment->save();

        return $this->paymentTransaction($payment, $plan);
    }

    public function setUserPlan($user, $plan)
    {

    }

    private function paymentTransaction(PlanSubscription $plan_subscription, Plan $plan)
    {
        $payment = new Payment();
        $payment->plan_subscription_id = $plan_subscription->id;
        try {
            $response = $this->transfers24
                ->setEmail(\Auth::user()->email)
                ->setAmount($plan_subscription->plan->price)
                ->init();
            if($response->isSuccess())
            {
                // todo: brakuje identyfikatora uzytkownika przelewy24
                $payment->status = PaymentStatus::IN_PROGRESS;
                $payment->session_id = $response->getSessionId();
                $payment->save();
                // save registration parameters in payment object

                return redirect($this->transfers24->execute($response->getToken()));
            } else {
                $payment->status = PaymentStatus::FAIL;
                $payment->error_code = $response->getErrorCode();
                $payment->error_description = json_encode($response->getErrorDescription());
                $payment->save();
                return back()->with('warning', 'Błąd__');
            }
        } catch (RequestException|RequestExecutionException $e) {
                \Log::error('Błąd transakcji', ['error' => $e]);
                return back()->with('warning', 'Błąd');
        }
    }
}
