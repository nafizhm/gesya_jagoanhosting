<?php

namespace App\Http\Controllers\Siteplan;

use App\Http\Controllers\Controller;
use App\Models\KavlingPeta;
use App\Models\LokasiKavling;
use App\Models\ProgresListPembangunan;
use App\Models\ProyekBangunanUnit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use TCPDF;

class SiteplanProyekController extends Controller
{
    public function index()
    {
        $lokasiKavling = LokasiKavling::with(['kavlingPeta.customer', 'masterSvg'])
            ->orderBy('urutan', 'asc')
            ->get();

        $legend = ProgresListPembangunan::orderBy('id', 'asc')->get();

        $units = ProyekBangunanUnit::with(['proyekBangunan.proyekBangunanDetail'])
            ->get()
            ->groupBy('kode_kavling');

        return view('admin.siteplan.st-proyek.index', compact('lokasiKavling', 'legend', 'units'));
    }

    public function show($id)
    {
        $data = KavlingPeta::with(['lokasi', 'customer'])->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data'   => $data,
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
        $clientName = 'gesya_proyek';

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
        $svg = DB::table('master_svg')->where('id_lokasi', $id_lokasi)->first();
        if (! $svg) {
            abort(404, "Data master_svg tidak ditemukan");
        }

        $legend = DB::table('progres_list_pembangunan')->orderBy('id', 'asc')->get();

        $units = DB::table('proyek_bangunan_unit as u')
            ->join('proyek_bangunan as pb', 'u.id_proyek_bangunan', '=', 'pb.id')
            ->leftJoin('proyek_bangunan_detail as pbd', 'pb.id', '=', 'pbd.id_proyek_bangunan')
            ->select(
                'u.kode_kavling',
                'pb.id_lokasi',
                DB::raw('SUM(CASE WHEN pbd.tanggal IS NOT NULL THEN pbd.persen ELSE 0 END) as persen')
            )
            ->groupBy('u.kode_kavling', 'pb.id_lokasi')
            ->get()
            ->groupBy('kode_kavling');

        ob_start();

        echo $svg->header_xml;
        $header = str_replace(['[[lebar]]', '[[tinggi]]'], [$width, $height], $svg->header_svg);
        echo $header;

        $kavlings = DB::table('kavling_peta as k')
            ->leftJoin('nasabah as n', 'k.id_kavling', '=', 'n.id_kavling')
            ->leftJoin('progres_list_pembangunan as p', 'n.id_status_progres', '=', 'p.id')
            ->where('k.id_lokasi', $id_lokasi)
            ->select('k.*', 'n.id_customer', 'n.id_status_progres', 'p.warna as warna_progres')
            ->get();

        foreach ($kavlings as $pt) {
            $warna = $legend->firstWhere('id', 1)->warna;

            if (isset($units[$pt->kode_kavling])) {
                $unit = $units[$pt->kode_kavling]->first();
                if ($unit && $unit->id_lokasi == $id_lokasi) {
                    $persen = $unit->persen ?? 0;
                    if ($persen > 0) {
                        if ($persen < 30) {
                            $warna = $legend->firstWhere('id', 2)->warna;
                        } elseif ($persen < 60) {
                            $warna = $legend->firstWhere('id', 3)->warna;
                        } elseif ($persen < 82) {
                            $warna = $legend->firstWhere('id', 4)->warna;
                        } elseif ($persen == 100) {
                            $warna = $legend->firstWhere('id', 5)->warna;
                        }
                    }
                }
            }

            if ($pt->id_status_progres == 5) {
                $warna = $legend->firstWhere('id', 6)->warna;
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
        $clientName = 'gesya_proyek';
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
