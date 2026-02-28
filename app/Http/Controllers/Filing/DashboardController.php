<?php

namespace App\Http\Controllers\Filing;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use App\Models\SuratRecentView;

class DashboardController extends Controller
{
    public function index()
    {
        $recentSurat = SuratRecentView::with('surat')
            ->where('user_id', auth()->id())
            ->whereHas('surat')
            ->orderByDesc('last_viewed_at')
            ->limit(5)
            ->get()
            ->map(fn ($item) => [
                'id' => $item->surat->id,
                'judul' => $item->surat->judul,
                'nomor_surat' => $item->surat->nomor_surat,
                'jenis' => $item->surat->jenis,
                'status' => $item->surat->status,
                'last_viewed_at' => $item->last_viewed_at->diffForHumans(),
            ])
            
            ->filter(fn ($item) => $item['id'] !== null);

        return Inertia::render('Dashboard', [
            'recentSurat' => $recentSurat,
        ]);
    }
}