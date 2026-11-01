<?php

namespace App\Http\Controllers\Legal;
use App\Http\Controllers\Controller;

use App\Models\ListrikAir;
use App\Models\KavlingPeta;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\LokasiKavling;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\Pengaturan\HakAksesController;

class ListrikAirController extends Controller
{
    public function index(Request $request)
    {
        $permissions = HakAksesController::getUserPermissions();

        if ($request->ajax()) {
            $opd = ListrikAir::orderBy('id', 'desc');

            return DataTables::of($opd)
                ->addIndexColumn()
                ->addColumn('lokasi_rumah', function ($row) {
                    $lokasi = LokasiKavling::find($row->id_lokasi);
                    $kavling = KavlingPeta::find($row->id_kavling);

                    $namaLokasi = $lokasi ? $lokasi->nama_kavling : '-';
                    $kodeKavling = $kavling ? $kavling->kode_kavling : '-';

                    return '<div>' . e($namaLokasi) . '</div>' .
                        '<span class="badge badge-info">' . e($kodeKavling) . '</span>';
                })
                ->addColumn('action', function ($row) use ($permissions): string {
                    $editUrl = route('listrik-air.edit', $row->id);
                    $deleteUrl = route('listrik-air.destroy', $row->id);

                    $btn = '<div class="d-flex justify-content-center">';
                    if ($permissions['edit']) {
                        $btn .= '<button class="btn btn-primary btn-sm mx-1" data-id="' . e($row->id) . '"
                         data-url="' . e($editUrl) . '" data-toggle="modal" data-target="#modalForm" id="edit-button">
                         Edit
                     </button>';
                    }

                    if ($permissions['hapus']) {
                        $btn .= '<form action="' . e($deleteUrl) . '" method="POST" style="display:inline;">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="delete-button btn btn-danger btn-sm">
                                Hapus
                            </button>
                         </form>';
                    }

                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['lokasi_rumah', 'action'])
                ->make(true);
        }
        $lokasi = LokasiKavling::all();

        return view('admin.legal.listrik_air.index', compact('permissions', 'lokasi'));
    }

    public function edit($id)
    {
        $list = ListrikAir::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $list,
            'foto_listrik_url' => $list->foto_listrik ? route('listrik-air.foto-listrik', $list->foto_listrik) : null,
            'foto_listrik_2_url' => $list->foto_listrik_2 ? route('listrik-air.foto-listrik-2', $list->foto_listrik_2) : null,
            'foto_air_url' => $list->foto_air ? route('listrik-air.foto-air', $list->foto_air) : null,
            'foto_air_2_url' => $list->foto_air_2 ? route('listrik-air.foto-air-2', $list->foto_air_2) : null,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_lokasi'        => 'required',
            'id_kavling'       => 'required',
            'norek_listrik'    => 'required',
            'norek_air'        => 'required',
            'foto_listrik'     => 'required|file',
            'foto_listrik_2'   => 'required|file',
            'foto_air'         => 'required|file',
            'foto_air_2'       => 'required|file',
        ], [
            'id_lokasi.required'      => 'Lokasi perumahan wajib diisi.',
            'id_kavling.required'     => 'Blok/Kavling wajib diisi.',
            'norek_listrik.required'  => 'No. rekening listrik wajib diisi.',
            'norek_air.required'      => 'No. rekening air wajib diisi.',
            'foto_listrik.required'   => 'Foto meteran listrik wajib diunggah.',
            'foto_listrik.file'       => 'Foto meteran listrik harus berupa file yang valid.',
            'foto_listrik_2.required' => 'Foto meteran listrik 2 wajib diunggah.',
            'foto_listrik_2.file'     => 'Foto meteran listrik 2 harus berupa file yang valid.',
            'foto_air.required'       => 'Foto meteran air wajib diunggah.',
            'foto_air.file'           => 'Foto meteran air harus berupa file yang valid.',
            'foto_air_2.required'     => 'Foto meteran air 2 wajib diunggah.',
            'foto_air_2.file'         => 'Foto meteran air 2 harus berupa file yang valid.',
        ]);

        if ($request->hasFile('foto_listrik')) {
            $fotolistrikName =  Str::random(30) . '.' . $request->file('foto_listrik')->extension();
            $request->file('foto_listrik')->storeAs('legal/listrik_air/foto_listrik', $fotolistrikName);
        }

        if ($request->hasFile('foto_listrik_2')) {
            $fotolistrik2Name = Str::random(30) . '.' . $request->file('foto_listrik_2')->extension();
            $request->file('foto_listrik_2')->storeAs('legal/listrik_air/foto_listrik_2', $fotolistrik2Name);
        }

        if ($request->hasFile('foto_air')) {
            $fotoairName = Str::random(30) . '.' . $request->file('foto_air')->extension();
            $request->file('foto_air')->storeAs('legal/listrik_air/foto_air', $fotoairName);
        }

        if ($request->hasFile('foto_air_2')) {
            $fotoair2Name = Str::random(30) . '.' . $request->file('foto_air_2')->extension();
            $request->file('foto_air_2')->storeAs('legal/listrik_air/foto_air_2', $fotoair2Name);
        }

