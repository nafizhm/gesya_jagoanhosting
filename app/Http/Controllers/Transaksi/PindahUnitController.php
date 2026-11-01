<?php
namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Pengaturan\HakAksesController;
use App\Models\Customer;
use App\Models\KavlingPeta;
use App\Models\LokasiKavling;
use App\Models\MetodeBayar;
use App\Models\PindahUnit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\Jc;
use TCPDF;
use Yajra\DataTables\DataTables;

class PindahUnitController extends Controller
{

    public function index(Request $request)
    {
        Carbon::setLocale('id');

        $permissions = HakAksesController::getUserPermissions();

        if ($request->ajax()) {
            $opd = PindahUnit::orderBy('id_pindah', 'desc');

            return DataTables::of($opd)
                ->addIndexColumn()
                ->addColumn('tgl_pindah', function ($row) {
                    return Carbon::parse($row->tgl_pindah)->translatedFormat('j F Y');
                })
                ->addColumn('biaya_admin', function ($row) {
                    return '
                        <div class="d-flex justify-content-between harga-format w-100">
                            <span>Rp.</span>
                            <span>' . number_format($row->biaya_admin, 0, ',', '.') . '</span>
                        </div>
                    ';
                })
                ->addColumn('action', function ($row) use ($permissions) {
                    $deleteUrl = route('pindah-unit.destroy', $row->id_pindah);

                    $btn = '<div class="d-flex justify-content-center">';

                    $btn .= '<a href="' . route('pindah-unit.kwitansi', $row->id_pindah) . '"
                            class="btn btn-primary btn-sm ml-1"
                            target="_blank">
                            Cetak Kwitansi
                            </a>';

                    $btn .= '<a href="' . route('pindah-unit.cetak-word', $row->id_pindah) . '"
                            class="btn btn-success btn-sm ml-1">
                            Cetak
                            </a>';

                    if ($permissions['hapus']) {
                        $btn .= '<form action="' . e($deleteUrl) . '" method="POST" style="display:inline;">' . csrf_field() . method_field('DELETE') . '<button type="submit" class="delete-button btn btn-danger btn-sm mx-1">Hapus</button></form>';
                    }

                    $btn .= '</div>';

                    return $btn;
                })

                ->rawColumns(['action', 'biaya_admin'])

                ->make(true);
        }
        $customers = Customer::all(['id_customer', 'nama_lengkap']);

        $tanggalSekarang = Carbon::now('Asia/Jakarta')->format('Y-m-d');

        $ownedKavlingIds = Customer::pluck('id_kavling')->toArray();

        $metodeBayar = MetodeBayar::all()->map(function ($item) {
            $item->jenis_bayar = strtoupper($item->jenis_bayar);
            return $item;
        });

        $kavBaru = KavlingPeta::whereNotIn('id_kavling', $ownedKavlingIds)
            // ->where('status', 0)
            ->select('id_kavling', 'kode_kavling')
            ->orderBy(DB::raw("SUBSTRING_INDEX(kode_kavling, '-', 1)"), 'asc')
            ->orderBy(DB::raw("CAST(SUBSTRING_INDEX(kode_kavling, '-', -1) AS UNSIGNED)"), 'desc')
            ->get();

        return view('admin.transaksi.pindah_unit.index', compact('permissions', 'customers', 'tanggalSekarang', 'kavBaru', 'metodeBayar'));
    }

    public function getKavlingBaru($id_customer)
    {
        $customer = Customer::findOrFail($id_customer);

        $lokasiLama = $customer->id_lokasi;

        $ownedKavlingIds = Customer::pluck('id_kavling')->toArray();

        $kavlings = KavlingPeta::where('id_lokasi', $lokasiLama)
            ->whereNotIn('id_kavling', $ownedKavlingIds)
            // ->where('status', 0)
            ->select('id_kavling', 'kode_kavling')
            ->orderBy(DB::raw("SUBSTRING_INDEX(kode_kavling, '-', 1)"), 'asc')
            ->orderBy(DB::raw("CAST(SUBSTRING_INDEX(kode_kavling, '-', -1) AS UNSIGNED)"), 'desc')
            ->get();

        return response()->json($kavlings);
    }

    public function getCustomerData($id_customer)
    {
        $customer = Customer::with('kavling')->findOrFail($id_customer);

        return response()->json([
            'no_ktp' => $customer->no_ktp,
            'alamat' => $customer->alamat,
            'blok'   => $customer->kavling->kode_kavling,
        ]);
    }

