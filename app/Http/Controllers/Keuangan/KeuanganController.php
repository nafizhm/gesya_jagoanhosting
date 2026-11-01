<?php

namespace App\Http\Controllers\Keuangan;
use App\Http\Controllers\Controller;

use TCPDF;
use Carbon\Carbon;
use App\Models\Keuangan;
use Illuminate\Http\Request;
use App\Models\KategoriModel;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\Pengaturan\HakAksesController;
Carbon::setLocale('id');

class KeuanganController extends Controller
{
    public function index(Request $request)
    {
        $permissions = HakAksesController::getUserPermissions();
        if ($request->ajax()) {
            $program = Keuangan::query();

            return DataTables::of($program)
                ->addIndexColumn()


                ->editColumn('tanggal', function ($row) {
                    $tgl = $row->transaksi_tanggal ? Carbon::parse($row->transaksi_tanggal)->translatedFormat('d F Y') : '-';
                    return $tgl;
                })

                ->addColumn('pemasukan', function ($row) {
                    return $row->transaksi_jenis === 'Pemasukan' ? number_format($row->transaksi_nominal, 0, ',', '.') : '-';
                })

                ->addColumn('pengeluaran', function ($row) {
                    return $row->transaksi_jenis === 'Pengeluaran' ? number_format($row->transaksi_nominal, 0, ',', '.') : '-';
                })

                ->editColumn('transaksi_kategori', function ($row) {
                    $kategoriMap = [
                        1 => 'CASH KERAS',
                        2 => 'CICILAN CASH LUNAK',
                        3 => 'PENCAIRAN KPR',
                        4 => 'CICILAN PRIBADI',
                        5 => 'PEMBAYARAN UTJ',
                        6 => 'PEMBAYARAN DP',
                        7 => 'PEMBAYARAN PIUTANG',
                        8 => 'PENCAIRAN DANA',
                        9 => 'PEMBELIAN ATK',
                        10 => 'PEMBAYARAN INTERNET',
                        11 => 'OPERASIONAL',
                        12 => 'KONSUMSI',
                        13 => 'LOGISTIK',
                        14 => 'HR',
                        15 => 'DIR',
                        16 => 'ADMIN',
                        17 => 'LEGAL',
                        18 => 'PABRIK PRODUKSI',
                        19 => 'ANGSURAN PT',
                    ];

                    return $kategoriMap[$row->transaksi_kategori] ?? 'Tidak Diketahui';
                })


                ->addColumn('action', function ($row) use ($permissions) {
                    $actions = '<div class="text-center">';

                    if ($permissions['hapus']) {
                        $actions = '
                        <form action="' . route('keuanganData.destroy', $row->transaksi_id) . '" method="POST" class="form-delete" style="display:inline;">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    ';
                    }

                    $actions .= '</div>';
                    return $actions;
                })

                ->rawColumns(['action'])
                ->make(true);
        }

        $permissions = HakAksesController::getUserPermissions();
        $data = Keuangan::first();


        $totalPemasukan = DB::table('transaksi')
            ->where('transaksi_jenis', 'pemasukan')
            ->sum('transaksi_nominal');

        $totalPengeluaran = DB::table('transaksi')
            ->where('transaksi_jenis', 'pengeluaran')
            ->sum('transaksi_nominal');

        $saldo = $totalPemasukan - $totalPengeluaran;

        $jumlahTransaksi = DB::table('transaksi')->count();
        $kategoriList = KategoriModel::all();

        return view('admin.keuangan.index', compact('data', 'permissions', 'totalPemasukan', 'totalPengeluaran', 'saldo', 'jumlahTransaksi', 'kategoriList'));
    }

    public function search(Request $request)
    {
        $search = $request->input('term'); // Select2 kirim pakai `term`

        $kategori = KategoriModel::where('kategori', 'like', '%' . $search . '%')
            ->select('kategori_id', 'kategori')
            ->limit(20)
            ->get();

        return response()->json($kategori);
    }


