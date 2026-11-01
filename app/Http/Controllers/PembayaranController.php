<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\JenisPembayaran;
use App\Models\JenisPengeluaran;
use App\Models\KavlingPeta;
use App\Models\LokasiKavling;
use App\Models\Pembayaran;
use App\Models\PengaturanMedia;
use App\Models\Pengeluaran;
use App\Models\Perusahaan;
use App\Models\ProgresListPenjualan;
use App\Models\Tagihan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use TCPDF;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\Pengaturan\HakAksesController;

class PembayaranController extends Controller
{
    public function index(Request $request)
    {
        $permissions = HakAksesController::getUserPermissions();

        if ($request->ajax()) {
            $totalTagihanSub = Tagihan::select(
                'nasabah',
                DB::raw('SUM(jumlah_tagihan) as total_tagihan_sum')
            )
                ->groupBy('nasabah');

            $totalBayarSub = Pembayaran::select(
                'id_customer',
                DB::raw('SUM(jumlah) as total_bayar_sum')
            )
                ->where('keterangan', '!=', 'GANTI NAMA')
                ->groupBy('id_customer');

            $data = Customer::orderBy('id_customer', 'desc')
                ->with(['progres', 'marketing'])
                ->leftJoinSub($totalTagihanSub, 'tagihan_sums', function ($join) {
                    $join->on('nasabah.id_customer', '=', 'tagihan_sums.nasabah');
                })
                ->leftJoinSub($totalBayarSub, 'pembayaran_sums', function ($join) {
                    $join->on('nasabah.id_customer', '=', 'pembayaran_sums.id_customer');
                })
                ->select(
                    'nasabah.*',
                    DB::raw('COALESCE(tagihan_sums.total_tagihan_sum, 0) as total_tagihan'),
                    DB::raw('COALESCE(pembayaran_sums.total_bayar_sum, 0) as total_bayar')
                );

            // LOGIKA PERBAIKAN SEARCHING NAMA NASABAH:
            if ($request->filled('search.value')) {
                $searchValue = $request->input('search.value');
                $data->where(function ($query) use ($searchValue) {
                    $query->where('nasabah.nama_lengkap', 'like', '%'.$searchValue.'%');
                });
            }

            if ($request->filled('status_progres')) {
                $data->where('id_status_progres', $request->status_progres);
            }

            if ($request->filled('status_bayar')) {
                $statusBayar = $request->status_bayar;

                if ($statusBayar === 'Lunas') {
                    $data->whereRaw('COALESCE(tagihan_sums.total_tagihan_sum, 0) - COALESCE(pembayaran_sums.total_bayar_sum, 0) = 0')
                        ->whereRaw('COALESCE(tagihan_sums.total_tagihan_sum, 0) > 0');
                } elseif ($statusBayar === 'Terhutang') {
                    $data->whereRaw('COALESCE(tagihan_sums.total_tagihan_sum, 0) - COALESCE(pembayaran_sums.total_bayar_sum, 0) > 0');
                }
            }

            return DataTables::of($data)
                ->addIndexColumn()

                ->addColumn('nasabah', function ($row) {
                    $badge = $row->jenis_pembelian == 'KPR'
                        ? '<span class="badge badge-secondary">KPR</span>'
                        : ($row->jenis_pembelian == 'Cash Bertahap'
                            ? '<span class="badge badge-success">Bertahap</span>' : '');

                    return '<b>'.$row->nama_lengkap.'</b><br>'.$row->no_telp.'<br>'.$badge;
                })

                ->addColumn('rincian_tagihan', function ($row) {
                    $tagihanUtama = $row->tagihan()->where('utama', 1)->get();
                    $tagihanLain = $row->tagihan()->where('utama', 0)->get();

                    $totalTagihan = $row->total_tagihan;
                    $totalBayar = $row->total_bayar;
                    $sisa = $totalTagihan - $totalBayar;

                    $html = '';

                    foreach ($tagihanUtama as $tagih) {
                        $html .= '<i class="fa fa-home text-danger"></i> '.$tagih->jenis_tagihan.' # <b>Rp. '.number_format($tagih->jumlah_tagihan, 0, ',', '.').'</b><br>';
                    }
                    foreach ($tagihanLain as $tagih) {
                        $html .= '<i class="fa fa-plus-square text-danger"></i> '.$tagih->jenis_tagihan.' # <b>Rp. '.number_format($tagih->jumlah_tagihan, 0, ',', '.').'</b><br>';
                    }

                    $html .= '<hr>';
                    $html .= '<b>Total Tagihan: </b> Rp. '.number_format($totalTagihan, 0, ',', '.').'<br>';
                    $html .= '<b>Jumlah Bayar: </b> Rp. '.number_format($totalBayar, 0, ',', '.').'<br>';
                    $html .= '<span class="text-danger"><b>Sisa Bayar: </b> Rp. '.number_format($sisa, 0, ',', '.').'</span>';

                    return $html;
                })

                ->addColumn('status', function ($row) {
                    $status = $row->progres ? $row->progres->status_progres : '';
                    $marketing = $row->marketing ? '<span class="badge badge-warning">'.$row->marketing->nama_marketing.'</span>' : '';

                    return $status.($status && $marketing ? '<br>' : '').$marketing;
                })

                ->addColumn('jumlah_tagihan', function ($row) {
                    $totalTagihan = $row->total_tagihan;
                    $totalBayar = $row->total_bayar;
                    $sisa = $totalTagihan - $totalBayar;

                    if ($sisa == 0 && $totalTagihan > 0) {
                        return '<img src="'.asset('assets/img/lunas.jpg').'" width="100px">';
                    }

                    return '<span class="badge badge-warning">Tagihan : Rp. '.number_format($totalTagihan, 0, ',', '.').'</span><br>'.
                        '<span class="badge badge-success">Sudah Bayar : Rp. '.number_format($totalBayar, 0, ',', '.').'</span><br>'.
                        '<span class="badge badge-danger">Sisa Bayar : Rp. '.number_format($sisa, 0, ',', '.').'</span>';
                })

                ->addColumn('action', function ($row) {
                    return '<a href="'.route('pembayaran.detail', $row->id_customer).'" class="btn d-block align-items-center btn-success btn-sm">Detail Data</a>';
                })

                ->rawColumns(['nasabah', 'rincian_tagihan', 'status', 'jumlah_tagihan', 'is_admin', 'action'])
                ->make(true);
        }

        $progreslists = ProgresListPenjualan::all(['id_status_progres', 'status_progres']);

        return view('admin.pembayaran.index', compact('permissions', 'progreslists'));
    }

