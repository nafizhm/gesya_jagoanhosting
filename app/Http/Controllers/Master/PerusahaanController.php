<?php
namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Pengaturan\HakAksesController;
use App\Models\Perusahaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class PerusahaanController extends Controller
{
    public function index(Request $request)
    {
        $permissions = HakAksesController::getUserPermissions();

        if ($request->ajax()) {
            $data = Perusahaan::orderBy('id_perusahaan', 'desc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) use ($permissions) {
                    $showUrl    = route('perusahaan.show', $row->id_perusahaan);
                    $deleteUrl  = route('perusahaan.destroy', $row->id_perusahaan);

                    $btn = '<div class="d-flex justify-content-center">';
                    if ($permissions['edit']) {
                        $btn .= '<button class="btn btn-primary btn-sm edit-button" data-id="' . e($row->id_perusahaan) . '" data-url="' . e($showUrl) . '">Edit</button>';
                    }

                    if ($permissions['hapus']) {
                        $btn .= '<form action="' . e($deleteUrl) . '" method="POST" style="display:inline;">' . csrf_field() . method_field('DELETE') . '<button type="submit" class="delete-button btn btn-danger btn-sm ml-2">Hapus</button></form>';
                    }
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.master.perusahaan.index', compact('permissions'));
    }

    public function show($id)
    {
        $data = Perusahaan::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data'   => $data,
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'nama_perusahaan'       => 'required|string|max:100',
            'alamat_perusahaan'     => 'required|string|max:200',
            'kawasan'               => 'nullable|string|max:255',
            'telp_perusahaan'       => 'required|string|max:35',
            'kota_penandatangan'    => 'required|string|max:35',
            'nama_penandatangan'    => 'required|string|max:50',
            'jabatan_penandatangan' => 'required|string|max:50',
            'nama_mengetahui'       => 'required|string|max:50',
            'bg_kwitansi'           => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'kop_surat'             => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ];

        $messages = [
            'nama_perusahaan.required'       => 'Nama perusahaan wajib diisi.',
            'alamat_perusahaan.required'     => 'Alamat perusahaan wajib diisi.',
            'telp_perusahaan.required'       => 'Telepon perusahaan wajib diisi.',
            'kota_penandatangan.required'    => 'Kota penandatangan wajib diisi.',
            'nama_penandatangan.required'    => 'Nama penandatangan wajib diisi.',
            'jabatan_penandatangan.required' => 'Jabatan penandatangan wajib diisi.',
            'nama_mengetahui.required'       => 'Nama mengetahui wajib diisi.',
        ];

        $request->validate($rules, $messages);

        DB::beginTransaction();
        try {
            $data = [
                'nama_perusahaan'       => $request->nama_perusahaan,
                'alamat_perusahaan'     => $request->alamat_perusahaan,
                'kawasan'               => $request->kawasan,
                'telp_perusahaan'       => $request->telp_perusahaan,
                'kota_penandatangan'    => $request->kota_penandatangan,
                'nama_penandatangan'    => $request->nama_penandatangan,
                'jabatan_penandatangan' => $request->jabatan_penandatangan,
                'nama_mengetahui'       => $request->nama_mengetahui,
                'bg_kwitansi'           => null,
                'kop_surat'             => null,
            ];

            if ($request->hasFile('bg_kwitansi')) {
                $file     = $request->file('bg_kwitansi');
                $ext      = $file->getClientOriginalExtension();
                $filename = Str::random(25) . '.' . $ext;
                $file->move(public_path('assets/lokasi_perumahan/bg_kwitansi/'), $filename);
                $data['bg_kwitansi'] = $filename;
            }

            if ($request->hasFile('kop_surat')) {
                $file     = $request->file('kop_surat');
                $ext      = $file->getClientOriginalExtension();
                $filename = Str::random(25) . '.' . $ext;
                $file->move(public_path('assets/lokasi_perumahan/kop_surat/'), $filename);
                $data['kop_surat'] = $filename;
            }

            Perusahaan::create($data);

            DB::commit();

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan perusahaan: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'error'  => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $data = Perusahaan::findOrFail($id);

        $rules = [
            'nama_perusahaan'       => 'required|string|max:100',
            'alamat_perusahaan'     => 'required|string|max:200',
            'kawasan'               => 'nullable|string|max:255',
            'telp_perusahaan'       => 'required|string|max:35',
            'kota_penandatangan'    => 'required|string|max:35',
            'nama_penandatangan'    => 'required|string|max:50',
            'jabatan_penandatangan' => 'required|string|max:50',
            'nama_mengetahui'       => 'required|string|max:50',
            'bg_kwitansi'           => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'kop_surat'             => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ];

        $messages = [
            'nama_perusahaan.required'       => 'Nama perusahaan wajib diisi.',
            'alamat_perusahaan.required'     => 'Alamat perusahaan wajib diisi.',
            'telp_perusahaan.required'       => 'Telepon perusahaan wajib diisi.',
            'kota_penandatangan.required'    => 'Kota penandatangan wajib diisi.',
            'nama_penandatangan.required'    => 'Nama penandatangan wajib diisi.',
            'jabatan_penandatangan.required' => 'Jabatan penandatangan wajib diisi.',
            'nama_mengetahui.required'       => 'Nama mengetahui wajib diisi.',
        ];

        $request->validate($rules, $messages);

        DB::beginTransaction();
        try {
            $db = [
                'nama_perusahaan'       => $request->nama_perusahaan,
                'alamat_perusahaan'     => $request->alamat_perusahaan,
                'kawasan'               => $request->kawasan,
                'telp_perusahaan'       => $request->telp_perusahaan,
                'kota_penandatangan'    => $request->kota_penandatangan,
                'nama_penandatangan'    => $request->nama_penandatangan,
                'jabatan_penandatangan' => $request->jabatan_penandatangan,
                'nama_mengetahui'       => $request->nama_mengetahui,
            ];

            if ($request->hasFile('bg_kwitansi')) {
                $file     = $request->file('bg_kwitansi');
                $ext      = $file->getClientOriginalExtension();
                $filename = Str::random(25) . '.' . $ext;
                $file->move(public_path('assets/lokasi_perumahan/bg_kwitansi/'), $filename);

                if ($data->bg_kwitansi && file_exists(public_path('assets/lokasi_perumahan/bg_kwitansi/' . $data->bg_kwitansi))) {
                    unlink(public_path('assets/lokasi_perumahan/bg_kwitansi/' . $data->bg_kwitansi));
                }

                $db['bg_kwitansi'] = $filename;
            }

            if ($request->hasFile('kop_surat')) {
                $file     = $request->file('kop_surat');
                $ext      = $file->getClientOriginalExtension();
                $filename = Str::random(25) . '.' . $ext;
                $file->move(public_path('assets/lokasi_perumahan/kop_surat/'), $filename);

                if ($data->kop_surat && file_exists(public_path('assets/lokasi_perumahan/kop_surat/' . $data->kop_surat))) {
                    unlink(public_path('assets/lokasi_perumahan/kop_surat/' . $data->kop_surat));
                }

                $db['kop_surat'] = $filename;
            }

            $data->update($db);

            DB::commit();

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal mengupdate perusahaan: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'error'  => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $data = Perusahaan::findOrFail($id);

            if ($data->bg_kwitansi && file_exists(public_path('assets/lokasi_perumahan/bg_kwitansi/' . $data->bg_kwitansi))) {
                unlink(public_path('assets/lokasi_perumahan/bg_kwitansi/' . $data->bg_kwitansi));
            }

            if ($data->kop_surat && file_exists(public_path('assets/lokasi_perumahan/kop_surat/' . $data->kop_surat))) {
                unlink(public_path('assets/lokasi_perumahan/kop_surat/' . $data->kop_surat));
            }

            $data->delete();

            DB::commit();

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menghapus perusahaan: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'error'  => $e->getMessage(),
            ], 500);
        }
    }
}
