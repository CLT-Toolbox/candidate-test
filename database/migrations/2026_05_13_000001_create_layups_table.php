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
        Schema::create('layups', function (Blueprint $table) {
            $table->string('layup_id', 20)->primary();
            $table->string('supplier_id', 20);
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('thickness')->nullable();
            $table->string('grade')->nullable();
            $table->string('status')->default('ACTIVE');
            $table->timestamps();

            $table->foreign('supplier_id')->references('supplier_id')->on('suppliers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('layups');
    }
};
