<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlanSubscriptionUsageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create(config('rinvex.subscriptions.tables.plan_subscription_usage'), function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->references('id')->on(config('rinvex.subscriptions.tables.plan_subscriptions'))
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('feature_id')->references('id')->on(config('rinvex.subscriptions.tables.plan_features'))
                ->onDelete('cascade')->onUpdate('cascade');
            $table->smallInteger('used')->unsigned();
            $table->dateTime('valid_until')->nullable();
            $table->string('timezone')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['subscription_id', 'feature_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists(config('rinvex.subscriptions.tables.plan_subscription_usage'));
    }
}
