<?php

namespace App\Http\Controllers;

use App\Models\SuratMasuk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SuratController extends Controller
{
    /**
     * Untuk Route::resource('surat-resource', ...)
     * redirect ke halaman utama manajemen surat.
     */
    public function index()
    {
        return redirect()->route('surat');
    }

    /**
     * Compatibility untuk route resource legacy.
     */
    public function create()
    {
        return redirect()->route('surat');
    }

    /**
     * Compatibility untuk route resource legacy.
     */
    public function store(Request $request)
    {
        return $this->storeMasuk($request);
    }

    /**
     * Compatibility untuk route resource legacy.
     * URL /surat-resource/{id} diarahkan ke halaman manajemen surat.
     */
    public function show($id)
    {
        return redirect()->route('surat');
    }

    /**
     * Halaman manajemen surat (list surat masuk + status balasan).
     * View: resources/views/surat.blade.php
     * URL : /surat
     */
    public function surat()
    {
        $query = SuratMasuk::query()->orderByDesc('created_at');

        if ($search = request('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('asal_surat', 'like', '%' . $search . '%')
                  ->orWhere('perihal', 'like', '%' . $search . '%');
            });
        }

        $surat = $query->get();

        return view('surat', compact('surat'));
    }

    /**
     * FORM TAMBAH SURAT MASUK (kalau tidak memakai modal).
     */
    public function createMasuk()
    {
        return view('surat-create-masuk');
    }

    /**
     * SIMPAN SURAT MASUK BARU
     * (kalau kamu pakai SuratMasukController untuk ini, method ini boleh dihapus)
     */
    public function storeMasuk(Request $request)
    {
        $data = $request->validate([
            'tanggal_surat'   => 'required|date',
            'asal_surat'      => 'required|string|max:255',
            'perihal'         => 'required|string',
            'perihal_lainnya' => 'nullable|string|max:500',
            'file_surat'      => 'nullable|file|mimes:pdf,docx|max:2048',
        ]);

        // Handle perihal "lainnya"
        $perihalFinal = ($data['perihal'] === 'lainnya') 
            ? ($data['perihal_lainnya'] ?? '') 
            : $data['perihal'];

        if ($request->hasFile('file_surat')) {
            $data['file_surat'] = $request->file('file_surat')
                ->store('surat-masuk', $this->suratDisk());
        }

        SuratMasuk::create([
            'tanggal_surat' => $data['tanggal_surat'],
            'asal_surat'    => $data['asal_surat'],
            'perihal'       => $perihalFinal,
            'file_surat'    => $data['file_surat'] ?? null,
            'status'        => 'Belum Dibalas',
        ]);

        return redirect()->route('surat')
            ->with('success', 'Surat masuk berhasil ditambahkan.');
    }

    /**
     * FORM EDIT SURAT MASUK
     * Route (contoh): GET /surat-resource/{id}/edit  -> surat-resource.edit
     */
    public function edit($id)
    {
        $surat = SuratMasuk::findOrFail($id);

        // kalau kamu pakai modal terpisah, bisa return view lain
        return view('surat-edit-masuk', compact('surat'));
    }

    /**
     * UPDATE SURAT MASUK
     * Route (resource): PUT/PATCH /surat-resource/{id} -> surat-resource.update
     */
    public function update(Request $request, $id)
    {
        $surat = SuratMasuk::findOrFail($id);

        $data = $request->validate([
            'tanggal_surat'   => 'required|date',
            'asal_surat'      => 'required|string|max:255',
            'perihal'         => 'required|string',
            'perihal_lainnya' => 'nullable|string|max:500',
            'file_surat'      => 'nullable|file|mimes:pdf,docx|max:2048',
        ]);

        // Handle perihal "lainnya"
        $perihalFinal = ($data['perihal'] === 'lainnya') 
            ? ($data['perihal_lainnya'] ?? '') 
            : $data['perihal'];

        if ($request->hasFile('file_surat')) {
            // hapus file lama kalau ada
            if ($surat->file_surat) {
                Storage::disk($this->suratDisk())->delete($surat->file_surat);
            }

            $data['file_surat'] = $request->file('file_surat')
                ->store('surat-masuk', $this->suratDisk());
        } else {
            // Jika tidak ada upload baru, gunakan file lama
            $data['file_surat'] = $surat->file_surat;
        }

        $surat->update([
            'tanggal_surat' => $data['tanggal_surat'],
            'asal_surat'    => $data['asal_surat'],
            'perihal'       => $perihalFinal,
            'file_surat'    => $data['file_surat'],
        ]);

        return redirect()->route('surat')
            ->with('success', 'Surat masuk berhasil diperbarui.');
    }

    /**
     * FORM BALASAN SURAT KELUAR (opsional kalau tidak pakai modal).
     */
    public function createKeluar(Request $request)
    {
        $ref = $request->get('ref'); // id surat masuk
        return view('surat-create-keluar', compact('ref'));
    }

    /**
     * SIMPAN BALASAN SURAT KELUAR (pertama kali buat balasan).
     * Route: surat-keluar.store
     */
    public function storeKeluar(Request $request)
    {
        Log::info('Balasan Request:', $request->all());

        $data = $request->validate([
            'surat_masuk_id'  => ['required', 'integer', 'exists:surat_masuk,id'],
            'no_surat'        => ['nullable', 'string', 'max:255'],
            'tanggal_surat'   => ['required', 'date'],
            'tujuan_surat'    => ['required', 'string', 'max:255'],
            'perihal'         => ['required', 'string'],
            'perihal_lainnya' => ['nullable', 'string'],
            'file_balasan'    => ['required', 'file', 'mimes:pdf,docx', 'max:2048'],
        ]);

        // Jika perihal adalah 'lainnya', gunakan perihal_lainnya
        $perihalFinal = ($data['perihal'] === 'lainnya' && !empty($data['perihal_lainnya']))
            ? $data['perihal_lainnya']
            : $data['perihal'];

        $suratMasuk = SuratMasuk::findOrFail($data['surat_masuk_id']);

        $filePath = $request->file('file_balasan')
            ->store('surat-keluar', $this->suratDisk());

        $noSurat = $data['no_surat']
            ?? 'SK/' . str_pad($suratMasuk->id, 3, '0', STR_PAD_LEFT) . '/' . date('Y');

        $suratMasuk->update([
            'no_surat_balasan' => $noSurat,
            'tanggal_balasan'  => $data['tanggal_surat'],
            'tujuan_surat'     => $data['tujuan_surat'],
            'perihal_balasan'  => $perihalFinal,
            'file_balasan'     => $filePath,
            'status'           => 'Sudah Dibalas',
        ]);

        return redirect()
            ->route('surat')
            ->with('success', 'Balasan surat keluar berhasil ditambahkan.');
    }

    /**
     * FORM EDIT BALASAN SURAT KELUAR.
     * Bisa pakai modal di halaman /surat, atau view terpisah.
     */
    public function editBalasan($id)
    {
        $surat = SuratMasuk::findOrFail($id);

        return view('surat-edit-keluar', compact('surat'));
        // kalau pakai modal, view di atas tinggal berisi form saja lalu kamu include.
    }

    /**
     * UPDATE BALASAN SURAT KELUAR.
     */
    public function updateBalasan(Request $request, $id)
    {
        $suratMasuk = SuratMasuk::findOrFail($id);

        $data = $request->validate([
            'no_surat'      => ['nullable', 'string', 'max:255'],
            'tanggal_surat' => ['required', 'date'],
            'tujuan_surat'  => ['required', 'string', 'max:255'],
            'perihal'       => ['required', 'string'],
            'file_balasan'  => ['nullable', 'file', 'mimes:pdf,docx', 'max:2048'],
        ]);

        $filePath = $suratMasuk->file_balasan;

        if ($request->hasFile('file_balasan')) {
            if ($filePath) {
                Storage::disk($this->suratDisk())->delete($filePath);
            }
            $filePath = $request->file('file_balasan')
                ->store('surat-keluar', $this->suratDisk());
        }

        // Handle no_surat - jika kosong atau null, gunakan nilai lama atau auto-generate
        $noSurat = !empty($data['no_surat'])
            ? $data['no_surat']
            : ($suratMasuk->no_surat_balasan ?? 'SK/' . str_pad($suratMasuk->id, 3, '0', STR_PAD_LEFT) . '/' . date('Y'));

        $suratMasuk->update([
            'no_surat_balasan' => $noSurat,
            'tanggal_balasan'  => $data['tanggal_surat'],
            'tujuan_surat'     => $data['tujuan_surat'],
            'perihal_balasan'  => $data['perihal'],
            'file_balasan'     => $filePath,
        ]);

        return redirect()
            ->route('surat')
            ->with('success', 'Balasan surat keluar berhasil diperbarui.');
    }

    /**
     * HAPUS BALASAN SURAT KELUAR
     */
    public function destroyBalasan(SuratMasuk $surat)
    {
        if ($surat->file_balasan) {
            Storage::disk($this->suratDisk())->delete($surat->file_balasan);
        }

        $surat->update([
            'no_surat_balasan' => null,
            'tanggal_balasan'  => null,
            'tujuan_surat'     => null,
            'perihal_balasan'  => null,
            'file_balasan'     => null,
            'status'           => 'Belum Dibalas',
        ]);

        return redirect()
            ->route('surat')
            ->with('success', 'Balasan surat keluar berhasil dihapus.');
    }

    /**
     * HAPUS SURAT MASUK
     * Route: surat-resource.destroy
     */
    public function destroy($id)
    {
        $surat = SuratMasuk::findOrFail($id);

        if ($surat->file_surat) {
            Storage::disk($this->suratDisk())->delete($surat->file_surat);
        }
        if ($surat->file_balasan) {
            Storage::disk($this->suratDisk())->delete($surat->file_balasan);
        }

        $surat->delete();

        return redirect()
            ->route('surat')
            ->with('success', 'Surat berhasil dihapus.');
    }

    private function suratDisk(): string
    {
        return config('filesystems.surat_disk', 'public');
    }
}
