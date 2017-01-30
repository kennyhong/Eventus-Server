<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EventService extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_service', function (Blueprint $table) {
            $table->unsignedInteger('event_id');
            $table->unsignedInteger('service_id');
            $table->primary(['event_id','service_id']);
            $table->timestamps();

            // TODO: Should we maintain these constraints, with a cascade?
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('event_service');
    }
}
