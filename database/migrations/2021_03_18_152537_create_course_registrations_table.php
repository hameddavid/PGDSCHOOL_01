<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseRegistrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_registrations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('matric_number');
            $table->string('semester');
            $table->string('session_id');
            $table->string('course_code');
            $table->string('unit');
            $table->string('registration_level');
            $table->string('lecturer_id')->nullable();
            $table->string('status')->nullable();
            $table->string('score')->default(-1);
            $table->string('grade')->nullable();
            $table->string('remarks')->nullable();
            $table->string('last_updated_by')->nullable();
            $table->string('deleted')->nullable();
            $table->string('satisfied')->nullable();
            $table->string('unit_id')->nullable();
            $table->string('reg_state')->default(1);
            $table->bigInteger('CTCUP')->nullable();
            $table->bigInteger('CTEUP')->nullable();
            $table->bigInteger('CTNUR')->nullable();
            $table->double('CGPA',5,2)->nullable();
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
        Schema::dropIfExists('course_registrations');
    }
}
