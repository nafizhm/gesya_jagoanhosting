<?php


namespace App\Http\Controllers\Pengaturan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ListPembangunan;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\Pengaturan\HakAksesController;

class ListPembangunanController extends Controller
{
    public function index(Request $request)
    {
        $permissions = HakAksesController::getUserPermissions();
        if ($request->ajax()) {
            $program = ListPembangunan::query();

            return DataTables::of($program)
                ->addIndexColumn()

                ->addColumn('warna_display', function ($row) {
                    return '<div style="display: flex; align-items: center;">
                            <div style="width: 15px; height: 15px; background-color: ' . $row->warna . '; border: 1px solid #000; margin-right: 5px;"></div>
                            <span>' . $row->warna . '</span>
                        </div>';
                })

                ->addColumn('action', function ($row) use ($permissions) {
                    $actions = '<div class="text-center">';

                    if ($permissions['edit']) {
                        $actions .= '<button type="button" onclick="modalAction(\'/admin/pengaturan/list-pembangunan/'
                            . $row->id_list_pembangunan
                            . '/edit\')" class="edit btn btn-primary btn-sm">Edit</button>';
                    }

                    if ($permissions['hapus']) {
                        $actions .= '
        <form action="/admin/pengaturan/list-pembangunan/'
                            . $row->id_list_pembangunan
                            . '" method="POST" style="display:inline;">
            ' . csrf_field() . method_field('DELETE') . '
            <button type="submit" class="btn btn-danger btn-sm delete-button">Delete</button>
        </form>';
                    }

                    $actions .= '</div>';
                    return $actions;
                })

                ->rawColumns(['action', 'warna_display', 'is_admin'])
                ->make(true);
        }

        $permissions = HakAksesController::getUserPermissions();
        $data = ListPembangunan::first();

        return view('admin.pengaturan.list_pembangunan.index', compact('data', 'permissions'));
    }

    public function create()
    {
        return view('admin.pengaturan.list_pembangunan.tambah');
    }
    public function store(Request $request)
    {
        $request->validate([
            'keterangan' => 'required|string|max:50',
            'warna'      => 'required',
        ], [
            'keterangan.required' => 'Keterangan wajib diisi.',
            'keterangan.string'   => 'Keterangan harus berupa teks.',
            'keterangan.max'      => 'Keterangan maksimal 50 karakter.',

            'warna.required'      => 'Warna wajib diisi.',
        ]);


        // Menyimpan data
        ListPembangunan::create([
            'keterangan' => $request->keterangan,
            'warna' => $request->warna,
        ]);

        // Mengirim respons JSON setelah berhasil
        return response()->json([
            'status' => true,
            'message' => 'Data berhasil ditambahkan!',
        ]);
    }


    public function edit(string $id)
    {
        //
        $data = ListPembangunan::find($id);
        return view('admin.pengaturan.list_pembangunan.edit', compact('data'));
    }


    public function update(Request $request, $id_list_pembangunan)
    {
        // Validasi data yang diterima
        $request->validate([
            'keterangan' => 'required|string|max:50',
            'warna'      => 'required|string',
        ], [
            'keterangan.required' => 'Keterangan wajib diisi.',
            'keterangan.string'   => 'Keterangan harus berupa teks.',
            'keterangan.max'      => 'Keterangan maksimal 50 karakter.',

            'warna.required'      => 'Warna wajib diisi.',
            'warna.string'        => 'Warna harus berupa teks.',
        ]);


        // Cari data berdasarkan id
        $data = ListPembangunan::findOrFail($id_list_pembangunan);

        // Update data dengan request baru
        $data->update([
            'keterangan' => $request->keterangan,
            'warna' => $request->warna,
        ]);

        // Kembalikan response sukses
        return response()->json([
            'status' => true,
            'message' => 'Data berhasil disimpan'
        ]);
    }





    public function destroy($id_list_pembangunan)
    {
        try {
            $user = ListPembangunan::findOrFail($id_list_pembangunan);
            $user->delete();

            return response()->json(['status' => 'success', 'message' => 'Data berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal menghapus data'], 500);
        }
    }
}
