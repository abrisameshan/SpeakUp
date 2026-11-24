<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Aspiration;
use App\Models\AspirationUpvote;
use Illuminate\Http\Request;

class AspirationController extends Controller
{
    // Mengambil daftar aspirasi publik (hanya yang sudah diverifikasi dan dipublikasikan)
    public function index(Request $request)
    {
        $query = Aspiration::with('user:id,name')
            ->published()
            ->latest();

        if ($request->filled('category') && $request->category !== 'Semua') {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
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

        // Statistik ringkas publik (hanya menghitung aspirasi yang terpublikasi)
        $stats = [
            'total' => Aspiration::published()->count(),
            'diproses' => Aspiration::published()->where('status', 'diproses')->count(),
            'selesai' => Aspiration::published()->where('status', 'selesai')->count(),
        ];

        return response()->json([
            'status' => 'success',
            'stats' => $stats,
            'upvoted_ids' => $upvotedIds,
            'data' => $aspirations
        ]);
    }

    // Mengambil daftar semua aspirasi untuk panel Guru & Admin (termasuk yang belum dipublikasikan)
    public function adminIndex(Request $request)
    {
        $user = $request->user();
        if (!$user || !in_array($user->role, ['admin', 'guru'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Akses ditolak. Khusus guru dan admin.'
            ], 403);
        }

        $query = Aspiration::with('user:id,name,email')->latest();

        // Filter berdasarkan kategori
        if ($request->filled('category') && $request->category !== 'Semua') {
            $query->where('category', $request->category);
        }

        // Filter berdasarkan status proses (pending, diproses, selesai)
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter status publikasi (all, published, unpublished)
        if ($request->filled('publish_status')) {
            if ($request->publish_status === 'published') {
                $query->where('is_published', true);
            } elseif ($request->publish_status === 'unpublished') {
                $query->where('is_published', false);
            }
        }

        // Filter pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $aspirations = $query->paginate(20);

        // Statistik panel admin
        $stats = [
            'total' => Aspiration::count(),
            'unverified' => Aspiration::where('is_published', false)->count(),
            'published' => Aspiration::where('is_published', true)->count(),
            'diproses' => Aspiration::where('status', 'diproses')->count(),
            'selesai' => Aspiration::where('status', 'selesai')->count(),
        ];

        return response()->json([
            'status' => 'success',
            'stats' => $stats,
            'data' => $aspirations
        ]);
    }

    // Mengirim aspirasi baru oleh siswa (default is_published = false)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'category' => 'required|string|in:Sarana,Akademik,Kebersihan,Layanan,Umum',
            'description' => 'required|string|min:15',
        ]);

        $validated['is_published'] = false;
        $validated['published_at'] = null;

        $aspiration = $request->user()->aspirations()->create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Aspirasi berhasil disampaikan! Menunggu verifikasi dari admin/guru sebelum ditampilkan ke publik.',
            'data' => $aspiration->load('user:id,name')
        ], 201);
    }

    // Verifikasi dan Publikasikan / Batalkan Publikasi oleh Admin/Guru
    public function updatePublishStatus(Request $request, $id)
    {
        $user = $request->user();
        if (!$user || !in_array($user->role, ['admin', 'guru'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Akses ditolak. Hanya guru dan admin yang dapat memoderasi publikasi.'
            ], 403);
        }

        $validated = $request->validate([
            'is_published' => 'required|boolean'
        ]);

        $aspiration = Aspiration::findOrFail($id);
        $isPublished = (bool) $validated['is_published'];

        $aspiration->update([
            'is_published' => $isPublished,
            'published_at' => $isPublished ? now() : null
        ]);

        return response()->json([
            'status' => 'success',
            'message' => $isPublished 
                ? 'Aspirasi berhasil diverifikasi dan dipublikasikan ke publik!' 
                : 'Publikasi aspirasi berhasil dibatalkan (ditarik dari publik).',
            'data' => $aspiration->load('user:id,name')
        ]);
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

        $aspiration = Aspiration::published()->findOrFail($id);

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

    // Update status penyelesaian aspirasi dan tanggapan dari guru/admin
    public function updateStatus(Request $request, $id)
    {
        $user = $request->user();

        // Verifikasi role (Hanya admin atau guru yang diizinkan)
        if (!in_array($user->role, ['admin', 'guru'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Akses ditolak. Hanya guru dan admin yang dapat mengelola status aspirasi.'
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

    // Hapus aspirasi (khusus admin/guru jika ada postingan tidak pantas/spam)
    public function destroy(Request $request, $id)
    {
        $user = $request->user();

        if (!in_array($user->role, ['admin', 'guru'])) {
            return response()->json([
                'status' => 'error',
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
