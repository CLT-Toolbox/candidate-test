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
        Schema::create('layers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('layup_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('layer_order');
            $table->decimal('thickness', 10, 2);
            $table->decimal('width', 10, 2);
            $table->decimal('angle', 8, 2);
            $table->timestamps();

            $table->unique(['layup_id', 'layer_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('layers');
    }
};
