<?php
namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Pengaturan\HakAksesController;
use App\Models\ArsipNasabah;
use App\Models\BankModel;
use App\Models\Customer;
use App\Models\Freelance;
use App\Models\GantiNama;
use App\Models\KavlingPeta;
use App\Models\LokasiKavling;
use App\Models\Marketing;
use App\Models\Pembayaran;
use App\Models\ProgresListPenjualan;
use App\Models\Tagihan;
use App\Models\Wawancara;
use App\Models\WawancaraSp3k;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use TCPDF;
use Yajra\DataTables\Facades\DataTables;

class GantiNamaController extends Controller
{
    public function index(Request $request)
    {
        Carbon::setLocale('id');
        $permissions = HakAksesController::getUserPermissions();

        if ($request->ajax()) {
            $data = GantiNama::with(['arsipCustomerLama', 'customerBaru.lokasi', 'customerBaru.kavling'])
                ->where('stt_delete', 0)
                ->select([
                    'id',
                    'tgl_ganti as tanggal',
                    'id_customer_lama',
                    'id_customer_baru',
                    'nama_customer_lama',
                    'kode_kavling',
                    'biaya_ganti_nama',
                    'keterangan_ganti',
                    'stt_approval',
                ])
                ->orderBy('tgl_ganti', 'desc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('tanggal', function ($row) {
                    return Carbon::parse($row->tanggal)->translatedFormat('j F Y');
                })
                ->addColumn('nama_customer_lama', function ($row) {
                    return $row->nama_customer_lama ?? 'N/A';
                })
                ->addColumn('nama_customer_baru', function ($row) {
                    return $row->customerBaru->nama_lengkap ?? 'N/A';
                })
                ->addColumn('biaya_formatted', function ($row) {
                    return 'Rp ' . number_format($row->biaya_ganti_nama, 0, ',', '.');
                })
                ->addColumn('status', function ($row) {
                    return $row->stt_approval == 1 ? 'Approved' : 'Pending';
                })
                ->addColumn('perumahan', function ($row) {
                    return '
                        <div class="d-flex flex-column">
                            <span class="font-weight-bold">
                                ' . ($row->customerBaru->kavling->kode_kavling ?? 'N/A') . '
                            </span>
                            <small class="text-muted">
                                ' . ($row->arsipCustomerLama->lokasi->nama_kavling ?? 'N/A') . '
                            </small>
                        </div>
                    ';
                })
                ->addColumn('biaya_formatted', function ($row) {
                    return '
                        <div class="d-flex justify-content-between harga-format w-100">
                            <span>Rp.</span>
                            <span>' . number_format($row->biaya_ganti_nama, 0, ',', '.') . '</span>
                        </div>
                    ';
                })
                ->addColumn('action', function ($row) use ($permissions) {
                    $detailUrl = route('admin.ganti-nama.detail', $row->id);
                    $cetakUrl  = route('admin.ganti-nama.cetak', $row->id_customer_baru);
                    $deleteUrl = route('gantiNama.destroy', $row->id);

                    $btn  = '<div class="d-flex justify-content-center">';
                    $btn .= '<button id="edit-button" class="btn btn-sm btn-primary detail-btn" data-id="' . $row->id . '"data-url="' . $detailUrl . '">Detail</button>';
                    $btn .= '<a href="' . e($cetakUrl) . '" target="_blank" class="btn btn-warning btn-sm edit-a ml-1">Cetak</a>';

                    if ($permissions['hapus']) {
                        $btn .= '<form action="' . e($deleteUrl) . '" method="POST" style="display:inline;">' . csrf_field() . method_field('DELETE') . '<button type="submit" class="delete-button btn btn-danger btn-sm mx-1">Hapus</button></form>';
                    }
                    $btn .= '</div>';

                    return $btn;
                })
                ->filterColumn('nama_customer_lama', function ($query, $keyword) {
                    $query->where('nama_customer_lama', 'like', "%{$keyword}%");
                })
                ->filterColumn('nama_customer_baru', function ($query, $keyword) {
                    $query->whereHas('customerBaru', function ($q) use ($keyword) {
                        $q->where('nama_lengkap', 'like', "%{$keyword}%");
                    });
                })
                ->rawColumns([
                    'action',
                    'perumahan',
                    'biaya_formatted',
                ])->make(true);
        }

        $dataCustomer = Customer::all();
        $marketing    = Marketing::all();
        $freelance    = Freelance::all();
        $bank         = BankModel::all();
        $progres      = ProgresListPenjualan::all();
        $lokasi       = LokasiKavling::all();