    public function destroy($id_pindah)
    {
        DB::beginTransaction();

        try {
            $pindahUnit = PindahUnit::findOrFail($id_pindah);

            $customer = Customer::findOrFail(
                $pindahUnit->id_customer
            );

            $kavlingLama = KavlingPeta::lockForUpdate()->findOrFail($pindahUnit->id_kavling_lama);
            $kavlingBaru = KavlingPeta::lockForUpdate()->findOrFail($pindahUnit->id_kavling_baru);

            $customer->update([
                'id_kavling' => $pindahUnit->id_kavling_lama,
                'id_lokasi'  => optional(
                    KavlingPeta::find($pindahUnit->id_kavling_lama)
                )->id_lokasi,
                'hrg_rumah'  => optional(
                    KavlingPeta::find($pindahUnit->id_kavling_lama)
                )->hrg_jual,
            ]);

            if (
                ! empty($pindahUnit->lampiran_bukti) &&
                file_exists(public_path($pindahUnit->lampiran_bukti))
            ) {
                unlink(public_path($pindahUnit->lampiran_bukti));
            }

            $pindahUnit->delete();

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Data pindah unit berhasil dihapus.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Gagal hapus pindah unit: ' . $e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->merge([
            'biaya_admin' => (int) str_replace('.', '', $request->biaya_admin),
        ]);

        $rules = [
            'tanggal'           => 'required|date',
            'id_customer'       => 'required',
            'biaya_admin'       => 'required|numeric',
            'id_kavling_baru'   => 'required',
            'keterangan'        => 'required',
            'metode_pembayaran' => 'required|string',
            'lampiran_bukti'    => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ];

        $messages = [
            'keterangan.required'        => 'Keterangan pindah wajib diisi.',
            'tanggal.required'           => 'Tanggal pindah wajib diisi.',
            'tanggal.date'               => 'Tanggal pindah harus berupa format tanggal yang valid.',
            'id_customer.required'       => 'Customer wajib dipilih.',
            'id_kavling_baru.required'   => 'Lokasi kavling baru wajib dipilih.',
            'metode_pembayaran.required' => 'Cara Bayar wajib diisi.',
            'biaya_admin.required'       => 'Biaya Administrasi wajib diisi.',
            'biaya_admin.numeric'        => 'Biaya Administrasi harus berupa angka.',
            'lampiran_bukti.required'    => 'Bukti pembayaran wajib diunggah.',
            'lampiran_bukti.mimes'       => 'File harus berupa jpeg, png, jpg, atau pdf.',
            'lampiran_bukti.max'         => 'Ukuran file maksimal 2MB.',
        ];

        $request->validate($rules, $messages);

        DB::beginTransaction();

