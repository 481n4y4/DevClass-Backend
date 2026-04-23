<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'nis' => 'teacher@devclass.com',
            'email' => 'kheiralovedila@gmail.com',
            'name' => 'DevClass Teacher',
            'password' => Hash::make('teacher123'),
            'no_absen' => 0,
            'kelas' => '10',
            'kelas_index' => '1',
            'role' => User::ROLE_TEACHER,
        ]);

        $nisStart = 1001;
        for ($i = 0; $i < 30; $i++) {
            $nis = (string) ($nisStart + $i);

            User::create([
                'nis' => $nis,
                'email' => 'thakeiabinaya@gmail.com',
                'name' => 'Student ' . ($i + 1),
                'password' => Hash::make($nis),
                'no_absen' => $i + 1,
                'kelas' => (string) random_int(10, 13),
                'kelas_index' => (string) random_int(1, 3),
                'role' => User::ROLE_STUDENT,
            ]);
        }
    }
}
