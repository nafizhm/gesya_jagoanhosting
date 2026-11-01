<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;

use Carbon\Carbon;
use App\Models\Customer;
use App\Models\MetodeBayar;
use App\Models\Pembatalan;
use App\Models\KavlingPeta;
use Illuminate\Http\Request;
use App\Models\LokasiKavling;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Pengaturan\HakAksesController;
use App\Models\Wawancara;
use App\Models\WawancaraSp3k;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use TCPDF;

class PembelianCancelController extends Controller
{
    public function index(Request $request)
    {
        $permissions = HakAksesController::getUserPermissions();

        if ($request->ajax()) {
            $opd = Pembatalan::orderBy('id_batal', 'desc');

            return DataTables::of($opd)
                ->addIndexColumn()

                ->addColumn('nama_customer', function ($row) {
                    $statusBadge = $row->stt_approval == 1
                        ? '<span class="badge bg-success">Disetujui</span>'
                        : '<span class="badge bg-warning text-dark">Menunggu Persetujuan</span>';

                    return '<div>' . e($row->nama_customer) . '<br>' . $statusBadge . '</div>';
                })

                ->addColumn('no_telp', function ($row) {
                    $customer = Customer::where('id_customer', $row->id_customer)->first();
                    return $customer ? $customer->no_telp : '-';
                })

                ->addColumn('surat_pernyataan', function ($row) {
                    return '';
                })

                ->addColumn('action', function ($row) use ($permissions) {
                    $showUrl = route('pembelian-cancel.show', $row->id_batal);
                    $deleteUrl = route('pembelian-cancel.destroy', $row->id_batal);

                    $btn = '<div class="d-flex justify-content-center">';

                    $btn .= '
                        <button class="btn btn-primary btn-sm mr-1"
                            data-id="' . e($row->id_batal) . '"
                            data-url="' . e($showUrl) . '"
                            data-toggle="modal" data-target="#modalDetail" id="detail-button">
                            Detail
                        </button>';

                    if ($permissions['hapus']) {
                        $btn .= '
                            <form action="' . e($deleteUrl) . '" method="POST">
                                ' . csrf_field() . method_field('DELETE') . '
                                <button type="submit" class="delete-button btn btn-danger btn-sm">
                                    Hapus
                                </button>
                            </form>';
                    }

                    $btn .= '</div>';

                    return $btn;
                })

                ->filterColumn('nama_customer', function ($query, $keyword) {
                    $query->where('nama_customer', 'like', "%{$keyword}%");
                })

                ->rawColumns(['no_telp', 'action', 'nama_customer'])
                ->make(true);
        }

        $customers = $customers = Customer::get(['id_customer', 'nama_lengkap']);

        $tanggalSekarang = Carbon::now('Asia/Jakarta')->format('Y-m-d');
        $metodeBayar = MetodeBayar::all()->map(function ($item) {
            $item->jenis_bayar = strtoupper($item->jenis_bayar);
            return $item;
        });

        return view('admin.transaksi.pembelian_cancel.index', compact('permissions', 'customers', 'tanggalSekarang', 'metodeBayar'));
    }

    public function getCustomerData($id_customer)
    {
        $customer = Customer::findOrFail($id_customer);

        $kavling = KavlingPeta::where('id_kavling', $customer->id_kavling)->first();

        return response()->json([
            'no_ktp' => $customer->no_ktp,
            'alamat_ktp' => $customer->alamat,
            'blok' => $kavling->kode_kavling,
        ]);
    }

