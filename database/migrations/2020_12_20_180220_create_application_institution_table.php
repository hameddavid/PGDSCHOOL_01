<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicationInstitutionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('application_institution', function (Blueprint $table) {
            $table->id();
            // $table->json('institution_details')->nullable();
            $table->string('name_of_institution')->nullable();
            $table->string('address_of_institution')->nullable();
            $table->string('date_admitted')->nullable();
            $table->string('date_graduated')->nullable();
            $table->string('qualification_obtained')->nullable();
            $table->string('class_of_degree_obtained')->nullable();
            $table->string('field_discipline')->nullable();
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
        Schema::dropIfExists('application_institution');
    }
}
