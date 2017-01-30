<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ServiceServicetag extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_service_tag', function (Blueprint $table) {
            $table->unsignedInteger('service_id');
            $table->unsignedInteger('service_tag_id');
            $table->primary(['service_id','service_tag_id']);
            $table->timestamps();

            // TODO: Should we maintain these constraints, with a cascade?
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
            $table->foreign('service_tag_id')->references('id')->on('service_tags')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('service_servicetag');
    }
}
