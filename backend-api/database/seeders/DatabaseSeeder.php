<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Aspiration;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@speakup.sch.id'],
            [
                'name' => 'Administrator Sekolah',
                'password' => Hash::make('password123'),
                'role' => 'admin'
            ]
        );

        $guru = User::firstOrCreate(
            ['email' => 'guru@speakup.sch.id'],
            [
                'name' => 'Bapak Budi (Guru Kesiswaan)',
                'password' => Hash::make('password123'),
                'role' => 'guru'
            ]
        );

        $siswa = User::firstOrCreate(
            ['email' => 'siswa@speakup.sch.id'],
            [
                'name' => 'Ahmad Siswa',
                'password' => Hash::make('password123'),
                'role' => 'siswa'
            ]
        );

        // Contoh aspirasi yang sudah disetujui & dipublikasikan ke publik
        Aspiration::firstOrCreate(
            ['title' => 'Penambahan Stopkontak di Perpustakaan Lantai 2'],
            [
                'user_id' => $siswa->id,
                'category' => 'Sarana',
                'description' => 'Banyak siswa yang kesulitan mengisi daya laptop saat mengerjakan tugas bersama di perpustakaan. Mohon penambahan beberapa stopkontak di meja belajar utama.',
                'status' => 'diproses',
                'feedback' => 'Usulan sudah kami terima dan sedang dikoordinasikan dengan teknisi listrik sekolah.',
                'upvotes_count' => 12,
                'is_published' => true,
                'published_at' => now()->subDays(2)
            ]
        );

        // Contoh aspirasi baru yang belum diverifikasi (hanya admin/guru yang bisa lihat)
        Aspiration::firstOrCreate(
            ['title' => 'Usulan Perbaikan Kebersihan Toilet Gedung B'],
            [
                'user_id' => $siswa->id,
                'category' => 'Kebersihan',
                'description' => 'Kran air di toilet lantai 1 gedung B sering menetes dan sabun cuci tangan sering habis di siang hari.',
                'status' => 'pending',
                'feedback' => null,
                'upvotes_count' => 0,
                'is_published' => false,
                'published_at' => null
            ]
        );
    }
}
