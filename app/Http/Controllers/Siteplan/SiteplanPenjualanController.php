<?php

namespace App\Http\Controllers\Siteplan;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\FotoKavling;
use App\Models\KavlingPeta;
use App\Models\ListrikAir;
use App\Models\LokasiKavling;
use App\Models\MasterSVG;
use App\Models\Pembayaran;
use App\Models\PengaturanMedia;
use App\Models\Pengeluaran;
use App\Models\ProgresListPenjualan;
use App\Models\Registrasi;
use App\Models\Tagihan;
use App\Models\UploadFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use TCPDF;

class SiteplanPenjualanController extends Controller
{
    public function index()
    {
        $lokasiKavling = LokasiKavling::whereIn('stt_tampil', [1, 3])
            ->with(['masterSvg', 'kavlingPeta.customer.progres'])
            ->orderBy('urutan', 'asc')
            ->get();

        $idKavlingHoldAktif = Registrasi::whereIn('id_lokasi', $lokasiKavling->pluck('id_lokasi'))
            ->where('stt_reg', 1)
            ->pluck('id_kavling')
            ->flip();

        foreach ($lokasiKavling as $lokasi) {
            foreach ($lokasi->kavlingPeta as $kavling) {
                $kavling->memiliki_hold_aktif = $idKavlingHoldAktif->has($kavling->id_kavling);
            }
        }

        $legend = ProgresListPenjualan::whereNotNull('warna')
            ->where('warna', '!=', '')
            ->where('stt_tampil', '!=', 0)
            ->orderBy('urutan', 'asc')
            ->get();

        return view('admin.siteplan.st-penjualan.index', compact('lokasiKavling', 'legend'));
    }

    public function show($id)
    {
        $kavling = KavlingPeta::with('lokasi')->where('id_kavling', $id)->first();
        $customer = Customer::where('id_kavling', $id)->first();

        $fileUrls = [];
        $files = collect([]);

        if ($customer) {
            $files = UploadFile::where('nasabah', $customer->id_customer)->get();

            foreach ($files as $file) {
                $fileType = str_replace(' ', '_', strtolower($file->nama_file));
                $fileUrls[$fileType] = $file->lampiran ?
                    url('berkas_user', $file->lampiran) : null;
            }
        }

        $listrikAir = ListrikAir::where('id_kavling', $id)->first();

        if ($listrikAir) {
            $listrikAir->foto_listrik_url = $listrikAir->foto_listrik ?
                route('listrik-air.foto-listrik', $listrikAir->foto_listrik) : null;

            $listrikAir->foto_listrik_2_url = $listrikAir->foto_listrik_2 ?
                route('listrik-air.foto-listrik-2', $listrikAir->foto_listrik_2) : null;

            $listrikAir->foto_air_url = $listrikAir->foto_air ?
                route('listrik-air.foto-air', $listrikAir->foto_air) : null;

            $listrikAir->foto_air_2_url = $listrikAir->foto_air_2 ?
                route('listrik-air.foto-air-2', $listrikAir->foto_air_2) : null;
        }

        return response()->json([
            'status'     => 'success',
            'kavling'    => $kavling,
            'customer'   => $customer,
            'files'      => $fileUrls,
            'listrikAir' => $listrikAir,
            'lokasi'     => $kavling && $kavling->lokasi ? $kavling->lokasi->nama_kavling : '-',
        ]);
    }

