<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Wawancara;
use App\Models\Customer;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\Pengaturan\HakAksesController;
use Carbon\Carbon;
use App\Models\BankModel;
use App\Models\Notaris;
use App\Models\WawancaraSp3k;

class WawancaraController extends Controller
{
    public function index(Request $request)
    {
        Carbon::setLocale('id');

        $permissions = HakAksesController::getUserPermissions();
        if ($request->ajax()) {
            $program = Wawancara::with(['nasabah', 'kavling.lokasi', 'bank'])->where('stt_wawancara', '=', 'WAWANCARA')->get();

            return DataTables::of($program)
                ->addIndexColumn()

                ->addColumn('id_kavling', function ($row) {
                    $kode = $row->kavling->kode_kavling ?? '-';
                    $nama = $row->kavling->lokasi->nama_kavling ?? '-';
                    return "$kode - $nama";
                })

                ->addColumn('tgl_wawancara', function ($row) {
                    return Carbon::parse($row->tgl_wawancara)->translatedFormat('d M Y');
                })

                ->addColumn('stt_wawancara', function ($row) {
                    return '<span class="badge bg-success">' . e($row->stt_wawancara) . '</span>';
                })

                ->addColumn('id_bank', function ($row) {
                    return $row->bank ? $row->bank->bank : '-';
                })

                ->addColumn('action', function ($row) use ($permissions) {
                    $actions = '<div class="text-center">';

                    if ($permissions['edit']) {
                        $actions .= '<button type="button" class="btn btn-info btn-sm edit" data-url="' . route('wawancara.edit', $row->id_wawancara) . '">Edit</button>';
                    }

                    if ($permissions['hapus']) {
                        $actions .= '
                        <form action="' . route('wawancara.destroy', $row->id_wawancara) . '" method="POST" style="display:inline;">
                        ' . csrf_field() . method_field('DELETE') . '
                        <button type="submit" class="btn btn-danger btn-sm delete-button">Delete</button>
                        </form>';
                    }

                    if (strtolower($row->stt_wawancara) === 'wawancara') {
                        $actions .= '<button type="button" onclick="modalAction(\'' . route('wawancara.acc', $row->id_wawancara) . '\')" class="edit btn btn-warning btn-sm">Acc Bank</button>';
                    } else {
                        $actions .= '<button type="button" class="btn btn-warning btn-sm" disabled>Acc Bank</button>';
                    }

                    $actions .= '</div>';
                    return $actions;
                })

                ->rawColumns(['action', 'is_admin', 'stt_wawancara'])
                ->make(true);
        }

        $permissions = HakAksesController::getUserPermissions();
        $data = Wawancara::first();

        return view('admin.transaksi.wawancara.index', compact('data', 'permissions'));
    }

    public function getNasabah(Request $request)
    {
        $query = Customer::query();

        if ($request->has('q')) {
            $query->where('nama_lengkap', 'like', '%' . $request->q . '%');
        }

        if ($request->has('exclude')) {
            $exclude = $request->exclude;
            if (is_string($exclude)) {
                $exclude = explode(',', $exclude);
            }
            $query->whereNotIn('id_customer', $exclude);
        }

        $nasabah = $query->get(['id_customer', 'nama_lengkap', 'kode_customer']);

        return response()->json($nasabah);
    }