    public function store(Request $request)
    {
        $tahunSekarang = now('Asia/Jakarta')->year;

        $lastRecord = Pembatalan::where('no_kwitansi', 'like', "%/PC/{$tahunSekarang}")
            ->orderBy('no_kwitansi', 'desc')
            ->first();

        if ($lastRecord) {
            $lastNumber = explode('/', $lastRecord->no_kwitansi)[0];
            $newNumber = str_pad((int) $lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }

        $no_kwitansi = "{$newNumber}/PC/{$tahunSekarang}";

        $request->merge([
            'jumlah_bayar' => str_replace('.', '', $request->jumlah_bayar ?? 0),
        ]);

        $rules = [
            'tanggal'        => 'required|date',
            'nama_customer'  => 'required',
            'keterangan'     => 'required|string',
            'nama_bank'      => 'nullable|string',
            'no_rekening'    => 'nullable|string',
            'atas_nama'      => 'nullable|string',
            'jumlah_bayar'   => 'numeric',
            'lampiran_bukti' => 'mimes:jpeg,png,pdf,jpg|max:2048',
        ];

        $messages = [
            'tanggal.required'        => 'Tanggal pembatalan wajib diisi.',
            'tanggal.date'            => 'Format tanggal tidak valid.',
            'nama_customer.required'  => 'Nama customer wajib dipilih.',
            'keterangan.required'     => 'Keterangan wajib diisi.',
            'jumlah_bayar.numeric'    => 'Jumlah bayar harus berupa angka.',
            'lampiran_bukti.mimes'    => 'Lampiran hanya boleh berupa file JPG, JPEG, PNG, atau PDF.',
            'lampiran_bukti.max'      => 'Ukuran file maksimal 2MB.',
        ];

        $request->validate($rules, $messages);

        $lampiranPath = null;
        if ($request->hasFile('lampiran_bukti')) {
            try {
                $file = $request->file('lampiran_bukti');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $destinationPath = public_path('lampiran');

                if (!is_dir($destinationPath)) {
                    mkdir($destinationPath, 0777, true);
                }

                $file->move($destinationPath, $fileName);
                $lampiranPath = 'lampiran/' . $fileName;
            } catch (\Exception $e) {
                Log::error('Gagal upload file bukti: ' . $e->getMessage());
            }
        }

        try {
            DB::beginTransaction();

            $customer = Customer::findOrFail($request->nama_customer);
            $kavling = KavlingPeta::lockForUpdate()->findOrFail($customer->id_kavling);
            $lokasi = LokasiKavling::findOrFail($kavling->id_lokasi);

            Pembatalan::create([
                'no_kwitansi' => $no_kwitansi,
                'tgl_batal' => $request->tanggal,
                'id_customer' => $customer->id_customer,
                'id_kavling' => $customer->id_kavling,
                'nama_customer' => $customer->nama_lengkap,
                'kode_kavling' => $kavling->kode_kavling,
                'no_ktp' => $customer->no_ktp,
                'alamat' => $customer->alamat,
                'nama_perumahan' => $lokasi->nama_kavling,
                'tanggal_utj' => null,
                'nominal_utj' => 0,
                'nama_bank' => $request->nama_bank,
                'no_rekening' => $request->no_rekening,
                'atas_nama' => $request->atas_nama,
                'keterangan_batal' => $request->keterangan,
                'lampiran_bukti' => $lampiranPath,
                'jumlah_bayar' => $request->jumlah_bayar,
                'metode_bayar' => $request->metode_pembayaran,
                'stt_approval' => 1,
                'stt_delete' => 0,
            ]);

            try {
                if (class_exists('App\\Models\\Wawancara')) {
                    $wawancaras = Wawancara::where('id_customer', $customer->id_customer)->get();

                    if (class_exists('App\\Models\\WawancaraSp3k')) {
                        foreach ($wawancaras as $wawancara) {
                            WawancaraSp3k::where('id_wawancara', $wawancara->id_wawancara)->delete();
                        }
                    }

                    Wawancara::where('id_customer', $customer->id_customer)->delete();
                }
            } catch (\Exception $e) {
                Log::warning('Gagal menghapus data terkait: ' . $e->getMessage());
            }

            $customer->delete();

            DB::commit();

            return response()->json(['status' => 'success', 'message' => 'Data Pembelian Cancel Berhasil Disimpan dan Customer telah diarsipkan!']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal simpan pembatalan: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Gagal menyimpan data pembatalan. Silakan cek log.'], 500);
        }
    }

    public function show($id)
    {
        $data = Pembatalan::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    public function destroy($id)
    {
        $opd = Pembatalan::findOrFail($id);

        $opd->delete();

        return response()->json(['status' => 'success', 'message' => 'Data Pembelian cancel Berhasil Dihapus!']);
    }

    public function cetakKwitansi($id)
    {
        $data = Pembatalan::with('kavling')->findOrFail($id);

        $width = 210;
        $height = 120;
        $pdf = new TCPDF('L', 'mm', [$width, $height]);
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetAutoPageBreak(false, 0);
        $pdf->AddPage();

        $bgPath = public_path('assets/img/bg_kwitansi.jpg');
        if (file_exists($bgPath)) {
            $pdf->Image($bgPath, 0, 0, $width, $height);
        }

        // Header
        // $pdf->SetFont('Times', 'B', 10);
        // $pdf->SetTextColor(0, 0, 0);
        // $pdf->SetXY(40, 10);
        // $pdf->Cell(0, 7, 'PT. HAMZAH MAJU BERSAMA', 0, 2, 'L');

        // $pdf->SetTextColor(0, 176, 240);
        // $pdf->SetXY(40, 15);
        // $pdf->Cell(0, 7, 'developer & kontraktor', 0, 2, 'L');

        $pdf->SetTextColor(255, 0, 0);
        $pdf->SetFont('Times', 'B', 10);

        $pdf->SetXY(15, 29);
        $pdf->SetTextColor(255, 0, 0);
        $pdf->Cell(0, 18, $data->no_kwitansi ?? '-', 0, 1, 'L');

        $pdf->SetTextColor(0, 0, 0);

        $pdf->SetFont('Times', '', 12);
        $pdf->SetXY(5, 36);
        $pdf->Cell(55, 5, '', 0, 0, 'L');
        $pdf->Cell(90, 15, ($data->nama_customer ?? '-'), 0, 1, 'L');

        $pdf->SetXY(5, 48);
        $pdf->Cell(55, 0, '', 0, 0, 'L');
        $pdf->Cell(90, 0, $this->terbilang($data->jumlah_bayar) . ' Rupiah', 0, 1, 'L');

        $pdf->SetXY(5, 56);
        $pdf->Cell(55, 5, '', 0, 0, 'L');
        $pdf->MultiCell(90, 3, ($data->keterangan_batal ?? '-'), 0, 'L');

        $pdf->SetXY(5, 60);
        $pdf->Cell(55, 5, '', 0, 0, 'L');
        $pdf->Cell(90, 10, $data->kavling->kode_kavling ?? '-', 0, 1, 'L');


        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Text(30, 80, number_format($data->jumlah_bayar, 0, ',', '.') . ',-');

        $metode = strtoupper($data->metode_bayar ?? '-');
        $checkIcon = public_path('check-solid.png');
        $checkSize = 5;

        if (file_exists($checkIcon)) {
            switch ($metode) {
                case 'CASH':
                    $pdf->Image($checkIcon, 11.5, 88, $checkSize);
                    break;

                case 'TRANSFER':
                    $pdf->Image($checkIcon, 28, 88, $checkSize);
                    break;

                case 'CHEQUE':
                    $pdf->Image($checkIcon, 53, 88, $checkSize);
                    break;

                case 'BILYET GIRO':
                    $pdf->Image($checkIcon, 74, 88, $checkSize);
                    break;
            }
        }


        $pdf->Image(public_path('assets/img/ttd_direktur.jpg'), 153, 90, 25);
        $pdf->Ln(-15);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Cell(155, 0, '', 0, 0, 'C');
        $pdf->Cell(70, 40, Carbon::parse($data->tgl_pindah)->translatedFormat('d F Y'), 0, 1, 'L');

        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetXY(14, 101);
        $pdf->Cell(0, 0, $data->no_rekening ?? '-', 0, 'L');

        $pdf->Output('Pindah-unit_' . $data->id_pindah . '.pdf', 'I');
        exit;
    }


    private function terbilang($angka)
    {
        $angka = abs($angka);
        $baca = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];
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
