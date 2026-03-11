<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clt_layers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('layup_id')
                  ->constrained('clt_layups')
                  ->cascadeOnDelete();

            $table->integer('layer_order');
            $table->decimal('thickness', 10, 2);
            $table->decimal('width', 10, 2);
            $table->decimal('angle', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clt_layers');
    }
};