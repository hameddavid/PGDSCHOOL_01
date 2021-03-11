<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicationEmploymenthistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('application_employmenthistory', function (Blueprint $table) {
            $table->id();
            $table->string('organisation')->nullable();
            $table->string('address')->nullable();
            $table->string('start_date')->nullable();
            $table->string('end_date')->nullable();
            $table->string('last_post_held')->nullable();
            $table->string('last_salary_per_annum')->nullable();
            $table->string('reason_for_leaving')->nullable();
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
        Schema::dropIfExists('application_employmenthistory');
    }
}