    public function cetakRekap($id)
    {
        $konfigurasi = DB::table('konfigurasi')->select('nama_perusahaan', 'telp')->first();
        $totalTagihan = Tagihan::where('nasabah', $id)->sum('jumlah_tagihan');

        $namaPerusahaan = $konfigurasi->nama_perusahaan ?? 'PT. APLIKASI KAVLING';
        $telp = $konfigurasi->telp ?? '081250274777';

        $customer = Customer::with(['pembayaran' => function ($query) {
            $query->where('keterangan', '!=', 'GANTI NAMA');
        }])
            ->findOrFail($id);

        $kavling = $customer->kavlingPeta;
        $pengaturanMedia = PengaturanMedia::where('jenis_data', 'kop surat')->first();
        $logoPath = null;

        if ($pengaturanMedia && $pengaturanMedia->nama_file) {
            $logoPath = public_path('config_media/'.$pengaturanMedia->nama_file);
        }

        $pdf = new TCPDF('P', 'mm', 'A4');
        $pdf->AddPage();

        if ($logoPath && file_exists($logoPath)) {
            $pdf->Image($logoPath, 0, 5, 210);
        }

        $pdf->SetXY(90, 32);

        // $pdf->SetDrawColor(0, 0, 0);
        // $pdf->SetLineWidth(0.7);
        // $pdf->Line(10, 42, 200, 42);

        // $pdf->SetLineWidth(0.3);
        // $pdf->Line(10, 41, 200, 41);

        $pdf->Ln(10);
        $pdf->SetFont('Times', 'B', 10);
        $pdf->SetTextColor(218, 0, 0);
        $pdf->Cell(190, 8, 'TABEL REKAP PEMBAYARAN', 0, 1, 'C');
        $pdf->Ln(3);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Times', '', 9);

        $pdf->Cell(5, 6, '', 0, 0);
        $pdf->Cell(17, 6, 'Nama', 0, 0);
        $pdf->Cell(3, 6, ':', 0, 0);
        $pdf->Cell(45, 6, strtoupper($customer->nama_lengkap), 0, 0);
        $pdf->Cell(5, 6, '', 0, 0);
        $pdf->Cell(17, 6, 'Lokasi', 0, 0);
        $pdf->Cell(3, 6, ':', 0, 0);
        $pdf->Cell(20, 6, $kavling->kode_kavling ?? '-', 0, 0);
        $pdf->Cell(5, 6, '', 0, 0);
        $pdf->Cell(25, 6, 'Harga', 0, 0);
        $pdf->Cell(3, 6, ':', 0, 0);
        $pdf->Cell(30, 6, 'Rp '.number_format($totalTagihan ?? 0, 0, ',', '.'), 0, 1);

        $pdf->Cell(5, 6, '', 0, 0);
        $pdf->Cell(17, 6, 'No. KTP', 0, 0);
        $pdf->Cell(3, 6, ':', 0, 0);
        $pdf->Cell(45, 6, $customer->no_ktp ?? '-', 0, 0);
        $pdf->Cell(5, 6, '', 0, 0);
        $pdf->Cell(17, 6, 'Luas Tanah', 0, 0);
        $pdf->Cell(3, 6, ':', 0, 0);
        $pdf->Cell(25, 6, ($kavling->luas_tanah ?? '-').' M2', 0, 0);
        $pdf->Cell(25, 6, 'Luas Bangunan', 0, 0);
        $pdf->Cell(3, 6, ':', 0, 0);
        $pdf->Cell(30, 6, ($kavling->luas_bangunan ?? '-').' M2', 0, 1);

        $pdf->Cell(5, 6, '', 0, 0);
        $pdf->Cell(17, 6, 'Alamat', 0, 0);
        $pdf->Cell(3, 6, ':', 0, 0);
        $pdf->Cell(100, 6, $customer->alamat, 0, 1);

        $pdf->Ln(2);
        $pdf->Cell(5, 6, '', 0, 0);
        $pdf->Cell(35, 6, 'Metode Pembayaran', 0, 0);
        $pdf->Cell(35, 6, ': '.($customer->jenis_pembelian ?? '-'), 0, 1);

        $pdf->Ln(5);
        $pdf->SetFont('Times', 'B', 9);
        $pdf->SetFillColor(211, 236, 230);
        $pdf->Cell(5, 7, '', 0, 0);
        $pdf->Cell(8, 7, 'No', 1, 0, 'C', true);
        $pdf->Cell(30, 7, 'Tanggal', 1, 0, 'C', true);
        $pdf->Cell(75, 7, 'Keterangan', 1, 0, 'L', true);
        $pdf->Cell(31, 7, 'Pembayaran', 1, 0, 'C', true);
        $pdf->Cell(31, 7, 'Sisa Pembayaran', 1, 1, 'C', true);

        $pdf->SetFont('Times', '', 9);
        $no = 1;
        $sisa = $totalTagihan ?? 0;

        foreach ($customer->pembayaran->sortBy('id_pembayaran') as $byr) {
            $sisa -= $byr->jumlah;
            $keterangan = explode('#', $byr->keterangan)[0] ?? '';
            $fill = ($no % 2 == 0) ? [255, 243, 243] : [255, 255, 255];
            $pdf->SetFillColor(...$fill);

            $pdf->Cell(5, 7, '', 0, 0);
            $pdf->Cell(8, 7, $no++, 1, 0, 'C', true);
            $pdf->Cell(30, 7, date('d-m-Y', strtotime($byr->tanggal)), 1, 0, 'C', true);
            $pdf->Cell(75, 7, ($sisa <= 0) ? 'LUNAS' : $keterangan, 1, 0, 'L', true);
            $pdf->Cell(31, 7, 'Rp '.number_format($byr->jumlah, 0, ',', '.'), 1, 0, 'R', true);
            $pdf->Cell(31, 7, 'Rp '.number_format(max($sisa, 0), 0, ',', '.'), 1, 1, 'R', true);
        }

        $pdf->Ln(10);
        $pdf->SetFont('', '', 8);
        $pdf->SetTextColor(0, 0, 0);
        $catatan = "Catatan:\n"
            ."- Bukti pembayaran dinyatakan sah apabila disertai kwitansi dari tangan pemilik kavling.\n"
            ."- Apabila ada yang mengaku-ngaku petugas kami \"$namaPerusahaan\" meminta/menagih pembayaran angsuran, harap waspada. HATI-HATI PENIPUAN.\n"
            ."- Konsumen dapat menanyakan atau menghubungi informasi resmi \"$namaPerusahaan\" di nomor $telp.";

        $pdf->MultiCell(0, 0, $catatan, 0, 'L');

        $pdf->Ln(15);
        $pdf->SetFont('Times', '', 9);
        $pdf->Cell(60, 6, 'Mengetahui', 0, 0, 'C');
        $pdf->Cell(70, 6, '', 0, 0);
        $pdf->Cell(60, 6, 'Mengetahui', 0, 1, 'C');
        $pdf->Ln(20);
        $pdf->Cell(60, 6, 'ADMIN KEUANGAN', 0, 0, 'C');
        $pdf->Cell(70, 6, '', 0, 0);
        $pdf->Cell(60, 6, 'ADMIN DUA', 0, 1, 'C');

        $pdf->Output();
        exit;
    }

