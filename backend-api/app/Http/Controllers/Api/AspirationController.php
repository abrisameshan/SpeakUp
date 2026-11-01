<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Aspiration;
use Illuminate\Http\Request;

class AspirationController extends Controller
{
    // Mengambil daftar aspirasi (dengan filter kategori & status)
    public function index(Request $request)
    {
        $query = Aspiration::with('user:id,name')->latest();

        if ($request->filled('category') && $request->category !== 'Semua') {
            $query->where('category', $request->category);
        }

        $aspirations = $query->paginate(10);

        // Statistik ringkas untuk widget beranda
        $stats = [
            'total' => Aspiration::count(),
            'diproses' => Aspiration::where('status', 'diproses')->count(),
            'selesai' => Aspiration::where('status', 'selesai')->count(),
        ];

        return response()->json([
            'status' => 'success',
            'stats' => $stats,
            'data' => $aspirations
        ]);
    }

    // Mengirim aspirasi baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'category' => 'required|string|in:Sarana,Akademik,Kebersihan,Layanan,Umum',
            'description' => 'required|string|min:15',
        ]);

        $aspiration = $request->user()->aspirations()->create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Aspirasi berhasil disampaikan!',
            'data' => $aspiration->load('user:id,name')
        ], 201);
    }

    // Upvote aspirasi
    public function upvote($id)
    {
        $aspiration = Aspiration::findOrFail($id);
        $aspiration->increment('upvotes_count');

        return response()->json([
            'status' => 'success',
            'upvotes_count' => $aspiration->upvotes_count
        ]);
    }

    // Update status aspirasi dan tanggapan dari guru/admin
    public function updateStatus(Request $request, $id)
    {
        $user = $request->user();

        // Verifikasi role (Hanya admin atau guru yang diizinkan)
        if (!in_array($user->role, ['admin', 'guru'])) {
            return response()->json([
                'message' => 'Akses ditolak. Hanya guru dan admin yang dapat mengelola aspirasi.'
            ], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,diproses,selesai',
            'feedback' => 'nullable|string'
        ]);

        $aspiration = Aspiration::findOrFail($id);
        $aspiration->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Status aspirasi berhasil diperbarui.',
            'data' => $aspiration
        ]);
    }

    // Hapus aspirasi (khusus admin/guru jika ada postingan spam)
    public function destroy(Request $request, $id)
    {
        $user = $request->user();

        if (!in_array($user->role, ['admin', 'guru'])) {
            return response()->json([
                'message' => 'Akses ditolak.'
            ], 403);
        }

        $aspiration = Aspiration::findOrFail($id);
        $aspiration->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Aspirasi berhasil dihapus.'
        ]);
    }
}