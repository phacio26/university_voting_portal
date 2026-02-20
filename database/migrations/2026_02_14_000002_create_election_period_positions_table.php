<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('election_period_positions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('election_period_id');
            $table->unsignedBigInteger('position_id');
            $table->timestamps();

            $table->unique(['election_period_id', 'position_id']);
            $table->foreign('election_period_id')->references('id')->on('election_periods')->onDelete('cascade');
            $table->foreign('position_id')->references('id')->on('positions')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('election_period_positions');
    }
};
