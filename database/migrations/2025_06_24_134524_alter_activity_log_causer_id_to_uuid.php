<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
    {
        Schema::table('activity_log', function (Blueprint $table) {
            $table->dropColumn('causer_id');
        });

        Schema::table('activity_log', function (Blueprint $table) {
            $table->uuid('causer_id')->nullable()->after('id')->index();
        });
    }

    public function down(): void
    {
        Schema::table('activity_log', function (Blueprint $table) {
            $table->dropColumn('causer_id');
        });

        Schema::table('activity_log', function (Blueprint $table) {
            $table->unsignedBigInteger('causer_id')->nullable()->index();
        });
    }
};
