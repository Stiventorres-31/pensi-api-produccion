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
        Schema::create('inmuebles', function (Blueprint $table) {
            $table->id("id_inmueble");
            $table->string('codigo', 20);
            $table->string('nombre', 200);
            $table->string('direccion', 200);
            $table->string('pais', 100);
            $table->string('region', 100);
            $table->string('ciudad', 100);
            $table->string('medida', 100);
            $table->decimal('precio',20);
            $table->double('porcentaje_descuento', 5, 2)->nullable();
            $table->decimal('precio_descuento',20)->nullable();
            $table->string('habitaciones', 5);
            $table->foreignId('id_usuario')->constrained("users");
            $table->foreignId('id_genero')->constrained("generos","id_genero");
            $table->tinyInteger('destacado')->default(0);
            $table->string('link', 255)->nullable();
            $table->tinyInteger('estado')->default(1);
            $table->text('descripcion')->nullable();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inmuebles');
    }
};
