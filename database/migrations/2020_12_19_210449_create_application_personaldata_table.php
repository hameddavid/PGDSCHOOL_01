<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicationPersonaldataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('application_personaldata', function (Blueprint $table) {
            $table->id();
            $table->string('title', 10)->nullable();
            $table->char('gender', 1)->nullable();
            $table->string('marital_status', 12)->nullable();
            $table->string('date_of_birth', 12)->nullable();
            $table->string('state_of_origin', 12)->nullable();
            $table->string('place_of_birth', 50)->nullable();
            $table->string('place_of_birth_LG', 50)->nullable();
            $table->string('nationality', 50)->nullable();
            $table->string('health_status')->nullable();
            $table->longText('hobbies')->nullable();
            $table->string('contact_address')->nullable();
            $table->string('residential_address')->nullable();
            $table->string('next_of_kin_name', 60)->nullable();
            $table->string('next_of_kin_relationship', 60)->nullable();
            $table->string('next_of_kin_email', 60)->nullable();
            $table->string('next_of_kin_phone', 60)->nullable();
            $table->string('next_of_kin_address', 60)->nullable();
            // $table->string('picture')->nullable();
            $table->boolean('is_form_completed')->default(false);
            $table->foreignId('applicant_id')->unique()->constrained('applicants')
            ->onUpdate('cascade')
            ->onDelete('cascade');
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
        Schema::dropIfExists('application_personaldata');
    }
}
