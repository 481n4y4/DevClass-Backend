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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nis')->unique();
            $table->string('name');
            $table->string('password');
            $table->unsignedInteger('no_absen');
            $table->enum('kelas', ['10', '11', '12', '13'])->index();
            $table->enum('kelas_index', ['1', '2', '3'])->index();
            $table->enum('role', ['teacher', 'student'])->default('student')->index();
            $table->timestamps();

            $table->index(['kelas', 'kelas_index']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
