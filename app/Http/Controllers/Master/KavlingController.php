<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Pengaturan\HakAksesController;
use App\Models\KavlingPeta;
use App\Models\FotoKavling;
use App\Models\Perusahaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class KavlingController extends Controller
{
    public function index(Request $request)
    {
        $permissions = HakAksesController::getUserPermissions();

        if ($request->ajax()) {
            $data = KavlingPeta::select(
                'kavling_peta.*',
                'lokasi_kavling.nama_kavling as nama_cluster'
            )
                ->leftJoin('lokasi_kavling', 'kavling_peta.id_lokasi', '=', 'lokasi_kavling.id_lokasi');

            $searchValue = $request->input('search.value', '');
            if ($searchValue !== '') {
                $data->where('kode_kavling', 'like', '%' . $searchValue . '%');
            }

            $data->orderBy('id_kavling', 'desc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('panjang', function ($row) {
                    return '<p>pjg kanan: <strong>' . $row->panjang_kanan . ' m</strong></p>'
                         . '<p>pjg kiri: <strong>' . $row->panjang_kiri . ' m</strong></p>';
                })
                ->addColumn('lebar', function ($row) {
                    return '<p>lebar depan: <strong>' . $row->lebar_depan . ' m</strong></p>'
                         . '<p>lebar belakang: <strong>' . $row->lebar_belakang . ' m</strong></p>';
                })
                ->addColumn('luas', function ($row) {
                    return '<p>luas tanah: <strong>' . $row->luas_tanah . ' m</strong></p>'
                         . '<p>luas bangunan: <strong>' . $row->luas_bangunan . ' m</strong></p>';
                })
                ->addColumn('harga', function ($row) {
                    return number_format($row->hrg_jual, 0, ',', '.');
                })
                ->addColumn('lokasi', function ($row) {
                    return $row->kode_kavling;
                })
                ->addColumn('action', function ($row) use ($permissions) {
                    $showUrl = route('kavling.show', $row->id_kavling);

                    $btn = '<div class="d-flex justify-content-center">';
                    if ($permissions['edit']) {
                        $btn .= '<button class="btn btn-primary btn-sm edit-button" data-id="' . e($row->id_kavling) . '" data-url="' . e($showUrl) . '">Edit</button>';
                    }
                    $btn .= ' <a href="' . route('kavling.lampiran', $row->id_kavling) . '" class="btn btn-success btn-sm ml-1">Lampiran</a>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['panjang', 'lebar', 'luas', 'harga', 'action'])
                ->make(true);
        }

        return view('admin.master.kavling.index', compact('permissions'));
    }

    public function show($id)
    {
        $data = KavlingPeta::with('lokasi')->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data'   => $data,
        ]);
    }

    public function getPerusahaanByLokasi($id_lokasi)
    {
        $perusahaan = Perusahaan::whereHas('lokasiKavling', function ($q) use ($id_lokasi) {
            $q->where('lokasi_kavling.id_lokasi', $id_lokasi);
        })->orderBy('nama_perusahaan', 'asc')->get();

        return response()->json([
            'success' => true,
            'data'    => $perusahaan,
        ]);
    }

    public function update(Request $request, $id)
    {
        $data = KavlingPeta::findOrFail($id);

        $request->merge([
            'hrg_jual'   => str_replace('.', '', $request->hrg_jual),
            'biaya_lain' => str_replace('.', '', $request->biaya_lain ?? '0'),
        ]);

        $rules = [
            'panjang_kanan'      => 'required|integer',
            'panjang_kiri'       => 'required|integer',
            'lebar_depan'        => 'required|integer',
            'lebar_belakang'     => 'required|integer',
            'luas_tanah'         => 'required|integer',
            'hrg_meter'          => 'required|integer',
            'tipe_bangunan'      => 'required',
            'luas_bangunan'      => 'required',
            'hrg_jual'           => 'required|string',
            'biaya_lain'         => 'nullable|numeric',
            'keterangan'         => 'nullable|string|max:100',
            'daya_listrik'       => 'nullable|string',
            'id_rumah_sikumbang' => 'nullable|string|max:50',
            'no_sertifikat'      => 'nullable|string|max:35',
            'id_perusahaan'      => 'nullable|integer',
        ];

        $messages = [
            'panjang_kanan.required'      => 'Panjang kanan wajib diisi.',
            'panjang_kanan.integer'       => 'Panjang kanan harus berupa angka.',
            'panjang_kiri.required'       => 'Panjang kiri wajib diisi.',
            'panjang_kiri.integer'        => 'Panjang kiri harus berupa angka.',
            'lebar_depan.required'        => 'Lebar depan wajib diisi.',
            'lebar_depan.integer'         => 'Lebar depan harus berupa angka.',
            'lebar_belakang.required'     => 'Lebar belakang wajib diisi.',
            'lebar_belakang.integer'      => 'Lebar belakang harus berupa angka.',
            'luas_tanah.required'         => 'Luas tanah wajib diisi.',
            'luas_tanah.integer'          => 'Luas tanah harus berupa angka.',
            'hrg_meter.required'          => 'Harga per meter wajib diisi.',
            'hrg_meter.integer'           => 'Harga per meter harus berupa angka.',
            'tipe_bangunan.required'      => 'Tipe bangunan wajib diisi.',
            'luas_bangunan.required'      => 'Luas bangunan wajib diisi.',
            'hrg_jual.required'           => 'Harga jual wajib diisi.',
            'hrg_jual.string'             => 'Harga jual harus berupa teks.',
            'biaya_lain.numeric'          => 'Biaya lain harus berupa angka.',
            'keterangan.string'           => 'Keterangan harus berupa teks.',
            'keterangan.max'              => 'Keterangan maksimal 100 karakter.',
            'daya_listrik.integer'        => 'Daya listrik harus berupa angka.',
            'id_rumah_sikumbang.string'   => 'ID Rumah Sikumbang harus berupa teks.',
            'id_rumah_sikumbang.max'      => 'ID Rumah Sikumbang maksimal 50 karakter.',
            'no_sertifikat.string'        => 'Nomor sertifikat harus berupa teks.',
            'no_sertifikat.max'           => 'Nomor sertifikat maksimal 35 karakter.',
        ];

        $request->validate($rules, $messages);

        DB::beginTransaction();
        try {
            $data->update([
                'panjang_kanan'      => $request->panjang_kanan,
                'panjang_kiri'       => $request->panjang_kiri,
                'lebar_depan'        => $request->lebar_depan,
                'lebar_belakang'     => $request->lebar_belakang,
                'luas_tanah'         => $request->luas_tanah,
                'hrg_meter'          => $request->hrg_meter,
                'tipe_bangunan'      => $request->tipe_bangunan,
                'luas_bangunan'      => $request->luas_bangunan,
                'hrg_jual'           => $request->hrg_jual,
                'biaya_lain'         => $request->biaya_lain,
                'keterangan'         => $request->keterangan ?? '',
                'daya_listrik'       => $request->daya_listrik ?? '',
                'id_rumah_sikumbang' => $request->id_rumah_sikumbang ?? '',
                'no_sertifikat'      => $request->no_sertifikat ?? '',
                'id_perusahaan'      => $request->id_perusahaan ?? null,
            ]);

            DB::commit();

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal mengupdate kavling: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'error'  => $e->getMessage(),
            ], 500);
        }
    }

    public function lampiran($id)
    {
        $kavling = KavlingPeta::with('lokasi')->findOrFail($id);
        $fotos = FotoKavling::where('id_kavling', $id)->get();

        return view('admin.master.kavling.lampiran', compact('kavling', 'fotos'));
    }

    public function uploadLampiran(Request $request, $id)
    {
        $rules = [
            'tanggal'    => 'required|date',
            'nama_file'  => 'required|string|max:255',
            'lampiran'   => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'keterangan' => 'required|string|max:500',
        ];

        $messages = [
            'tanggal.required'    => 'Tanggal upload wajib diisi.',
            'tanggal.date'        => 'Tanggal upload tidak valid.',
            'nama_file.required'  => 'Nama file wajib diisi.',
            'nama_file.string'    => 'Nama file harus berupa teks.',
            'nama_file.max'       => 'Nama file maksimal 255 karakter.',
            'lampiran.required'   => 'File wajib diunggah.',
            'lampiran.file'       => 'Lampiran harus berupa file.',
            'lampiran.mimes'      => 'File harus berupa JPG, JPEG, PNG, atau PDF.',
            'lampiran.max'        => 'Ukuran file maksimal 5MB.',
            'keterangan.required' => 'Keterangan wajib diisi.',
            'keterangan.string'   => 'Keterangan harus berupa teks.',
            'keterangan.max'      => 'Keterangan maksimal 500 karakter.',
        ];

        $request->validate($rules, $messages);

        $file = $request->file('lampiran');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('foto_kavling'), $filename);

        FotoKavling::create([
            'tanggal'    => $request->tanggal,
            'id_kavling' => $id,
            'file_name'  => $request->nama_file,
            'lampiran'   => 'foto_kavling/' . $filename,
            'keterangan' => $request->keterangan,
        ]);

        return response()->json(['status' => 'success']);
    }

    public function destroyLampiran($id)
    {
        DB::beginTransaction();
        try {
            $foto = FotoKavling::findOrFail($id);

            if (file_exists(public_path($foto->lampiran))) {
                unlink(public_path($foto->lampiran));
            }

            $foto->delete();

            DB::commit();

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menghapus lampiran kavling: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'error'  => $e->getMessage(),
            ], 500);
        }
    }
}
