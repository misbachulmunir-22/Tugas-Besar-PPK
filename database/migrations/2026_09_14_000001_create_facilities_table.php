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
        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('type'); // ruang_kelas, aula, laboratorium, alat, lapangan
            $table->string('location');
            $table->integer('capacity')->default(0);
            $table->text('description')->nullable();
            $table->string('photo')->nullable();
            $table->string('status')->default('aktif'); // aktif, dalam_perbaikan, nonaktif
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facilities');
    }
};
