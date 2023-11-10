<?php

namespace App\Http\Controllers;

use App\Http\Controllers\IFirmaApi\Account;
use App\Http\Controllers\IFirmaApi\Invoice;
use App\Models\IFirmaApi\Contractor;
use App\Models\IFirmaApi\InvoiceDomestic;
use App\Models\IFirmaApi\Item;
use App\Models\Payout;
use App\Models\Plan;
use App\Models\Quiz;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        /**
         *  invitation link:
         *  route('register')?invitation=$token
         */

        return view('dashboard')->with([
            'plans' => Plan::where('is_active',1)->get(),
            'user_plan' => $user->activePlanSubscriptions(),
            'inactive_quizzes' => Quiz::inactive()->paginate('10',['*'],'quizzesPage'),
            'payouts' => Payout::with('user')->orderByDesc('id')->paginate(10, ['*'], 'payoutsPage'),
        ]);
    }
}