    public function cetak(Request $request)
    {
        $kavling = KavlingPeta::with('lokasi')
            ->where('kode_kavling', $request->kode_kavling)
            ->first();

        $namaLokasi = $kavling && $kavling->lokasi
            ? $kavling->lokasi->nama_kavling
            : '-';

        $cust = Customer::with('marketing')
            ->where('id_kavling', $kavling->id_kavling)
            ->where('no_ktp', $request->no_ktp)
            ->first();

        $listrikAir = ListrikAir::where('id_kavling', $kavling->id_kavling)->first();

        $kavlingData = [
            'status'        => $request->status ?? 'Terjual',
            'marketing'     => $cust->marketing->nama_marketing ?? '-',
            'lokasi'        => $namaLokasi,
            'blok'          => $request->blok ?? $request->kode_kavling ?? '-',
            'luas_tanah'    => $request->luas_tanah ?? '-',
            'luas_bangunan' => $request->luas_bangunan ?? '-',
            'daya_listrik'  => $request->daya_listrik ?? '-',
            'harga'         => $request->harga ? (int) $request->harga : 0,
            'no_sertifikat' => $request->no_sertifikat ?? '-',
            'foto'          => null,
        ];

        $customerData = [
            'nama'          => $request->nama_customer ?? '-',
            'no_ktp'        => $request->no_ktp ?? '-',
            'tempat_lahir'  => $request->tempat_lahir ?? '-',
            'tanggal_lahir' => $request->tgl_lahir ?? '-',
            'alamat'        => $request->alamat ?? '-',
            'no_hp'         => $request->no_hp ?? '-',
            'pekerjaan'     => $request->pekerjaan ?? '-',
        ];

        $listrikAirData = [
            'daya_listrik'   => $kavling->daya_listrik ?? '-',
            'no_sertifikat'  => $kavling->no_sertifikat ?? '-',
            'no_rek_listrik' => $listrikAir->norek_listrik ?? '-',
            'no_rek_air'     => $listrikAir->norek_air ?? '-',
        ];

        $pengaturanMedia = PengaturanMedia::where('jenis_data', 'kop surat')->first();
        $logoPath        = null;
        if ($pengaturanMedia && $pengaturanMedia->nama_file) {
            $logoPath = public_path('config_media/' . $pengaturanMedia->nama_file);
        }

        $pdf = new \TCPDF('P', 'mm', 'A4');
        $pdf->SetCreator('Sistem Kavling');
        $pdf->SetAuthor('Sistem Kavling');
        $pdf->SetTitle('Data Unit');
        $pdf->SetMargins(15, 45, 15);
        $pdf->AddPage();

        $tanggalCetak = \Carbon\Carbon::now()->translatedFormat('d F Y');

        if ($logoPath && file_exists($logoPath)) {
            $pdf->Image($logoPath, 0, 5, 210);
        }

        $pdf->SetY(35);

        $pdf->SetFont('times', '', 10);
        $pdf->Cell(0, 8, 'Tanggal Cetak : ' . $tanggalCetak, 0, 1, 'R');
        $pdf->Ln(3);

        $pdf->SetTextColor(255, 0, 0);
        $pdf->SetFont('times', 'BU', 14);
        $pdf->Cell(0, 4, 'DATA UNIT RUMAH', 0, 1, 'C');
        $pdf->SetTextColor(0, 0, 0);

        $pdf->SetFont('times', '', 8);
        $pdf->Cell(0, 2, 'Status Rumah : ' . $kavlingData['status'], 0, 1, 'C');
        $pdf->Cell(0, 2, 'Marketing : ' . $kavlingData['marketing'], 0, 1, 'C');
        $pdf->Ln(3);

        $pdf->SetFont('times', 'BU', 12);
        $pdf->Cell(0, 8, 'DATA UNIT RUMAH', 0, 1, 'L');
        $pdf->SetFont('times', '', 10);
        $this->rowData($pdf, 'Lokasi Perumahan', $namaLokasi);
        $this->rowData($pdf, 'No. Blok / Kav', $kavlingData['blok']);
        $this->rowData($pdf, 'Luas Tanah', $kavlingData['luas_tanah']);
        $this->rowData($pdf, 'Luas / Tipe Bangunan', $kavlingData['luas_bangunan']);
        $this->rowData($pdf, 'Daya Listrik', $listrikAirData['daya_listrik']);
        $this->rowData($pdf, 'Harga Jual', 'Rp ' . number_format($kavlingData['harga'], 0, ',', '.'));
        $pdf->Ln(2);

        $pdf->SetFont('times', 'BU', 12);
        $pdf->Cell(0, 8, 'DATA LEGAL', 0, 1, 'L');
        $pdf->SetFont('times', '', 10);
        $this->rowData($pdf, 'ID Rumah', $kavling->id_rumah_sikumbang ?? '-');
        $this->rowData($pdf, 'No. Sertipikat', $listrikAirData['no_sertifikat']);
        $this->rowData($pdf, 'No. Rekening Listrik', $listrikAirData['no_rek_listrik']);
        $this->rowData($pdf, 'No. Rekening Air', $listrikAirData['no_rek_air']);
        $pdf->Ln(2);

        $pdf->SetFont('times', 'BU', 12);
        $pdf->Cell(0, 8, 'DATA CUSTOMER', 0, 1, 'L');
        $pdf->SetFont('times', '', 10);
        $this->rowData($pdf, 'Nama Lengkap', $customerData['nama']);
        $this->rowData($pdf, 'No. KTP', $customerData['no_ktp']);
        $this->rowData($pdf, 'Tempat / Tgl Lahir', ($customerData['tempat_lahir'] ?? '-') . ', ' . ($customerData['tanggal_lahir'] ?? '-'));
        $this->rowData($pdf, 'Alamat KTP', $customerData['alamat']);
        $this->rowData($pdf, 'No. Telp / WA', $customerData['no_hp']);
        $this->rowData($pdf, 'Pekerjaan', $customerData['pekerjaan']);
        $pdf->Ln(2);

        $pdf->SetFont('times', 'BU', 12);
        $pdf->Cell(0, 8, 'LAMPIRAN FOTO DATA UNIT RUMAH', 0, 1, 'L');
        $pdf->SetFont('times', 'I', 10);
        $pdf->Cell(0, 10, 'Foto tidak tersedia', 0, 1, 'L');

        $pdf->Output('Data_Unit_' . ($kavlingData['blok'] ?? 'unknown') . '.pdf', 'I');
    }

