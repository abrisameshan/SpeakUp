<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Aspiration;
use App\Models\AspirationUpvote;
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

        // Ambil daftar aspirasi yang sudah di-upvote oleh user (jika sedang login)
        $user = auth('sanctum')->user();
        $upvotedIds = [];
        if ($user) {
            $upvotedIds = AspirationUpvote::where('user_id', $user->id)
                ->pluck('aspiration_id')
                ->toArray();
        }

        $aspirations->getCollection()->transform(function ($item) use ($upvotedIds) {
            $item->has_upvoted = in_array($item->id, $upvotedIds);
            return $item;
        });

        // Statistik ringkas untuk widget beranda
        $stats = [
            'total' => Aspiration::count(),
            'diproses' => Aspiration::where('status', 'diproses')->count(),
            'selesai' => Aspiration::where('status', 'selesai')->count(),
        ];

        return response()->json([
            'status' => 'success',
            'stats' => $stats,
            'upvoted_ids' => $upvotedIds,
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

    // Upvote aspirasi (1 akun hanya bisa 1 kali per aspirasi)
    public function upvote(Request $request, $id)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Silakan login terlebih dahulu untuk memberikan dukungan.'
            ], 401);
        }

        $aspiration = Aspiration::findOrFail($id);

        // Cek apakah akun ini sudah pernah upvote aspirasi ini
        $alreadyUpvoted = AspirationUpvote::where('user_id', $user->id)
            ->where('aspiration_id', $aspiration->id)
            ->exists();

        if ($alreadyUpvoted) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda sudah memberikan dukungan pada aspirasi ini.',
                'already_upvoted' => true,
                'upvotes_count' => $aspiration->upvotes_count
            ], 422);
        }

        // Catat upvote
        AspirationUpvote::create([
            'user_id' => $user->id,
            'aspiration_id' => $aspiration->id
        ]);

        $aspiration->increment('upvotes_count');

        return response()->json([
            'status' => 'success',
            'message' => 'Dukungan berhasil ditambahkan!',
            'upvotes_count' => $aspiration->upvotes_count,
            'has_upvoted' => true
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

    // Ambil riwayat aspirasi milik user yang sedang login
    public function myAspirations(Request $request)
    {
        $user = $request->user();

        $aspirations = Aspiration::where('user_id', $user->id)
            ->latest()
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $aspirations
        ]);
    }
}