<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_transactions', function (Blueprint $table) {
            $table->id();
            $table->string("orderId")->unique()->nullable();
            $table->string("rrr")->nullable();
            $table->longText("payment_payload");
            $table->integer("amount");
            $table->longText("transactionId")->nullable();
            $table->string("status")->nullable();
            $table->integer("user_id");
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
        Schema::dropIfExists('student_transactions');
    }
}