    private function rowData($pdf, $label, $value)
    {
        $pdf->SetFont('times', 'B', 10);
        $pdf->Cell(60, 7, $label, 0, 0, 'L');
        $pdf->SetFont('times', '', 10);
        $pdf->MultiCell(0, 7, ': ' . $value, 0, 'L', false, 1);
    }

    public function foto($id)
    {
        $fotoKavling = FotoKavling::where('id_kavling', $id)->get();

        $html = '';

        if ($fotoKavling->count() > 0) {
            foreach ($fotoKavling as $foto) {
                $html .= '<div class="col-md-3 mb-3 text-center">';
                $html .= '<img src="' . asset($foto->lampiran) . '" class="img-fluid mb-2" alt="Foto" style="max-height:200px; object-fit:cover; border:1px solid #ddd; padding:5px;">';
                $html .= '<div><strong>' . $foto->keterangan . '</strong></div>';
                $html .= '</div>';
            }
        } else {
            $html .= '<p>Tidak ada foto untuk kavling ini.</p>';
        }

        return response()->json([
            'status' => 'success',
            'html'   => $html,
        ]);
    }

    public function tagihan($id_kavling)
    {
        $nasabahIds = DB::table('nasabah')
            ->where('id_kavling', $id_kavling)
            ->pluck('id_customer');

        if ($nasabahIds->isEmpty()) {
            return response()->json([
                'status'      => 'success',
                'tagihan'     => [],
                'pembayaran'  => [],
                'pengeluaran' => [],
                'message'     => 'Tidak ada nasabah untuk kavling ini',
            ]);
        }

        $tagihan = Tagihan::whereIn('nasabah', $nasabahIds)->get();

        $pembayaran = Pembayaran::with('jenisPembayaran')
            ->whereIn('id_customer', $nasabahIds)
            ->get();

        $pengeluaran = Pengeluaran::with('jenisPembayaran')
            ->whereIn('id_customer', $nasabahIds)
            ->get();

        return response()->json([
            'status'      => 'success',
            'tagihan'     => $tagihan,
            'pembayaran'  => $pembayaran,
            'pengeluaran' => $pengeluaran,
        ]);
    }

    public function cetakJPG($id_lokasi)
    {
        $svgContent = $this->generateSVG($id_lokasi);

        $svgFilename = "siteplan_{$id_lokasi}.svg";

        $svgPath = public_path("svg/{$svgFilename}");
        if (! file_exists(dirname($svgPath))) {
            mkdir(dirname($svgPath), 0755, true);
        }
        file_put_contents($svgPath, $svgContent);

        $endpoint   = 'https://aplikasikavling.com/convert/proses.php';;
        $clientName = 'gesya_marketing';

        $response = Http::attach(
            'svg_file',
            file_get_contents($svgPath),
            $svgFilename
        )->post($endpoint, [
            'client' => $clientName,
        ]);

        if (! $response->successful()) {
            abort(500, "Gagal upload ke server convert: " . $response->body());
        }

        $data = $response->json();
        if (! isset($data['jpg_url'])) {
            abort(500, "Gagal convert ke JPG: " . json_encode($data));
        }

        $jpgUrl = $data['jpg_url'];

        $jpgContent  = Http::get($jpgUrl)->body();
        $jpgFilename = basename(parse_url($jpgUrl, PHP_URL_PATH));

        $jpgPath = public_path("hasil/{$jpgFilename}");
        if (! file_exists(dirname($jpgPath))) {
            mkdir(dirname($jpgPath), 0755, true);
        }
        file_put_contents($jpgPath, $jpgContent);

        return response()->download($jpgPath);
    }

