<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePGLecturersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('p_g_lecturers', function (Blueprint $table) {
            $table->id();
            $table->string('lecturer_id');
            $table->string('firstname');
            $table->string('surname');
            $table->string('phone');
            $table->string('campus_ext');
            $table->string('email');
            $table->string('deleted')->default('N');
            $table->string('login_name');
            $table->string('program_id_FK')->nullable();
            $table->string('lecturer_category');
            $table->string('picture');
            $table->string('is_verified');
            $table->string('signature')->nullable();
            $table->string('semester_last_seen');
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
        Schema::dropIfExists('p_g_lecturers');
    }
}
