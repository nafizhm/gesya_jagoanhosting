<?php
namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Pengaturan\HakAksesController;
use App\Models\Wawancara;
use App\Models\WawancaraSp3k;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

Carbon::setLocale('id');

class AccBankController extends Controller
{
    public function index(Request $request)
    {
        $permissions = HakAksesController::getUserPermissions();

        if ($request->ajax()) {
            $program = WawancaraSp3k::with('wawancara.nasabah', 'wawancara.kavling.lokasi')->where('stt_arsip', 0)->orderByDesc('id_sp3k');

            return DataTables::of($program)
                ->addIndexColumn()

                ->addColumn('kode_kavling', function ($row) {
                    $kavling = optional($row->wawancara->kavling);
                    $lokasi  = optional($kavling->lokasi);
                    return ($kavling->kode_kavling ?? '-') . ' / ' . ($lokasi->nama_kavling ?? '-');
                })
                ->addColumn('bank', function ($row) {
                    return optional($row->bank)->bank ?? '-';
                })
                ->addColumn('harga_jual', function ($row) {
                    return number_format($row->harga_jual, 0, ',', '.') ?? '0';
                })
                ->addColumn('acc_plafon', function ($row) {
                    return 'Rp ' . number_format($row->acc_plafon, 0, ',', '.') ?? '0';
                })

                ->editColumn('tgl_terbit_sp3k', function ($row) {
                    return $row->tgl_terbit_sp3k ? Carbon::parse($row->tgl_terbit_sp3k)->translatedFormat('d F Y') : '-';
                })

                ->editColumn('tgl_expired', function ($row) {
                    return $row->tgl_expired ? Carbon::parse($row->tgl_expired)->translatedFormat('d F Y') : '-';
                })
                ->addColumn('sisa_hari', function ($row) {
                    if ($row->tgl_terbit_sp3k && $row->tgl_expired) {
                        try {
                            $tglExpired = Carbon::parse($row->tgl_expired)->startOfDay();
                            $today      = Carbon::now()->startOfDay();

                            $sisaHari = $today->diffInDays($tglExpired, false);

                            return $sisaHari > 0
                                ? $sisaHari . ' hari'
                                : '<span class="text-danger">Kadaluarsa</span>';
                        } catch (\Exception $e) {
                            return '<span class="text-danger">Tanggal tidak valid</span>';
                        }
                    }

                    return '-';
                })

                ->addColumn('action', function ($row) use ($permissions) {
                    $actions = '<div class="text-center">';

                    $actions .= '
        <a href="' . route('acc-bank.show', $row->id_sp3k) . '" class="btn btn-info btn-xs">Detail</a>
    ';

                    if ($permissions['hapus']) {
                        $actions .= '
            <form action="' . route('acc-bank.destroy', $row->id_sp3k) . '" method="POST" style="display:inline;">
                ' . csrf_field() . method_field('DELETE') . '
                <button type="submit" class="btn btn-danger btn-xs delete-button">Batalkan</button>
            </form>';
                    }

                    $isExpired = false;

                    if ($row->tgl_expired) {
                        try {
                            $tglExpired = Carbon::parse($row->tgl_expired)->startOfDay();
                            $today      = Carbon::now()->startOfDay();

                            $isExpired = $today->greaterThanOrEqualTo($tglExpired);
                        } catch (\Exception $e) {
                            $isExpired = false;
                        }
                    }

                    if ($isExpired) {
                        $actions .= '
            <form action="' . route('acc-bank.update', $row->id_sp3k) . '" method="POST" style="display:inline;">
                ' . csrf_field() . method_field('PUT') . '
                <button type="submit" class="btn btn-dark btn-xs arsip-button">Arsipkan</button>
            </form>';
                    }

                    $actions .= '</div>';
                    return $actions;
                })

                ->rawColumns(['action', 'is_admin', 'sisa_hari'])
                ->make(true);
        }

        $permissions = HakAksesController::getUserPermissions();
        $data        = WawancaraSp3k::first();

        return view('admin.transaksi.acc-bank.index', compact('data', 'permissions'));
    }

