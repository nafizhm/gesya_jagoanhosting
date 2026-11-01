<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;

use Carbon\Carbon;
use App\Models\Akad;
use App\Models\Customer;
use App\Models\KavlingPeta;
use Illuminate\Http\Request;
use App\Models\AkadDetail;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Pengaturan\HakAksesController;
use PhpOffice\PhpWord\TemplateProcessor;
use App\Models\PengajuanBerkasLegal;
use TCPDF;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AkadController extends Controller
{
    public function index(Request $request)
    {
        Carbon::setLocale('id');
        $permissions = HakAksesController::getUserPermissions();

        if ($request->ajax()) {
            $opd = Akad::orderBy('id_akad', 'desc');

            return DataTables::of($opd)
                ->addIndexColumn()
                ->addColumn('total_akad', function ($row) {
                    return AkadDetail::where('id_akad', $row->id_akad)->count();
                })

                ->editColumn('tgl_akad', function ($row) {
                    return Carbon::parse($row->tgl_akad)->translatedFormat('d F Y');
                })

                ->addColumn('action', function ($row) use ($permissions) {
                    $editUrl = route('akad.edit', $row->id_akad);
                    $detailUrl = route('akad.detail', $row->id_akad);
                    $btn = '<div class="d-flex align-items-center">';

                    if ($permissions['edit']) {
                        $btn .= '<button class="btn btn-primary btn-sm" data-id="' . e($row->id_akad) . '"
                            data-url="' . e($editUrl) . '" data-toggle="modal" data-target="#modalForm" id="edit-button">
                            Edit
                        </button>';
                    }

                    $btn .= '<a href="' . e($detailUrl) . '" class="btn btn-info btn-sm mx-1">Detail</a>';

                    if ($permissions['hapus']) {
                        $btn .= '<form action="' . route('akad.destroy', $row->id_akad) . '" method="POST">
                        ' . csrf_field() . method_field('DELETE') . '
                        <button type="submit" class="delete-button btn btn-danger btn-sm">Hapus</button>
                      </form>';
                    }
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.transaksi.akad.index', compact('permissions'));
    }

    public function detail($id)
    {
        $data = Akad::with('detail')->findOrFail($id);
        $selectedCustomerIds = AkadDetail::where('id_akad', $data->id_akad)->pluck('id_customer')->toArray();
        $customers = Customer::where('id_status_progres', 4)
            ->whereNotIn('id_customer', $selectedCustomerIds)
            ->orderBy('nama_lengkap')
            ->get();

        return view('admin.transaksi.akad.detail', compact('data', 'customers'));
    }

    public function getCustomerDetail($id)
    {
        $customer = Customer::with(['lokasi', 'kavling'])->find($id);
        $legal = PengajuanBerkasLegal::where('id_customer', $id)->first();

        if (!$customer) {
            return response()->json(['status' => 'error', 'message' => 'Customer tidak ditemukan'], 404);
        }

        return response()->json([
            'status' => 'success',
            'alamat' => $customer->alamat,
            'no_wa' => $customer->no_wa,
            'nama_kavling' => $customer->lokasi->nama_kavling ?? '-',
            'kode_kavling' => $customer->kavling->kode_kavling ?? '-',

            'iph' => $legal->IPH ?? 0,
            'shgb' => $legal->SHGB ?? 0,
            'pajak' => $legal->pajak ?? 0,
            'id_persyaratan' => $legal->id ?? null,
        ]);
    }

    public function detailData($id)
    {
        Carbon::setLocale('id');
        $data = AkadDetail::with(['customer.kavling', 'persyaratan'])
            ->where('id_akad', $id)
            ->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('id_customer', function ($row) {
                return $row->customer->nama_lengkap ?? '-';
            })
            ->editColumn('tgl_masuk', function ($row) {
                return Carbon::parse($row->tgl_masuk)->translatedFormat('d F Y');
            })
            ->addColumn('nama_kavling', function ($row) {
                $namaLokasi = $row->customer->lokasi->nama_kavling ?? '-';
                $kodeKavling = $row->customer->kavling->kode_kavling ?? '-';
                return '<strong>' . $namaLokasi . '</strong><br> ' . $kodeKavling;
            })
            ->editColumn('iph', function ($row) {
                return $row->persyaratan && $row->persyaratan->IPH
                    ? '<i class="fas fa-check-circle text-success"></i>'
                    : '<i class="fas fa-times-circle text-danger"></i>';
            })
            ->editColumn('shgb', function ($row) {
                return $row->persyaratan && $row->persyaratan->SHGB
                    ? '<i class="fas fa-check-circle text-success"></i>'
                    : '<i class="fas fa-times-circle text-danger"></i>';
            })
            ->editColumn('pajak', function ($row) {
                return $row->persyaratan && $row->persyaratan->pajak
                    ? '<i class="fas fa-check-circle text-success"></i>'
                    : '<i class="fas fa-times-circle text-danger"></i>';
            })
            ->editColumn('tgl_masuk', function ($row) {
                return Carbon::parse($row->tgl_masuk)->translatedFormat('d F Y');
            })
            ->editColumn('keterangan', fn($row) => $row->keterangan ?? '')
            ->addColumn('action', function ($row) {
                $btn = '<div class="text-center">';

                if ($row->status == 'hadir') {
                    $btn .= '<button class="btn btn-success btn-sm mx-1" disabled>
                                <i class="fas fa-check-circle"></i>
                            </button>';
                } else {
                    $btn .= '<button class="btn btn-success btn-sm hadir-button mx-1"
                                data-id="' . $row->id . '">
                                Hadir
                            </button>';
                }

                $btn .= '<button class="btn btn-sm btn-danger delete-button mx-1"
                            data-id="' . $row->id . '"
                            data-url="' . route('akad.detail.destroy', $row->id) . '">
                            Hapus
                        </button>';

                return $btn;
            })

            ->rawColumns(['nama_kavling', 'iph', 'shgb', 'pajak', 'action'])
            ->make(true);
    }

    public function seleksi($id)
    {
        $akad = Akad::findOrFail($id);

        $selectedCustomerIds = AkadDetail::where('id_akad', $id)->pluck('id_customer')->toArray();

        $data = Customer::with(['lokasi', 'kavling'])
            ->whereNotIn('id_customer', $selectedCustomerIds)
            ->where('id_status_progres', 4)
            ->orderBy('nama_lengkap', 'asc')
            ->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('pilih', function ($row) {
                $legal = PengajuanBerkasLegal::where('id_customer', $row->id_customer)->first();
                $id_persyaratan = $legal->id ?? '';

                return '
        <div class="form-check">
            <input type="checkbox" name="id_customer[]" class="form-check-input" value="' . $row->id_customer . '">
            <input type="hidden" name="id_persyaratan[' . $row->id_customer . ']" value="' . $id_persyaratan . '">
        </div>';
            })
            ->addColumn('nama_lengkap', fn($row) => $row->nama_lengkap)
            ->addColumn('nama_kavling', function ($row) {
                $namaLokasi = $row->lokasi->nama_kavling ?? '-';
                $kodeKavling = $row->kavling->kode_kavling ?? '-';
                return '<strong>' . $namaLokasi . '</strong><br>' . $kodeKavling;
            })
            ->addColumn('iph', fn($row) => optional($row->legal)->IPH ? '<i class="fas fa-check-circle text-success"></i>' : '<i class="fas fa-times-circle text-danger"></i>')
            ->addColumn('shgb', fn($row) => optional($row->legal)->SHGB ? '<i class="fas fa-check-circle text-success"></i>' : '<i class="fas fa-times-circle text-danger"></i>')
            ->addColumn('pajak', fn($row) => optional($row->legal)->pajak ? '<i class="fas fa-check-circle text-success"></i>' : '<i class="fas fa-times-circle text-danger"></i>')
            ->rawColumns(['nama_kavling', 'pilih', 'iph', 'shgb', 'pajak'])
            ->make(true);
    }

    public function destroyDetail($id)
    {
        try {
            AkadDetail::findOrFail($id)->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus data.'
            ], 500);
        }
    }

    public function updateHadir($id, Request $request)
    {
        $request->validate(
            [
                'keterangan' => 'nullable|string|max:255',
            ],
            [
                'keterangan.string' => 'Keterangan harus berupa teks.',
                'keterangan.max' => 'Keterangan tidak boleh lebih dari 255 karakter.',
            ]
        );

        $detail = AkadDetail::findOrFail($id);
        $detail->keterangan = $request->keterangan;
        $detail->status = 'hadir';
        $detail->save();

        $cust = Customer::find($detail->id_customer);
        $cust->id_status_progres = 3;
        $cust->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Status diubah menjadi hadir.'
        ]);
    }

    public function storeDetailAkad(Request $request)
    {
        $isArray = is_array($request->id_customer);

        $rules = [
            'id_akad' => 'required|exists:akad,id_akad',
        ];

        if ($isArray) {
            $rules['id_customer'] = 'required|array';
            $rules['id_customer.*'] = 'exists:nasabah,id_customer';
            $rules['id_persyaratan'] = 'nullable|array';
        } else {
            $rules['id_customer'] = 'required|exists:nasabah,id_customer';
            $rules['id_persyaratan'] = 'nullable|exists:persyaratan_legal,id';
        }

        $request->validate($rules);

        if ($isArray) {
            foreach ($request->id_customer as $id_customer) {
                // Ensure id_persyaratan exists even if data is missing in DB
                $id_persyaratan = $request->id_persyaratan[$id_customer] ?? null;
                if (empty($id_persyaratan)) {
                    $legal = PengajuanBerkasLegal::firstOrCreate(['id_customer' => $id_customer]);
                    $id_persyaratan = $legal->id;
                }

                AkadDetail::create([
                    'id_akad' => $request->id_akad,
                    'id_customer' => $id_customer,
                    'id_persyaratan' => $id_persyaratan,
                    'tgl_masuk' => now(),
                    'status' => 'belum hadir',
                ]);
            }
        } else {
            $id_persyaratan = $request->id_persyaratan;
            if (empty($id_persyaratan)) {
                $legal = PengajuanBerkasLegal::firstOrCreate(['id_customer' => $request->id_customer]);
                $id_persyaratan = $legal->id;
            }

            AkadDetail::create([
                'id_akad' => $request->id_akad,
                'id_customer' => $request->id_customer,
                'id_persyaratan' => $id_persyaratan,
                'tgl_masuk' => now(),
                'status' => 'belum hadir',
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil disimpan'
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tgl_akad' => 'required|date',
            'keterangan' => 'required',
        ], [
            'keterangan.required' => 'keterangan wajib diisi.',
            'tgl_akad.required' => 'Kolom tanggal wajib diisi.',
            'tgl_akad.date' => 'Format tanggal tidak valid.',
        ]);


        Akad::create([
            'tgl_akad' => $request->tgl_akad,
            'keterangan' => $request->keterangan ?? '',
            'stt_delete' => 0,
        ]);


        return response()->json(['status' => 'success']);
    }



    public function edit($id)
    {
        $list = Akad::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $list,
        ]);
    }

    public function destroy($id)
    {
        $data = Akad::findOrFail($id);

        $data->delete();

        return response()->json(['status' => 'success']);
    }

    public function update(Request $request, $id)
    {
        $data = Akad::findOrFail($id);

        $request->validate([
            'tgl_akad' => 'required|date',
            'keterangan' => 'required',
        ], [
            'keterangan.required' => 'keterangan wajib diisi.',
            'tgl_akad.required' => 'Kolom tanggal wajib diisi.',
            'tgl_akad.date' => 'Format tanggal tidak valid.',
        ]);


        $db = [
            'tgl_akad' => $request->tgl_akad,
            'keterangan' => $request->keterangan,
        ];

        $data->update($db);

        return response()->json(['status' => 'success']);
    }


    public function downloadWord($id)
    {
        $akad = Akad::findOrFail($id);
        $customer = Customer::findOrFail($akad->id_customer);
        $kavling = KavlingPeta::findOrFail($akad->id_kavling);
        $lokasi = $kavling->lokasi;

        $templatePath = public_path('templates/template.docx');
        $template = new TemplateProcessor($templatePath);

        $tanggal = Carbon::parse($akad->tgl_akad);
        $hari = $tanggal->locale('id')->isoFormat('dddd');
        $tanggalBulan = $tanggal->locale('id')->isoFormat('D MMMM');
        $tahun = $tanggal->year;

        $hrgJual = $kavling->hrg_jual ?? 0;
        $hrgText = ucwords($this->terbilang($hrgJual)) . ' rupiah';

        $template->setValue('nama_lengkap', $customer->nama_lengkap);
        $template->setValue('no_ktp', $customer->no_ktp);
        $template->setValue('no_wa', $customer->no_wa);
        $template->setValue('alamat', $customer->alamat);
        $template->setValue('hari', $hari);
        $template->setValue('tanggal_bulan', $tanggalBulan);
        $template->setValue('tahun', $tahun);
        $template->setValue('lebar', $kavling->lebar_depan . ' x ' . $kavling->lebar_belakang);
        $template->setValue('panjang', $kavling->panjang_kanan . ' x ' . $kavling->panjang_kiri);
        $template->setValue('luas_tanah', $kavling->luas_tanah);
        $template->setValue('no_blok', $kavling->kode_kavling);
        $template->setValue('hrg_jual', number_format($hrgJual, 0, ',', '.'));
        $template->setValue('hrg_text', $hrgText);

        $fileName = 'akad_kavling_' . $customer->nama_lengkap . '.docx';
        $filePath = storage_path('app/public/' . $fileName);
        $template->saveAs($filePath);

        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    private function terbilang($angka)
    {
        $angka = (int) $angka;
        $bilangan = ["", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas"];
        if ($angka < 12)
            return $bilangan[$angka];
        if ($angka < 20)
            return $this->terbilang($angka - 10) . " belas";
        if ($angka < 100)
            return $this->terbilang($angka / 10) . " puluh " . $this->terbilang($angka % 10);
        if ($angka < 200)
            return "seratus " . $this->terbilang($angka - 100);
        if ($angka < 1000)
            return $this->terbilang($angka / 100) . " ratus " . $this->terbilang($angka % 100);
        if ($angka < 2000)
            return "seribu " . $this->terbilang($angka - 1000);
        if ($angka < 1000000)
            return $this->terbilang($angka / 1000) . " ribu " . $this->terbilang($angka % 1000);
        if ($angka < 1000000000)
            return $this->terbilang($angka / 1000000) . " juta " . $this->terbilang($angka % 1000000);
        return "terlalu besar";
    }

    public function cetakDetailPDF($id)
    {
        $akad = Akad::findOrFail($id);
        $detailList = AkadDetail::with(['customer.kavling'])
            ->where('id_akad', $id)
            ->get();

        Carbon::setLocale('id');
        $tglFormatted = Carbon::parse($akad->tgl_akad)->translatedFormat('d F Y');

        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetTitle('Daftar Peserta Akad - ' . $tglFormatted);
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        $pdf->SetMargins(10, 10, 10, true);
        $pdf->AddPage();

        $html = '<h3 style="text-align:center;">Akad tanggal ' . $tglFormatted . '</h3>';
        $html .= '<table border="1" cellpadding="6" cellspacing="0">
            <thead>
                <tr style="font-weight: bold; background-color: #f2f2f2;">
                    <th width="10%">No</th>
                    <th width="45%">Nama Lengkap</th>
                    <th width="45%">Kode Kavling</th>
                </tr>
            </thead>
            <tbody>';

        foreach ($detailList as $index => $item) {
            $nama = $item->customer->nama_lengkap ?? '-';
            $kodeKavling = $item->customer->kavling->kode_kavling ?? '-';

            $html .= '<tr>
                <td width="10%" align="center">' . ($index + 1) . '</td>
                <td width="45%" >' . $nama . '</td>
                <td width="45%" >' . $kodeKavling . '</td>
            </tr>';
        }

        $html .= '</tbody></table>';

        $html .= '<br><br><br><div style="text-align: right; margin-top: 50px;">
                <p>.......................</p>
            </div>';


        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output('detail-akad-' . now()->format('d-m-Y') . '.pdf', 'I');
    }

    public function cetakDetailExcel($id)
    {
        $akad = Akad::findOrFail($id);
        $detailList = AkadDetail::with(['customer.kavling'])
            ->where('id_akad', $id)
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Daftar Akad Tanggal ' . \Carbon\Carbon::parse($akad->tgl_akad)->translatedFormat('d F Y'));
        $sheet->mergeCells('A1:C1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        $sheet->setCellValue('A3', 'No');
        $sheet->setCellValue('B3', 'Nama Lengkap');
        $sheet->setCellValue('C3', 'Kode Kavling');

        $row = 4;
        foreach ($detailList as $index => $item) {
            $sheet->setCellValue("A{$row}", $index + 1);
            $sheet->setCellValue("B{$row}", $item->customer->nama_lengkap ?? '-');
            $sheet->setCellValue("C{$row}", $item->customer->kavling->kode_kavling ?? '-');
            $row++;
        }

        foreach (range('A', 'C') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'detail-akad-' . now()->format('d-m-Y') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
