<?php

namespace App\Http\Controllers\PengaturanWa;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PengaturanPesan;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\Pengaturan\HakAksesController;

class PengaturanPesanController extends Controller
{
    public function index(Request $request)
    {
        $permissions = HakAksesController::getUserPermissions();
        if ($request->ajax()) {
            $program = PengaturanPesan::query();

            return DataTables::of($program)
                ->addIndexColumn()
                ->addColumn('action', function ($row) use ($permissions) {
                    $actions = '<div class="text-center">';

                    if ($permissions['edit']) {
                        $actions .= '<button type="button" onclick="modalAction(\'' . route('template-pesan.edit', $row->id_template) . '\')"  class="edit btn btn-primary btn-sm ">Edit</button> ';
                    }

                    if ($permissions['hapus']) {
                        $actions .= '
                            <form action="' . route('template-pesan.destroy', $row->id_template) . '" method="POST" style="display:inline;">
                                ' . csrf_field() . method_field('DELETE') . '
                                <button type="submit" class="delete btn btn-danger btn-sm delete-button">delete</button>
                            </form>';
                    }

                    $actions .= '</div>';
                    return $actions;
                })

                ->rawColumns(['action'])
                ->make(true);
        }

        $permissions = HakAksesController::getUserPermissions();
        $data = PengaturanPesan::first();

        return view('admin.pengaturan_wa.template_pesan.index', compact('data', 'permissions'));
    }

    public function create()
    {
        $title = 'Pengaturan Pesan';
        return view('admin.pengaturan_wa.template_pesan.index', compact('title'));
    }
    public function store(Request $request)
    {

        $request->validate([
            'nama_template' => 'required|string|max:50',
            'isi_template'  => 'required|string|max:50',
            'jenis_pesan'   => 'required|string|max:400',
        ], [
            'nama_template.required' => 'Nama template wajib diisi.',
            'nama_template.string'   => 'Nama template harus berupa teks.',
            'nama_template.max'      => 'Nama template maksimal 50 karakter.',

            'isi_template.required'  => 'Isi template wajib diisi.',
            'isi_template.string'    => 'Isi template harus berupa teks.',
            'isi_template.max'       => 'Isi template maksimal 50 karakter.',

            'jenis_pesan.required'   => 'Jenis pesan wajib diisi.',
            'jenis_pesan.string'     => 'Jenis pesan harus berupa teks.',
            'jenis_pesan.max'        => 'Jenis pesan maksimal 400 karakter.',
        ]);


        PengaturanPesan::create([
            'nama_template' => $request->nama_template,
            'isi_template' => $request->isi_template,
            'jenis_pesan' => $request->jenis_pesan,
        ]);

        return response()->json([
            'success' => 'success',
            'message' => 'Data berhasil disimpan.',
        ]);
    }


    public function edit($id_template)
    {
        $data = PengaturanPesan::find($id_template);
        return view('admin.pengaturan_wa.template_pesan.edit', compact('data'));
    }

    public function update(Request $request, $id_template)
    {
        // Validasi data yang diterima
        $request->validate([
            'nama_template' => 'required|string|max:50',
            'isi_template'  => 'required|string|max:500',
            'jenis_pesan'   => 'required|string',
        ], [
            'nama_template.required' => 'Nama template wajib diisi.',
            'nama_template.string'   => 'Nama template harus berupa teks.',
            'nama_template.max'      => 'Nama template maksimal 50 karakter.',

            'isi_template.required'  => 'Isi template wajib diisi.',
            'isi_template.string'    => 'Isi template harus berupa teks.',
            'isi_template.max'       => 'Isi template maksimal 500 karakter.',

            'jenis_pesan.required'   => 'Jenis pesan wajib diisi.',
            'jenis_pesan.string'     => 'Jenis pesan harus berupa teks.',
        ]);


        // Cari data berdasarkan id
        $data = PengaturanPesan::findOrFail($id_template);

        // Update data dengan request baru
        $data->update([
            'nama_template' => $request->nama_template,
            'isi_template' => $request->isi_template,
            'jenis_pesan' => $request->jenis_pesan,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Data berhasil disimpan'
        ]);
    }



    public function destroy($id)
    {
        $user = PengaturanPesan::findOrFail($id);
        $user->delete();

        return response()->json(['status' => 'success', 'message' => 'Data berhasil dihapus']);
    }
}
