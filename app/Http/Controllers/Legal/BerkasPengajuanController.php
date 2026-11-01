<?php

namespace App\Http\Controllers\Legal;

use App\Http\Controllers\Controller;

use App\Models\Customer;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PengajuanBerkasLegal;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\Pengaturan\HakAksesController;

class BerkasPengajuanController extends Controller
{
    public function index(Request $request)
    {
        $permissions = HakAksesController::getUserPermissions();

        if ($request->ajax()) {
            $opd = PengajuanBerkasLegal::with('customer.lokasiKavling', 'customer.kavlingPeta')->orderBy('id', 'desc');

            return DataTables::of($opd)
                ->addIndexColumn()
                ->addColumn('id_customer', function ($row) {
                    $customer = Customer::find($row->id_customer);
                    return $customer ? $customer->nama_lengkap : 'Tidak Diketahui';
                })
                ->addColumn('IPH', function ($row) {
                    return $row->IPH == 1
                        ? '<i class="fas fa-check-circle text-success"></i>'
                        : '<i class="fas fa-times-circle text-danger"></i>';
                })

                ->addColumn('SHGB', function ($row) {
                    return $row->SHGB == 1
                        ? '<i class="fas fa-check-circle text-success"></i>'
                        : '<i class="fas fa-times-circle text-danger"></i>';
                })

                ->addColumn('pajak', function ($row) {
                    return $row->pajak == 1
                        ? '<i class="fas fa-check-circle text-success"></i>'
                        : '<i class="fas fa-times-circle text-danger"></i>';
                })

                ->addColumn('lokasi', function ($row) {
                    if (!$row->customer) return '-';

                    $lokasiNama = $row->customer->lokasiKavling ? $row->customer->lokasiKavling->nama_kavling : '-';
                    $kodeKav = $row->customer->kavlingPeta ? $row->customer->kavlingPeta->kode_kavling : '-';

                    return $lokasiNama . '<br><small style="color: gray;">' . $kodeKav . '</small>';
                })


                ->addColumn('action', function ($row) use ($permissions): string {
                    $editUrl = route('pengajuan-berkas.edit', $row->id);
                    $deleteUrl = route('pengajuan-berkas.destroy', $row->id);

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
                ->filterColumn('id_customer', function ($query, $keyword) {
                    $query->whereHas('customer', function ($q) use ($keyword) {
                        $q->where('nama_lengkap', 'like', "%{$keyword}%");
                    });
                })
                ->rawColumns(['IPH', 'SHGB', 'pajak', 'action', 'lokasi'])
                ->make(true);
        }

        return view('admin.legal.pengajuan_berkas.index', compact('permissions'));
    }

    public function edit($id)
    {
        $list = PengajuanBerkasLegal::findOrFail($id);

        $customer = Customer::where('id_customer', $list->id_customer)->first();

        return response()->json([
            'status' => 'success',
            'data' => $list,
            'nama_customer' => $customer ? $customer->nama_lengkap : 'Tidak Diketahui',
            'percakapan_wa_url' => $list->percakapan_wa ? route('pengajuan-berkas.percakapan-wa', $list->percakapan_wa) : null,
        ]);
    }
    public function update(Request $request, $id)
    {
        $data = PengajuanBerkasLegal::findOrFail($id);

        $request->validate([
            'IPH'                 => 'required',
            'SHGB'                => 'required',
            'pajak'               => 'required',
            'catatan_kekurangan' => 'nullable|string',
            'percakapan_wa'       => 'nullable|file',
        ], [
            'IPH.required'                 => 'Silakan pilih status IPH.',
            'SHGB.required'                => 'Silakan pilih status SHGB.',
            'pajak.required'               => 'Silakan pilih status pajak.',
            'catatan_kekurangan.string'   => 'Catatan kekurangan harus berupa teks.',
            'percakapan_wa.file'          => 'Percakapan WA harus berupa file yang valid.',
        ]);

        if ($request->hasFile('percakapan_wa')) {
            if ($data->percakapan_wa && Storage::exists('legal/pengajuan_berkas/percakapan_wa/' . $data->percakapan_wa)) {
                Storage::delete('legal/pengajuan_berkas/percakapan_wa/' . $data->percakapan_wa);
            }

            $percakapanwaName = Str::random(30) . '.' . $request->file('percakapan_wa')->extension();
            $request->file('percakapan_wa')->storeAs('legal/pengajuan_berkas/percakapan_wa', $percakapanwaName);
        }

        $data->update([
            'IPH'                  => $request->IPH,
            'SHGB'                 => $request->SHGB,
            'pajak'                => $request->pajak,
            'catatan_kekurangan'   => $request->catatan_kekurangan ?? null,
            'percakapan_wa'        => isset($percakapanwaName) ? $percakapanwaName : $data->percakapan_wa,
        ]);

        return response()->json(['status' => 'success']);
    }

    public function destroy($id)
    {
        $data = PengajuanBerkasLegal::findOrFail($id);
        if ($data->percakapan_wa && Storage::exists('legal/pengajuan_berkas/percakapan_wa/' . $data->percakapan_wa)) {
            Storage::delete('legal/pengajuan_berkas/percakapan_wa/' . $data->percakapan_wa);
        }
        $data->delete();

        return response()->json(['status' => 'success']);
    }
}