    public function getNasabahDetails($id)
    {
        $nasabah = Customer::with(['lokasiKavling', 'kavlingPeta', 'marketing', 'bank'])
            ->where('id_customer', $id)
            ->first();

        if ($nasabah) {
            return response()->json([
                'no_ktp' => $nasabah->no_ktp,
                'alamat' => $nasabah->alamat,
                'id_bank' => $nasabah->id_bank,
                'id_marketing' => $nasabah->id_marketing,
                'nama_marketing' => $nasabah->marketing ? $nasabah->marketing->nama_marketing : null,
                'bank' => $nasabah->bank ? $nasabah->bank->bank : null,
                'lokasi_perumahan' => $nasabah->lokasiKavling ? $nasabah->lokasiKavling->nama_kavling : null,
                'no_telp' => $nasabah->no_wa,
                'lokasi_kav_blok' => $nasabah->kavlingPeta ? $nasabah->kavlingPeta->kode_kavling : null,
                'tipe_bangunan' => $nasabah->kavlingPeta ? $nasabah->kavlingPeta->tipe_bangunan : null,
                'luas_tanah' => $nasabah->kavlingPeta ? $nasabah->kavlingPeta->luas_tanah : null,
                'luas_bangunan' => $nasabah->kavlingPeta ? $nasabah->kavlingPeta->luas_bangunan : null,
                'id_kavling' => $nasabah->kavlingPeta ? $nasabah->kavlingPeta->id_kavling : null,
            ]);
        }

        return response()->json([], 404);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tgl_wawancara' => 'required|date',
            'id_customer' => 'required',
            'id_kavling' => 'required',
            'id_marketing' => 'required',
            'id_bank' => 'required',
            'catatan_kekurangan' => 'required',
        ], [
            'tgl_wawancara.required' => 'Tanggal wawancara wajib diisi.',
            'tgl_wawancara.date' => 'Format tanggal wawancara tidak valid.',
            'id_customer.required' => 'Silakan pilih nasabah dahulu.',
            'id_kavling.required' => 'Silakan pilih kavling dahulu.',
            'id_marketing.required' => 'Silakan pilih marketing dahulu.',
            'id_bank.required' => 'Silakan pilih nama bank dahulu.',
            'catatan_kekurangan.required' => 'Catatan kekurangan wajib diisi.',
        ]);


        Wawancara::create([
            'id_bank' => $request->id_bank,
            'tgl_wawancara' => $request->tgl_wawancara,
            'id_customer' => $request->id_customer,
            'id_marketing' => $request->id_marketing,
            'id_kavling' => $request->id_kavling,
            'catatan_kekurangan' => $request->catatan_kekurangan,
            'stt_wawancara' => 'WAWANCARA',

        ]);

        Customer::where('id_customer', $request->id_customer)->update([
            'id_status_progres' => 18,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Data berhasil ditambahkan!',
        ]);
    }

    public function edit($id)
    {
        $wawancara = Wawancara::with(['nasabah', 'kavling', 'marketing', 'bank'])->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => [
                'id_wawancara' => $wawancara->id_wawancara,
                'tgl_wawancara' => $wawancara->tgl_wawancara,
                'hari_wawancara' => $wawancara->hari_wawancara,

                'id_customer' => $wawancara->nasabah->id_customer,
                'nama_nasabah' => $wawancara->nasabah->nama_lengkap ?? '',
                'no_ktp' => $wawancara->nasabah->no_ktp,
                'alamat' => $wawancara->nasabah->alamat_ktp,

                'kode_kavling' => $wawancara->kavling->kode ?? '',
                'id_kavling' => $wawancara->kavling->id_kavling ?? '',
                'tipe_bangunan' => $wawancara->kavling->tipe ?? '',
                'luas_tanah' => $wawancara->kavling->luas_tanah ?? '',
                'luas_bangunan' => $wawancara->kavling->luas_bangunan ?? '',

                'nama_marketing' => $wawancara->marketing->nama ?? '',
                'id_marketing' => $wawancara->marketing->id_marketing ?? '',

                'id_bank' => $wawancara->id_bank,
                'bank' => $wawancara->bank ? $wawancara->bank->bank : null,

                'catatan_kekurangan' => $wawancara->catatan_kekurangan,
            ]
        ]);
    }

    public function update(Request $request, $id)
    {
        $wawancara = Wawancara::findOrFail($id);

        $request->validate([
            'catatan_kekurangan' => 'nullable|string',
        ]);

        $wawancara->update([
            'id_bank' => $request->id_bank,
            'catatan_kekurangan' => $request->catatan_kekurangan,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Data wawancara berhasil diperbarui.'
        ]);
    }


    public function acc(string $id)
    {
        $data = Wawancara::find($id);
        $bankList = BankModel::all();
        $notarisList = Notaris::all();

        return view('admin.transaksi.wawancara.acc', compact('data', 'bankList', 'notarisList'));
    }

    public function simpanSp3k(Request $request, $id)
    {
        $request->validate([
            'id_bank' => 'required|exists:bank,id_bank',
            'acc_plafon' => 'required|string|max:255',
            'tenor' => 'required|numeric',
            'tgl_terbit_sp3k' => 'required|date',
            'id_notaris' => 'required|exists:notaris,id_notaris',
            'catatan_acc' => 'nullable|string',
        ], [
            'id_bank.required' => 'Nama bank wajib diisi.',
            'id_bank.exists' => 'Bank yang dipilih tidak valid.',

            'acc_plafon.required' => 'Acc Plafon wajib diisi.',
            'acc_plafon.numeric' => 'Acc Plafon harus berupa angka.',

            'tenor.required' => 'Tenor wajib diisi.',
            'tenor.numeric' => 'Tenor harus berupa angka.',

            'tgl_terbit_sp3k.required' => 'Tanggal SP3K wajib diisi.',
            'tgl_terbit_sp3k.date' => 'Tanggal SP3K harus berupa tanggal yang valid.',

            'id_notaris.required' => 'Nama notaris wajib dipilih.',
            'id_notaris.exists' => 'Notaris yang dipilih tidak valid.',

            'catatan_acc.string' => 'Catatan ACC harus berupa teks.',
        ]);

        $accPlafon = str_replace('.', '', $request->acc_plafon);

        $tglTerbit = Carbon::parse($request->tgl_terbit_sp3k);
        $tglExpired = $tglTerbit->copy()->addDays(90);
        $hariSisa = max(0, now()->diffInDays($tglExpired, false));

        WawancaraSp3k::create([
            'id_wawancara' => $id,
            'id_bank' => $request->id_bank,
            'acc_plafon' => $accPlafon,
            'tenor' => $request->tenor,
            'id_notaris' => $request->id_notaris,
            'tgl_terbit_sp3k' => $tglTerbit,
            'tgl_expired' => $tglExpired,
            'catatan_acc' => $request->catatan_acc,
            // 'stt_sp3k' => 1,

        ]);

        $wawancara = Wawancara::with('nasabah')->findOrFail($id);

        $wawancara->update([
            'stt_wawancara' => 'ACC BANK',
        ]);

        $wawancara->nasabah->update([
            'id_status_progres' => 4,
        ]);

        return response()->json(['status' => 'success', 'message' => 'Data SP3K berhasil disimpan.']);
    }

    public function destroy($id_wawancara)
    {
        $user = Wawancara::findOrFail($id_wawancara);
        
        Customer::where('id_customer', $user->id_customer)->update([
            'id_status_progres' => 1
        ]);

        $user->delete();

        return response()->json(['status' => 'success', 'message' => 'Data berhasil dihapus']);
    }
}
