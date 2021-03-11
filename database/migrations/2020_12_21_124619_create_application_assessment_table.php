<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicationAssessmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('application_assessment', function (Blueprint $table) {
            $table->id();
            $table->boolean('nysc_completed')->default(false);
            $table->string('choose_campus')->nullable();
            $table->longText('essay')->nullable();
            // $table->longText('essayFileName')->nullable();
            $table->longText('academic_distinction_prize')->nullable();
            $table->longText('publications')->nullable();
            $table->string('college_attending_currently')->nullable();
            $table->longText('relevant_info')->nullable();
            $table->longtext('relevant_file')->nullable();
            $table->boolean('is_form_completed')->default(false);
            $table->integer('programme_id')->nullable();
            $table->integer('approved_programme_id')->nullable();
            $table->string('apply_for')->nullable();
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
        Schema::dropIfExists('application_assessment');
    }
}
