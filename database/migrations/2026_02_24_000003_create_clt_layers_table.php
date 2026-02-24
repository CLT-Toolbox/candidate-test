<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCltLayersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clt_layers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('layup_id')->constrained('clt_layups')->cascadeOnDelete();
            $table->integer('layer_order');
            $table->decimal('thickness', 8, 3);
            $table->decimal('width', 8, 3);
            $table->decimal('angle', 8, 3);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clt_layers');
    }
}
