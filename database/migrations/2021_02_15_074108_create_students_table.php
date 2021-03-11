<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('martic_no')->nullable();
            $table->string('email')->unique();
            // $table->string('surname');
            // $table->string('firstname');
            // $table->string('lastname');
            $table->boolean('is_active')->default(1);
            $table->string('password');
            $table->string('token')->nullable();
            $table->string('type')->default('student');
            $table->foreignId('applicant_id')
            ->nullable()
            ->constrained('applicants')
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
        Schema::dropIfExists('students');
    }
}
