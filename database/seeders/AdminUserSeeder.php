<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat user Admin jika belum ada
        if (!User::where('email', 'admin@bbpbl.com')->exists()) {
            User::create([
                'name' => 'Administrator',
                'email' => 'admin@bbpbl.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]);

            $this->command->info('Admin user created: admin@bbpbl.com / admin123');
        }

        // Buat user Pimpinan contoh
        if (!User::where('email', 'pimpinan@bbpbl.com')->exists()) {
            User::create([
                'name' => 'Pimpinan BBPBL',
                'email' => 'pimpinan@bbpbl.com',
                'password' => Hash::make('pimpinan123'),
                'role' => 'pimpinan',
                'email_verified_at' => now(),
            ]);

            $this->command->info('Pimpinan user created: pimpinan@bbpbl.com / pimpinan123');
        }
    }
}
