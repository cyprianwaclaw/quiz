<?php

namespace App\Http\Controllers;

use App\Enums\PayoutStatus;
use App\Models\Payout;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\In;

class PayoutController extends Controller
{
    public function show(Payout $payout)
    {
        $payout = Payout::where('id', $payout->id)->with(['user','user.financial','user.address'])->first();
        return view('payouts.show')->with([
            'payout' => $payout,
            'user' => $payout->user,
            'address' => $payout->user->address,
            'financial' => $payout->user->financial,
            // 'statuses' => $payout->user->status,
            'statuses' => PayoutStatus::TYPES_WITH_TEXT,
        ]);
    }

    public function setStatus(Request $request, Payout $payout)
    {
        $validated = $request->validate(['status' => ['required', new In(PayoutStatus::TYPES)]]);
        $payout->status = $validated['status'];
        $payout->save();
        return redirect()->back()->with('success','Zaktualizowano pomyślnie');
    }
}