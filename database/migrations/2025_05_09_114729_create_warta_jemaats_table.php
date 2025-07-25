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
        Schema::create('warta_jemaats', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 255); 
            $table->string('file_pdf_path');
            $table->date('tanggal_terbit');
            $table->string('slug', 255)->unique();
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warta_jemaats');
    }
};