        try {
            $tahun = now('Asia/Jakarta')->year;

            $lastData = PindahUnit::lockForUpdate()
                ->where('no_kwitansi', 'like', "%/PU/{$tahun}")
                ->select('no_kwitansi')
                ->orderByRaw("
                CAST(
                    SUBSTRING_INDEX(no_kwitansi, '/', 1)
                    AS UNSIGNED
                ) DESC
            ")
                ->first();

            $lastNumber = 0;

            if ($lastData) {
                $lastNumber = (int) explode('/', $lastData->no_kwitansi)[0];
            }

            $nextNumber = str_pad(
                $lastNumber + 1,
                3,
                '0',
                STR_PAD_LEFT
            );

            $noKwitansi = "{$nextNumber}/PU/{$tahun}";

            $lampiranPath = null;

            if ($request->hasFile('lampiran_bukti')) {
                $file = $request->file('lampiran_bukti');

                $fileName = time() . '_' . $file->getClientOriginalName();

                $destinationPath = public_path('lampiran');

                if (! is_dir($destinationPath)) {
                    mkdir($destinationPath, 0777, true);
                }

                $file->move($destinationPath, $fileName);

                $lampiranPath = 'lampiran/' . $fileName;
            }

            $customer = Customer::findOrFail($request->id_customer);

            $kavlingLama = KavlingPeta::lockForUpdate()->findOrFail($customer->id_kavling);
            $kavlingBaru = KavlingPeta::lockForUpdate()->findOrFail($request->id_kavling_baru);

            /*
            if ($kavlingBaru->status != 0) {
                throw new \RuntimeException('Kavling baru sudah tidak tersedia.');
            }
            */

            PindahUnit::create([
                'no_kwitansi'       => $noKwitansi,
                'tgl_pindah'        => $request->tanggal,
                'id_customer'       => $customer->id_customer,
                'id_kavling_lama'   => $kavlingLama->id_kavling,
                'id_kavling_baru'   => $kavlingBaru->id_kavling,
                'nama_customer'     => $customer->nama_lengkap,
                'kode_kavling_lama' => optional(
                    $kavlingLama
                )->kode_kavling,
                'kode_kavling_baru' => $kavlingBaru->kode_kavling,
                'no_ktp'            => $customer->no_ktp,
                'alamat'            => $customer->alamat,
                'nama_perumahan'    => '',
                'biaya_admin'       => $request->biaya_admin,
                'nominal_utj'       => 0,
                'keterangan_pindah' => $request->keterangan,
                'metode_bayar'      => $request->metode_pembayaran,
                'lampiran_bukti'    => $lampiranPath,
                'stt_approval'      => 0,
                'stt_delete'        => 0,
            ]);

            $customer->update([
                'id_kavling' => $kavlingBaru->id_kavling,
                'id_lokasi'  => $kavlingBaru->id_lokasi,
                'hrg_rumah'  => $kavlingBaru->hrg_jual,
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Gagal simpan pindah unit: ' . $e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function cetakWord($id)
    {
        $data = PindahUnit::findOrFail($id);

        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        $phpWord->addTitleStyle(1, [
            'bold'      => true,
            'size'      => 18,
            'alignment' => Jc::CENTER,
        ]);

        $section->addText("Data Pindah Unit", [
            'bold' => true,
            'size' => 18,
        ], [
            'alignment'  => Jc::CENTER,
            'spaceAfter' => 200,
        ]);

        $table = $section->addTable([
            'borderSize'  => 6,
            'borderColor' => '999999',
            'cellMargin'  => 80,
        ]);

        $addRow = function ($label, $value) use ($table) {
            $table->addRow();
            $table->addCell(3000)->addText($label, ['bold' => true]);
            $table->addCell(8000)->addText($value);
        };

        $addRow("Tanggal Pindah", $data->tgl_pindah);
        $addRow("Nama Customer", $data->nama_customer);
        $addRow("No KTP", $data->no_ktp);
        $addRow("Alamat", $data->alamat);
        $addRow("Kavling Lama", $data->kode_kavling_lama);
        $addRow("Kavling Baru", $data->kode_kavling_baru);
        $addRow("Nominal UTJ", "Rp. " . number_format($data->nominal_utj, 0, ',', '.'));
        $addRow("Biaya Admin", "Rp. " . number_format($data->biaya_admin, 0, ',', '.'));
        $addRow("Metode Bayar", strtoupper($data->metode_bayar));
        // $addRow("No Rekening", $data->no_rekening);
        // $addRow("Jumlah Bayar", "Rp. " . number_format($data->jumlah_bayar, 0, ',', '.'));
        $addRow("Keterangan", $data->keterangan_pindah ?? '-');

        $fileName = 'pindah_unit_' . $data->id_pindah . '.docx';
        $tempFile = storage_path($fileName);

        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($tempFile);

        return response()->download($tempFile)->deleteFileAfterSend(true);
    }

    public function cetakKwitansi($id)
    {
        $data   = PindahUnit::findOrFail($id);
        $lokasi = LokasiKavling::where('id_lokasi', $nasabah->id_lokasi ?? null)->first();

        $noKwitansi  = $data->no_kwitansi ?? '-';
        $nama        = $data->nama_customer ?? '-';
        $alamat      = $data->alamat ?? '-';
        $jumlah      = $data->jumlah_bayar ?? 0;
        $namaKavling = $data->nama_perumahan ?? '-';

        $terbilang   = '#' . strtoupper($this->terbilang($jumlah)) . ' RUPIAH#';
        $keterangan  = $data->keterangan_pindah ?? '-';
        $kodeKavling = $data->kode_kavling_baru ?? '-';
        $metodeBayar = $data->metode_bayar ?? '-';
        $tanggal     = $data->tgl_pindah
            ? Carbon::parse($data->tgl_pindah)->translatedFormat('d F Y')
            : '-';

        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

        $pdf->SetTitle('Kwitansi Pindah Unit');
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        $pdf->SetMargins(20, 0, 20);
        $pdf->SetAutoPageBreak(false);

        $pdf->AddPage();
        $pdf->SetTextColor(0, 0, 0);

        $pdf->Ln(35);

        $pdf->SetFont('Helvetica', 'BU', 16);
        $pdf->Cell(170, 6, 'TANDA TERIMA', 0, 1, 'C');

        $pdf->SetFont('Helvetica', 'I', 12);
        $pdf->Cell(170, 6, $noKwitansi, 0, 1, 'C');

        $pdf->Ln(5);

        $pdf->SetFont('Helvetica', '', 10);

        $pdf->Cell(100, 5, 'Sudah diterima dari : ', 0, 1, 'L');

        $pdf->Cell(20, 5, '', 0, 0, 'L');
        $pdf->Cell(25, 5, 'Nama', 0, 0, 'L');
        $pdf->Cell(60, 5, ': ' . $nama, 0, 1, 'L');

        $pdf->Cell(20, 5, '', 0, 0, 'L');
        $pdf->Cell(25, 5, 'Alamat', 0, 0, 'L');
        $pdf->Cell(100, 5, ': ' . $alamat, 0, 1, 'L');

        $pdf->Ln(3);

        $pdf->MultiCell(
            170,
            5,
            'Uang sejumlah Rp. ' . number_format($jumlah, 0, ',', '.') .
            ' (' . $terbilang . ')',
            0,
            'L'
        );

        $pdf->Ln(1);

        $pdf->Cell(170, 5, 'Untuk pembayaran pindah unit : ', 0, 1, 'L');

        $pdf->Ln(2);

        $pdf->Cell(20, 5, '', 0, 0, 'L');
        $pdf->Cell(25, 5, 'Keterangan', 0, 0, 'L');
        $pdf->Cell(60, 5, ': ' . $keterangan, 0, 1, 'L');

        $pdf->Cell(20, 5, '', 0, 0, 'L');
        $pdf->Cell(25, 5, 'Perumahan', 0, 0, 'L');
        $pdf->Cell(60, 5, ': ' . $namaKavling, 0, 1, 'L');

        $pdf->Cell(20, 5, '', 0, 0, 'L');
        $pdf->Cell(25, 5, 'Kavling Baru', 0, 0, 'L');
        $pdf->Cell(60, 5, ': ' . $kodeKavling, 0, 1, 'L');

        $pdf->Cell(20, 5, '', 0, 0, 'L');
        $pdf->Cell(25, 5, 'No Rekening', 0, 0, 'L');
        $pdf->Cell(60, 5, ': ' . ($data->no_rekening ?? '-'), 0, 1, 'L');

        $pdf->Ln(15);

        $pdf->SetFont('Helvetica', '', 8.5);
        $pdf->Cell(35, 5, 'Keterangan : ', 0, 0, 'L');
        $pdf->Cell(45, 5, 'Kasir', 0, 0, 'C');
        $pdf->Cell(45, 5, 'Penyetor', 0, 0, 'C');
        $pdf->Cell(45, 5, 'Customer Service', 0, 1, 'C');

        $pdf->Cell(35, 5, $metodeBayar, 0, 0, 'L');
        $pdf->Cell(100, 5, '', 0, 0, 'L');
        $pdf->Ln(20);

        $pdf->SetLineWidth(0.2);
        $pdf->Line(60, 135, 95, 135);
        $pdf->Line(105, 135, 140, 135);
        $pdf->Line(150, 135, 185, 135);

        $pdf->SetFont('Helvetica', 'I', 8.5);
        $pdf->Cell(20, 5, 'NB : Kwitansi ini sah, apabila ada cap perusahaan dan tanda tangan kasir.', 0, 1, 'L');

        return response($pdf->Output('kwitansi_pindah_unit.pdf', 'I'), 200)
            ->header('Content-Type', 'application/pdf');
    }

    private function terbilang($angka)
    {
        $angka = abs($angka);
        $baca  = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];
        $hasil = "";

        if ($angka < 12) {
            $hasil = " " . $baca[$angka];
        } elseif ($angka < 20) {
            $hasil = $this->terbilang($angka - 10) . " Belas";
        } elseif ($angka < 100) {
            $hasil = $this->terbilang($angka / 10) . " Puluh" . $this->terbilang($angka % 10);
        } elseif ($angka < 200) {
            $hasil = " Seratus" . $this->terbilang($angka - 100);
        } elseif ($angka < 1000) {
            $hasil = $this->terbilang($angka / 100) . " Ratus" . $this->terbilang($angka % 100);
        } elseif ($angka < 2000) {
            $hasil = " Seribu" . $this->terbilang($angka - 1000);
        } elseif ($angka < 1000000) {
            $hasil = $this->terbilang($angka / 1000) . " Ribu" . $this->terbilang($angka % 1000);
        } elseif ($angka < 1000000000) {
            $hasil = $this->terbilang($angka / 1000000) . " Juta" . $this->terbilang($angka % 1000000);
        }

        return trim($hasil);
    }
}