    // cetak pemasukan
    public function cetak($id)
    {
        $pembayaran = Pembayaran::with(['customer', 'jenisPembayaran'])->where('id_pembayaran', $id)->firstOrFail();
        $nasabah = $pembayaran->customer;

        $lokasi = LokasiKavling::where('id_lokasi', $nasabah->id_lokasi)->first();
        $kavling = KavlingPeta::where('id_kavling', $nasabah->id_kavling)->first();

        $totalTagihan = Tagihan::where('nasabah', $nasabah->id_customer)->sum('jumlah_tagihan');

        $blokNomor = '-';
        if ($lokasi) {
            if ($lokasi->is_cluster) {
                $blokNomor = ($kavling->cluster ?? '-').'-'.($kavling->no ?? '-');
            } else {
                $blokNomor = $kavling->kode_kavling ?? '-';
            }
        }

        $width = 310;
        $height = 220;
        $fpdf = new TCPDF('P', 'mm', [$width, $height]);

        $fpdf->SetTitle('Kwitansi');
        $fpdf->SetPrintHeader(false);
        $fpdf->SetPrintFooter(false);
        $fpdf->SetMargins(0, 0, 0);
        $fpdf->SetAutoPageBreak(false, 0);
        $fpdf->AddPage();

        $backgroundPath = public_path('assets/img/kwitasi_rangkap.jpg');
        if (file_exists($backgroundPath)) {
            $imgWidth = 220;
            $imgHeight = 310;

            $x = 0;
            $y = 0;

            $fpdf->Image($backgroundPath, $x, $y, $imgWidth, $imgHeight, '', '', '', false, 150, '', false, false, 0);
        }

        // penyetor
        $fpdf->SetFont('helvetica', '', 10);
        $fpdf->SetMargins(10, 0, 0);
        $fpdf->SetAutoPageBreak(false);

        $fpdf->Ln(20);
        $fpdf->Cell(6, 0, '', 0, 0, 'L');
        $fpdf->Cell(0, 18, $pembayaran->no_kwitansi ?? '-', 0, 1, 'L');

        $fpdf->SetTextColor(0, 0, 0);

        $fpdf->SetFont('Times', '', 12);
        $fpdf->SetXY(6, 26);
        $fpdf->Cell(55, 5, '', 0, 0, 'L');
        $fpdf->Cell(90, 15, $nasabah->nama_lengkap ?? '-', 0, 1, 'L');

        $fpdf->SetXY(6, 38);
        $fpdf->Cell(55, 0, '', 0, 0, 'L');
        $fpdf->Cell(90, 0, $this->terbilang($pembayaran->jumlah).' Rupiah', 0, 1, 'L');

        $fpdf->SetXY(6, 46);
        $fpdf->Cell(55, 5, '', 0, 0, 'L');
        $fpdf->Cell(90, 3, $pembayaran->jenisPembayaran->nama_jenis.' Pembelian Rumah '.($lokasi->nama_kavling ?? '-'), 0, 1, 'L');

        $fpdf->SetXY(6, 50);
        $fpdf->Cell(55, 5, '', 0, 0, 'L');
        $fpdf->Cell(90, 10, 'Lokasi Rumah: '.$blokNomor, 0, 1, 'L');

        $fpdf->SetFont('helvetica', 'B', 12);
        $fpdf->Text(25, 64, number_format($pembayaran->jumlah, 0, ',', '.').',-');

        $fpdf->SetFont('helvetica', 'B', 10);
        $fpdf->SetTextColor(0, 0, 0);

        $metode = strtoupper($pembayaran->metode_bayar ?? '-');

        $checkIcon = public_path('check-solid.png');
        $checkSize = 5;

        if (file_exists($checkIcon)) {
            switch ($metode) {
                case 'CASH':
                    $fpdf->Image($checkIcon, 11.5, 72, $checkSize);
                    break;

                case 'TRANSFER':
                    $fpdf->Image($checkIcon, 28, 72, $checkSize);
                    break;

                case 'CHEQUE':
                    $fpdf->Image($checkIcon, 53, 72, $checkSize);
                    break;

                case 'BILYET GIRO':
                    $fpdf->Image($checkIcon, 74, 72, $checkSize);
                    break;
            }
        }

        $fpdf->Ln(-22);
        $fpdf->SetX(35);
        $fpdf->SetFont('helvetica', '', 9);
        $fpdf->Cell(120, 0, '', 0, 0, 'C');
        $fpdf->Cell(70, 40, ($lokasi->kota_penandatangan ?? '-').', '.Carbon::parse($pembayaran->tanggal)->translatedFormat('d F Y'), 0, 1, 'C');

        // kasir
        $fpdf->SetFont('helvetica', '', 10);
        $fpdf->SetMargins(10, 0, 0);
        $fpdf->SetAutoPageBreak(false);

        $fpdf->Ln(41);
        $fpdf->Cell(6, 0, '', 0, 0, 'L');
        $fpdf->Cell(0, 18, $pembayaran->no_kwitansi ?? '-', 0, 1, 'L');

        $fpdf->SetTextColor(0, 0, 0);

        $fpdf->SetFont('Times', '', 12);
        $fpdf->SetXY(6, 129);
        $fpdf->Cell(55, 5, '', 0, 0, 'L');
        $fpdf->Cell(90, 15, $nasabah->nama_lengkap ?? '-', 0, 1, 'L');

        $fpdf->SetXY(6, 141);
        $fpdf->Cell(55, 0, '', 0, 0, 'L');
        $fpdf->Cell(90, 0, $this->terbilang($pembayaran->jumlah).' Rupiah', 0, 1, 'L');

        $fpdf->SetXY(6, 148);
        $fpdf->Cell(55, 5, '', 0, 0, 'L');
        $fpdf->Cell(90, 3, $pembayaran->jenisPembayaran->nama_jenis.' Pembelian Rumah '.($lokasi->nama_kavling ?? '-'), 0, 1, 'L');

        $fpdf->SetXY(6, 152);
        $fpdf->Cell(55, 5, '', 0, 0, 'L');
        $fpdf->Cell(90, 10, 'Lokasi Rumah: '.$blokNomor, 0, 1, 'L');

        $fpdf->SetFont('helvetica', 'B', 12);
        $fpdf->Text(25, 167, number_format($pembayaran->jumlah, 0, ',', '.').',-');

        $fpdf->SetFont('helvetica', 'B', 10);
        $fpdf->SetTextColor(0, 0, 0);

        $metode = strtoupper($pembayaran->metode_bayar ?? '-');

        $checkIcon = public_path('check-solid.png');
        $checkSize = 5;

        if (file_exists($checkIcon)) {
            switch ($metode) {
                case 'CASH':
                    $fpdf->Image($checkIcon, 11.5, 175, $checkSize);
                    break;

                case 'TRANSFER':
                    $fpdf->Image($checkIcon, 28, 175, $checkSize);
                    break;

                case 'CHEQUE':
                    $fpdf->Image($checkIcon, 53, 175, $checkSize);
                    break;

                case 'BILYET GIRO':
                    $fpdf->Image($checkIcon, 74, 175, $checkSize);
                    break;
            }
        }

        $fpdf->Ln(-22);
        $fpdf->SetX(35);
        $fpdf->SetFont('helvetica', '', 9);
        $fpdf->Cell(120, 0, '', 0, 0, 'C');
        $fpdf->Cell(70, 40, ($lokasi->kota_penandatangan ?? '-').', '.Carbon::parse($pembayaran->tanggal)->translatedFormat('d F Y'), 0, 1, 'C');

        // cs
        $fpdf->SetFont('helvetica', '', 10);
        $fpdf->SetMargins(10, 0, 0);
        $fpdf->SetAutoPageBreak(false);

        $fpdf->Ln(42);
        $fpdf->Cell(6, 0, '', 0, 0, 'L');
        $fpdf->Cell(0, 18, $pembayaran->no_kwitansi ?? '-', 0, 1, 'L');

        $fpdf->SetTextColor(0, 0, 0);

        $fpdf->SetFont('Times', '', 12);
        $fpdf->SetXY(6, 232);
        $fpdf->Cell(55, 5, '', 0, 0, 'L');
        $fpdf->Cell(90, 15, $nasabah->nama_lengkap ?? '-', 0, 1, 'L');

        $fpdf->SetXY(6, 244);
        $fpdf->Cell(55, 0, '', 0, 0, 'L');
        $fpdf->Cell(90, 0, $this->terbilang($pembayaran->jumlah).' Rupiah', 0, 1, 'L');

        $fpdf->SetXY(6, 251);
        $fpdf->Cell(55, 5, '', 0, 0, 'L');
        $fpdf->Cell(90, 3, $pembayaran->jenisPembayaran->nama_jenis.' Pembelian Rumah '.($lokasi->nama_kavling ?? '-'), 0, 1, 'L');

        $fpdf->SetXY(6, 255);
        $fpdf->Cell(55, 5, '', 0, 0, 'L');
        $fpdf->Cell(90, 10, 'Lokasi Rumah: '.$blokNomor, 0, 1, 'L');

        $fpdf->SetFont('helvetica', 'B', 12);
        $fpdf->Text(25, 270, number_format($pembayaran->jumlah, 0, ',', '.').',-');

        $fpdf->SetFont('helvetica', 'B', 10);
        $fpdf->SetTextColor(0, 0, 0);

        $metode = strtoupper($pembayaran->metode_bayar ?? '-');

        $checkIcon = public_path('check-solid.png');
        $checkSize = 5;

        if (file_exists($checkIcon)) {
            switch ($metode) {
                case 'CASH':
                    $fpdf->Image($checkIcon, 11.5, 278, $checkSize);
                    break;

                case 'TRANSFER':
                    $fpdf->Image($checkIcon, 28, 278, $checkSize);
                    break;

                case 'CHEQUE':
                    $fpdf->Image($checkIcon, 53, 278, $checkSize);
                    break;

                case 'BILYET GIRO':
                    $fpdf->Image($checkIcon, 74, 278, $checkSize);
                    break;
            }
        }

        $fpdf->Ln(-22);
        $fpdf->SetX(35);
        $fpdf->SetFont('helvetica', '', 9);
        $fpdf->Cell(120, 0, '', 0, 0, 'C');
        $fpdf->Cell(70, 40, ($lokasi->kota_penandatangan ?? '-').', '.Carbon::parse($pembayaran->tanggal)->translatedFormat('d F Y'), 0, 1, 'C');

        $fpdf->Output();
        exit;
    }

    private function terbilang($angka)
    {
        $angka = abs($angka);
        $baca = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
        $hasil = '';

        if ($angka < 12) {
            $hasil = ' '.$baca[$angka];
        } elseif ($angka < 20) {
            $hasil = $this->terbilang($angka - 10).' Belas';
        } elseif ($angka < 100) {
            $hasil = $this->terbilang($angka / 10).' Puluh'.$this->terbilang($angka % 10);
        } elseif ($angka < 200) {
            $hasil = ' Seratus'.$this->terbilang($angka - 100);
        } elseif ($angka < 1000) {
            $hasil = $this->terbilang($angka / 100).' Ratus'.$this->terbilang($angka % 100);
        } elseif ($angka < 2000) {
            $hasil = ' Seribu'.$this->terbilang($angka - 1000);
        } elseif ($angka < 1000000) {
            $hasil = $this->terbilang($angka / 1000).' Ribu'.$this->terbilang($angka % 1000);
        } elseif ($angka < 1000000000) {
            $hasil = $this->terbilang($angka / 1000000).' Juta'.$this->terbilang($angka % 1000000);
        }

        return trim($hasil);
    }

