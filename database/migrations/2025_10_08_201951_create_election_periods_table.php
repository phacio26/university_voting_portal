<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('election_periods', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamp('start_time')->useCurrent();
            $table->timestamp('end_time')->useCurrent();
            $table->boolean('is_active')->default(false);
            $table->boolean('results_available')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('election_periods');
    }
};