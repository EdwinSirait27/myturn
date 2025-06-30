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
         Schema::create('family', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('subcategories_id')->nullable()->index();
            $table->string('name')->nullable()->index();
            $table->string('code')->nullable(); 
              $table->foreign('subcategories_id')
                ->references('id')
                ->on('subcategories')->onDelete('cascade');
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
