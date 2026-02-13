<?php

namespace App\Http\Controllers;

use App\Models\SuratMasuk;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

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
            'file_surat' => 'required|file|mimes:pdf,docx|max:5120', // 5MB
        ]);

        // Handle perihal "lainnya"
        $perihalFinal = $data['perihal'] === 'lainnya'
            ? ($data['perihal_lainnya'] ?? '')
            : $data['perihal'];

        // Upload file PDF/DOCX
        if ($request->hasFile('file_surat')) {
            $storedPath = $this->storeUploadedFile(
                $request->file('file_surat'),
                'surat-masuk',
                'surat masuk'
            );

            if (!$storedPath) {
                return back()->withInput()->with(
                    'error',
                    'Gagal upload file surat. Periksa konfigurasi storage server lalu coba lagi.'
                );
            }

            $data['file_surat'] = $storedPath;
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
            'file_surat' => 'nullable|file|mimes:pdf,docx|max:5120',
        ]);

        // Handle perihal "lainnya"
        $perihalFinal = $data['perihal'] === 'lainnya'
            ? ($data['perihal_lainnya'] ?? '')
            : $data['perihal'];

        // Update file jika ada upload baru
        if ($request->hasFile('file_surat')) {
            // Hapus file lama
            if ($suratMasuk->file_surat) {
                Storage::disk($this->suratDisk())->delete($suratMasuk->file_surat);
            }

            $storedPath = $this->storeUploadedFile(
                $request->file('file_surat'),
                'surat-masuk',
                'surat masuk'
            );

            if (!$storedPath) {
                return back()->withInput()->with(
                    'error',
                    'Gagal upload file surat. Periksa konfigurasi storage server lalu coba lagi.'
                );
            }

            $data['file_surat'] = $storedPath;
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
            Storage::disk($this->suratDisk())->delete($suratMasuk->file_surat);
        }

        // Hapus file balasan jika ada
        if ($suratMasuk->file_balasan) {
            Storage::disk($this->suratDisk())->delete($suratMasuk->file_balasan);
        }

        $suratMasuk->delete();

        return redirect()->route('surat')
            ->with('success', 'Surat masuk berhasil dihapus!');
    }

    /**
     * Download file surat masuk.
     */
    public function download(SuratMasuk $suratMasuk)
    {
        $filePath = $this->resolveFilePath($suratMasuk->file_surat);
        if (!$filePath) {
            Log::warning('File surat masuk tidak ditemukan saat download.', [
                'surat_masuk_id' => $suratMasuk->id,
                'stored_path' => $suratMasuk->file_surat,
            ]);
            abort(404, 'File tidak ditemukan');
        }

        return Storage::disk($this->suratDisk())->download($filePath, basename($filePath));
    }

    /**
     * Preview file surat masuk (inline).
     */
    public function preview(SuratMasuk $suratMasuk)
    {
        $filePath = $this->resolveFilePath($suratMasuk->file_surat);
        if (!$filePath) {
            Log::warning('File surat masuk tidak ditemukan saat preview.', [
                'surat_masuk_id' => $suratMasuk->id,
                'stored_path' => $suratMasuk->file_surat,
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
        $disk = config('filesystems.surat_disk', 'public');

        try {
            Storage::disk($disk);
            return $disk;
        } catch (Throwable $e) {
            Log::error('Disk storage surat tidak valid. Fallback ke disk public.', [
                'configured_disk' => $disk,
                'error' => $e->getMessage(),
            ]);

            return 'public';
        }
    }

    private function storeUploadedFile(UploadedFile $file, string $directory, string $context): ?string
    {
        $disk = $this->suratDisk();

        try {
            $path = $file->store($directory, $disk);
        } catch (Throwable $e) {
            Log::error('Terjadi exception saat upload file.', [
                'context' => $context,
                'disk' => $disk,
                'directory' => $directory,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'error' => $e->getMessage(),
            ]);

            return null;
        }

        if (!$path) {
            Log::error('Upload file gagal tanpa exception (path kosong/false).', [
                'context' => $context,
                'disk' => $disk,
                'directory' => $directory,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
            ]);

            return null;
        }

        return $path;
    }
}
