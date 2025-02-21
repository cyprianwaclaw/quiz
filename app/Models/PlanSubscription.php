<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class PlanSubscription extends \Rinvex\Subscriptions\Models\PlanSubscription
{

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
