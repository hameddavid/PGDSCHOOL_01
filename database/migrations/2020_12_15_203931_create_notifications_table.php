<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

     /**
     * type admission
     * applicants is applicantId
     * students in StudentId
     * data [type=> notification type]
     *
     *
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            // $table->morphs('notifiable');
            $table->longText('data');
            $table->timestamp('read_at')->nullable();
            //new
            $table->integer('applicants')->nullable();
            $table->integer('students')->nullable();
            $table->integer('users')->nullable();
            $table->longText('msg')->nullable();
            $table->boolean('activated')->default(false);
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
        Schema::dropIfExists('notifications');
    }
}
