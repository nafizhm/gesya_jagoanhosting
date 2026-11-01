<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Pengaturan\HakAksesController;
use App\Models\UploadFile;
use Illuminate\Support\Facades\Storage;

class BerkasCustomerController extends Controller
{
    public function index()
    {
        $data = UploadFile::first();
        $customers = Customer::select('id_customer', 'kode_customer', 'nama_lengkap')->orderBy('nama_lengkap')->get();
        $permissions = HakAksesController::getUserPermissions();

        return view('admin.customer_induk.berkas_customer.index', compact('data', 'customers', 'permissions'));
    }

    public function getFileNasabah($id)
    {
        $files = UploadFile::where('nasabah', $id)->get();
        return response()->json($files);
    }

    public function uploadFile(Request $request)
    {
        $request->validate([
            'tanggal'    => 'required|date',
            'nama_file'  => 'required|string',
            'lampiran'   => 'required|file|mimes:jpg,jpeg,png,webp,pdf',
        ], [
            'tanggal.required'      => 'Tanggal wajib diisi.',
            'tanggal.date'          => 'Tanggal tidak valid.',
            'nama_file.required'    => 'Nama file wajib diisi.',
            'lampiran.required'     => 'Lampiran wajib diunggah.',
            'lampiran.file'         => 'Lampiran harus berupa file.',
            'lampiran.mimes'        => 'Hanya mendukung jpg, jpeg, png, webp, atau pdf.',
        ]);

        $folder = 'berkas_user';
        $destination = public_path($folder);

        if (!file_exists($destination)) {
            mkdir($destination, 0755, true);
        }

        $file = $request->file('lampiran');
        $originalExtension = strtolower($file->getClientOriginalExtension());

        $cleanName = preg_replace('/[^a-zA-Z0-9-_\.]/', '_', pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $finalName = time() . '_' . $cleanName . '.' . $originalExtension;

        $file->move($destination, $finalName);

        UploadFile::create([
            'tanggal'     => $request->tanggal,
            'nasabah'     => $request->nasabah,
            'folder'      => $folder,
            'nama_file'   => $request->nama_file,
            'keterangan'  => $request->keterangan,
            'lampiran'    => $finalName,
        ]);

        return response()->json(['success' => true]);
    }

    public function deleteFile($id)
    {
        $file = UploadFile::findOrFail($id);
        if (Storage::exists('public/' . $file->folder . '/' . $file->lampiran)) {
            Storage::delete('public/' . $file->folder . '/' . $file->lampiran);
        }
        $file->delete();

        return response()->json(['success' => true]);
    }

    public function getNasabah(Request $request)
    {
        $search = $request->get('q');
        $nasabah = Customer::where('nama_lengkap', 'like', "%$search%")
            ->orWhere('kode_customer', 'like', "%$search%")
            ->select('id_customer', 'nama_lengkap', 'kode_customer')
            ->get();

        return response()->json($nasabah);
    }

    public function getNasabahDetails($id)
    {
        $nasabah = Customer::with(['lokasiKavling', 'kavlingPeta'])
            ->where('id_customer', $id)
            ->first();

        if ($nasabah) {
            return response()->json([
                'no_ktp' => $nasabah->no_ktp,
                'lokasi_perumahan' => $nasabah->lokasiKavling ? $nasabah->lokasiKavling->nama_kavling : null,
                'no_telp' => $nasabah->no_wa,
                'lokasi_kav_blok' => $nasabah->kavlingPeta ? $nasabah->kavlingPeta->kode_kavling : null,
            ]);
        }

        return response()->json([], 404);
    }

    public function update(Request $request)
    {
        try {
            $id = $request->input('id');
            $konfigurasi = UploadFile::findOrFail($id);

            $request->validate([
                'nama_file' => 'required',
            ]);

            $konfigurasi->update([
                'nama_file' => $request->nama_file,
                'keterangan' => $request->keterangan,
            ]);

            return response()->json(['status' => 'success', 'message' => 'Data Berhasil Diubah!']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal mengubah data'], 500);
        }
    }
}
