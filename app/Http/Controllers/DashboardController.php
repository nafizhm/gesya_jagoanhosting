<?php

namespace App\Http\Controllers;

use App\Models\ArsipNasabah;
use App\Models\ProgresUnitReady;
use Carbon\Carbon;
use App\Models\Customer;
use App\Models\BankModel;
use App\Models\Freelance;
use App\Models\Marketing;
use App\Models\KavlingPeta;
use App\Models\LokasiKavling;
use App\Models\Pembatalan;
use App\Models\ProgresListPenjualan;
use App\Models\Registrasi;
use App\Models\Wawancara;
use App\Models\WawancaraSp3k;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->withError('Silahkan Login terlebih dahulu');
        }

        Carbon::setLocale('id');
        $tglSekarang = Carbon::now()->translatedFormat('j F Y');
        $totalKavling = KavlingPeta::count();
        $totalCustomer = Customer::count();
        $abu = Customer::whereIn('id_status_progres', [3])->count();
        $kuning = Customer::whereIn('id_status_progres', [1])->count();
        $hijau = Customer::where('id_status_progres', [18])->count();

        $kolomStatus = ProgresListPenjualan::where('status_progres', '!=', 'HOLD')->where('stt_tampil', 1)->orderBy('urutan', 'asc')->get();

        $hold = Registrasi::where('stt_reg', '=', 1)->get();

        $dataLokasi = LokasiKavling::where('stt_tampil', 3)->orWhere('stt_tampil', 1)->get()->map(function ($lokasi) use ($kolomStatus) {
            $id = $lokasi->id_lokasi;
            $data = [
                'id' => $id,
                'nama' => $lokasi->nama_kavling,
                'nama_singk_1' => $lokasi->nama_singkat,
                'jumlah' => KavlingPeta::where('id_lokasi', $id)->count(),
                'kpr' => Customer::where('id_lokasi', $id)->where('jenis_pembelian', 'KPR')->count(),
                'cash' => Customer::where('id_lokasi', $id)->whereIn('jenis_pembelian', ['Cash Keras', 'Cash Bertahap'])->count(),
                'kredit' => Customer::where('id_lokasi', $id)->where('jenis_pembelian', 'Kredit Inhouse')->count(),
                'hold' => Registrasi::where('id_lokasi', $id)->where('stt_reg', '=', 1)->count()
            ];

            foreach ($kolomStatus as $status) {
                $key = strtolower(str_replace(' ', '_', $status->short_name));
                $data[$key] = Customer::where('id_lokasi', $id)
                    ->where('id_status_progres', $status->id_status_progres)
                    ->count();
            }

            return $data;
        });

        $totalLokasi = [
            'jumlah' => 0,
            'hold'   => 0,
            'kpr'    => 0,
            'cash'   => 0,
            'kredit' => 0
        ];

        foreach ($kolomStatus as $status) {
            $key = strtolower(str_replace(' ', '_', $status->short_name));
            $totalLokasi[$key] = 0;
        }

        foreach ($dataLokasi as $lokasi) {
            $totalLokasi['jumlah'] += $lokasi['jumlah'];
            $totalLokasi['hold']   += $lokasi['hold'];
            $totalLokasi['kpr']    += $lokasi['kpr'];
            $totalLokasi['cash']   += $lokasi['cash'];
            $totalLokasi['kredit'] += $lokasi['kredit'];

            foreach ($kolomStatus as $status) {
                $key = strtolower(str_replace(' ', '_', $status->short_name));
                $totalLokasi[$key] += $lokasi[$key] ?? 0;
            }
        }

        $totalData = 0;

        foreach ($dataLokasi as $lokasi) {
            $totalData += ($lokasi['bf'] ?? 0);
            $totalData += ($lokasi['wwc'] ?? 0);
            $totalData += ($lokasi['sp3k'] ?? 0);
        }

        $totalCustomer = Customer::count();

        $progresList = ProgresListPenjualan::all();
        $dataProgres = [];

        $noProgres = 1;
        foreach ($progresList as $progres) {
            $jumlah = Customer::where('id_status_progres', $progres->id_status_progres)->count();
            $persentase = $totalCustomer > 0 ? round(($jumlah / $totalCustomer) * 100) : 0;

            $dataProgres[] = [
                'no' => $noProgres++,
                'status_progres' => $progres->status_progres,
                'jumlah' => $jumlah,
                'persentase' => $persentase,
                'id_status_progres' => $progres->id_status_progres,
            ];
        }

        $bankList = BankModel::all();
        $dataBank = [];

        $noBank = 1;
        foreach ($bankList as $bank) {
            $jumlah = WawancaraSp3k::where('id_bank', $bank->id_bank)->count();
            $persentase = $totalCustomer > 0 ? round(($jumlah / $totalCustomer) * 100) : 0;

            $dataBank[] = [
                'no' => $noBank++,
                'bank' => $bank->bank,
                'jumlah' => $jumlah,
                'persentase' => $persentase,
                'id_bank' => $bank->id_bank,
            ];
        }

        $marketingList = Marketing::all();
        $dataMarketing = [];

        $noMarketing = 1;
        foreach ($marketingList as $marketing) {
            $jumlah = Customer::where('id_marketing', $marketing->id_marketing)->count();
            $persentase = $totalCustomer > 0 ? round(($jumlah / $totalCustomer) * 100) : 0;

            $dataMarketing[] = [
                'no' => $noMarketing++,
                'marketing' => $marketing->nama_marketing,
                'jumlah' => $jumlah,
                'persentase' => $persentase,
                'id_marketing' => $marketing->id_marketing,
            ];
        }

        $freelanceList = Freelance::all();
        $dataFreelance = [];

        $noFreelance = 1;
        foreach ($freelanceList as $freelance) {
            $jumlah = Customer::where('id_freelance', $freelance->id_freelance)->count();
            $persentase = $totalCustomer > 0 ? round(($jumlah / $totalCustomer) * 100) : 0;

            $dataFreelance[] = [
                'no' => $noFreelance++,
                'freelance' => $freelance->nama_freelance,
                'jumlah' => $jumlah,
                'persentase' => $persentase,
                'id_freelance' => $freelance->id_freelance,
            ];
        }

        $kolomStatusReady = ProgresUnitReady::all();

        $dataLokasiReady = LokasiKavling::all()->map(function ($lokasi) use ($kolomStatusReady) {
            $id   = $lokasi->id_lokasi;
            $data = [
                'id'     => $id,
                'nama'   => $lokasi->nama_kavling,
                'jumlah' => KavlingPeta::where('id_lokasi', $id)->count(),
            ];

            foreach ($kolomStatusReady as $status) {
                $key        = strtolower(str_replace(' ', '_', $status->keterangan));
                $data[$key] = KavlingPeta::where('id_lokasi', $id)
                    ->where('status_ready', $status->id)
                    ->count();
            }

            return $data;
        });

        return view('admin.dashboard.dashboard', compact(
            'dataLokasi',
            'kolomStatus',
            'totalLokasi',
            'totalData',
            'totalKavling',
            'tglSekarang',
            'dataProgres',
            'dataBank',
            'dataMarketing',
            'dataFreelance',
            'abu',
            'kuning',
            'hijau',
            'hold',
            'kolomStatusReady',
            'dataLokasiReady'
        ));
    }

    public function showLokasiPenjualan(string $id)
    {
        $getName = LokasiKavling::where('id_lokasi', $id)->first();
        $viewData = [
            'lokasi_id' => $id,
            'scope' => 'Lokasi Kavling',
            'nama' => $getName->nama_kavling
        ];

        $query = KavlingPeta::with(['customer.progres', 'customer.marketing'])
            ->withCount('customer')
            ->where('id_lokasi', $id)
            ->orderByDesc('customer_count');

        if (request()->ajax()) {
            return DataTables::of($query->get())
                ->addIndexColumn()
                ->addColumn('id_status_progres', function ($row) {
                    return optional(optional($row->customer)->progres)->status_progres ?? '';
                })
                ->addColumn('nama_lengkap', function ($row) {
                    return optional($row->customer)->nama_lengkap ?? '';
                })
                ->addColumn('id_marketing', function ($row) {
                    return optional(optional($row->customer)->marketing)->nama_marketing ?? '';
                })
                ->make(true);
        }

        return view('admin.dashboard.statistik.lokasi', $viewData);
    }

    public function showCustomer(string $id)
    {
        $routeName = request()->route()->getName();
        $getName = [];
        $viewData = [];

        if ($routeName === 'dashboard.customer-status-progres-show') {
            $query = Customer::where('id_status_progres', $id);
            $getName = ProgresListPenjualan::where('id_status_progres', $id)->first();
            $viewData = [
                'status_progres_id' => $id,
                'scope' => 'Progres Penjualan',
                'nama' => $getName->status_progres ?? '-'
            ];
        } elseif ($routeName === 'dashboard.customer-bank-show') {
            $query = WawancaraSp3k::where('id_bank', $id);
            $getName = BankModel::where('id_bank', $id)->first();
            $viewData = [
                'bank_id' => $id,
                'scope' => 'Bank',
                'nama' => $getName->bank ?? '-'
            ];
        } elseif ($routeName === 'dashboard.customer-marketing-show') {
            $query = Customer::where('id_marketing', $id);
            $getName = Marketing::where('id_marketing', $id)->first();
            $viewData = [
                'marketing_id' => $id,
                'scope' => 'Marketing',
                'nama' => $getName->nama_marketing ?? '-'
            ];
        } elseif ($routeName === 'dashboard.customer-freelance-show') {
            $query = Customer::where('id_freelance', $id);
            $getName = Freelance::where('id_freelance', $id)->first();
            $viewData = [
                'freelance_id' => $id,
                'scope' => 'Freelance',
                'nama' => $getName->nama_freelance ?? '-'
            ];
        }

        if (request()->ajax()) {
            Carbon::setLocale('id');

            if ($routeName === 'dashboard.customer-bank-show') {
                $program = $query->with(['wawancara.nasabah.marketing', 'wawancara.nasabah.freelance', 'wawancara.nasabah.lokasi', 'wawancara.nasabah.kavling', 'wawancara.nasabah.progres'])->get();
            } else {
                $program = $query->with(['marketing', 'freelance', 'lokasi', 'kavling', 'progres']);
            }

            return DataTables::of($program)
                ->addIndexColumn()

                ->editColumn('tgl_terima', function ($row) use ($routeName) {
                    if ($routeName === 'dashboard.customer-bank-show') {
                        $tgl = $row->tgl_terbit_sp3k ? Carbon::parse($row->tgl_terbit_sp3k)->translatedFormat('d F Y') : '-';
                    } else {
                        $tgl = $row->tgl_terima ? Carbon::parse($row->tgl_terima)->translatedFormat('d F Y') : '-';
                    }
                    $kode = $row->kode_customer ? '<strong>' . $row->kode_customer . '</strong>' : '';
                    return "$tgl<br>$kode";
                })

                ->editColumn('id_marketing', function ($row) use ($routeName) {
                    if ($routeName === 'dashboard.customer-bank-show') {
                        $namaMarketing = $row->wawancara->nasabah->marketing->nama_marketing ?? '<span class="badge bg-danger">None Marketing</span>';
                        $namaFreelance = $row->wawancara->nasabah->freelance->nama_freelance ?? null;
                    } else {
                        $namaMarketing = $row->marketing->nama_marketing ?? '<span class="badge bg-danger">None Marketing</span>';
                        $namaFreelance = $row->freelance->nama_freelance ?? null;
                    }

                    $freelanceBadge = $namaFreelance ? '<span class="badge bg-info">' . $namaFreelance . '</span>' : '';
                    return $namaMarketing . ($freelanceBadge ? '<br>' . $freelanceBadge : '');
                })

                ->editColumn('id_lokasi', function ($row) use ($routeName) {
                    if ($routeName === 'dashboard.customer-bank-show') {
                        $namaLokasi = $row->wawancara->nasabah->lokasi->nama_kavling ?? '-';
                        $kodeKavling = $row->wawancara->nasabah->kavling->kode_kavling ?? '-';
                    } else {
                        $namaLokasi = $row->lokasi->nama_kavling ?? '-';
                        $kodeKavling = $row->kavling->kode_kavling ?? '-';
                    }
                    return '<strong>' . $namaLokasi . '</strong><br>' . $kodeKavling;
                })

                ->editColumn('id_status_progres', function ($row) use ($routeName) {
                    if ($routeName === 'dashboard.customer-bank-show') {
                        $status = $row->wawancara->nasabah->progres->status_progres ?? '-';
                        $ketCashback = $row->wawancara->nasabah->progres->ket_cashback ?? '';
                    } else {
                        $status = $row->progres->status_progres ?? '-';
                        $ketCashback = $row->progres->ket_cashback ?? '';
                    }

                    $badgeColors = [
                        'BOOKING FEE' => 'warning',
                        'SP3K' => 'success',
                        'AKAD' => 'info',
                        'SERAH TERIMA' => 'dark',
                    ];
                    $statusDisplay = isset($badgeColors[$status])
                        ? '<span class="badge bg-' . $badgeColors[$status] . '">' . $status . '</span>'
                        : $status;
                    $cashbackText = $ketCashback ? '<br><small>' . $ketCashback . '</small>' : '';
                    return $statusDisplay . $cashbackText;
                })

                ->editColumn('nama_lengkap', function ($row) use ($routeName) {
                    if ($routeName === 'dashboard.customer-bank-show') {
                        $nama = '<strong>' . $row->wawancara->nasabah->nama_lengkap ?? '-' . '</strong>';
                        $wa = $row->wawancara->nasabah->no_wa ?? '-';
                        $ktp = $row->wawancara->nasabah->no_ktp ? '<span class="badge bg-info">NIK: ' . $row->wawancara->nasabah->no_ktp . '</span>' : '';
                    } else {
                        $nama = '<strong>' . $row->nama_lengkap . '</strong>';
                        $wa = $row->no_wa ?? '-';
                        $ktp = $row->no_ktp ? '<span class="badge bg-info">NIK: ' . $row->no_ktp . '</span>' : '';
                    }

                    return "$nama<br>$wa<br>$ktp";
                })

                ->rawColumns([
                    'tgl_terima',
                    'nama_lengkap',
                    'id_marketing',
                    'id_lokasi',
                    'id_status_progres',
                ])
                ->make(true);
        }

        return view('admin.dashboard.statistik.customer', $viewData);
    }

    public function showPenggunaanBank()
    {

        return view('admin.dashboard.statistik.customer');
    }


    public function totalUnit(Request $request)
    {
        if ($request->ajax()) {
            $data = KavlingPeta::select(
                'kavling_peta.*',
                'lokasi_kavling.nama_kavling as nama_cluster'
            )
                ->leftJoin('lokasi_kavling', 'kavling_peta.id_lokasi', '=', 'lokasi_kavling.id_lokasi');

            return datatables()->of($data)
                ->addIndexColumn()
                ->addColumn('panjang', function ($row) {
                    return '
                        <p>pjg kanan: <strong>' . $row->panjang_kanan . ' m</strong></p>
                        <p>pjg kiri: <strong>' . $row->panjang_kiri . ' m</strong></p>
                    ';
                })
                ->addColumn('lebar', function ($row) {
                    return '
                        <p>lebar depan: <strong>' . $row->lebar_depan . ' m</strong></p>
                        <p>lebar belakang: <strong>' . $row->lebar_belakang . ' m</strong></p>
                    ';
                })
                ->addColumn('luas', function ($row) {
                    return '
                        <p>luas tanah: <strong>' . $row->luas_tanah . ' m</strong></p>
                        <p>luas bangunan: <strong>' . $row->luas_bangunan . ' m</strong></p>
                    ';
                })
                ->addColumn('harga', function ($row) {
                    return number_format($row->hrg_jual, 0, ',', '.');
                })
                ->addColumn('lokasi', function ($row) {
                    return $row->kode_kavling;
                })
                ->rawColumns(['panjang', 'lebar', 'luas'])
                ->make(true);
        }

        return view('admin.dashboard.TotalUnit');
    }

    public function booking(Request $request)
    {
        if ($request->ajax()) {
            $data = Customer::with(['lokasi', 'progres'])
                ->whereIn('id_status_progres', [1, 22]);

            return datatables()->of($data)
                ->addIndexColumn()
                ->addColumn('id_lokasi', function ($row) {
                    return $row->lokasi ? $row->lokasi->nama_kavling : '-';
                })
                ->addColumn('id_status_progres', function ($row) {
                    return $row->progres ? $row->progres->status_progres : '-';
                })
                ->make(true);
        }

        return view('admin.dashboard.Booking');
    }

    public function wawancara(Request $request)
    {
        if ($request->ajax()) {
            $data = Customer::with(['lokasi', 'progres'])
                ->whereIn('id_status_progres', [18]);

            return datatables()->of($data)
                ->addIndexColumn()
                ->addColumn('id_lokasi', function ($row) {
                    return $row->lokasi ? $row->lokasi->nama_kavling : '-';
                })
                ->addColumn('id_status_progres', function ($row) {
                    return $row->progres ? $row->progres->status_progres : '-';
                })
                ->make(true);
        }

        return view('admin.dashboard.Wawancara');
    }
    public function akad(Request $request)
    {
        if ($request->ajax()) {
            $data = Customer::with(['lokasi', 'progres'])
                ->whereIn('id_status_progres', [3]);

            return datatables()->of($data)
                ->addIndexColumn()
                ->addColumn('id_lokasi', function ($row) {
                    return $row->lokasi ? $row->lokasi->nama_kavling : '-';
                })
                ->addColumn('id_status_progres', function ($row) {
                    return $row->progres ? $row->progres->status_progres : '-';
                })
                ->make(true);
        }

        return view('admin.dashboard.Akad');
    }
}