    private function generateSVG($id_lokasi, $width = 500, $height = 200)
    {
        $svg = MasterSVG::where('id_lokasi', $id_lokasi)->first();
        if (! $svg) {
            abort(404, "Data master_svg tidak ditemukan");
        }

        ob_start();

        echo $svg->header_xml;

        $header = str_replace(['[[lebar]]', '[[tinggi]]'], [$width, $height], $svg->header_svg);
        echo $header;

        $kavlings = KavlingPeta::with('customer.progres')
            ->where('id_lokasi', $id_lokasi)
            ->get();

        $idKavlingHoldAktif = Registrasi::whereIn('id_kavling', $kavlings->pluck('id_kavling'))
            ->where('stt_reg', 1)
            ->pluck('id_kavling')
            ->flip();

        foreach ($kavlings as $pt) {
            $warna = '#ffffff';

            if ($pt->customer && $pt->customer->progres) {
                $warna = $pt->customer->progres->warna ?? '#ffffff';
            } elseif ($idKavlingHoldAktif->has($pt->id_kavling)) {
                $warna = '#00ffff';
            }

            $replacements = [$pt->map, $warna, $pt->matrik, $pt->kode_kavling];
            if ($pt->jenis_map === 'polygon') {
                echo str_replace(['[[1]]', '[[2]]', '[[3]]', '[[4]]'], $replacements, $svg->polygon_svg);
            } elseif ($pt->jenis_map === 'path') {
                echo str_replace(['[[1]]', '[[2]]', '[[3]]', '[[4]]'], $replacements, $svg->path_svg);
            }
        }

        echo $svg->footer_svg;

        return ob_get_clean();
    }

    public function cetakPDF($id_lokasi)
    {
        $namaPerusahaan = DB::table('konfigurasi')->value('nama_perusahaan');
        $lokasi         = DB::table('lokasi_kavling')->where('id_lokasi', $id_lokasi)->first();
        $namaKavling    = $lokasi->nama_kavling ?? '-';
        $periodeCetak   = now()->translatedFormat('d F Y');

        $svgContent  = $this->generateSVG($id_lokasi);
        $svgFilename = "siteplan_{$id_lokasi}.svg";
        $svgPath     = public_path("svg/{$svgFilename}");
        if (! file_exists(dirname($svgPath))) {
            mkdir(dirname($svgPath), 0755, true);
        }

        file_put_contents($svgPath, $svgContent);

        $endpoint   = 'https://aplikasikavling.com/convert/proses.php';;
        $clientName = 'gesya_marketing';
        $response   = Http::attach('svg_file', file_get_contents($svgPath), $svgFilename)
            ->post($endpoint, ['client' => $clientName]);
        if (! $response->successful()) {
            abort(500, "Gagal upload ke server convert: " . $response->body());
        }

        $data = $response->json();
        if (! isset($data['jpg_url'])) {
            abort(500, "Gagal convert ke JPG: " . json_encode($data));
        }

        $jpgUrl      = $data['jpg_url'];
        $jpgContent  = Http::get($jpgUrl)->body();
        $jpgFilename = basename(parse_url($jpgUrl, PHP_URL_PATH));
        $jpgPath     = public_path("hasil/{$jpgFilename}");
        if (! file_exists(dirname($jpgPath))) {
            mkdir(dirname($jpgPath), 0755, true);
        }

        file_put_contents($jpgPath, $jpgContent);

        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor($namaPerusahaan);
        $pdf->SetTitle("Site Plan - {$namaKavling}");
        $pdf->SetMargins(10, 10, 10);
        $pdf->AddPage();

        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 7, strtoupper($namaPerusahaan), 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 6, 'SITE PLAN ' . strtoupper($namaKavling), 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(0, 6, 'Periode Cetak : ' . $periodeCetak, 0, 1, 'C');

        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetLineWidth(0.7);
        $pdf->Line(10, $pdf->GetY() + 2, 200, $pdf->GetY() + 2);
        $pdf->SetLineWidth(0.3);
        $pdf->Line(10, $pdf->GetY() + 3, 200, $pdf->GetY() + 3);

        $pdf->Ln(10);

        $pdf->Image($jpgPath, 25, $pdf->GetY(), 160, 0, 'JPG');

        $pdf->Output("siteplan_{$namaKavling}.pdf", 'I');
    }
}