    public function store(Request $request)
    {
          $request->merge([
            'transaksi_nominal' => str_replace('.', '', $request->transaksi_nominal)
        ]);
        $request->validate([
            'transaksi_tanggal'     => 'required|date',
            'transaksi_jenis'       => 'required',
            'transaksi_kategori'    => 'required',
            'transaksi_keterangan'  => 'nullable|string',
            'transaksi_nominal'     => 'required|numeric',
            'transaksi_bank'        => 'required|numeric',
        ], [
            'transaksi_tanggal.required'    => 'Tanggal transaksi wajib diisi.',
            'transaksi_tanggal.date'        => 'Tanggal transaksi harus berupa format tanggal yang valid.',
            'transaksi_jenis.required'      => 'Jenis transaksi wajib dipilih.',
            'transaksi_kategori.required'   => 'Kategori transaksi wajib dipilih.',
            'transaksi_keterangan.string'   => 'Keterangan transaksi harus berupa teks.',
            'transaksi_nominal.required'    => 'Nominal transaksi wajib diisi.',
            'transaksi_nominal.numeric'     => 'Nominal transaksi harus berupa angka.',
            'transaksi_bank.required'       => 'Bank transaksi wajib dipilih.',
        ]);


        try {
            Keuangan::create([
                'transaksi_tanggal' => $request->transaksi_tanggal,
                'transaksi_jenis' => $request->transaksi_jenis,
                'transaksi_kategori' => $request->transaksi_kategori,
                'transaksi_keterangan' => $request->transaksi_keterangan,
                'transaksi_nominal' => $request->transaksi_nominal,
                'transaksi_bank' => $request->transaksi_bank,
                'id_kavling' => $request->id_kavling ?? 0,
                'id_tbl_pembayaran' => $request->id_tbl_pembayaran ?? 0,
                'transaksi_barang' => $request->transaksi_barang ?? 0,
                'no_kwitansi' => $request->no_kwitansi ?? 0,
            ]);

            // Kirim respons JSON agar bisa ditangani JavaScript
            return response()->json([
                'status' => true,
                'message' => 'Data berhasil ditambahkan!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($transaksi_id)
    {
        try {
            $data = Keuangan::findOrFail($transaksi_id);
            $data->delete();

            return response()->json(['status' => 'success', 'message' => 'Data berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal menghapus data'], 500);
        }
    }

    public function cetakTransaksi(Request $request)
    {
        $tgl_awal = $request->tgl_awal;
        $tgl_akhir = $request->tgl_akhir;

        $query = Keuangan::query();

        if ($tgl_awal && $tgl_akhir) {
            $query->whereBetween('transaksi_tanggal', [$tgl_awal, $tgl_akhir]);
        }

        $data = $query->orderBy('transaksi_tanggal', 'asc')->get();

        $pdf = new TCPDF('P', 'mm', 'A4');
        $pdf->SetTitle('Laporan Keuangan');
        $pdf->AddPage();

        // Header logo dan teks
        $logoPath = public_path('assets/img/header.png');
        if (file_exists($logoPath)) {
            $pdf->Image($logoPath, 20, 5, 30);
        }

        $pdf->SetFont('Times', 'B', 16);
        $pdf->SetXY(70, 10);
        $pdf->Cell(0, 7, 'PT. HAMZAH MAJU BERSAMA', 0, 2, 'L');

        $pdf->SetFont('Times', 'B', 20);
        $pdf->SetFont('', 'B', 12);
        $pdf->SetXY(90, 15);
        $pdf->Cell(0, 7, 'developer & kontraktor', 0, 2, 'L');

        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetLineWidth(0.7);
        $pdf->Line(10, 30, 200, 30);
        $pdf->SetLineWidth(0.3);
        $pdf->Line(10, 31, 200, 31);

        $pdf->Ln(12);
        $pdf->SetFont('Times', 'B', 10);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(190, 8, 'LAPORAN KEUANGAN', 0, 1, 'C');

        if ($tgl_awal && $tgl_akhir) {
            $periode = Carbon::parse($tgl_awal)->translatedFormat('d F Y') . ' s/d ' . Carbon::parse($tgl_akhir)->translatedFormat('d F Y');
            $pdf->SetFont('Times', '', 9);
            $pdf->Cell(190, 5, 'Periode: ' . $periode, 0, 1, 'C');
        }

        $pdf->Ln(5);
        $pdf->SetFont('Times', 'B', 9);
        $pdf->SetFillColor(220, 220, 220);

        // Header tabel
        $pdf->Cell(10, 7, 'No', 1, 0, 'C', true);
        $pdf->Cell(30, 7, 'Tanggal', 1, 0, 'C', true);
        $pdf->Cell(50, 7, 'Keterangan', 1, 0, 'C', true);
        $pdf->Cell(40, 7, 'Kategori', 1, 0, 'C', true);
        $pdf->Cell(30, 7, 'Pemasukan', 1, 0, 'C', true);
        $pdf->Cell(30, 7, 'Pengeluaran', 1, 1, 'C', true);

        $pdf->SetFont('Times', '', 9);
        $no = 1;
        $totalPemasukan = 0;
        $totalPengeluaran = 0;

        $kategoriMap = [
            1 => 'CASH KERAS',
            2 => 'CICILAN CASH LUNAK',
            3 => 'PENCAIRAN KPR',
            4 => 'CICILAN PRIBADI',
            5 => 'PEMBAYARAN UTJ',
            6 => 'PEMBAYARAN DP',
            7 => 'PEMBAYARAN PIUTANG',
            8 => 'PENCAIRAN DANA',
            9 => 'PEMBELIAN ATK',
            10 => 'PEMBAYARAN INTERNET',
            11 => 'OPERASIONAL',
            12 => 'KONSUMSI',
            13 => 'LOGISTIK',
            14 => 'HR',
            15 => 'DIR',
            16 => 'ADMIN',
            17 => 'LEGAL',
            18 => 'PABRIK PRODUKSI',
            19 => 'ANGSURAN PT',
        ];

        foreach ($data as $item) {
            $pdf->Cell(10, 7, $no++, 1, 0, 'C');
            $pdf->Cell(30, 7, Carbon::parse($item->transaksi_tanggal)->format('d-m-Y'), 1, 0, 'C');
            $pdf->Cell(50, 7, $item->transaksi_keterangan, 1, 0, 'L');
            $kategori = $kategoriMap[$item->transaksi_kategori] ?? 'Tidak Diketahui';
            $pdf->Cell(40, 7, $kategori, 1, 0, 'L');

            if (strtolower($item->transaksi_jenis) === 'pemasukan') {
                $pdf->Cell(30, 7, number_format($item->transaksi_nominal, 0, ',', '.'), 1, 0, 'R');
                $pdf->Cell(30, 7, '-', 1, 1, 'C');
                $totalPemasukan += $item->transaksi_nominal;
            } else {
                $pdf->Cell(30, 7, '-', 1, 0, 'C');
                $pdf->Cell(30, 7, number_format($item->transaksi_nominal, 0, ',', '.'), 1, 1, 'R');
                $totalPengeluaran += $item->transaksi_nominal;
            }
        }

        // Total
        $pdf->SetFont('Times', 'B', 9);
        $pdf->Cell(130, 7, 'Total', 1, 0, 'C', true);
        $pdf->Cell(30, 7, number_format($totalPemasukan, 0, ',', '.'), 1, 0, 'R', true);
        $pdf->Cell(30, 7, number_format($totalPengeluaran, 0, ',', '.'), 1, 1, 'R', true);

        // Saldo
        $pdf->Cell(130, 7, 'Saldo Akhir', 1, 0, 'C');
        $pdf->Cell(60, 7, number_format($totalPemasukan - $totalPengeluaran, 0, ',', '.'), 1, 1, 'R');

        $pdf->Output('laporan_keuangan.pdf', 'I');
        exit;
    }
}
