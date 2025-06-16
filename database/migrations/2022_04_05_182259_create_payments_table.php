<?php

use App\Enums\PaymentStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('payments', function (Blueprint $table) {
        //     $table->id();
        //     $table->enum('status', [PaymentStatus::TYPES])->default(PaymentStatus::IN_PROGRESS);
        //     $table->integer('error_code')->nullable();
        //     $table->string('error_description',2000)->nullable();
        //     $table->string('session_id',100)->nullable();
        //     $table->foreignId('plan_subscription_id')->constrained();
        //     $table->string('ifirma_invoice_id')->nullable();
        //     $table->timestamps();
        // });
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->enum('status', [PaymentStatus::TYPES])->default(PaymentStatus::IN_PROGRESS);
            $table->integer('error_code')->nullable();
            $table->string('error_description', 2000)->nullable();
            $table->string('session_id', 100)->nullable();

            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('plan_id')->constrained()->onDelete('cascade');

            $table->string('ifirma_invoice_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
