<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Plan
 *
 * @property int $id
 * @property string $slug
 * @property array $name
 * @property array|null $description
 * @property bool $is_active
 * @property float $price
 * @property float $signup_fee
 * @property string $currency
 * @property int $trial_period
 * @property string $trial_interval
 * @property int $invoice_period
 * @property string $invoice_interval
 * @property int $grace_period
 * @property string $grace_interval
 * @property int|null $prorate_day
 * @property int|null $prorate_period
 * @property int|null $prorate_extend_due
 * @property int|null $active_subscribers_limit
 * @property int $sort_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Rinvex\Subscriptions\Models\PlanFeature[] $features
 * @property-read int|null $features_count
 * @property-read array $translations
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PlanSubscription[] $subscriptions
 * @property-read int|null $subscriptions_count
 * @method static \Illuminate\Database\Eloquent\Builder|Plan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Plan newQuery()
 * @method static \Illuminate\Database\Query\Builder|Plan onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Plan ordered(string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|Plan query()
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereActiveSubscribersLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereGraceInterval($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereGracePeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereInvoiceInterval($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereInvoicePeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereProrateDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereProrateExtendDue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereProratePeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereSignupFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereTrialInterval($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereTrialPeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Plan withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Plan withoutTrashed()
 * @mixin \Eloquent
 */
class Plan extends \Rinvex\Subscriptions\Models\Plan
{
    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->mergeRules([

            'trial_interval' => 'sometimes|in:hour,day,week,month,year',
            'invoice_interval' => 'sometimes|in:hour,day,week,month,year',
            'grace_interval' => 'sometimes|in:hour,day,week,month,year',
        ]);

        parent::__construct($attributes);
    }
}
