<?php

namespace App\Http\Controllers\Siteplan;

use App\Http\Controllers\Controller;
use App\Models\KavlingPeta;
use App\Models\LokasiKavling;
use App\Models\ProgresListPembangunan;
use App\Models\ProyekBangunanUnit;
use Illuminate\Support\Facades\DB;
use App\Models\MasterSVG;
use App\Models\PengaturanProfil;

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
                'nama_file' => 'siteplan_proyek_' . $id_lokasi,
            ],
        ])->header('Cache-Control', 'no-store, private');
    }

    private function generateSVG($id_lokasi, $width = 500, $height = 200)
    {
        $svg = MasterSVG::query()->where('id_lokasi', $id_lokasi)->first();
        if (! $svg) {
            abort(404, "Data master_svg tidak ditemukan");
        }

        $legend = ProgresListPembangunan::query()->orderBy('id', 'asc')->get();

        $units = ProyekBangunanUnit::query()->from('proyek_bangunan_unit as u')
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

        $kavlings = KavlingPeta::query()->from('kavling_peta as k')
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
        return $this->halamanCetak($id_lokasi, 'pdf');
    }
}
