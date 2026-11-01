<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Pengaturan\HakAksesController;
use App\Models\Notaris;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class NotarisController extends Controller
{
    public function index(Request $request)
    {
        Carbon::setLocale('id');

        $permissions = HakAksesController::getUserPermissions();

        if ($request->ajax()) {
            $opd = Notaris::orderBy('id_notaris', 'desc');

            return DataTables::of($opd)
                ->addIndexColumn()
                ->addColumn('action', function ($row) use ($permissions) {
                    $editUrl = route('notaris.edit', $row->id_notaris);
                    $deleteUrl = route('notaris.destroy', $row->id_notaris);

                    $btn = '<div class="d-flex justify-content-center">';
                    if ($permissions['edit']) {
                        $btn .= '<button class="btn btn-primary btn-sm edit-button" data-id="' . e($row->id_notaris) . '" data-url="' . e($editUrl) . '">Edit</button>';
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

        return view('admin.master.notaris.index', compact('permissions'));
    }

    public function edit($id)
    {
        $data = Notaris::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data'   => $data,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_notaris'       => 'required|string|max:255',
            'alamat_notaris'     => 'required|string|max:500',
            'telp_notaris'       => ['required', 'string', 'max:20', 'regex:/^\+?[0-9\s\-]+$/'],
            'keterangan_notaris' => 'required|string|max:1000',
        ], [
            'nama_notaris.required'       => 'Nama notaris wajib diisi.',
            'alamat_notaris.required'     => 'Alamat notaris wajib diisi.',
            'telp_notaris.required'       => 'Nomor telepon notaris wajib diisi.',
            'telp_notaris.regex'          => 'Format nomor telepon notaris tidak valid.',
            'keterangan_notaris.required' => 'Keterangan notaris wajib diisi.',
        ]);

        DB::beginTransaction();
        try {
            $db = [
                'nama_notaris'       => $request->nama_notaris,
                'alamat_notaris'     => $request->alamat_notaris,
                'telp_notaris'       => $request->telp_notaris,
                'keterangan_notaris' => $request->keterangan_notaris,
            ];

            Notaris::create($db);

            DB::commit();

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan notaris: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'error'  => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $data = Notaris::findOrFail($id);

        $request->validate([
            'nama_notaris'       => 'required|string|max:255',
            'alamat_notaris'     => 'required|string|max:500',
            'telp_notaris'       => ['required', 'string', 'max:20', 'regex:/^\+?[0-9\s\-]+$/'],
            'keterangan_notaris' => 'required|string|max:1000',
        ], [
            'nama_notaris.required'       => 'Nama notaris wajib diisi.',
            'alamat_notaris.required'     => 'Alamat notaris wajib diisi.',
            'telp_notaris.required'       => 'Nomor telepon notaris wajib diisi.',
            'telp_notaris.regex'          => 'Format nomor telepon notaris tidak valid.',
            'keterangan_notaris.required' => 'Keterangan notaris wajib diisi.',
        ]);

        DB::beginTransaction();
        try {
            $db = [
                'nama_notaris'       => $request->nama_notaris,
                'alamat_notaris'     => $request->alamat_notaris,
                'telp_notaris'       => $request->telp_notaris,
                'keterangan_notaris' => $request->keterangan_notaris,
            ];

            $data->update($db);

            DB::commit();

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal mengupdate notaris: ' . $e->getMessage());
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
            $data = Notaris::findOrFail($id);
            $data->delete();

            DB::commit();

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menghapus notaris: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'error'  => $e->getMessage(),
            ], 500);
        }
    }
}
