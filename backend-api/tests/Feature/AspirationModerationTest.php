<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Aspiration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AspirationModerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_feed_only_shows_published_aspirations(): void
    {
        $user = User::factory()->create(['role' => 'siswa']);

        // 1 aspirasi yang sudah diverifikasi (publik)
        Aspiration::create([
            'user_id' => $user->id,
            'title' => 'Aspirasi Terverifikasi',
            'category' => 'Sarana',
            'description' => 'Ini aspirasi yang sudah lolos moderasi dan layak dilihat publik.',
            'status' => 'pending',
            'is_published' => true,
        ]);

        // 1 aspirasi yang belum diverifikasi (privat)
        Aspiration::create([
            'user_id' => $user->id,
            'title' => 'Aspirasi Menunggu Review',
            'category' => 'Umum',
            'description' => 'Ini aspirasi baru yang belum diperiksa oleh pihak sekolah.',
            'status' => 'pending',
            'is_published' => false,
        ]);

        $response = $this->getJson('/api/aspirations');

        $response->assertStatus(200);
        $data = $response->json('data.data');

        // Pastikan hanya 1 aspirasi yang tampil di feed publik
        $this->assertCount(1, $data);
        $this->assertEquals('Aspirasi Terverifikasi', $data[0]['title']);
    }

    public function test_student_aspiration_is_unpublished_by_default(): void
    {
        $student = User::factory()->create(['role' => 'siswa']);

        $response = $this->actingAs($student, 'sanctum')->postJson('/api/aspirations', [
            'title' => 'Pengajuan AC Baru untuk Kelas 12',
            'category' => 'Sarana',
            'description' => 'AC di ruang kelas 12 sering mati mendadak saat siang hari.',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('aspirations', [
            'title' => 'Pengajuan AC Baru untuk Kelas 12',
            'is_published' => false,
        ]);
    }

    public function test_guru_and_admin_can_view_all_and_publish_aspirations(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $student = User::factory()->create(['role' => 'siswa']);

        $aspiration = Aspiration::create([
            'user_id' => $student->id,
            'title' => 'Aspirasi Belum Disetujui',
            'category' => 'Sarana',
            'description' => 'Aspirasi yang menunggu peninjauan guru atau staf admin.',
            'status' => 'pending',
            'is_published' => false,
        ]);

        // Guru mengakses panel admin
        $adminView = $this->actingAs($guru, 'sanctum')->getJson('/api/admin/aspirations');
        $adminView->assertStatus(200);
        $this->assertCount(1, $adminView->json('data.data'));

        // Guru mempublikasikan aspirasi
        $publishResponse = $this->actingAs($guru, 'sanctum')->putJson("/api/aspirations/{$aspiration->id}/publish", [
            'is_published' => true
        ]);

        $publishResponse->assertStatus(200);
        $this->assertDatabaseHas('aspirations', [
            'id' => $aspiration->id,
            'is_published' => true,
        ]);

        // Sekarang aspirasi harus muncul di publik
        $publicFeed = $this->getJson('/api/aspirations');
        $publicFeed->assertStatus(200);
        $this->assertCount(1, $publicFeed->json('data.data'));
    }
}
