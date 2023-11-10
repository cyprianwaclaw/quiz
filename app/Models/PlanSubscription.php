<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\PlanSubscription
 *
 * @property int $id
 * @property string $subscriber_type
 * @property int $subscriber_id
 * @property int $plan_id
 * @property string $slug
 * @property array $name
 * @property array|null $description
 * @property \Illuminate\Support\Carbon|null $trial_ends_at
 * @property \Illuminate\Support\Carbon|null $starts_at
 * @property \Illuminate\Support\Carbon|null $ends_at
 * @property \Illuminate\Support\Carbon|null $cancels_at
 * @property \Illuminate\Support\Carbon|null $canceled_at
 * @property string|null $timezone
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read array $translations
 * @property-read \App\Models\Payment|null $payment
 * @property-read \App\Models\Plan $plan
 * @property-read Model|\Eloquent $subscriber
 * @property-read \Illuminate\Database\Eloquent\Collection|\Rinvex\Subscriptions\Models\PlanSubscriptionUsage[] $usage
 * @property-read int|null $usage_count
 * @method static Builder|PlanSubscription byPlanId(int $planId)
 * @method static Builder|PlanSubscription findEndedPeriod()
 * @method static Builder|PlanSubscription findEndedTrial()
 * @method static Builder|PlanSubscription findEndingPeriod(int $dayRange = 3)
 * @method static Builder|PlanSubscription findEndingTrial(int $dayRange = 3)
 * @method static \Illuminate\Database\Eloquent\Builder|PlanSubscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PlanSubscription newQuery()
 * @method static Builder|PlanSubscription ofSubscriber(\Illuminate\Database\Eloquent\Model $subscriber)
 * @method static \Illuminate\Database\Query\Builder|PlanSubscription onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PlanSubscription query()
 * @method static \Illuminate\Database\Eloquent\Builder|PlanSubscription whereCanceledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlanSubscription whereCancelsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlanSubscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlanSubscription whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlanSubscription whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlanSubscription whereEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlanSubscription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlanSubscription whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlanSubscription wherePlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlanSubscription whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlanSubscription whereStartsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlanSubscription whereSubscriberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlanSubscription whereSubscriberType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlanSubscription whereTimezone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlanSubscription whereTrialEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlanSubscription whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|PlanSubscription withTrashed()
 * @method static \Illuminate\Database\Query\Builder|PlanSubscription withoutTrashed()
 * @mixin \Eloquent
 */
class PlanSubscription extends \Rinvex\Subscriptions\Models\PlanSubscription
{

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