    // cetak pengeluaran
    public function cetakPengeluaran($id)
    {
        $pengeluaran = Pengeluaran::with(['customer', 'jenisPembayaran'])->where('id_pembayaran_keluar', $id)->firstOrFail();
        $nasabah = $pengeluaran->customer;
        $lokasi = LokasiKavling::where('id_lokasi', $nasabah->id_lokasi)->first();
        $kavling = KavlingPeta::where('id_kavling', $nasabah->id_kavling)->first();

        $width = 210;
        $height = 120;
        $fpdf = new TCPDF('L', 'mm', [$width, $height]);

        $fpdf->SetPrintHeader(false);
        $fpdf->SetPrintFooter(false);
        $fpdf->SetMargins(0, 0, 0);
        $fpdf->SetAutoPageBreak(false, 0);
        $fpdf->AddPage();

        $idPerusahaan = $kavling->id_perusahaan ?? null;

        $backgroundPath = public_path('assets/img/bg_kwitansi.jpg');

        if ($idPerusahaan) {
            $perusahaan = Perusahaan::find($idPerusahaan);
            if ($perusahaan && $perusahaan->bg_kwitansi) {
                $customBg = public_path("assets/lokasi_perumahan/bg_kwitansi/{$perusahaan->bg_kwitansi}");
                if (file_exists($customBg)) {
                    $backgroundPath = $customBg;
                }
            }
        }

        // Masukkan ke FPDF
        $fpdf->Image($backgroundPath, 0, 0, $width, $height, '', '', '', false, 300, '', false, false, 0);

        $fpdf->SetFont('helvetica', '', 10);
        $fpdf->SetMargins(10, 0, 0);
        $fpdf->SetAutoPageBreak(false);

        $logoPath = public_path('assets/img/header.png');
        if (file_exists($logoPath)) {
            $fpdf->Image($logoPath, 15, 7, 20);
        }

        $fpdf->SetFont('Times', 'B', 10);
        $fpdf->SetTextColor(0, 0, 0);
        $fpdf->SetXY(40, 10);
        $fpdf->Cell(0, 7, 'PT. HAMZAH MAJU BERSAMA', 0, 2, 'L');

        $fpdf->SetFont('Times', 'B', 10);
        $fpdf->SetTextColor(0, 176, 240);
        $fpdf->SetXY(40, 15);
        $fpdf->Cell(0, 7, 'developer & kontraktor', 0, 2, 'L');

        $fpdf->SetFont('Times', 'B', 10);
        $fpdf->SetTextColor(0, 0, 0);

        $fpdf->Ln(7);
        $fpdf->Cell(6, 0, '', 0, 0, 'L');

        $fpdf->SetTextColor(255, 0, 0);
        $fpdf->Cell(0, 18, $pembayaran->no_kwitansi ?? '-', 0, 1, 'L');

        $fpdf->SetTextColor(0, 0, 0);

        $fpdf->SetFont('Times', '', 12);
        $fpdf->SetXY(5, 36);
        $fpdf->Cell(55, 5, '', 0, 0, 'L');
        $fpdf->Cell(90, 15, ($nasabah->nama_lengkap ?? '-'), 0, 1, 'L');

        $fpdf->SetXY(5, 48);
        $fpdf->Cell(55, 0, '', 0, 0, 'L');
        $fpdf->Cell(90, 0, $this->terbilang($pengeluaran->jumlah).' Rupiah', 0, 1, 'L');

        $fpdf->SetXY(5, 56);
        $fpdf->Cell(55, 5, '', 0, 0, 'L');
        $fpdf->Cell(90, 3, ($pengeluaran->jenisPembayaran->nama_jenis ?? '-'), 0, 1, 'L');

        $fpdf->SetFont('helvetica', 'B', 12);
        $fpdf->Text(30, 80, number_format($pengeluaran->jumlah, 0, ',', '.').',-');

        $metode = strtoupper($pengeluaran->metode_bayar ?? '-');

        $checkIcon = public_path('check-solid.png');
        $checkSize = 5;

        if (file_exists($checkIcon)) {
            switch ($metode) {
                case 'CASH':
                    $fpdf->Image($checkIcon, 11.5, 88, $checkSize);
                    break;

                case 'TRANSFER':
                    $fpdf->Image($checkIcon, 28, 88, $checkSize);
                    break;

                case 'CHEQUE':
                    $fpdf->Image($checkIcon, 53, 88, $checkSize);
                    break;

                case 'BILYET GIRO':
                    $fpdf->Image($checkIcon, 74, 88, $checkSize);
                    break;
            }
        }

        $fpdf->Image(public_path('assets/img/ttd_direktur.jpg'), 153, 90, 25);
        $fpdf->Ln(-15);
        $fpdf->SetFont('helvetica', '', 9);
        $fpdf->Cell(120, 0, '', 0, 0, 'C');
        $fpdf->Cell(70, 40, ($lokasi->kota_penandatangan ?? '-').', '.Carbon::parse($pengeluaran->tanggal)->translatedFormat('d F Y'), 0, 1, 'C');

        $fpdf->Ln(0);
        $fpdf->SetFont('Times', 'U', 10);
        $fpdf->Cell(120, 5, '', 0, 0, 'C');
        $fpdf->Cell(70, 10, 'Direktur', 0, 1, 'C');

        $fpdf->SetFont('helvetica', 'B', 10);
        $fpdf->SetXY(14, 101);
        $fpdf->Cell(0, 0, $lokasi->informasi_rek ?? '-', 0, 'L');

        $fpdf->Output();
        exit;
    }

