<?php

namespace App\Http\Controllers;

use App\Models\SuratMasuk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class SuratKeluarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return redirect()->route('surat');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('surat-create-keluar');
    }

    /**
     * Store a newly created resource in storage.
     * POST /surat-keluar → route('surat-keluar.store')
     */
    public function store(Request $request)
    {
        // Delegate ke SuratController::storeKeluar
        return app(SuratController::class)->storeKeluar($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(SuratMasuk $surat_keluar)
    {
        return view('surat-keluar.show', ['surat' => $surat_keluar]);
    }

    /**
     * Show the form for editing the specified resource.
     * GET /surat-keluar/{id} → route('surat-keluar.edit')
     */
    public function edit(SuratMasuk $surat_keluar)
    {
        return view('surat-edit-keluar', ['surat' => $surat_keluar]);
    }

    /**
     * Update the specified resource in storage.
     * PUT /surat-keluar/{surat_keluar} → route('surat-keluar.update')
     */
    public function update(Request $request, SuratMasuk $surat_keluar)
    {
        Log::info('Update balasan request:', $request->all());
        Log::info('Surat Keluar ID:', ['id' => $surat_keluar->id]);

        $data = $request->validate([
            'no_surat'        => ['nullable', 'string', 'max:255'],
            'tanggal_surat'   => ['required', 'date'],
            'tujuan_surat'    => ['required', 'string', 'max:255'],
            'perihal'         => ['required', 'string'],
            'perihal_lainnya' => ['nullable', 'string'],
            'file_balasan'    => ['nullable', 'file', 'mimes:pdf,docx', 'max:2048'],
        ]);

        // Jika perihal adalah 'lainnya', gunakan perihal_lainnya
        $perihalFinal = ($data['perihal'] === 'lainnya' && !empty($data['perihal_lainnya']))
            ? $data['perihal_lainnya']
            : $data['perihal'];

        $filePath = $surat_keluar->file_balasan;

        if ($request->hasFile('file_balasan')) {
            // Hapus file lama
            if ($filePath) {
                Storage::disk($this->suratDisk())->delete($filePath);
            }
            $filePath = $request->file('file_balasan')->store('surat-keluar', $this->suratDisk());
        }

        // Handle no_surat - jika kosong atau null, gunakan nilai lama atau auto-generate
        $noSurat = !empty($data['no_surat'])
            ? $data['no_surat']
            : ($surat_keluar->no_surat_balasan ?? 'SK/' . str_pad($surat_keluar->id, 3, '0', STR_PAD_LEFT) . '/' . date('Y'));

        $surat_keluar->update([
            'no_surat_balasan' => $noSurat,
            'tanggal_balasan'  => $data['tanggal_surat'],
            'tujuan_surat'     => $data['tujuan_surat'],
            'perihal_balasan'  => $perihalFinal,
            'file_balasan'     => $filePath,
            'status'           => 'Sudah Dibalas',
        ]);

        return redirect()->route('surat')
            ->with('success', 'Balasan surat keluar berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SuratMasuk $surat_keluar)
    {
        // Hapus file kalau ada
        if ($surat_keluar->file_balasan) {
            Storage::disk($this->suratDisk())->delete($surat_keluar->file_balasan);
        }

        $surat_keluar->update([
            'no_surat_balasan' => null,
            'tanggal_balasan'  => null,
            'tujuan_surat'     => null,
            'perihal_balasan'  => null,
            'file_balasan'     => null,
            'status'           => 'Belum Dibalas',
        ]);

        return redirect()->route('surat')
            ->with('success', 'Balasan surat keluar berhasil dihapus.');
    }

    /**
     * Download file balasan.
     */
    public function download(SuratMasuk $surat_keluar)
    {
        $filePath = $this->resolveFilePath($surat_keluar->file_balasan);
        if (!$filePath) {
            Log::warning('File balasan tidak ditemukan saat download.', [
                'surat_masuk_id' => $surat_keluar->id,
                'stored_path' => $surat_keluar->file_balasan,
            ]);
            abort(404, 'File tidak ditemukan');
        }

        return Storage::disk($this->suratDisk())->download($filePath, basename($filePath));
    }

    /**
     * Preview file balasan (inline).
     */
    public function preview(SuratMasuk $surat_keluar)
    {
        $filePath = $this->resolveFilePath($surat_keluar->file_balasan);
        if (!$filePath) {
            Log::warning('File balasan tidak ditemukan saat preview.', [
                'surat_masuk_id' => $surat_keluar->id,
                'stored_path' => $surat_keluar->file_balasan,
            ]);
            abort(404, 'File tidak ditemukan');
        }

        return Storage::disk($this->suratDisk())->response(
            $filePath,
            basename($filePath),
            ['Content-Disposition' => 'inline; filename="' . basename($filePath) . '"']
        );
    }

    private function resolveFilePath(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        $normalized = ltrim(str_replace('\\', '/', $path), '/');
        $candidates = array_unique([
            $normalized,
            preg_replace('#^public/#', '', $normalized),
            preg_replace('#^storage/#', '', $normalized),
        ]);

        foreach ($candidates as $candidate) {
            if ($candidate && Storage::disk($this->suratDisk())->exists($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    private function suratDisk(): string
    {
        return config('filesystems.surat_disk', 'public');
    }
}