    public function arsip(Request $request)
    {

        if ($request->ajax()) {
            $program = WawancaraSp3k::with('wawancara.nasabah', 'wawancara.kavling.lokasi')->where('stt_arsip', 1)->orderByDesc('id_sp3k');

            return DataTables::of($program)
                ->addIndexColumn()

                ->addColumn('kode_kavling', function ($row) {
                    $kavling = optional($row->wawancara->kavling);
                    $lokasi  = optional($kavling->lokasi);
                    return ($kavling->kode_kavling ?? '-') . ' / ' . ($lokasi->nama_kavling ?? '-');
                })
                ->addColumn('bank', function ($row) {
                    return optional($row->bank)->bank ?? '-';
                })
                ->addColumn('harga_jual', function ($row) {
                    return number_format($row->harga_jual, 0, ',', '.') ?? '0';
                })
                ->addColumn('acc_plafon', function ($row) {
                    return 'Rp ' . number_format($row->acc_plafon, 0, ',', '.') ?? '0';
                })

                ->editColumn('tgl_terbit_sp3k', function ($row) {
                    return $row->tgl_terbit_sp3k ? Carbon::parse($row->tgl_terbit_sp3k)->translatedFormat('d F Y') : '-';
                })

                ->editColumn('tgl_expired', function ($row) {
                    return $row->tgl_expired ? Carbon::parse($row->tgl_expired)->translatedFormat('d F Y') : '-';
                })
                ->addColumn('sisa_hari', function ($row) {
                    if ($row->tgl_terbit_sp3k && $row->tgl_expired) {
                        try {
                            $tglExpired = Carbon::parse($row->tgl_expired)->startOfDay();
                            $today      = Carbon::now()->startOfDay();

                            $sisaHari = $today->diffInDays($tglExpired, false);

                            return $sisaHari > 0
                                ? $sisaHari . ' hari'
                                : '<span class="text-danger">Kadaluarsa</span>';
                        } catch (\Exception $e) {
                            return '<span class="text-danger">Tanggal tidak valid</span>';
                        }
                    }

                    return '-';
                })
                ->rawColumns(['is_admin', 'sisa_hari'])
                ->make(true);
        }

        return view('admin.transaksi.acc-bank.arsip');
    }

    public function getDataSp3k(Request $request)
    {
        $idWawancara = $request->input('id_wawancara');

        $query = WawancaraSp3k::with(['wawancara.nasabah', 'notaris'])
            ->select('wawancara_sp3k.*');

        if ($idWawancara) {
            $query->where('id_wawancara', $idWawancara);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('nama_customer', function ($row) {
                return optional(optional($row->wawancara)->nasabah)->nama_lengkap ?? '-';
            })
            ->addColumn('acc_plafon', function ($row) {
                return 'Rp ' . number_format($row->acc_plafon, 0, ',', '.') ?? '0';
            })
            ->addColumn('nama_notaris', function ($row) {
                return $row->notaris->nama_notaris ?? '-';
            })
            ->editColumn('tgl_wawancara', function ($row) {
                return $row->wawancara && $row->wawancara->tgl_wawancara
                    ? Carbon::parse($row->wawancara->tgl_wawancara)->translatedFormat('d F Y')
                    : '-';
            })
            ->editColumn('tgl_expired', function ($row) {
                return $row->tgl_expired
                    ? Carbon::parse($row->tgl_expired)->translatedFormat('d F Y')
                    : '-';
            })
            ->addColumn('sisa_hari', function ($row) {
                if ($row->tgl_terbit_sp3k && $row->tgl_expired) {
                    try {
                        $tglExpired = Carbon::parse($row->tgl_expired)->startOfDay();
                        $today      = Carbon::now()->startOfDay();

                        $sisaHari = $today->diffInDays($tglExpired, false);

                        return $sisaHari > 0
                            ? $sisaHari . ' hari'
                            : '<span class="text-danger">Kadaluarsa</span>';
                    } catch (\Exception $e) {
                        return '<span class="text-danger">Tanggal tidak valid</span>';
                    }
                }

                return '-';
            })
            ->rawColumns(['sisa_hari', 'action'])
            ->make(true);
    }

    public function show($id)
    {
        $this->checkAndUpdateExpiredSp3k();

        $dataSp3k = WawancaraSp3k::with('wawancara.nasabah', 'wawancara.kavling.lokasi', 'bank')->findOrFail($id);

        return view('admin.transaksi.acc-bank.detail', compact('dataSp3k'));
    }

    public function destroy($id_sp3k)
    {
        $sp3k = WawancaraSp3k::findOrFail($id_sp3k);
        $sp3k->delete();

        $wawancara = Wawancara::with('nasabah')->findOrFail($sp3k->id_wawancara);

        $wawancara->update([
            'stt_wawancara' => 'WAWANCARA',
        ]);

        return response()->json(['status' => 'success', 'message' => 'Data berhasil dikembalikan']);
    }

    private function checkAndUpdateExpiredSp3k()
    {
        $today = now()->startOfDay();

        $expiredSp3ks = WawancaraSp3k::where('tgl_expired', '<', $today)
            ->whereHas('wawancara', function ($query) {
                $query->where('stt_wawancara', '!=', 'WAWANCARA');
            })
            ->with(['wawancara.nasabah'])
            ->get();

        foreach ($expiredSp3ks as $sp3k) {
            try {

                if ($sp3k->wawancara && $sp3k->wawancara->nasabah->id_status_progres == 4) {
                    $sp3k->wawancara->nasabah->update([
                        'id_status_progres' => 18,
                    ]);

                    $sp3k->wawancara->update([
                        'stt_wawancara' => 'WAWANCARA',
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Gagal update expired SP3K: ' . $e->getMessage());
            }
        }
    }

    public function update(Request $request, $id)
    {
        $data = WawancaraSp3k::findOrFail($id);

        $db = [
            'stt_arsip' => 1,
        ];

        $data->update($db);

        return response()->json(['status' => 'success']);
    }
}