        $db = [
            'id_lokasi'        => $request->id_lokasi,
            'id_kavling'       => $request->id_kavling,
            'norek_listrik'    => $request->norek_listrik,
            'norek_air'        => $request->norek_air,
            'foto_listrik'     => $fotolistrikName,
            'foto_listrik_2'   => $fotolistrik2Name,
            'foto_air'         => $fotoairName,
            'foto_air_2'       => $fotoair2Name,
        ];

        ListrikAir::create($db);

        return response()->json(['status' => 'success']);
    }

    public function update(Request $request, $id)
    {
        $data = ListrikAir::findOrFail($id);

        $request->validate([
            'id_lokasi'      => 'required',
            'id_kavling'     => 'required',
            'norek_listrik'  => 'required',
            'norek_air'      => 'required',
            'foto_listrik'   => 'nullable|file',
            'foto_listrik_2' => 'nullable|file',
            'foto_air'       => 'nullable|file',
            'foto_air_2'     => 'nullable|file',
        ], [
            'id_lokasi.required'      => 'Lokasi perumahan wajib diisi.',
            'id_kavling.required'     => 'Blok/Kavling wajib diisi.',
            'norek_listrik.required'  => 'No. rekening listrik wajib diisi.',
            'norek_air.required'      => 'No. rekening air wajib diisi.',
            'foto_listrik.file'       => 'Foto meteran listrik harus berupa file yang valid.',
            'foto_listrik_2.file'     => 'Foto meteran listrik 2 harus berupa file yang valid.',
            'foto_air.file'           => 'Foto meteran air harus berupa file yang valid.',
            'foto_air_2.file'         => 'Foto meteran air 2 harus berupa file yang valid.',
        ]);

        if ($request->hasFile('foto_listrik')) {
            if ($data->foto_listrik && Storage::exists('legal/listrik_air/foto_listrik/' . $data->foto_listrik)) {
                Storage::delete('legal/listrik_air/foto_listrik/' . $data->foto_listrik);
            }

            $fotolistrikName = Str::random(30) . '.' . $request->file('foto_listrik')->extension();
            $request->file('foto_listrik')->storeAs('legal/listrik_air/foto_listrik', $fotolistrikName);
        }

        if ($request->hasFile('foto_listrik_2')) {
            if ($data->foto_listrik_2 && Storage::exists('legal/listrik_air/foto_listrik_2/' . $data->foto_listrik_2)) {
                Storage::delete('legal/listrik_air/foto_listrik_2/' . $data->foto_listrik_2);
            }

            $fotolistrik2Name = Str::random(30) . '.' . $request->file('foto_listrik_2')->extension();
            $request->file('foto_listrik_2')->storeAs('legal/listrik_air/foto_listrik_2', $fotolistrik2Name);
        }

        if ($request->hasFile('foto_air')) {
            if ($data->foto_air && Storage::exists('legal/listrik_air/foto_air/' . $data->foto_air)) {
                Storage::delete('legal/listrik_air/foto_air/' . $data->foto_air);
            }

            $fotoairName = Str::random(30) . '.' . $request->file('foto_air')->extension();
            $request->file('foto_air')->storeAs('legal/listrik_air/foto_air', $fotoairName);
        }

        if ($request->hasFile('foto_air_2')) {
            if ($data->foto_air_2 && Storage::exists('legal/listrik_air/foto_air_2/' . $data->foto_air_2)) {
                Storage::delete('legal/listrik_air/foto_air_2/' . $data->foto_air_2);
            }

            $fotoair2Name = Str::random(30) . '.' . $request->file('foto_air_2')->extension();
            $request->file('foto_air_2')->storeAs('legal/listrik_air/foto_air_2', $fotoair2Name);
        }

        $db = [
            'id_lokasi'        => $request->id_lokasi,
            'id_kavling'       => $request->id_kavling,
            'norek_listrik'    => $request->norek_listrik,
            'norek_air'        => $request->norek_air,
            'foto_listrik'     => isset($fotolistrikName) ? $fotolistrikName : $data->foto_listrik,
            'foto_listrik_2'   => isset($fotolistrik2Name) ? $fotolistrik2Name : $data->foto_listrik_2,
            'foto_air'         => isset($fotoairName) ? $fotoairName : $data->foto_air,
            'foto_air_2'       => isset($fotoair2Name) ? $fotoair2Name : $data->foto_air_2,
        ];

        $data->update($db);

        return response()->json(['status' => 'success']);
    }

    public function destroy($id)
    {
        $data = ListrikAir::findOrFail($id);
        if ($data->foto_listrik && Storage::exists('legal/listrik_air/foto_listrik/' . $data->foto_listrik)) {
            Storage::delete('legal/listrik_air/foto_listrik/' . $data->foto_listrik);
        }
        if ($data->foto_listrik_2 && Storage::exists('legal/listrik_air/foto_listrik_2/' . $data->foto_listrik_2)) {
            Storage::delete('legal/listrik_air/foto_listrik_2/' . $data->foto_listrik_2);
        }
        if ($data->foto_air && Storage::exists('legal/listrik_air/foto_air/' . $data->foto_air)) {
            Storage::delete('legal/listrik_air/foto_air/' . $data->foto_air);
        }
        if ($data->foto_air_2 && Storage::exists('legal/listrik_air/foto_air_2/' . $data->foto_air_2)) {
            Storage::delete('legal/listrik_air/foto_air_2/' . $data->foto_air_2);
        }
        $data->delete();

        return response()->json(['status' => 'success']);
    }
}
