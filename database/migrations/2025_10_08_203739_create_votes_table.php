<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('candidate_id')->constrained()->onDelete('cascade');
            $table->foreignId('position_id')->constrained()->onDelete('cascade');
            $table->foreignId('election_period_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['student_id', 'position_id', 'election_period_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('votes');
    }
};