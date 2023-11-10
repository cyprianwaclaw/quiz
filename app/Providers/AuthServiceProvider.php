<?php

namespace App\Providers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\SubmissionAnswer;
use App\Policies\PaymentPolicy;
use App\Policies\SubmissionAnswerPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        SubmissionAnswer::class => SubmissionAnswerPolicy::class,
        Payment::class => PaymentPolicy::class,

    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
