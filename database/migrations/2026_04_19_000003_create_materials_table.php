<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content')->nullable();
            $table->string('file_path')->nullable();
            $table->enum('kelas_target', ['10', '11', '12', '13'])->index();
            $table->enum('kelas_index_target', ['1', '2', '3'])->index();
            $table->dateTime('deadline')->nullable();
            $table->boolean('submission_required')->default(false);
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['kelas_target', 'kelas_index_target']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
