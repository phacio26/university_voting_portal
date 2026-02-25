<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('votes', function (Blueprint $table) {
            $table->index(['election_period_id', 'student_id'], 'votes_election_student_index');
            $table->index(['candidate_id', 'election_period_id'], 'votes_candidate_election_index');
            $table->index('election_period_id', 'votes_election_index');
            $table->index('student_id', 'votes_student_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('votes', function (Blueprint $table) {
            $table->dropIndex('votes_election_student_index');
            $table->dropIndex('votes_candidate_election_index');
            $table->dropIndex('votes_election_index');
            $table->dropIndex('votes_student_index');
        });
    }
};
