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
          Schema::create('categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('departments_id')->nullable()->index();
            $table->string('name')->nullable()->index();
            $table->string('code')->nullable(); 
              $table->foreign('departments_id')
                ->references('id')
                ->on('departments')->onDelete('cascade');
        });
           
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
