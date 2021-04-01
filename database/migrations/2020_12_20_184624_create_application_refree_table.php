<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicationRefreeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('application_refree', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('fullname')->nullable();
            $table->longText('position')->nullable();
            $table->longText('organisation')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->longText('address')->nullable();
            $table->boolean('is_form_completed')->default(false);
            $table->foreignId('application_id')
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
        Schema::dropIfExists('application_refree');
    }
}
