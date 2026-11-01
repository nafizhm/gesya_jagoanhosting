<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Pengaturan\HakAksesController;
use App\Models\KavlingPeta;
use App\Models\LokasiKavling;
use App\Models\Perusahaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Yajra\DataTables\Facades\DataTables;

class LokasiKavlingController extends Controller
{
    public function index(Request $request)
    {
        $permissions = HakAksesController::getUserPermissions();

        if ($request->ajax()) {
            $data = LokasiKavling::with('perusahaan')->withCount('kavlingPeta');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('jumlah_kavling', function ($row) {
                    return $row->kavling_peta_count;
                })
                ->editColumn('nama_kavling', function ($row) {
                    $field = '<strong>' . $row->nama_kavling . '</strong>';
                    foreach ($row->perusahaan as $p) {
                        $field .= '<br><span>' . $p->nama_perusahaan . '</span>';
                    }
                    return $field;
                })
                ->addColumn('detail', function ($row) {
                    return '<a href="' . route('lokasi-kavling.detail', $row->id_lokasi) . '" class="btn btn-sm btn-warning text-white">Detail Kavling</a>';
                })
                ->addColumn('action', function ($row) use ($permissions) {
                    $showUrl = route('lokasi-kavling.show', $row->id_lokasi);

                    $btn = '<div class="d-flex justify-content-center">';
                    if ($permissions['edit']) {
                        $btn .= '<button class="btn btn-primary btn-sm edit-button" data-id="' . e($row->id_lokasi) . '" data-url="' . e($showUrl) . '">Edit</button>';
                    }
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['nama_kavling', 'jumlah_kavling', 'detail', 'action'])
                ->make(true);
        }

        $perusahaanList = Perusahaan::orderBy('nama_perusahaan', 'asc')->get();

        return view('admin.master.lokasi_kavling.index', compact('permissions', 'perusahaanList'));
    }

    public function show($id)
    {
        $data = LokasiKavling::with('perusahaan')->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data'   => $data,
        ]);
    }

    public function update(Request $request, $id)
    {
        $data = LokasiKavling::findOrFail($id);

        $rules = [
            'nama_kavling'    => 'required|string|max:100',
            'nama_singkat'    => 'required|string|max:15',
            'alamat'          => 'required|string|max:255',
            'urutan'          => 'required|integer',
            'header'          => 'required|string',
            'id_perusahaan.*' => 'exists:perusahaan,id_perusahaan',
            'stt_tampil'      => 'required',
            'is_cluster'      => 'required|in:0,1',
        ];

        $messages = [
            'nama_kavling.required'   => 'Nama kavling wajib diisi.',
            'nama_kavling.max'        => 'Nama kavling maksimal 100 karakter.',
            'nama_singkat.required'   => 'Nama singkat wajib diisi.',
            'nama_singkat.max'        => 'Nama singkat maksimal 15 karakter.',
            'alamat.required'         => 'Alamat wajib diisi.',
            'alamat.max'              => 'Alamat maksimal 255 karakter.',
            'urutan.required'         => 'Urutan wajib diisi.',
            'urutan.integer'          => 'Urutan harus berupa angka.',
            'header.required'         => 'Header wajib diisi.',
            'id_perusahaan.required'  => 'Perusahaan wajib dipilih.',
            'id_perusahaan.*.exists'  => 'Perusahaan tidak ditemukan.',
            'stt_tampil.required'     => 'Status tampil wajib dipilih.',
            'is_cluster.required'     => 'Cluster wajib dipilih.',
            'is_cluster.in'           => 'Cluster harus bernilai ya atau tidak.',
        ];

        $request->validate($rules, $messages);

        DB::beginTransaction();
        try {
            $data->update([
                'nama_kavling'  => $request->nama_kavling,
                'nama_singkat'  => $request->nama_singkat,
                'alamat'        => $request->alamat,
                'urutan'        => $request->urutan,
                'header'        => $request->header,
                'stt_tampil'    => $request->stt_tampil,
                'is_cluster'    => $request->is_cluster,
            ]);

            $data->perusahaan()->sync($request->id_perusahaan);

            DB::commit();

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal mengupdate lokasi kavling: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'error'  => $e->getMessage(),
            ], 500);
        }
    }

    public function detail($id)
    {
        $permissions = HakAksesController::getUserPermissions();

        $lokasi = LokasiKavling::where('id_lokasi', $id)->first();
        $is_cluster = $lokasi->is_cluster;
        $nama_kavling = $lokasi->nama_kavling;

        if (request()->ajax()) {
            $kavlings = KavlingPeta::where('id_lokasi', $lokasi->id_lokasi)->get();

            return DataTables::of($kavlings)
                ->addIndexColumn()
                ->addColumn('cluster', function ($kavling) use ($is_cluster) {
                    if ($is_cluster == 1) {
                        return $kavling->cluster ?? '';
                    }
                    return '';
                })
                ->editColumn('hrg_jual', function ($kavling) {
                    return 'Rp. ' . number_format($kavling->hrg_jual, 0, ',', '.');
                })
                ->editColumn('status', function ($kavling) {
                    if ($kavling->status == 1) {
                        return '<span class="badge badge-success text-sm">Ada</span>';
                    } else {
                        return '<span class="badge badge-secondary text-sm">Kosong</span>';
                    }
                })
                ->rawColumns(['hrg_jual', 'status'])
                ->make(true);
        }

        return view('admin.master.lokasi_kavling.detail', compact('permissions', 'id', 'nama_kavling', 'is_cluster'));
    }

    public function exportDetail($id)
    {
        $lokasi = LokasiKavling::findOrFail($id);
        $kavlings = KavlingPeta::where('id_lokasi', $id)->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Kode Kavling');
        $sheet->setCellValue('C1', 'Panjang Kanan');
        $sheet->setCellValue('D1', 'Panjang Kiri');
        $sheet->setCellValue('E1', 'Lebar Depan');
        $sheet->setCellValue('F1', 'Lebar Belakang');
        $sheet->setCellValue('G1', 'Luas Tanah');
        $sheet->setCellValue('H1', 'Harga Jual');
        $sheet->setCellValue('I1', 'Keterangan');

        $row = 2;
        foreach ($kavlings as $index => $kavling) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $kavling->kode_kavling);
            $sheet->setCellValue('C' . $row, $kavling->panjang_kanan);
            $sheet->setCellValue('D' . $row, $kavling->panjang_kiri);
            $sheet->setCellValue('E' . $row, $kavling->lebar_depan);
            $sheet->setCellValue('F' . $row, $kavling->lebar_belakang);
            $sheet->setCellValue('G' . $row, $kavling->luas_tanah);
            $sheet->setCellValue('H' . $row, $kavling->hrg_jual);
            $sheet->setCellValue('I' . $row, $kavling->keterangan);
            $row++;
        }

        $lastRow = $row - 1;
        $sheet->getStyle('A1:I' . $lastRow)
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        $writer = new Xlsx($spreadsheet);
        $filename = 'Data_Kavling_' . $lokasi->nama_kavling . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
