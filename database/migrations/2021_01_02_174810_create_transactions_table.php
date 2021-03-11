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
            $table->longText('transactionId',191)->unique()->nullable();
            //update
            $table->string('rrr')->nullable()->unique();///remita
            $table->string('orderId')->nullable()->unique();
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
