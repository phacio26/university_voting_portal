<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('election_periods', function (Blueprint $table) {
            $table->boolean('is_revote')->default(false)->after('results_available');
            $table->unsignedBigInteger('parent_id')->nullable()->after('is_revote');
        });
    }

    public function down()
    {
        Schema::table('election_periods', function (Blueprint $table) {
            $table->dropColumn(['is_revote', 'parent_id']);
        });
    }
};
