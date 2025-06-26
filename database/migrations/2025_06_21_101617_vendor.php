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
        
        Schema::create('vendor', function (Blueprint $table) {
            $table->id(); 
            $table->enum('type', ['Regular Vendor','Consignment','Consignment Open Price','General Allocation'])->nullable()->index();
            $table->string('code')->unique()->nullable();
            $table->string('name')->index()->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('email')->nullable();
            $table->string('phonenumber')->nullable();
            $table->enum('consignment', ['Yes','No'])->nullable()->index();
            $table->enum('orderdate', ['Yes','No'])->nullable()->index();
            $table->enum('typeloc', ['DC','Store','Restaurant','General Affair'])->nullable()->index();
            $table->enum('typeliabilities', ['Retail','Grosir'])->nullable()->index();
            $table->enum('vendorpkp', ['Yes','No'])->nullable()->index();
            $table->enum('transactiontype', ['Cash','Credit'])->nullable()->index();
            $table->enum('directincoming', ['Yes','No'])->nullable()->index();
            $table->string('top')->nullable()->index();
            $table->string('vendorcp')->nullable();
            $table->string('npwpname')->nullable();
            $table->string('npwpnumber')->nullable()->index();
            $table->string('npwpaddress')->nullable();
            $table->uuid('bank_id')->nullable()->index();
            $table->text('description')->nullable();
            $table->timestamps();
              $table->foreign('bank_id')
                ->references('id')
                ->on('banks_tables')->onDelete('cascade');
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
