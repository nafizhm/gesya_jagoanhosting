<?php

namespace App\Http\Controllers\Siteplan;

use App\Http\Controllers\Controller;
use App\Models\KavlingPeta;
use App\Models\LokasiKavling;
use App\Models\ProgresUnitReady;
use App\Models\MasterSVG;
use App\Models\PengaturanProfil;

class SiteplanUnitReadyController extends Controller
{
    public function index()
    {
        $lokasiKavling = LokasiKavling::orderBy('urutan', 'asc')->get();

        $legend = ProgresUnitReady::orderBy('id', 'asc')
            ->get();

        return view('admin.siteplan.st-unit-ready.index', compact('lokasiKavling', 'legend'));
    }

    public function show($id)
    {
        $data = KavlingPeta::with(['lokasi', 'customer'])->findOrFail($id);

        return response()->json([
            'status'          => 'success',
            'data'            => $data,
        ]);
    }

    public function cetakJPG($id_lokasi)
    {
        return $this->halamanCetak($id_lokasi, 'jpg');
    }

    private function halamanCetak($id_lokasi, $format)
    {
        $lokasi = LokasiKavling::where('id_lokasi', $id_lokasi)->firstOrFail();

        return response()->view('admin.siteplan.cetak', [
            'data_cetak' => [
                'svg' => $this->generateSVG($id_lokasi),
                'format' => $format,
                'nama_lokasi' => $lokasi->nama_kavling,
                'nama_perusahaan' => PengaturanProfil::value('nama_perusahaan') ?? '',
                'periode' => now()->translatedFormat('d F Y'),
                'nama_file' => 'siteplan_unit_ready_' . $id_lokasi,
            ],
        ])->header('Cache-Control', 'no-store, private');
    }

    private function generateSVG($id_lokasi, $width = 500, $height = 200)
    {
        $svg = MasterSVG::query()->where('id_lokasi', $id_lokasi)->first();
        if (! $svg) {
            abort(404, "Data master_svg tidak ditemukan");
        }

        ob_start();

        echo $svg->header_xml;
        $header = str_replace(['[[lebar]]', '[[tinggi]]'], [$width, $height], $svg->header_svg);
        echo $header;

        $kavlings = KavlingPeta::query()->from('kavling_peta as k')
            ->leftJoin('progres_unit_ready as u', 'k.status_ready', '=', 'u.id')
            ->where('k.id_lokasi', $id_lokasi)
            ->select('k.*', 'u.warna')
            ->get();

        foreach ($kavlings as $pt) {
            $warna = '#ffffff';
            if ($pt->status_ready) {
                $warna = $pt->warna;
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
        return $this->halamanCetak($id_lokasi, 'pdf');
    }
}