        return view('admin.transaksi.ganti_nama.index', compact(
            'dataCustomer',
            'permissions',
            'marketing',
            'freelance',
            'lokasi',
            'progres',
            'bank'
        ));
    }

    public function detail($id)
    {
        $data = GantiNama::with([
            'arsipCustomerLama',
            'customerBaru',
            'customerBaru.lokasiKavling',
            'customerBaru.kavlingPeta',
            'customerBaru.marketing',
            'customerBaru.freelance',
            'customerBaru.progres',
        ])
            ->findOrFail($id);

        return response()->json([
            'data'          => $data,
            'customer_lama' => [
                'nama'        => $data->arsipCustomerLama->nama_lengkap ?? 'N/A',
                'nik'         => $data->arsipCustomerLama->no_ktp ?? 'N/A',
                'alamat'      => $data->arsipCustomerLama->alamat ?? 'N/A',
                'lokasi_unit' => ($data->customerBaru->lokasiKavling->nama_kavling ?? '') . ' - ' .
                ($data->customerBaru->kavlingPeta->kode_kavling ?? ''),
            ],
            'customer_baru' => [
                'nama'              => $data->customerBaru->nama_lengkap ?? 'N/A',
                'no_ktp'            => $data->customerBaru->no_ktp ?? '',
                'no_ktp_p'          => $data->customerBaru->no_ktp_p ?? '',
                'tempat_lahir'      => $data->customerBaru->tempat_lahir ?? '',
                'tgl_lahir'         => $data->customerBaru->tgl_lahir ?? '',
                'jenis_kelamin'     => $data->customerBaru->jenis_kelamin ?? '',
                'no_telp'           => $data->customerBaru->no_telp ?? '',
                'email'             => $data->customerBaru->email ?? '',
                'npwp'              => $data->customerBaru->npwp ?? '',
                'alamat'            => $data->customerBaru->alamat ?? '',
                'alamat_domisili'   => $data->customerBaru->alamat_domisili ?? '',
                'pekerjaan'         => $data->customerBaru->pekerjaan ?? '',
                'warga_negara'      => $data->customerBaru->warga_negara ?? '',
                'id_lokasi'         => $data->customerBaru->id_lokasi ?? '',
                'id_kavling'        => $data->customerBaru->id_kavling ?? '',
                'hrg_jual'          => $data->customerBaru->hrg_rumah ?? 0,
                'id_marketing'      => $data->customerBaru->id_marketing ?? '',
                'id_freelance'      => $data->customerBaru->id_freelance ?? '',
                'id_status_progres' => $data->customerBaru->id_status_progres ?? '',
                'ket_cashback'      => $data->customerBaru->ket_cashback ?? '',
                'jenis_pembelian'   => $data->customerBaru->jenis_pembelian ?? '',
            ],
        ]);
    }

    public function getCustomer($id)
    {
        $customer = Customer::with(['kavlingPeta', 'lokasiKavling', 'marketing', 'freelance', 'progres'])
            ->findOrFail($id);

        return response()->json([
            'data'      => $customer,
            'kavling'   => [
                'id_lokasi'        => $customer->id_lokasi,
                'nama_lokasi'      => $customer->lokasiKavling->nama_kavling ?? '',
                'id_kavling'       => $customer->id_kavling,
                'kode_kavling'     => $customer->kavlingPeta->kode_kavling ?? '',
                'hrg_jual'         => $customer->hrg_rumah,
                'lokasi_unit'      => ($customer->lokasiKavling->nama_kavling ?? '') . ' - ' . ($customer->kavlingPeta->kode_kavling ?? ''),
                'lokasi_perumahan' => optional($customer->lokasiKavling)->nama_kavling ?? '',
            ],
            'marketing' => [
                'id'   => $customer->id_marketing,
                'nama' => $customer->marketing->nama_marketing ?? '',
            ],
            'freelance' => [
                'id'   => $customer->id_freelance,
                'nama' => $customer->freelance->nama_freelance ?? '',
            ],
            'progres'   => [
                'id'     => $customer->id_status_progres,
                'status' => $customer->progres->status_progres ?? '',
            ],
            'transaksi' => [
                'ket_cashback'    => $customer->ket_cashback,
                'jenis_pembelian' => $customer->jenis_pembelian,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tgl_pindah'         => 'required|date',
            'id_customer_lama'   => 'required',
            'nama_lengkap'       => 'required',
            'no_ktp'             => 'required|numeric|unique:nasabah,no_ktp',
            'no_ktp_p'           => 'nullable|numeric',
            'tempat_lahir'       => 'required',
            'tgl_lahir'          => 'required|date',
            'jenis_kelamin'      => 'required|in:Laki-laki,Perempuan',
            'no_telp'            => 'required|numeric',
            'email'              => 'required|email',
            'alamat'             => 'required',
            'alamat_domisili'    => 'required',
            'pekerjaan'          => 'required',
            'warga_negara'       => 'required',
            'id_marketing'       => 'nullable',
            'id_freelance'       => 'nullable',
            'ket_cashback'       => 'nullable',
            'keterangan_ganti'   => 'nullable',
            'pembayaran_booking' => 'nullable',
            'tgl_batas_booking'  => 'nullable|date',
            'ket_booking'        => 'nullable',
        ], [
            'tgl_pindah.required'       => 'Tanggal pindah harus diisi',
            'tgl_pindah.date'           => 'Format tanggal pindah tidak valid',
            'id_customer_lama.required' => 'Customer lama harus dipilih',
            'nama_lengkap.required'     => 'Nama lengkap wajib diisi',
            'no_ktp.required'           => 'No. KTP wajib diisi',
            'no_ktp.numeric'            => 'No. KTP harus berupa angka',
            'no_ktp.unique'             => 'No. KTP sudah terdaftar',
            'no_ktp_p.numeric'          => 'No. KTP pasangan harus berupa angka',
            'tempat_lahir.required'     => 'Tempat lahir wajib diisi',
            'tgl_lahir.required'        => 'Tanggal lahir wajib diisi',
            'tgl_lahir.date'            => 'Format tanggal lahir tidak valid',
            'jenis_kelamin.required'    => 'Jenis kelamin wajib dipilih',
            'jenis_kelamin.in'          => 'Jenis kelamin tidak valid',
            'no_telp.required'          => 'No. Telepon wajib diisi',
            'no_telp.numeric'           => 'No. Telepon harus berupa angka',
            'email.email'               => 'Format email tidak valid',
            'alamat.required'           => 'Alamat wajib diisi',
            'alamat_domisili.required'  => 'Alamat domisili wajib diisi',
            'pekerjaan.required'        => 'Pekerjaan wajib diisi',
            'warga_negara.required'     => 'Warga negara wajib diisi',
            'tgl_batas_booking.date'    => 'Format tanggal batas booking tidak valid',
        ]);

        DB::beginTransaction();

        try {
            $customerLama = Customer::with([
                'kavlingPeta',
                'lokasiKavling',
            ])->findOrFail($request->id_customer_lama);

            $customerBaru = $customerLama->replicate();

            $customerBaru->fill([
                'nama_lengkap'        => $request->nama_lengkap,
                'no_ktp'              => $request->no_ktp,
                'no_ktp_p'            => $request->no_ktp_p,
                'tempat_lahir'        => $request->tempat_lahir,
                'tgl_lahir'           => $request->tgl_lahir,
                'jenis_kelamin'       => $request->jenis_kelamin,
                'no_telp'             => $request->no_telp,
                'email'               => $request->email,
                'alamat'              => $request->alamat,
                'alamat_domisili'     => $request->alamat_domisili,
                'pekerjaan'           => $request->pekerjaan,
                'warga_negara'        => $request->warga_negara,
                'tgl_terima'          => now(),
                'inhouse_jatuh_tempo' => (
                    ! empty($customerLama->inhouse_jatuh_tempo) &&
                    $customerLama->inhouse_jatuh_tempo !== '0000-00-00'
                )
                    ? $customerLama->inhouse_jatuh_tempo
                    : null,
            ]);

            $customerBaru->save();

            ArsipNasabah::create([
                'id_customer'       => $customerLama->id_customer,
                'tgl_terima'        => $customerLama->tgl_terima,
                'nama_lengkap'      => $customerLama->nama_lengkap,
                'kode_customer'     => $customerLama->kode_customer,
                'no_ktp'            => $customerLama->no_ktp,
                'tempat_lahir'      => $customerLama->tempat_lahir,
                'jenis_kelamin'     => $customerLama->jenis_kelamin,
                'no_wa'             => $customerLama->no_telp ?? 0,
                'alamat'            => $customerLama->alamat,
                'alamat_domisili'   => $customerLama->alamat_domisili,
                'pekerjaan'         => $customerLama->pekerjaan,
                'id_marketing'      => $customerLama->id_marketing,
                'id_status_progres' => $customerLama->id_status_progres,
                'no_ktp_p'          => $customerLama->no_ktp_p,
                'id_lokasi'         => $customerLama->id_lokasi,
                'id_kavling'        => $customerLama->id_kavling,
                'id_freelance'      => $customerLama->id_freelance,
                'tgl_lahir'         => $customerLama->tgl_lahir,
            ]);

            GantiNama::create([
                'tgl_ganti'          => $request->tgl_pindah,
                'id_customer_lama'   => $customerLama->id_customer,
                'id_customer_baru'   => $customerBaru->id_customer,
                'nama_customer_lama' => $customerLama->nama_lengkap,
                'id_kavling'         => $customerLama->id_kavling,
                'kode_kavling'       => $customerLama->kavlingPeta->kode_kavling,
                'biaya_ganti_nama'   => (int) str_replace('.', '', $request->biaya_ganti_nama ?? 0),
                'keterangan_ganti'   => $request->keterangan_ganti ?? 'Ganti nama a/n ' . $customerLama->nama_lengkap,
                'stt_approval'       => 0,
                'stt_delete'         => 0,
            ]);

            if (class_exists(Wawancara::class)) {
                $wawancaras = Wawancara::where(
                    'id_customer',
                    $customerLama->id_customer
                )->get();

                if (class_exists(WawancaraSp3k::class)) {
                    foreach ($wawancaras as $wawancara) {
                        WawancaraSp3k::where(
                            'id_wawancara',
                            $wawancara->id_wawancara
                        )->delete();
                    }
                }

                Wawancara::where(
                    'id_customer',
                    $customerLama->id_customer
                )->delete();
            }

            if (class_exists(Tagihan::class)) {
                Tagihan::where(
                    'nasabah',
                    $customerLama->id_customer
                )->update([
                    'nasabah' => $customerBaru->id_customer,
                ]);
            }

            if (class_exists(Pembayaran::class)) {
                Pembayaran::where(
                    'id_customer',
                    $customerLama->id_customer
                )->update([
                    'id_customer' => $customerBaru->id_customer,
                ]);
            }

            $tahun = now()->format('Y');

            $bulanRomawi = $this->bulanRomawi(
                now()->format('n')
            );

            $lokasiSingkat = LokasiKavling::where(
                'id_lokasi',
                $customerBaru->id_lokasi
            )->value('nama_singkat') ?? 'XXX';

            $lastPembayaran = Pembayaran::lockForUpdate()
                ->where('id_lokasi', $customerBaru->id_lokasi)
                ->where('no_kwitansi', 'like', "%/{$tahun}")
                ->select('no_kwitansi')
                ->orderByRaw("
                CAST(
                    SUBSTRING_INDEX(no_kwitansi, '/', 1)
                    AS UNSIGNED
                ) DESC
            ")
                ->first();

            $lastNumber = 0;

            if ($lastPembayaran) {
                $lastNumber = (int) explode('/', $lastPembayaran->no_kwitansi)[0];
            }

            $nextNumber = str_pad(
                $lastNumber + 1,
                4,
                '0',
                STR_PAD_LEFT
            );

            $noKwitansi = $nextNumber
                . '/'
                . $lokasiSingkat
                . '/'
                . $bulanRomawi
                . '/'
                . $tahun;

            Pembayaran::create([
                'tanggal'             => $request->tgl_pindah,
                'id_customer'         => $customerBaru->id_customer,
                'no_kwitansi'         => $noKwitansi,
                'id_lokasi'           => $customerBaru->id_lokasi,
                'id_jenis_pembayaran' => 12,
                'jumlah'              => (int) str_replace('.', '', $request->biaya_ganti_nama ?? 0),
                'keterangan'          => 'GANTI NAMA',
                'metode_bayar'        => 'CASH',
                'lampiran'            => '',
                'nama_notaris'        => '',
                'stt_delete_dp'       => 0,
            ]);

            $customerLama->delete();

            DB::commit();

            return response()->json([
                'success'          => true,
                'message'          => 'Proses ganti nama berhasil!',
                'customer_baru_id' => $customerBaru->id_customer,
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan ganti nama: ' . $th->getMessage(),
            ], 500);
        }
    }

    public function cetak($id)
    {
        $pembayaran = Pembayaran::with(['customer', 'jenisPembayaran'])->where('id_customer', $id)->where('keterangan', 'GANTI NAMA')->firstOrFail();
        $nasabah    = $pembayaran->customer;
        $lokasi     = LokasiKavling::where('id_lokasi', $nasabah->id_lokasi ?? null)->first();
        $kavling    = KavlingPeta::where('id_kavling', $nasabah->id_kavling ?? null)->first();
        $gantiNama  = GantiNama::where('id_customer_baru', $nasabah->id_customer)->first();

        $noKwitansi      = $pembayaran->no_kwitansi ?? '-';
        $nama            = $nasabah->nama_lengkap ?? '-';
        $alamat          = $nasabah->alamat ?? '-';
        $jumlah          = $pembayaran->jumlah ?? 0;
        $terbilang       = '#' . strtoupper($this->terbilang($jumlah)) . ' Rupiah#';
        $namaKavling     = $lokasi->nama_kavling ?? '-';
        $tipe            = $kavling->tipe_bangunan ?? '-';
        $blokNomor       = $kavling->kode_kavling ?? '-';
        $rumahId         = $kavling->id_rumah_sikumbang ?? '-';
        $kotaTtd         = $lokasi->kota_penandatangan ?? '-';
        $tanggal         = $pembayaran->tanggal ? Carbon::parse($pembayaran->tanggal)->translatedFormat('d F Y') : '-';
        $jenisPembayaran = $gantiNama->keterangan_ganti ?? 'Cicilan Pribadi';
        $metodeBayar     = $pembayaran->metode_bayar ?? 'CASH';

        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetTitle('Kwitansi');
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        $pdf->SetMargins(20, 0, 0);
        $pdf->SetAutoPageBreak(false);
        $pdf->AddPage();
        $pdf->SetTextColor(0, 0, 0);

        $pdf->Ln(40);

        $pdf->SetFont('Helvetica', 'BU', 16);
        $pdf->Cell(170, 6, 'TANDA TERIMA', 0, 1, 'C');

        $pdf->SetFont('Helvetica', 'I', 12);
        $pdf->Cell(170, 6, $noKwitansi, 0, 1, 'C');

        $pdf->SetFont('Helvetica', '', 10);
        $pdf->Cell(100, 5, 'Sudah diterima dari : ', 0, 1, 'L');

        $pdf->Cell(20, 5, '', 0, 0, 'L');
        $pdf->Cell(25, 5, 'Nama', 0, 0, 'L');
        $pdf->Cell(60, 5, ' : ' . $nama, 0, 1, 'L');

        $pdf->Cell(20, 5, '', 0, 0, 'L');
        $pdf->Cell(25, 5, 'Alamat', 0, 0, 'L');
        $pdf->Cell(100, 5, ' : ' . $alamat, 0, 1, 'L');

        $pdf->Ln(3);
        $pdf->Cell(20, 5, 'Uang sejumlah Rp. ' . number_format($jumlah, 0, ',', '.') . ' (' . $terbilang . ')', 0, 1, 'L');
        $pdf->Cell(20, 5, 'Untuk pembayaran ' . $jenisPembayaran . ' : ', 0, 1, 'L');

        $pdf->Ln(1);
        $pdf->Cell(20, 4, '', 0, 0, 'L');
        $pdf->Cell(25, 4, 'Perumahan', 0, 0, 'L');
        $pdf->Cell(60, 4, ' : ' . $namaKavling, 0, 1, 'L');

        $pdf->Cell(20, 4, '', 0, 0, 'L');
        $pdf->Cell(25, 4, 'Type', 0, 0, 'L');
        $pdf->Cell(60, 4, ' : ' . $tipe, 0, 1, 'L');

        $pdf->Cell(20, 4, '', 0, 0, 'L');
        $pdf->Cell(25, 4, 'Blok / Nomor', 0, 0, 'L');
        $pdf->Cell(60, 4, ' : ' . $blokNomor, 0, 1, 'L');

        $pdf->Cell(20, 4, '', 0, 0, 'L');
        $pdf->Cell(25, 4, 'Rumah ID', 0, 0, 'L');
        $pdf->Cell(60, 4, ' : ' . $rumahId, 0, 1, 'L');

        // $pdf->Cell(20, 4, '', 0, 0, 'L');
        // $pdf->Cell(25, 4, 'Harga Jual', 0, 0, 'L');
        // $pdf->Cell(60, 4, ' : Rp. ' . number_format($hargaJual, 0, ',', '.'), 0, 1, 'L');

        $pdf->SetFont('Helvetica', 'B', 8.5);
        $pdf->Cell(20, 4, '', 0, 1, 'L');
        $pdf->Cell(20, 4, '', 0, 0, 'L');
        $pdf->Cell(100, 4, '', 0, 0, 'L');
        $pdf->Cell(55, 4, $kotaTtd . ', ' . $tanggal, 0, 1, 'C');

        $pdf->Ln(2);

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

        return response($pdf->Output('kwitansi.pdf', 'I'), 200)
            ->header('Content-Type', 'application/pdf');
    }

    private function bulanRomawi($bulan)
    {
        $romawi = [
            1  => 'I',
            2  => 'II',
            3  => 'III',
            4  => 'IV',
            5  => 'V',
            6  => 'VI',
            7  => 'VII',
            8  => 'VIII',
            9  => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII',
        ];

        return $romawi[(int) $bulan] ?? '';
    }

    private function terbilang($angka)
    {
        $angka = abs($angka);
        $baca  = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
        $hasil = '';

        if ($angka < 12) {
            $hasil = ' ' . $baca[$angka];
        } elseif ($angka < 20) {
            $hasil = $this->terbilang($angka - 10) . ' Belas';
        } elseif ($angka < 100) {
            $hasil = $this->terbilang($angka / 10) . ' Puluh' . $this->terbilang($angka % 10);
        } elseif ($angka < 200) {
            $hasil = ' Seratus' . $this->terbilang($angka - 100);
        } elseif ($angka < 1000) {
            $hasil = $this->terbilang($angka / 100) . ' Ratus' . $this->terbilang($angka % 100);
        } elseif ($angka < 2000) {
            $hasil = ' Seribu' . $this->terbilang($angka - 1000);
        } elseif ($angka < 1000000) {
            $hasil = $this->terbilang($angka / 1000) . ' Ribu' . $this->terbilang($angka % 1000);
        } elseif ($angka < 1000000000) {
            $hasil = $this->terbilang($angka / 1000000) . ' Juta' . $this->terbilang($angka % 1000000);
        }

        return trim($hasil);
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $gantiNama = GantiNama::findOrFail($id);

            $customerLama = ArsipNasabah::where(
                'id_customer',
                $gantiNama->id_customer_lama
            )->firstOrFail();

            $customerBaru = Customer::findOrFail(
                $gantiNama->id_customer_baru
            );

            $customerBaru->update([
                'nama_lengkap'      => $customerLama->nama_lengkap,
                'kode_customer'     => $customerLama->kode_customer,
                'no_ktp'            => $customerLama->no_ktp,
                'tempat_lahir'      => $customerLama->tempat_lahir,
                'tgl_lahir'         => $customerLama->tgl_lahir,
                'jenis_kelamin'     => $customerLama->jenis_kelamin,
                'no_telp'           => $customerLama->no_wa,
                'alamat'            => $customerLama->alamat,
                'alamat_domisili'   => $customerLama->alamat_domisili,
                'pekerjaan'         => $customerLama->pekerjaan,
                'id_marketing'      => $customerLama->id_marketing,
                'id_status_progres' => $customerLama->id_status_progres,
                'no_ktp_p'          => $customerLama->no_ktp_p,
                'id_lokasi'         => $customerLama->id_lokasi,
                'id_kavling'        => $customerLama->id_kavling,
                'id_freelance'      => $customerLama->id_freelance,
                'tgl_terima'        => $customerLama->tgl_terima,
            ]);

            if (class_exists(Tagihan::class)) {
                Tagihan::where(
                    'nasabah',
                    $customerBaru->id_customer
                )->update([
                    'nasabah' => $customerLama->id_customer,
                ]);
            }

            if (class_exists(Pembayaran::class)) {
                Pembayaran::where(
                    'id_customer',
                    $customerBaru->id_customer
                )->update([
                    'id_customer' => $customerLama->id_customer,
                ]);

                Pembayaran::where(
                    'id_customer',
                    $customerLama->id_customer
                )->where(
                    'id_jenis_pembayaran',
                    12
                )->delete();
            }

            if (class_exists(Wawancara::class)) {
                Wawancara::where(
                    'id_customer',
                    $customerBaru->id_customer
                )->delete();
            }

            $gantiNama->delete();

            ArsipNasabah::where(
                'id_customer',
                $customerLama->id_customer
            )->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data ganti nama berhasil dihapus.',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $th->getMessage(),
            ], 500);
        }
    }
}
