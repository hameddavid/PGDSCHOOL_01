<?php

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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->integer('amount')->nullable();
            $table->string('type')->nullable();
            //working here
            // $table->string('category')->nullable();
            $table->integer('setting_id')->nullable();
            $table->string('details')->nullable();
            $table->string('description')->nullable();
            $table->string('serviceType_id')->nullable(); //remita
            $table->integer('programme_id')->nullable();
            $table->string('programme_type')->nullable();
            $table->string('insatllment')->nullable();
            $table->string('session')->nullable();
            // $table->string('type')
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
