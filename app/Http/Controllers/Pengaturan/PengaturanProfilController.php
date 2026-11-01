<?php


namespace App\Http\Controllers\Pengaturan;
use App\Http\Controllers\Controller;

use App\Models\PengaturanProfil;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Pengaturan\HakAksesController;

class pengaturanProfilController extends Controller
{
    public function index()
    {
        $data = PengaturanProfil::first();
        $permissions = HakAksesController::getUserPermissions(); // pakai hak akses dinamis

        return view('admin.pengaturan.pengaturan_profil.index', compact('data', 'permissions'));
    }
    public function update(Request $request): RedirectResponse
    {

        $id = $request->input('id');
        $konfigurasi = PengaturanProfil::findOrFail($id);

        $request->validate([
            'nama_perusahaan' => 'string|max:255',
            'alamat' => 'string|max:255',
            'email' => 'email|max:255',
            'telp' => 'max:9999999999',
            'hape' => 'max:9999999999',
            'fax' => 'string|max:20',
            'npwp_perusahaan' => 'nullable|string|max:20',
        ], [
            'nama_perusahaan.' => 'Nama Perusahaan wajib diisi.',
            'nama_perusahaan.string' => 'Nama Perusahaan harus berupa teks.',
            'nama_perusahaan.max' => 'Nama Perusahaan tidak boleh lebih dari 255 karakter.',

            'alamat.string' => 'Alamat harus berupa teks.',
            'alamat.max' => 'Alamat tidak boleh lebih dari 255 karakter.',

            'email.email' => 'Email harus berupa alamat email yang valid.',
            'email.max' => 'Email tidak boleh lebih dari 255 karakter.',

            'telp.max' => 'Nomor Telepon tidak boleh lebih dari 100 digit.',

            'hape.max' => 'Nomor Handphone tidak boleh lebih dari 100 digit.',

            'fax.string' => 'Nomor Fax harus berupa teks.',
            'fax.max' => 'Nomor Fax tidak boleh lebih dari 20 karakter.',

            'npwp_perusahaan.nullable' => 'NPWP Perusahaan bersifat opsional.',
            'npwp_perusahaan.string' => 'NPWP Perusahaan harus berupa teks.',
            'npwp_perusahaan.max' => 'NPWP Perusahaan tidak boleh lebih dari 20 karakter.',

        ]);

        $data = PengaturanProfil::first();
        if ($data) {
            $data->nama_perusahaan = $request->nama_perusahaan;
            $data->alamat = $request->alamat;
            $data->email = $request->email;

            $data->save();
        }
        $konfigurasi->update([
            'nama_perusahaan' => $request->nama_perusahaan,
            'alamat' => $request->alamat,
            'email' => $request->email,
            'telp' => $request->telp,
            'hape' => $request->hape,
            'npwp_perusahaan' => $request->npwp_perusahaan ?? '',
            'folder_svg' => $request->folder_svg,
        ]);

        return response()->json(['status' => 'success', 'message' => 'Data Berhasil Diubah!']);
    }
}
