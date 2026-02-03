<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfilController extends Controller
{
    /**
     * Tampilkan halaman profil custom (/profil).
     */
    public function index()
    {
        $user = Auth::user();

        return view('profil', compact('user'));
    }

    /**
     * Update data profil (nama, email, dan no. HP).
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // Validasi input
        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:50'],
        ]);

        // Update data user
        $user->update($validated);

        return redirect()
            ->route('profil')
            ->with('success', 'Profil berhasil diperbarui.');
    }
}
