<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Payment
 *
 * @property int                 $id
 * @property PaymentStatus       $status
 * @property int                 $error_code
 * @property string              $error_description
 * @property string              $session_id
 * @property int                 $plan_subscription_id
 * @property int                 $ifirma_invoice_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|Payment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereErrorCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereErrorDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment wherePlanSubscriptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereUpdatedAt($value)
 */
class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        "status",   
        'error_code',
        'error_description',
        'session_id',
        'plan_subscription_id',
    ];

    public function planSubscription()
    {
        return $this->belongsTo(PlanSubscription::class);
    }

    public function downloadInvoice()
    {
        return Invoice::getAsPDF($this);
    }

    public function scopeSuccessful(Builder $query)
    {
        $query->where('status', PaymentStatus::SUCCESS);
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
