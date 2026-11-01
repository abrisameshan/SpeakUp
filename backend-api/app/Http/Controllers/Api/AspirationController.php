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
}