<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $existingCodes = collect(
            DB::table('classes')
                ->whereNotNull('code')
                ->pluck('code')
                ->all()
        );

        DB::table('classes')
            ->whereNull('code')
            ->orderBy('id')
            ->chunkById(100, function ($classes) use (&$existingCodes) {
                foreach ($classes as $class) {
                    do {
                        $code = strtoupper(Str::random(6));
                    } while ($existingCodes->contains($code));

                    DB::table('classes')->where('id', $class->id)->update([
                        'code' => $code,
                    ]);

                    $existingCodes->push($code);
                }
            });

        Schema::table('classes', function (Blueprint $table) {
            $table->string('code', 12)->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->string('code', 12)->nullable()->change();
        });
    }
};
