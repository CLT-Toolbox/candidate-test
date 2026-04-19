<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('clt_layups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['supplier_id', 'name']);
        });

        Schema::create('clt_layers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clt_layup_id')->constrained('clt_layups')->cascadeOnDelete();
            $table->unsignedInteger('layer_order');
            $table->decimal('thickness', 12, 3);
            $table->decimal('width', 12, 3);
            $table->decimal('angle', 10, 3);
            $table->timestamps();

            $table->unique(['clt_layup_id', 'layer_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clt_layers');
        Schema::dropIfExists('clt_layups');
        Schema::dropIfExists('suppliers');
    }
};
