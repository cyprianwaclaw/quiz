<?php

namespace App\Providers;

use App\Events\AnsweredQuestion;
use App\Events\QuizStarted;
use App\Events\Subscribed;
use App\Listeners\AddPointsAfterSubscribe;
use App\Listeners\GenerateInvoiceAfterSubscribe;
use App\Listeners\UpdateStatsAfterAnswerQuestion;
use App\Listeners\UpdateUserPointsAfterAnswerQuestion;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        Subscribed::class => [
            AddPointsAfterSubscribe::class,
        ],
        QuizStarted::class => [],
        AnsweredQuestion::class => [
//            UpdateUserPointsAfterAnswerQuestion::class,
            UpdateStatsAfterAnswerQuestion::class,
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
