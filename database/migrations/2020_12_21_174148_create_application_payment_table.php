<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicationPaymentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('application_payment', function (Blueprint $table) {
            $table->id();
            $table->longText('transactionId')->nullable();
            $table->string('amount');
            $table->string('status');
            $table->string('reference')->nullable();
            $table->string('rrr')->nullable(); // for remita implementation
            $table->foreignId('application_id')->unique()
            ->nullable()
            ->constrained('applications')
            ->onUpdate('cascade')
            ->onDelete('cascade');
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
        Schema::dropIfExists('application_payment');
    }
}
