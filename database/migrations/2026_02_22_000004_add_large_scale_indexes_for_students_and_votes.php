<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('votes', function (Blueprint $table) {
            $table->index(['election_period_id', 'student_id'], 'votes_election_student_idx');
            $table->index(['election_period_id', 'position_id'], 'votes_election_position_idx');
            $table->index(['election_period_id', 'candidate_id'], 'votes_election_candidate_idx');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->index('is_active', 'students_is_active_idx');
        });
    }

    public function down(): void
    {
        Schema::table('votes', function (Blueprint $table) {
            $table->dropIndex('votes_election_student_idx');
            $table->dropIndex('votes_election_position_idx');
            $table->dropIndex('votes_election_candidate_idx');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex('students_is_active_idx');
        });
    }
};
