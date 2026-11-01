<?php


namespace App\Http\Controllers\Pengaturan;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ListPenjualan;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\Pengaturan\HakAksesController;

class ListPenjualanController extends Controller
{
    public function index(Request $request)
    {
        $permissions = HakAksesController::getUserPermissions();
        if ($request->ajax()) {
            $program = ListPenjualan::query();

            return DataTables::of($program)
                ->addIndexColumn()

                ->addColumn('warna_display', function ($row) {
                    return '<div style="display: flex; align-items: center;">
                            <div style="width: 15px; height: 15px; background-color: ' . $row->warna . '; border: 1px solid #000; margin-right: 5px;"></div>
                            <span>' . $row->warna . '</span>
                        </div>';
                })
                ->addColumn('stt_tampil', function ($row) {
                    if ($row->stt_tampil == 1) {
                        return '<span class="badge bg-success">Aktif</span>';
                    } else {
                        return '<span class="badge bg-danger">Tidak Aktif</span>';
                    }
                })

                ->addColumn('action', function ($row) use ($permissions) {
                    $actions = '<div class="text-center">';

                    if ($permissions['edit']) {
                        $actions .= '<button type="button" onclick="modalAction(\'' . route('list-penjualan.edit', $row->id_status_progres) . '\')" class="edit btn btn-primary btn-sm">Edit</button> ';
                    }

                    if ($permissions['hapus']) {
                        $actions .= '
                        <form action="' . route('list-penjualan.destroy', $row->id_status_progres) . '" method="POST" style="display:inline;">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="btn btn-danger btn-sm delete-button">Delete</button>
                        </form>';
                    }

                    $actions .= '</div>';
                    return $actions;
                })

                ->rawColumns(['action', 'warna_display', 'is_admin', 'stt_tampil'])
                ->make(true);
        }

        $permissions = HakAksesController::getUserPermissions();
        $data = ListPenjualan::first();

        return view('admin.pengaturan.list_penjualan.index', compact('data', 'permissions'));
    }

    public function create()
    {
        $title = 'List Penjualan';
        return view('admin.pengaturan.list_penjualan.index', compact('title'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'short_name'      => 'required|string|max:50',
            'status_progres'  => 'required|string|max:50',
            'keterangan'      => 'required|max:100',
            'urutan'          => 'required',
            'warna'           => 'required',
            'stt_tampil' => 'required',
        ], [
            'short_name.required'     => 'Nama singkat wajib diisi.',
            'short_name.string'       => 'Nama singkat harus berupa teks.',
            'short_name.max'          => 'Nama singkat maksimal 50 karakter.',

            'status_progres.required' => 'Status progres wajib diisi.',
            'status_progres.string'   => 'Status progres harus berupa teks.',
            'status_progres.max'      => 'Status progres maksimal 50 karakter.',

            'keterangan.required'     => 'Keterangan wajib diisi.',
            'keterangan.max'          => 'Keterangan maksimal 100 karakter.',

            'urutan.required'         => 'Urutan wajib diisi.',

            'warna.required'          => 'Warna wajib dipilih.',

            'stt_tampil.required' => 'Status tampil wajib dipilih.',

        ]);

        ListPenjualan::create([
            'short_name' => $request->short_name,
            'status_progres' => $request->status_progres,
            'keterangan' => $request->keterangan,
            'urutan' => $request->urutan,
            'warna' => $request->warna,
            'warna_bootstrap' => '',
            'stt_tampil' => $request->stt_tampil,
        ]);

        return response()->json(['status' => 'success', 'message' => 'Data berhasil ditambahkan!']);
    }



    public function edit($id)
    {
        $data = ListPenjualan::find($id);
        return view('admin.pengaturan.list_penjualan.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        // Validasi data yang diterima
        $request->validate([
            'status_progres' => 'required|string|max:50',
            'short_name'     => 'required|string|max:10',
            'keterangan'     => 'required|string',
            'urutan'         => 'required|string',
            'warna'          => 'required|string',
             'stt_tampil' => 'required',
        ], [
            'status_progres.required' => 'Status progres wajib diisi.',
            'status_progres.string'   => 'Status progres harus berupa teks.',
            'status_progres.max'      => 'Status progres maksimal 50 karakter.',

            'short_name.required'     => 'Nama singkat wajib diisi.',
            'short_name.string'       => 'Nama singkat harus berupa teks.',
            'short_name.max'          => 'Nama singkat maksimal 10 karakter.',

            'keterangan.required'     => 'Keterangan wajib diisi.',
            'keterangan.string'       => 'Keterangan harus berupa teks.',

            'urutan.required'         => 'Urutan wajib diisi.',
            'urutan.string'           => 'Urutan harus berupa teks.',

            'warna.required'          => 'Warna wajib diisi.',
            'warna.string'            => 'Warna harus berupa teks.',
            'stt_tampil.required' => 'Status tampil wajib dipilih.',
        ]);


        // Cari data berdasarkan id
        $data = ListPenjualan::findOrFail($id);

        // Update data dengan request baru
        $data->update([
            'status_progres' => $request->status_progres,
            'short_name' => $request->short_name,
            'keterangan' => $request->keterangan,
            'urutan' => $request->urutan,
            'warna' => $request->warna,
            'stt_tampil' => $request->stt_tampil,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil disimpan'
        ]);
    }



    public function destroy($id_status_progres)
    {
        try {
            $user = ListPenjualan::findOrFail($id_status_progres);
            $user->delete();

            return response()->json(['status' => 'success', 'message' => 'Data berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal menghapus data'], 500);
        }
    }
}
