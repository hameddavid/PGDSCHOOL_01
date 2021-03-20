<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->morphs('transaction');
            $table->bigInteger('payment_id');
            $table->string('status')->nullable();
            $table->string('amount')->nullable();
            $table->string('details')->nullable();
            $table->string('reference')->nullable();
            $table->foreignId('application_id')->nullable();
            $table->string('session')->nullable();
            $table->longText('transactionId',191)->unique()->nullable();
            //update
            //check me
            $table->string('rrr')->nullable();///remita
            //check me
            $table->string('orderId')->nullable();
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
        Schema::dropIfExists('transactions');
    }
}
