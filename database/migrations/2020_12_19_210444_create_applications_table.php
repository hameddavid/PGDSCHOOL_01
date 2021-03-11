<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            //working here
            $table->string('rrr')->nullable()->unique();
            $table->longText('payments')->nullable();
            $table->longText("deny_reason")->nullable();
            //testing
            $table->foreignId('applicant_id')
            ->nullable()
            ->constrained('applicants')
            ->onUpdate('cascade')
            ->onDelete('cascade');
            $table->set('status',['submitted under processing','awaiting submission','denied','approved','student']);
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
        Schema::dropIfExists('applications');
    }
}