    public function getCustomerKPR()
    {
        $data = Customer::where('jenis_pembelian', 'KPR')
            ->orderBy('id_customer', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id_customer' => $item->id_customer,
                    'kode_customer' => $item->kode_customer,
                    'nama_lengkap' => $item->nama_lengkap,
                ];
            });

        if (! $data) {
            return response()->json([
                'success' => false,
                'message' => 'Data customer kpr tidak ditemukan.',
                'data' => [],
            ]);
        }

        if ($data) {
            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        }
    }

    public function detail($id)
    {
        $permissions = HakAksesController::getPermissionByRoute('pembayaran');
        $customer = Customer::where('id_customer', $id)->firstOrFail();

        $lokasi = LokasiKavling::where('id_lokasi', $customer->id_lokasi)->value('nama_kavling') ?? '-';
        $kavling = KavlingPeta::where('id_kavling', $customer->id_kavling)->value('kode_kavling') ?? '-';

        $tagihanList = Tagihan::where('nasabah', $customer->id_customer)->get();
        $pembayaranList = Pembayaran::where('id_customer', $customer->id_customer)->where('keterangan', '!=', 'GANTI NAMA')->get();

        $pembayaranList->each(function ($pembayaran) {
            $pembayaran->nama_jenis = JenisPembayaran::where('id_jenis_pembayaran', $pembayaran->id_jenis_pembayaran)->value('nama_jenis') ?? '-';
        });

        $totalTagihan = $tagihanList->sum('jumlah_tagihan');
        $jumlahBayar = $pembayaranList->sum('jumlah');
        $sisaBayar = $totalTagihan - $jumlahBayar;

        $jenisPembayaran = JenisPembayaran::whereNotIn('id_jenis_pembayaran', [3, 13])->get();
        $jenisPengeluaran = JenisPengeluaran::all();

        return view('admin.pembayaran.detail', compact(
            'customer',
            'lokasi',
            'kavling',
            'totalTagihan',
            'jumlahBayar',
            'sisaBayar',
            'jenisPembayaran',
            'jenisPengeluaran',
            'permissions'
        ));
    }

    public function detailTagihan(Request $request, $id)
    {
        $permissions = HakAksesController::getPermissionByRoute('pembayaran');

        if ($request->ajax()) {
            $tagihanList = Tagihan::where('nasabah', $id)->get();
            $totalTagihan = $tagihanList->sum('jumlah_tagihan');

            return DataTables::of($tagihanList)
                ->addIndexColumn()
                ->addColumn('jumlah_tagihan', function ($row) {
                    return 'Rp. '.number_format($row->jumlah_tagihan, 0, ',', '.');
                })
                ->addColumn('action', function ($row) use ($id, $permissions) {
                    $deleteUrl = route('pembayaran.DeleteTagihan', $row->id);

                    if ($row->utama == 1) {
                        if ($permissions['edit']) {
                            return '<form class="formHargaRumah" data-id="'.$id.'">'.
                                csrf_field().
                                '<button type="submit" class="btn btn-warning btn-xs ms-1 btn-update-harga">
                            <span class="swal-btn-text">Update</span>
                        </button>'.
                                '</form>';
                        }
                    } else {
                        if ($row->jenis_tagihan == 'Biaya Surat-surat , Biaya Notaris (AJB, SHGB), BPHTB,  promosi/iklan, pemasaran dan biaya lain-lain') {
                            return '';
                        }

                        return '<form action="'.e($deleteUrl).'" method="POST" style="display:inline;">'.csrf_field().method_field('DELETE').'<button type="submit" class="delete-tagihan btn btn-danger btn-xs">Hapus</button></form>';
                    }
                })
                ->rawColumns(['action'])
                ->with('total_tagihan', $totalTagihan)
                ->with('total_tagihan_formatted', number_format($totalTagihan, 0, ',', '.'))
                ->make(true);
        }
    }

    public function tambahTagihan(Request $request, $id)
    {
        $rules = [
            'jenis_tagihan' => 'required',
            'jumlah_tagihan' => 'required',
            'catatan' => 'nullable',
        ];

        $messages = [
            'jenis_tagihan.required' => 'Jenis tagihan wajib diisi.',
            'jumlah_tagihan.required' => 'Jumlah tagihan wajib diisi.',
        ];

        $request->validate($rules, $messages);

        DB::beginTransaction();
        try {
            Tagihan::create([
                'nasabah' => $id,
                'jenis_tagihan' => $request->jenis_tagihan,
                'jumlah_tagihan' => (int) str_replace(['.', ','], '', $request->jumlah_tagihan),
                'utama' => 0,
                'catatan' => $request->catatan ?? '',
            ]);

            $totalTagihan = Tagihan::where('nasabah', $id)->sum('jumlah_tagihan');
            $jumlahBayar = Pembayaran::where('id_customer', $id)->where('keterangan', '!=', 'GANTI NAMA')->sum('jumlah');
            $sisaBayar = $totalTagihan - $jumlahBayar;

            DB::commit();

            return response()->json([
                'status' => 'success',
                'total_tagihan_formatted' => number_format($totalTagihan, 0, ',', '.'),
                'sisa_bayar_formatted' => number_format($sisaBayar, 0, ',', '.'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getDataKavling($idCustomer)
    {
        $cust = Customer::findOrFail($idCustomer);
        $kav = KavlingPeta::with('lokasi')->findOrFail($cust->id_kavling);
        $lokasi = LokasiKavling::find($cust->id_lokasi);

        $namaLokasi = $lokasi->nama_kavling ?? '-';
        if ($lokasi && $lokasi->is_cluster) {
            $blokNomor = ($kav->cluster ?? '-').'-'.($kav->no ?? '-');
        } else {
            $blokNomor = $kav->kode_kavling ?? '-';
        }

        return response()->json([
            'nama_lengkap' => $cust->nama_lengkap,
            'lokasi_perumahan' => $namaLokasi.' # '.$blokNomor,
            'hrg_jual' => number_format($kav->hrg_jual, 0, ',', '.'),
            'biaya_lain' => number_format($kav->biaya_lain, 0, ',', '.'),
        ]);
    }

    public function UpdateHargaRumah(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $hrg_jual = str_replace('.', '', $request->hrg_jual);
            $biaya_lain = str_replace('.', '', $request->biaya_lain ?? '0');

            $cust = Customer::find($id);
            $kav = KavlingPeta::find($cust->id_kavling);

            $kav->update([
                'hrg_jual' => $hrg_jual,
                'biaya_lain' => $biaya_lain,
            ]);

            $tagihan = Tagihan::where('nasabah', $id)->where('utama', 1);
            $tagihan->update([
                'jumlah_tagihan' => $hrg_jual,
            ]);

            if ($biaya_lain > 0) {
                Tagihan::updateOrCreate(
                    [
                        'nasabah' => $id,
                        'jenis_tagihan' => 'Biaya Surat-surat , Biaya Notaris (AJB, SHGB), BPHTB,  promosi/iklan, pemasaran dan biaya lain-lain',
                        'utama' => 0,
                    ],
                    [
                        'jumlah_tagihan' => $biaya_lain,
                        'catatan' => 'Otomatis dari Update Harga Rumah',
                    ]
                );
            } else {
                Tagihan::where('nasabah', $id)
                    ->where('jenis_tagihan', 'Biaya Surat-surat , Biaya Notaris (AJB, SHGB), BPHTB,  promosi/iklan, pemasaran dan biaya lain-lain')
                    ->where('utama', 0)
                    ->delete();
            }

            $totalTagihan = Tagihan::where('nasabah', $id)->sum('jumlah_tagihan');
            $jumlahBayar = Pembayaran::where('id_customer', $id)->where('keterangan', '!=', 'GANTI NAMA')->sum('jumlah');
            $sisaBayar = $totalTagihan - $jumlahBayar;

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Data harga dan biaya lain berhasil diperbarui',
                'total_tagihan_formatted' => number_format($totalTagihan, 0, ',', '.'),
                'sisa_bayar_formatted' => number_format($sisaBayar, 0, ',', '.'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function DeleteTagihan($id)
    {
        $tagihan = Tagihan::findOrFail($id);
        $nasabahId = $tagihan->nasabah;

        $tagihan->delete();

        $totalTagihan = Tagihan::where('nasabah', $nasabahId)->sum('jumlah_tagihan');
        $jumlahBayar = Pembayaran::where('id_customer', $nasabahId)->where('keterangan', '!=', 'GANTI NAMA')->sum('jumlah');
        $sisaBayar = $totalTagihan - $jumlahBayar;

        return response()->json([
            'status' => 'success',
            'total_tagihan_formatted' => number_format($totalTagihan, 0, ',', '.'),
            'sisa_bayar_formatted' => number_format($sisaBayar, 0, ',', '.'),
        ]);
    }

    public function detailPemasukan(Request $request, $id)
    {
        Carbon::setLocale('id');

        $permissions = HakAksesController::getPermissionByRoute('pembayaran');

        if ($request->ajax()) {
            $data = Pembayaran::where('id_customer', $id)
                ->where('keterangan', '!=', 'GANTI NAMA')
                ->get();

            foreach ($data as $item) {
                $deleteUrl = route('pembayaran.DeletePemasukan', $item->id_pembayaran);
                $item->tanggal = Carbon::parse($item->tanggal)->translatedFormat('d F Y');
                $item->nama_jenis = JenisPembayaran::where('id_jenis_pembayaran', $item->id_jenis_pembayaran)->value('nama_jenis') ?? '-';
                $item->jumlah_formatted = 'Rp. '.number_format($item->jumlah, 0, ',', '.');

                $action = '';

                if (! in_array($item->id_jenis_pembayaran, [3, 13])) {
                    $action .= '
                    <!-- <a class="btn btn-xs btn-primary" href="'.route('pembayaran.Cetak', $item->id_pembayaran).'" target="_blank">Cetak</a> -->
                    <a class="btn btn-xs btn-warning" href="'.route('pembayaran.Print', $item->id_pembayaran).'" target="_blank">
                        <i class="fas fa-print me-1"></i> Print
                    </a>
                    <button type="button" class="btn btn-xs btn-info btn-edit-pemasukan" data-id="'.$item->id_pembayaran.'">
                        <i class="fas fa-edit me-1"></i> Edit
                    </button>
                ';
                }
                if ($permissions['hapus'] == 1) {
                    $deleteUrl = route('pembayaran.DeletePemasukan', $item->id_pembayaran);

                    $action .= '
                        <form action="'.e($deleteUrl).'" method="POST" style="display:inline;">
                            '.csrf_field().'
                            '.method_field('DELETE').'
                            <button type="submit" class="delete-pemasukan btn btn-danger btn-xs">Hapus</button>
                        </form>
                    ';
                }

                $item->action = $action;
            }

            $total = $data->sum('jumlah');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('tanggal', fn ($item) => $item->tanggal)
                ->addColumn('keterangan', fn ($item) => $item->keterangan)
                ->addColumn('nama_jenis', fn ($item) => $item->nama_jenis)
                ->addColumn('jumlah', fn ($item) => $item->jumlah_formatted)
                ->addColumn('action', fn ($item) => $item->action)
                ->with('total_pemasukan_formatted', number_format($total, 0, ',', '.'))
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    private function bulanRomawi($bulan)
    {
        $romawi = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII',
        ];

        return $romawi[(int) $bulan] ?? '';
    }

    public function tambahPemasukan(Request $request, $id)
    {
        $rules = [
            'tanggal_pembayaran' => 'required|date',
            'jenis_pembayaran' => 'required',
            'jumlah_bayar' => 'required',
            'keterangan_pembayaran' => 'required',
            'metode_pembayaran' => 'required',
            'lampiran' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ];

        $messages = [
            'tanggal_pembayaran.required' => 'Tanggal Pembayaran wajib diisi.',
            'tanggal_pembayaran.date' => 'Format Tanggal Pembayaran tidak valid.',
            'jenis_pembayaran.required' => 'Jenis Pembayaran wajib diisi.',
            'jumlah_bayar.required' => 'Jumlah Pembayaran wajib diisi.',
            'keterangan_pembayaran.required' => 'Keterangan wajib diisi.',
            'metode_pembayaran.required' => 'Cara Pembayaran wajib dipilih.',
            'lampiran.file' => 'Lampiran harus berupa file.',
            'lampiran.mimes' => 'Format lampiran harus jpeg, png, jpg, atau pdf.',
            'lampiran.max' => 'Ukuran lampiran maksimal 2MB.',
        ];

        $request->validate($rules, $messages);

        DB::beginTransaction();
        try {
            $customer = Customer::find($id);
            if (! $customer) {
                return response()->json(['error' => 'Customer tidak ditemukan.'], 404);
            }

            $jumlah = (int) str_replace(['.', ','], '', $request->jumlah_bayar);
            $jenis = $request->jenis_pembayaran;
            $no_kwitansi = '';

            if ($jenis != 3 && $jenis != 13) {
                $tahun = Carbon::now()->format('Y');
                $bulan = Carbon::now()->format('n');
                $bulanRomawi = $this->bulanRomawi($bulan);

                $lokasiSingkat = LokasiKavling::where('id_lokasi', $customer->id_lokasi)
                    ->value('nama_singkat') ?? 'XXX';

                $maxNumber = Pembayaran::where('id_lokasi', $customer->id_lokasi)
                    ->where('no_kwitansi', 'LIKE', '%/'.$tahun)
                    ->selectRaw('MAX(CAST(SUBSTRING(no_kwitansi, 1, 4) AS UNSIGNED)) as max_num')
                    ->value('max_num');

                $nextNumber = ($maxNumber ?? 0) + 1;
                $newNumber = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
                $no_kwitansi = $newNumber.'/'.$lokasiSingkat.'/'.$bulanRomawi.'/'.$tahun;
            }

            Pembayaran::create([
                'tanggal' => $request->tanggal_pembayaran,
                'id_customer' => $customer->id_customer,
                'no_kwitansi' => $no_kwitansi,
                'id_lokasi' => $customer->id_lokasi,
                'id_jenis_pembayaran' => $request->jenis_pembayaran,
                'jumlah' => $jumlah,
                'keterangan' => $request->keterangan_pembayaran,
                'metode_bayar' => $request->metode_pembayaran,
                'lampiran' => '',
                'nama_notaris' => '',
                'stt_delete_dp' => 0,
            ]);

            DB::commit();

            $totalBayar = Pembayaran::where('id_customer', $customer->id_customer)->sum('jumlah');
            $totalTagihan = Tagihan::where('nasabah', $customer->id_customer)->sum('jumlah_tagihan');
            $sisaBayar = $totalTagihan - $totalBayar;

            return response()->json([
                'status' => 'success',
                'jumlah_bayar' => 'Rp. '.number_format($totalBayar, 0, ',', '.'),
                'total_tagihan' => 'Rp. '.number_format($totalTagihan, 0, ',', '.'),
                'sisa_bayar' => 'Rp. '.number_format($sisaBayar, 0, ',', '.'),
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getDataPemasukan($id)
    {
        $pembayaran = Pembayaran::findOrFail($id);

        return response()->json([
            'id_pembayaran' => $pembayaran->id_pembayaran,
            'tanggal' => $pembayaran->tanggal,
            'id_jenis_pembayaran' => $pembayaran->id_jenis_pembayaran,
            'jumlah' => number_format($pembayaran->jumlah, 0, ',', '.'),
            'keterangan' => $pembayaran->keterangan,
            'metode_bayar' => $pembayaran->metode_bayar,
        ]);
    }

    public function updatePemasukan(Request $request, $id)
    {
        $rules = [
            'tanggal_pembayaran' => 'required|date',
            'jenis_pembayaran' => 'required',
            'jumlah_bayar' => 'required',
            'keterangan_pembayaran' => 'required',
            'metode_pembayaran' => 'required',
            'lampiran' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ];

        $request->validate($rules);

        DB::beginTransaction();
        try {
            $pembayaran = Pembayaran::findOrFail($id);
            $jumlah = (int) str_replace(['.', ','], '', $request->jumlah_bayar);

            $pembayaran->update([
                'tanggal' => $request->tanggal_pembayaran,
                'id_jenis_pembayaran' => $request->jenis_pembayaran,
                'jumlah' => $jumlah,
                'keterangan' => $request->keterangan_pembayaran,
                'metode_bayar' => $request->metode_pembayaran,
            ]);

            DB::commit();

            $totalBayar = Pembayaran::where('id_customer', $pembayaran->id_customer)->sum('jumlah');
            $totalTagihan = Tagihan::where('nasabah', $pembayaran->id_customer)->sum('jumlah_tagihan');
            $sisaBayar = $totalTagihan - $totalBayar;

            return response()->json([
                'status' => 'success',
                'message' => 'Data pemasukan berhasil diperbarui',
                'jumlah_bayar' => 'Rp. '.number_format($totalBayar, 0, ',', '.'),
                'total_tagihan' => 'Rp. '.number_format($totalTagihan, 0, ',', '.'),
                'sisa_bayar' => 'Rp. '.number_format($sisaBayar, 0, ',', '.'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['status' => 'error', 'error' => $e->getMessage()], 500);
        }
    }

    public function DeletePemasukan($id)
    {
        $pemasukan = Pembayaran::findOrFail($id);
        $id_customer = $pemasukan->id_customer;

        $pemasukan->delete();

        $jumlah_bayar = Pembayaran::where('id_customer', $id_customer)->where('keterangan', '!=', 'GANTI NAMA')->sum('jumlah');
        $total_tagihan = Tagihan::where('nasabah', $id_customer)->sum('jumlah_tagihan');
        $sisa_bayar = $total_tagihan - $jumlah_bayar;

        return response()->json([
            'status' => 'success',
            'jumlah_bayar' => 'Rp. '.number_format($jumlah_bayar, 0, ',', '.'),
            'total_tagihan' => 'Rp. '.number_format($total_tagihan, 0, ',', '.'),
            'sisa_bayar' => 'Rp. '.number_format($sisa_bayar, 0, ',', '.'),
        ]);
    }

    public function print($id)
    {
        $pembayaran = Pembayaran::with(['customer', 'jenisPembayaran'])->where('id_pembayaran', $id)->firstOrFail();
        $nasabah = $pembayaran->customer;
        $lokasi = LokasiKavling::where('id_lokasi', $nasabah->id_lokasi ?? null)->first();
        $kavling = KavlingPeta::where('id_kavling', $nasabah->id_kavling ?? null)->first();

        $noKwitansi = $pembayaran->no_kwitansi ?? '-';
        $nama = $nasabah->nama_lengkap ?? '-';
        $alamat = $nasabah->alamat ?? '-';
        $jumlah = $pembayaran->jumlah ?? 0;
        $terbilang = '#'.strtoupper($this->terbilang($jumlah)).' Rupiah#';
        $namaKavling = $lokasi->nama_kavling ?? '-';
        $tipe = $kavling->tipe_bangunan ?? '-';
        $blokNomor = '-';
        if ($lokasi) {
            if ($lokasi->is_cluster) {
                $blokNomor = ($kavling->cluster ?? '-').'-'.($kavling->no ?? '-');
            } else {
                $blokNomor = $kavling->kode_kavling ?? '-';
            }
        }
        $rumahId = $kavling->id_rumah_sikumbang ?? '-';
        $hargaJual = $kavling->hrg_jual ?? 0;
        $kotaTtd = $lokasi->kota_penandatangan ?? '-';
        $tanggal = $pembayaran->tanggal ? Carbon::parse($pembayaran->tanggal)->translatedFormat('d F Y') : '-';
        $jenisPembayaran = $pembayaran->jenisPembayaran->nama_jenis ?? 'Cicilan Pribadi';
        $metodeBayar = $pembayaran->metode_bayar ?? 'CASH';

        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetTitle('Kwitansi');
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        $pdf->SetMargins(20, 0, 0);    // Mengatur margin seperti dot matrix
        $pdf->SetAutoPageBreak(false); // Nonaktifkan auto page break
        $pdf->AddPage();
        $pdf->SetTextColor(0, 0, 0);

        // Tambahkan space atas seperti dot matrix
        $pdf->Ln(40);

        // Header - TANDA TERIMA dengan underline
        $pdf->SetFont('Helvetica', 'BU', 16);
        $pdf->Cell(170, 6, 'TANDA TERIMA', 0, 1, 'C');

        // Nomor kwitansi
        $pdf->SetFont('Helvetica', 'I', 12);
        $pdf->Cell(170, 6, $noKwitansi, 0, 1, 'C');

        // Detail penerima
        $pdf->SetFont('Helvetica', '', 10);
        $pdf->Cell(100, 5, 'Sudah diterima dari : ', 0, 1, 'L');

        $pdf->Cell(20, 5, '', 0, 0, 'L');
        $pdf->Cell(25, 5, 'Nama', 0, 0, 'L');
        $pdf->Cell(60, 5, ' : '.$nama, 0, 1, 'L');

        $pdf->Cell(20, 5, '', 0, 0, 'L');
        $pdf->Cell(25, 5, 'Alamat', 0, 0, 'L');
        $pdf->Cell(100, 5, ' : '.$alamat, 0, 1, 'L');

        // Jumlah uang
        $pdf->Ln(3);
        $pdf->Cell(20, 5, 'Uang sejumlah Rp. '.number_format($jumlah, 0, ',', '.').' ('.$terbilang.')', 0, 1, 'L');
        $pdf->Cell(20, 5, 'Untuk pembayaran '.$jenisPembayaran.' atas pembelian rumah di : ', 0, 1, 'L');

        // Detail properti
        $pdf->Ln(1);
        $pdf->Cell(20, 4, '', 0, 0, 'L');
        $pdf->Cell(25, 4, 'Perumahan', 0, 0, 'L');
        $pdf->Cell(60, 4, ' : '.$namaKavling, 0, 1, 'L');

        $pdf->Cell(20, 4, '', 0, 0, 'L');
        $pdf->Cell(25, 4, 'Type', 0, 0, 'L');
        $pdf->Cell(60, 4, ' : '.$tipe, 0, 1, 'L');

        $pdf->Cell(20, 4, '', 0, 0, 'L');
        $labelBlok = $lokasi->is_cluster ? 'Cluster / Nomor' : 'Blok / Nomor';
        $pdf->Cell(25, 4, $labelBlok, 0, 0, 'L');
        $pdf->Cell(60, 4, ' : '.$blokNomor, 0, 1, 'L');

        $pdf->Cell(20, 4, '', 0, 0, 'L');
        $pdf->Cell(25, 4, 'Rumah ID', 0, 0, 'L');
        $pdf->Cell(60, 4, ' : '.$rumahId, 0, 1, 'L');

        $pdf->Cell(20, 4, '', 0, 0, 'L');
        $pdf->Cell(25, 4, 'Harga Jual', 0, 0, 'L');
        $pdf->Cell(60, 4, ' : Rp. '.number_format($hargaJual, 0, ',', '.'), 0, 1, 'L');

        // Tanggal dan keterangan
        $pdf->SetFont('Helvetica', 'B', 8.5);
        $pdf->Cell(20, 4, '', 0, 1, 'L');
        $pdf->Cell(20, 4, '', 0, 0, 'L');
        $pdf->Cell(100, 4, '', 0, 0, 'L');
        $pdf->Cell(55, 4, $kotaTtd.', '.$tanggal, 0, 1, 'C');

        $pdf->Ln(2);

        // Footer dengan tanda tangan
        $pdf->SetFont('Helvetica', '', 8.5);
        $pdf->Cell(35, 5, 'Keterangan : ', 0, 0, 'L');
        $pdf->Cell(45, 5, 'Kasir', 0, 0, 'C');
        $pdf->Cell(45, 5, 'Penyetor', 0, 0, 'C');
        $pdf->Cell(45, 5, 'Customer Service', 0, 1, 'C');

        $pdf->Cell(35, 5, $metodeBayar, 0, 0, 'L');
        $pdf->Cell(100, 5, '', 0, 0, 'L');
        $pdf->Ln(20);

        // Garis untuk tanda tangan
        $pdf->SetLineWidth(0.2);
        $pdf->Line(60, 135, 95, 135);
        $pdf->Line(105, 135, 140, 135);
        $pdf->Line(150, 135, 185, 135);

        // Catatan kaki
        $pdf->SetFont('Helvetica', 'I', 8.5);
        $pdf->Cell(20, 5, 'NB : Kwitansi ini sah, apabila ada cap perusahaan dan tanda tangan kasir.', 0, 1, 'L');

        return response($pdf->Output('kwitansi.pdf', 'I'), 200)
            ->header('Content-Type', 'application/pdf');
    }

    public function rekapPembayaran(Request $request)
    {
        $pembayaran = JenisPembayaran::whereIn('id_jenis_pembayaran', [3, 13])->get();
        $lokasi = LokasiKavling::orderBy('id_lokasi', 'asc')->get();

        if ($request->ajax()) {
            $data = KavlingPeta::with(['customer', 'lokasi'])
                ->whereHas('lokasi', function ($q) use ($request) {
                    if ($request->lokasi_id) {
                        $q->where('id_lokasi', $request->lokasi_id);
                    }
                })
                ->when($request->status == 1, function ($q) {
                    $q->whereHas('customer');
                })
                ->orderBy('kode_kavling', 'asc')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('customer', function ($row) {
                    return optional($row->customer)->nama_lengkap ?? '';
                })
                ->addColumn('lokasi', function ($row) {
                    $namaLokasi = optional($row->lokasi)->nama_kavling ?? '-';

                    if (optional($row->lokasi)->is_cluster == 1) {
                        $kodeKavling = $row->cluster.'-'.$row->no ?? '-';
                    } else {
                        $kodeKavling = $row->kode_kavling ?? '-';
                    }

                    return '<strong>'.$namaLokasi.'</strong><br> '.$kodeKavling;
                })
                ->editColumn('hrg_jual', function ($row) {
                    return 'Rp. '.number_format($row->hrg_jual, 0, ',', '.');
                })
                ->addColumn('pembayaran', function ($row) {
                    $customerId = optional($row->customer)->id_customer;

                    if (! $customerId) {
                        return 'Rp. 0';
                    }

                    $jumlahBayar = Pembayaran::where('id_customer', $customerId)
                        ->where('keterangan', '!=', 'GANTI NAMA')
                        ->whereNotIn('id_jenis_pembayaran', [3, 13])
                        ->sum('jumlah');

                    return 'Rp. '.number_format($jumlahBayar, 0, ',', '.');
                })
                ->addColumn('pencairan', function ($row) {
                    $customerId = optional($row->customer)->id_customer;

                    if (! $customerId) {
                        return 'Rp. 0';
                    }

                    $pencairan = Pembayaran::where('id_customer', $customerId)
                        ->where('keterangan', '!=', 'GANTI NAMA')
                        ->where('id_jenis_pembayaran', 3)
                        ->sum('jumlah');

                    return 'Rp. '.number_format($pencairan, 0, ',', '.');
                })
                ->addColumn('sbum', function ($row) {
                    $customerId = optional($row->customer)->id_customer;

                    if (! $customerId) {
                        return 'Rp. 0';
                    }

                    $sbum = Pembayaran::where('id_customer', $customerId)
                        ->where('keterangan', '!=', 'GANTI NAMA')
                        ->where('id_jenis_pembayaran', 13)
                        ->sum('jumlah');

                    return 'Rp. '.number_format($sbum, 0, ',', '.');
                })
                ->addColumn('sisa', function ($row) {
                    $customerId = optional($row->customer)->id_customer;

                    if (! $customerId) {
                        return 'Rp. 0';
                    }

                    $totalTagihan = Tagihan::where('nasabah', $customerId)->sum('jumlah_tagihan');
                    $jumlahBayar = Pembayaran::where('id_customer', $customerId)
                        ->where('keterangan', '!=', 'GANTI NAMA')
                        ->sum('jumlah');

                    $sisaBayar = $totalTagihan - $jumlahBayar;

                    return 'Rp. '.number_format($sisaBayar, 0, ',', '.');
                })
                ->addColumn('action', function ($row) {
                    $customerId = optional($row->customer)->id_customer;

                    $detailUrl = $customerId
                        ? route('pembayaran.detail', $customerId)
                        : null;

                    $btn = '<div class="d-flex justify-content-center">';

                    if (! empty($customerId)) {
                        $btn .= '<button class="btn btn-primary btn-sm mx-1 bayar-button"
                        data-id="'.e($customerId).'"
                        data-toggle="modal"
                        data-target="#modalForm">
                        Bayar
                    </button>';
                    } else {
                        $btn .= '<button class="btn btn-secondary btn-sm mx-1" disabled>Bayar</button>';
                    }

                    if (! empty($customerId)) {
                        $btn .= '<a href="'.$detailUrl.'" class="btn btn-success btn-sm">Detail</a>';
                    } else {
                        $btn .= '<button class="btn btn-secondary btn-sm" disabled>Detail</button>';
                    }

                    $btn .= '</div>';

                    return $btn;
                })
                ->rawColumns(['action', 'lokasi'])
                ->make(true);
        }

        return view('admin.pembayaran.rekap', compact('pembayaran', 'lokasi'));
    }

    public function jatuhTempo(Request $request)
    {
        $pembayaran = JenisPembayaran::whereNotIn('id_jenis_pembayaran', [3, 13])->get();
        $lokasi = LokasiKavling::orderBy('id_lokasi', 'asc')->get();

        if ($request->ajax()) {

            $data = KavlingPeta::with([
                'customer',
                'lokasi',
                'customer.pembayaran' => function ($q) {
                    $q->where('keterangan', '!=', 'GANTI NAMA')
                        ->select('id_pembayaran', 'id_customer', 'jumlah', 'keterangan', 'id_jenis_pembayaran', 'tanggal');
                },
                'customer.tagihan' => function ($q) {
                    $q->select('nasabah', 'jumlah_tagihan');
                },
            ])
                ->whereHas('lokasi', function ($q) use ($request) {
                    if ($request->lokasi_id) {
                        $q->where('id_lokasi', $request->lokasi_id);
                    }
                })
                ->whereHas('customer')
                ->orderBy('kode_kavling', 'asc')
                ->get();

            $customerData = $this->precalculateCustomerData($data);

            if ($request->filled('filter')) {
                $data = $data->filter(function ($row) use ($customerData, $request) {
                    $customerId = optional($row->customer)->id_customer;
                    if (! $customerId) {
                        return false;
                    }

                    $keterlambatan = $customerData[$customerId]['keterlambatan'] ?? '';

                    switch ($request->filter) {
                        case 'telat':
                            return strpos($keterlambatan, 'Telat:') !== false;
                        case 'lancar':
                            return strpos($keterlambatan, 'Lancar') !== false;
                        case 'lunas':
                            return strpos($keterlambatan, 'Lunas') !== false;
                        default:
                            return true;
                    }
                });
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('customer', function ($row) {
                    return optional($row->customer)->nama_lengkap ?? '';
                })
                ->addColumn('lokasi', function ($row) {
                    $namaLokasi = optional($row->lokasi)->nama_kavling ?? '-';

                    if (optional($row->lokasi)->is_cluster == 1) {
                        $kodeKavling = $row->cluster.'-'.$row->no ?? '-';
                    } else {
                        $kodeKavling = $row->kode_kavling ?? '-';
                    }

                    return '<strong>'.$namaLokasi.'</strong><br> '.$kodeKavling;
                })
                ->editColumn('hrg_jual', function ($row) {
                    return 'Rp. '.number_format($row->hrg_jual, 0, ',', '.');
                })
                ->addColumn('pembayaran', function ($row) use ($customerData) {
                    $customerId = optional($row->customer)->id_customer;
                    if (! $customerId) {
                        return 'Rp. 0';
                    }

                    $jumlahBayar = $customerData[$customerId]['pembayaran'] ?? 0;

                    return 'Rp. '.number_format($jumlahBayar, 0, ',', '.');
                })
                ->addColumn('pencairan', function ($row) use ($customerData) {
                    $customerId = optional($row->customer)->id_customer;
                    if (! $customerId) {
                        return 'Rp. 0';
                    }

                    $pencairan = $customerData[$customerId]['pencairan'] ?? 0;

                    return 'Rp. '.number_format($pencairan, 0, ',', '.');
                })
                ->addColumn('sbum', function ($row) use ($customerData) {
                    $customerId = optional($row->customer)->id_customer;
                    if (! $customerId) {
                        return 'Rp. 0';
                    }

                    $sbum = $customerData[$customerId]['sbum'] ?? 0;

                    return 'Rp. '.number_format($sbum, 0, ',', '.');
                })
                ->addColumn('sisa', function ($row) use ($customerData) {
                    $customerId = optional($row->customer)->id_customer;
                    if (! $customerId) {
                        return 'Rp. 0';
                    }

                    $sisaBayar = $customerData[$customerId]['sisa'] ?? 0;

                    if ($sisaBayar <= 0) {
                        return '<span class="badge badge-success">Lunas</span>';
                    }

                    return 'Rp. '.number_format($sisaBayar, 0, ',', '.');
                })
                ->addColumn('keterlambatan', function ($row) use ($customerData) {
                    $customerId = optional($row->customer)->id_customer;
                    if (! $customerId) {
                        return '-';
                    }

                    return $customerData[$customerId]['keterlambatan'] ?? '-';
                })
                ->addColumn('action', function ($row) {
                    $customerId = optional($row->customer)->id_customer;

                    $detailUrl = $customerId
                        ? route('pembayaran.detail', $customerId)
                        : null;

                    $btn = '<div class="d-flex justify-content-center">';

                    if (! empty($customerId)) {
                        $btn .= '<button class="btn btn-info btn-sm mx-1 edit-button"
                        data-id="'.e($customerId).'"
                        data-toggle="modal"
                        data-target="#modalTempo">
                        Edit
                    </button>';
                    }

                    if (! empty($customerId)) {
                        $btn .= '<button class="btn btn-primary btn-sm mx-1 bayar-button"
                        data-id="'.e($customerId).'"
                        data-toggle="modal"
                        data-target="#modalForm">
                        Bayar
                    </button>';
                    } else {
                        $btn .= '<button class="btn btn-secondary btn-sm mx-1" disabled>Bayar</button>';
                    }

                    if (! empty($customerId)) {
                        $btn .= '<a href="'.$detailUrl.'" class="btn btn-success btn-sm">Detail</a>';
                    } else {
                        $btn .= '<button class="btn btn-secondary btn-sm" disabled>Detail</button>';
                    }

                    $btn .= '</div>';

                    return $btn;
                })
                ->rawColumns(['action', 'sisa', 'lokasi', 'keterlambatan'])
                ->make(true);
        }

        return view('admin.pembayaran.jatuhTempo', compact('pembayaran', 'lokasi'));
    }

    private function precalculateCustomerData($data)
    {
        $customerData = [];
        $hariIni = Carbon::today();

        foreach ($data as $row) {
            $customer = $row->customer;
            if (! $customer) {
                continue;
            }

            $customerId = $customer->id_customer;

            // Hitung total tagihan
            $totalTagihan = $customer->tagihan->sum('jumlah_tagihan');

            // Hitung pembayaran
            $pembayaranValid = $customer->pembayaran->whereNotIn('id_jenis_pembayaran', [3, 10, 11, 12, 13])
                ->where('keterangan', '!=', 'GANTI NAMA');

            $jumlahBayar = $customer->pembayaran->where('keterangan', '!=', 'GANTI NAMA')->sum('jumlah');
            $pembayaran = $pembayaranValid->sum('jumlah');
            $pencairan = $customer->pembayaran->where('id_jenis_pembayaran', 3)
                ->where('keterangan', '!=', 'GANTI NAMA')
                ->sum('jumlah');
            $sbum = $customer->pembayaran->where('id_jenis_pembayaran', 13)
                ->where('keterangan', '!=', 'GANTI NAMA')
                ->sum('jumlah');

            $sisaBayar = $totalTagihan - $jumlahBayar;

            // Hitung keterlambatan
            $keterlambatan = $this->calculateKeterlambatan($customer, $sisaBayar, $hariIni);

            $customerData[$customerId] = [
                'pembayaran' => $pembayaran,
                'pencairan' => $pencairan,
                'sbum' => $sbum,
                'sisa' => $sisaBayar,
                'keterlambatan' => $keterlambatan,
            ];
        }

        return $customerData;
    }

    private function calculateKeterlambatan($customer, $sisaBayar, $hariIni)
    {
        Carbon::setLocale('id');

        if ($sisaBayar <= 0) {
            return '<span class="badge badge-success">Lunas</span>';
        }

        // Validasi data inhouse
        if (
            is_null($customer->inhouse_jatuh_tempo) ||
            is_null($customer->inhouse_perbulan) ||
            is_null($customer->inhouse_tenor) ||
            $customer->inhouse_jatuh_tempo === '0000-00-00' ||
            $customer->inhouse_jatuh_tempo === '' ||
            trim($customer->inhouse_jatuh_tempo) === '' ||
            $customer->inhouse_perbulan <= 0 ||
            $customer->inhouse_tenor <= 0
        ) {
            return '<span class="badge badge-secondary">Belum di set</span>';
        }

        // Validasi format tanggal
        try {
            $jatuhTempoAwal = Carbon::parse($customer->inhouse_jatuh_tempo)->startOfDay();
            if ($jatuhTempoAwal->year < 1900) {
                return '<span class="badge badge-secondary">Tanggal tidak valid</span>';
            }
        } catch (\Exception $e) {
            return '<span class="badge badge-secondary">Format tanggal salah</span>';
        }

        $hariIni = $hariIni->copy()->startOfDay();

        // Ambil pembayaran valid dari relasi yang sudah di-load
        $pembayaranList = $customer->pembayaran
            ->whereNotIn('keterangan', ['GANTI NAMA', 'BOOKING FEE'])
            ->whereNotIn('id_jenis_pembayaran', [3, 10, 13])
            ->filter(function ($p) use ($jatuhTempoAwal) {
                return Carbon::parse($p->tanggal)->gte($jatuhTempoAwal);
            })
            ->sortBy('tanggal');

        $totalPembayaran = $pembayaranList->sum('jumlah');

        $angsuranPerBulan = (float) $customer->inhouse_perbulan;
        $tenor = (int) $customer->inhouse_tenor;

        if ($tenor > 60) {
            $tenor = 60;
        }

        // Generate jadwal jatuh tempo
        $jadwalJatuhTempo = [];
        for ($i = 0; $i < $tenor; $i++) {
            $jadwalJatuhTempo[] = [
                'bulan_ke' => $i + 1,
                'tanggal' => $jatuhTempoAwal->copy()->addMonths($i)->startOfDay(),
                'nominal' => $angsuranPerBulan,
                'terbayar' => 0,
                'sisa' => $angsuranPerBulan,
            ];
        }

        // Tutup hutang FIFO
        $sisaPembayaran = $totalPembayaran;
        foreach ($jadwalJatuhTempo as &$jadwal) {
            if ($sisaPembayaran <= 0) {
                break;
            }

            if ($sisaPembayaran >= $jadwal['nominal']) {
                $jadwal['terbayar'] = $jadwal['nominal'];
                $jadwal['sisa'] = 0;
                $sisaPembayaran -= $jadwal['nominal'];
            } else {
                $jadwal['terbayar'] = $sisaPembayaran;
                $jadwal['sisa'] = $jadwal['nominal'] - $sisaPembayaran;
                $sisaPembayaran = 0;
            }
        }
        unset($jadwal); // Penting: putus reference agar tidak corrupt array di loop berikutnya

        // Hitung keterlambatan
        $jumlahTelat = 0;
        $totalNominalTelat = 0;

        foreach ($jadwalJatuhTempo as $jadwal) {
            if ($jadwal['sisa'] > 0 && $hariIni->gte($jadwal['tanggal'])) {
                $jumlahTelat++;
                $totalNominalTelat += $jadwal['sisa'];
            }
        }

        // Format tampilan tanggal jatuh tempo dan tenor
        $infoInhouse = '
        <div class="small text-muted mt-1">
            <strong>Jatuh Tempo Awal:</strong> '.$jatuhTempoAwal->translatedFormat('d F Y').'<br>
            <strong>Tenor:</strong> '.$tenor.' bulan
        </div>
    ';

        if ($jumlahTelat > 0) {
            return '
            <div class="text-danger">
                <strong>Telat: '.$jumlahTelat.'x</strong><br>
                <small>Rp. '.number_format($totalNominalTelat, 0, ',', '.').'</small>
            </div>
            '.$infoInhouse;
        } else {
            return '
            <div class="text-success">
                <span class="badge badge-success">Lancar</span>
            </div>
            '.$infoInhouse;
        }

    }

    public function editJatuhTempo(Request $request, $id)
    {
        $customer = Customer::find($id);

        if (! $customer) {
            return response()->json(['success' => false, 'message' => 'Customer tidak ditemukan'], 404);
        }

        DB::BeginTransaction();

        try {
            $request->merge([
                'inhouse_perbulan' => str_replace('.', '', $request->inhouse_perbulan),
            ]);

            $request->validate(
                [
                    'inhouse_perbulan' => 'required',
                    'inhouse_tenor' => 'required',
                    'inhouse_jatuh_tempo' => 'required|date',
                ],
                [
                    'inhouse_perbulan.required' => 'Inhouse perbulan harus diisi.',
                    'inhouse_tenor.required' => 'Inhouse tenor harus diisi.',
                    'inhouse_jatuh_tempo.required' => 'Inhouse jatuh tempo harus diisi.',
                    'inhouse_jatuh_tempo.date' => 'Format tanggal jatuh tempo tidak valid.',
                ]
            );

            $customer->update([
                'inhouse_perbulan' => $request->inhouse_perbulan,
                'inhouse_tenor' => $request->inhouse_tenor,
                'inhouse_jatuh_tempo' => $request->inhouse_jatuh_tempo,
            ]);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Data customer berhasil diubah'], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat mengubah data customer: '.$e->getMessage()], 500);
        }
    }
}
