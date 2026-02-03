<?php

namespace App\Http\Controllers;

use App\Models\SuratMasuk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SuratMasukController extends Controller
{
    /**
     * Tampilkan daftar surat masuk.
     */
    public function index()
    {
        $surat = SuratMasuk::latest()->get();
        return view('surat-masuk.index', compact('surat'));
    }

    /**
     * Show form create surat masuk.
     */
    public function create()
    {
        return view('surat-masuk.create');
    }

    /**
     * Simpan surat masuk BARU (FIX: HAPUS no_surat).
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'asal_surat' => 'required|string|max:255',
            'perihal' => 'required|string|max:500',
            'perihal_lainnya' => 'nullable|string|max:500',
            'tanggal_surat' => 'required|date',
            'file_surat' => 'required|file|mimes:pdf|max:5120', // 5MB
        ]);

        // Handle perihal "lainnya"
        $perihalFinal = $data['perihal'] === 'lainnya' 
            ? ($data['perihal_lainnya'] ?? '') 
            : $data['perihal'];

        // Upload file PDF
        if ($request->hasFile('file_surat')) {
            $data['file_surat'] = $request->file('file_surat')
                ->store('surat-masuk', 'public');
        }

        // Data yang SIMPAN KE DB (MATCH TABEL)
        $suratData = [
            'asal_surat' => $data['asal_surat'],
            'perihal' => $perihalFinal,
            'tanggal_surat' => $data['tanggal_surat'],
            'file_surat' => $data['file_surat'],
            'status' => 'Belum Dibalas',
        ];

        // Simpan
        SuratMasuk::create($suratData);

        return redirect()->route('surat')
            ->with('success', 'Surat masuk berhasil ditambahkan!');
    }

    /**
     * COMPATIBILITY: Form lama yang pakai storeMasuk()
     */
    public function storeMasuk(Request $request)
    {
        return $this->store($request);
    }

    /**
     * Tampilkan detail surat masuk.
     */
    public function show(SuratMasuk $suratMasuk)
    {
        return view('surat-masuk.show', compact('suratMasuk'));
    }

    /**
     * Show form edit surat masuk.
     */
    public function edit(SuratMasuk $suratMasuk)
    {
        return view('surat-masuk.edit', compact('suratMasuk'));
    }

    /**
     * Update surat masuk.
     */
    public function update(Request $request, SuratMasuk $suratMasuk)
    {
        $data = $request->validate([
            'asal_surat' => 'required|string|max:255',
            'perihal' => 'required|string|max:500',
            'perihal_lainnya' => 'nullable|string|max:500',
            'tanggal_surat' => 'required|date',
            'file_surat' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        // Handle perihal "lainnya"
        $perihalFinal = $data['perihal'] === 'lainnya' 
            ? ($data['perihal_lainnya'] ?? '') 
            : $data['perihal'];

        // Update file jika ada upload baru
        if ($request->hasFile('file_surat')) {
            // Hapus file lama
            if ($suratMasuk->file_surat) {
                Storage::disk('public')->delete($suratMasuk->file_surat);
            }
            $data['file_surat'] = $request->file('file_surat')
                ->store('surat-masuk', 'public');
        }

        // Update data
        $suratMasuk->update([
            'asal_surat' => $data['asal_surat'],
            'perihal' => $perihalFinal,
            'tanggal_surat' => $data['tanggal_surat'],
            'file_surat' => $data['file_surat'] ?? $suratMasuk->file_surat,
        ]);

        return redirect()->route('surat')
            ->with('success', 'Surat masuk berhasil diupdate!');
    }

    /**
     * Hapus surat masuk beserta file.
     */
    public function destroy(SuratMasuk $suratMasuk)
    {
        // Hapus file surat masuk
        if ($suratMasuk->file_surat) {
            Storage::disk('public')->delete($suratMasuk->file_surat);
        }

        // Hapus file balasan jika ada
        if ($suratMasuk->file_balasan) {
            Storage::disk('public')->delete($suratMasuk->file_balasan);
        }

        $suratMasuk->delete();

        return redirect()->route('surat')
            ->with('success', 'Surat masuk berhasil dihapus!');
    }

    /**
     * Download file surat masuk PDF.
     */
    public function download(SuratMasuk $suratMasuk)
    {
        if (!$suratMasuk->file_surat) {
            abort(404, 'File tidak ditemukan');
        }

        return Storage::disk('public')->download($suratMasuk->file_surat);
    }
}
