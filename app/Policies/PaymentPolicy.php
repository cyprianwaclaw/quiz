<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PlanSubscription;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaymentPolicy
{
    use HandlesAuthorization;

    public function download(User $user, Payment $payment)
    {
        $planSubscription = PlanSubscription::with('subscriber')->findOrFail($payment->plan_subscription_id);
        return $planSubscription->subscriber->id == $user->id;
    }
}
