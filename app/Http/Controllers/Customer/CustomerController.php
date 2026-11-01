<?php
namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Pengaturan\HakAksesController;
use App\Models\ArsipNasabah;
use App\Models\BankModel;
use App\Models\Customer;
use App\Models\Freelance;
use App\Models\KavlingPeta;
use App\Models\LokasiKavling;
use App\Models\Marketing;
use App\Models\PengajuanBerkasLegal;
use App\Models\ProgresListPenjualan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\TemplateProcessor;
use TCPDF;
use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $permissions = HakAksesController::getUserPermissions();
        Carbon::setLocale('id');

        if ($request->ajax()) {
            $program = Customer::with(['marketing', 'freelance', 'lokasi', 'kavling', 'progres']);

            return DataTables::of($program)
                ->addIndexColumn()

                ->editColumn('tgl_terima', function ($row) {
                    $tgl  = $row->tgl_terima ? Carbon::parse($row->tgl_terima)->translatedFormat('d F Y') : '-';
                    $kode = $row->kode_customer ? '<strong>' . $row->kode_customer . '</strong>' : '';

                    $jenisPembelian = $row->jenis_pembelian
                        ? '<div><small><strong>' . strtoupper($row->jenis_pembelian) . '</strong></small></div>'
                        : '';

                    $btn = '<a href="' . route('subsidi.cetak', $row->id_customer) . '" target="_blank" class="btn btn-warning btn-xs mt-1">
                                Cetak Form Subsidi
                            </a>';

                    return "$tgl<br>$kode<br>$jenisPembelian$btn";
                })

                ->editColumn('id_marketing', function ($row) {
                    $namaMarketing = $row->marketing->nama_marketing ?? '<span class="badge bg-danger"> ' . 'None Marketing' . '</span>';
                    $namaFreelance = $row->freelance->nama_freelance ?? null;

                    $freelanceBadge = $namaFreelance
                        ? '<span class="badge bg-info"> ' . $namaFreelance . '</span>'
                        : '';

                    return $namaMarketing . ($freelanceBadge ? '<br>' . $freelanceBadge : '');
                })
                ->filterColumn('id_lokasi', function ($query, $keyword) {
                    $query->whereHas('lokasi', function ($q) use ($keyword) {
                        $q->where('nama_kavling', 'like', "%{$keyword}%");
                    })->orWhereHas('kavling', function ($q) use ($keyword) {
                        $q->where('kode_kavling', 'like', "%{$keyword}%");
                    });
                })
                ->editColumn('id_lokasi', function ($row) {
                    $namaLokasi  = $row->lokasi->nama_kavling ?? '-';
                    $kodeKavling = $row->kavling->kode_kavling ?? '-';
                    return '<strong>' . $namaLokasi . '</strong><br> ' . $kodeKavling;
                })

                ->editColumn('id_status_progres', function ($row) {
                    $status      = $row->progres->status_progres ?? '-';
                    $ketCashback = $row->progres->ket_cashback ?? '';

                    $badgeColors = [
                        'BF'           => 'warning',
                        'WAWANCARA'    => 'secondary',
                        'SP3K'         => 'success',
                        'AKAD'         => 'info',
                        'SERAH TERIMA' => 'dark',
                    ];

                    if (array_key_exists($status, $badgeColors)) {
                        $statusDisplay = '<span class="badge bg-' . $badgeColors[$status] . '">' . $status . '</span>';
                    } else {
                        $statusDisplay = $status;
                    }

                    $cashbackText = $ketCashback ? '<br><small>' . $ketCashback . '</small>' : '';

                    return $statusDisplay . $cashbackText;
                })

                ->editColumn('nama_lengkap', function ($row) {
                    $nama = '<strong>' . $row->nama_lengkap . '</strong>';
                    $wa   = $row->no_wa ?? '-';
                    $ktp  = $row->no_ktp ? '<span class="badge bg-info">NIK: ' . $row->no_ktp . '</span>' : '';
                    return "$nama<br>$wa<br>$ktp";
                })

                ->addColumn('action', function ($row) use ($permissions): string {
                    $editUrl   = route('customer.edit', $row->id_customer);
                    $deleteUrl = route('customer.destroy', $row->id_customer);
                    $btn       = '<div class="text-center">';

                    if ($permissions['edit']) {
                        $btn .= '<button class="btn btn-primary btn-sm" data-id="' . e($row->id) . '"
                         data-url="' . e($editUrl) . '" data-toggle="modal" data-target="#modalForm" id="edit-button">
                         Edit
                     </button>';
                    }

                    if ($permissions['hapus']) {
                        $btn .= '<form action="' . e($deleteUrl) . '" method="POST" style="display:inline;">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="delete-button btn btn-danger btn-sm">
                                Hapus
                            </button>
                         </form>';
                    }

                    $btn .= '</div>';
                    return $btn;
                })

                ->rawColumns([
                    'tgl_terima',
                    'nama_lengkap',
                    'id_marketing',
                    'id_lokasi',
                    'id_status_progres',
                    'action',
                ])
                ->make(true);
        }

        $marketing = Marketing::all();
        $freelance = Freelance::all();
        $bank      = BankModel::all();
        $progres   = ProgresListPenjualan::all();
        $lokasi    = LokasiKavling::all();

        return view('admin.customer_induk.customer.index', compact('permissions', 'marketing', 'freelance', 'lokasi', 'progres', 'bank'));
    }

    // public function detail($id_customer)
    // {
    //     $data = Customer::with([
    //         'lokasi',
    //         'kavling',
    //         'marketing',
    //         'freelance',
    //         'progres'
    //     ])->findOrFail($id_customer);

    //     $permissions = HakAksesController::getUserPermissions();

    //     return view('admin.Customer.detail', compact('data', 'permissions'));
    // }

    public function getKavling($idLokasi)
    {
        $data = KavlingPeta::where('id_lokasi', $idLokasi)
            ->select('id_kavling', 'kode_kavling')
            ->get();

        return response()->json($data);
    }

    public function getHargaKavling($id_kavling)
    {
        $data      = KavlingPeta::findOrFail($id_kavling);
        $formatted = 'Rp. ' . number_format($data->hrg_jual, 0, ',', '.');

        return response()->json([
            'hrg_jual'  => $data->hrg_jual,
            'formatted' => $formatted,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tgl_terima'           => 'required|date',
            'id_lokasi'            => 'required',
            'id_kavling'           => 'required',
            'id_status_progres'    => 'required',
            'nama_lengkap'         => 'required',
            'no_ktp'               => 'required',
            'no_ktp_p'             => 'nullable',
            'jenis_kelamin'        => 'nullable',
            'tempat_lahir'         => 'nullable',
            'tgl_lahir'            => 'nullable|date',
            'warga_negara'         => 'nullable',
            'alamat'               => 'required',
            'alamat_domisili'      => 'nullable',
            'no_telp'              => 'required',
            'email'                => 'nullable|email',
            'npwp'                 => 'nullable',
            'pekerjaan'            => 'nullable',
            'penghasilan'          => 'nullable',
            'nama_saudara'         => 'nullable',
            'no_telp_saudara'      => 'nullable',
            'kode_token'           => 'nullable',
            'id_bank'              => 'nullable',
            'id_marketing'         => 'nullable',
            'id_admin_pemberkasan' => 'nullable',
            'keterangan_stt'       => 'nullable',
            'ket_reject'           => 'nullable',
            'id_freelance'         => 'nullable|integer',
            'hrg_rumah'            => 'required|numeric',
            'cashback'             => 'nullable|numeric',
            'harga_bersih'         => 'nullable|numeric',
            'referal_fee'          => 'nullable|numeric',
            'jenis_pembelian'      => 'required',
            'pembayaran_booking'   => 'nullable|numeric',
            'tgl_batas_booking'    => 'nullable|date',
            'ket_booking'          => 'nullable',
            'diskon_cash'          => 'nullable|numeric',
            'pembayaran_cash'      => 'nullable|numeric',
            'sisa_bayar_ajb'       => 'nullable|numeric',
            'an_surat_cash'        => 'nullable',
            'dp_cash_b'            => 'nullable|numeric',
            'termin_x_cash_b'      => 'nullable',
            'dp_kredit'            => 'nullable|numeric',
            'lama_cicilan_kredit'  => 'nullable|numeric',
            'cicilan_kredit'       => 'nullable|numeric',
            'tgl_tempo_cicilan_1'  => 'nullable|date',
            'an_surat_kredit'      => 'nullable',
        ], [
            'tgl_terima.required'         => 'Tanggal Terima wajib diisi!',
            'tgl_terima.date'             => 'Tanggal Terima harus berupa tanggal yang valid!',
            'id_lokasi.required'          => 'Lokasi wajib diisi!',
            'id_kavling.required'         => 'Kavling wajib diisi!',
            'id_status_progres.required'  => 'Status progres wajib diisi!',
            'nama_lengkap.required'       => 'Nama lengkap wajib diisi!',
            'no_ktp.required'             => 'Nomor KTP wajib diisi!',
            'tgl_lahir.date'              => 'Tanggal Lahir harus berupa tanggal yang valid!',
            'alamat.required'             => 'Alamat wajib diisi!',
            'no_telp.required'            => 'Nomor telepon wajib diisi!',
            'no_wa.numeric'               => 'Nomor WhatsApp harus berupa angka!',
            'email.email'                 => 'Email harus berupa alamat email yang valid!',
            'id_freelance.integer'        => 'Freelance harus berupa angka bulat!',
            'hrg_rumah.required'          => 'Harga rumah wajib diisi!',
            'hrg_rumah.numeric'           => 'Harga rumah harus berupa angka!',
            'cashback.numeric'            => 'Cashback harus berupa angka!',
            'harga_bersih.numeric'        => 'Harga bersih harus berupa angka!',
            'referal_fee.numeric'         => 'Referral fee harus berupa angka!',
            'jenis_pembelian.required'    => 'Jenis pembelian wajib diisi!',
            'pembayaran_booking.numeric'  => 'Pembayaran booking harus berupa angka!',
            'tgl_batas_booking.date'      => 'Tanggal batas booking harus berupa tanggal yang valid!',
            'diskon_cash.numeric'         => 'Diskon cash harus berupa angka!',
            'pembayaran_cash.numeric'     => 'Pembayaran cash harus berupa angka!',
            'sisa_bayar_ajb.numeric'      => 'Sisa bayar AJB harus berupa angka!',
            'dp_cash_b.numeric'           => 'DP cash harus berupa angka!',
            'dp_kredit.numeric'           => 'DP kredit harus berupa angka!',
            'lama_cicilan_kredit.numeric' => 'Lama cicilan kredit harus berupa angka!',
            'cicilan_kredit.numeric'      => 'Cicilan kredit harus berupa angka!',
            'tgl_tempo_cicilan_1.date'    => 'Tanggal tempo cicilan pertama harus berupa tanggal yang valid!',
        ]);

        $prefix = DB::table('lokasi_kavling')
            ->where('id_lokasi', $request->id_lokasi)
            ->value('nama_singkat');

        $prefix = $prefix ?: 'GDI';

        $latest = Customer::where('kode_customer', 'LIKE', $prefix . '-%')
            ->latest('kode_customer')
            ->first();

        if ($latest && preg_match('/' . preg_quote($prefix, '/') . '-(\d+)/', $latest->kode_customer, $match)) {
            $number  = (int) $match[1] + 1;
            $newKode = $prefix . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
        } else {
            $newKode = $prefix . '-0001';
        }

        $data = [
            'kode_customer'        => $newKode,
            'tgl_terima'           => $request->tgl_terima,
            'id_lokasi'            => $request->id_lokasi,
            'id_kavling'           => $request->id_kavling,
            'id_status_progres'    => $request->id_status_progres,
            'nama_lengkap'         => $request->nama_lengkap,
            'no_ktp'               => $request->no_ktp,
            'no_ktp_p'             => $request->no_ktp_p ?? '',
            'no_kk'                => $request->no_kk ?? '',
            'jenis_kelamin'        => $request->jenis_kelamin ?? '',
            'tempat_lahir'         => $request->tempat_lahir ?? '',
            'tgl_lahir'            => $request->tgl_lahir ?? null,
            'warga_negara'         => $request->warga_negara ?? '',
            'alamat'               => $request->alamat,
            'alamat_domisili'      => $request->alamat_domisili ?? '',
            'no_telp'              => $request->no_telp ?? '',
            'no_wa'                => $request->no_wa ?? '',
            'email'                => $request->email ?? '',
            'npwp'                 => $request->npwp ?? null,
            'pekerjaan'            => $request->pekerjaan ?? '',
            'penghasilan'          => $request->penghasilan ?? 0,
            'nama_saudara'         => $request->nama_saudara ?? '',
            'no_telp_saudara'      => $request->no_telp_saudara ?? '',
            'ket_cashback'         => $request->ket_cashback ?? '',
            'kode_token'           => $request->kode_token ?? '',
            'id_bank'              => $request->id_bank ?? 0,
            'id_marketing'         => $request->id_marketing ?? 0,
            'id_admin_pemberkasan' => $request->id_admin_pemberkasan ?? 0,
            'keterangan_stt'       => $request->keterangan_stt ?? '',
            'ket_reject'           => $request->ket_reject ?? '',
            'id_freelance'         => $request->id_freelance ?? 0,
            'hrg_rumah'            => $request->hrg_rumah,
            'cashback'             => $request->cashback ?? 0,
            'harga_bersih'         => $request->harga_bersih ?? 0,
            'referal_fee'          => $request->referal_fee ?? 0,
            'jenis_pembelian'      => $request->jenis_pembelian,
            'pembayaran_booking'   => $request->pembayaran_booking ?? 0,
            'tgl_batas_booking'    => $request->tgl_batas_booking ?? null,
            'ket_booking'          => $request->ket_booking ?? '',
            'diskon_cash'          => $request->diskon_cash ?? 0,
            'pembayaran_cash'      => $request->pembayaran_cash ?? 0,
            'sisa_bayar_ajb'       => $request->sisa_bayar_ajb ?? 0,
            'an_surat_cash'        => $request->an_surat_cash ?? '',
            'dp_cash_b'            => $request->dp_cash_b ?? 0,
            'termin_x_cash_b'      => $request->termin_x_cash_b ?? 0,
            'dp_kredit'            => $request->dp_kredit ?? 0,
            'lama_cicilan_kredit'  => $request->lama_cicilan_kredit ?? 0,
            'cicilan_kredit'       => $request->cicilan_kredit ?? 0,
            'tgl_tempo_cicilan_1'  => $request->tgl_tempo_cicilan_1 ?? null,
            'an_surat_kredit'      => $request->an_surat_kredit ?? '',
        ];

        $customer = Customer::create($data);

        PengajuanBerkasLegal::create([
            'id_customer' => $customer->id_customer,
        ]);

        return response()->json(['status' => 'success']);
    }

    public function edit($id)
    {
        $list = Customer::findOrFail($id);
        $user = Auth::user();

        return response()->json([
            'status' => 'success',
            'data'   => $list,
            'user'   => $user,
        ]);
    }

    public function update(Request $request, $id)
    {
        $data = Customer::findOrFail($id);

        $request->validate([
            'tgl_terima'           => 'required|date',
            'id_lokasi'            => 'required',
            'id_kavling'           => 'required',
            'id_status_progres'    => 'required',
            'nama_lengkap'         => 'required',
            'no_ktp'               => 'required',
            'no_ktp_p'             => 'nullable',
            'jenis_kelamin'        => 'nullable',
            'tempat_lahir'         => 'nullable',
            'tgl_lahir'            => 'nullable|date',
            'warga_negara'         => 'nullable',
            'alamat'               => 'required',
            'alamat_domisili'      => 'nullable',
            'no_telp'              => 'required',
            'email'                => 'nullable|email',
            'npwp'                 => 'nullable',
            'pekerjaan'            => 'nullable',
            'penghasilan'          => 'nullable',
            'nama_saudara'         => 'nullable',
            'no_telp_saudara'      => 'nullable',
            'kode_token'           => 'nullable',
            'id_bank'              => 'nullable',
            'id_marketing'         => 'nullable',
            'id_admin_pemberkasan' => 'nullable',
            'keterangan_stt'       => 'nullable',
            'ket_reject'           => 'nullable',
            'id_freelance'         => 'nullable|integer',
            'hrg_rumah'            => 'required|numeric',
            'cashback'             => 'nullable|numeric',
            'harga_bersih'         => 'nullable|numeric',
            'referal_fee'          => 'nullable|numeric',
            'jenis_pembelian'      => 'required',
            'pembayaran_booking'   => 'nullable|numeric',
            'tgl_batas_booking'    => 'nullable|date',
            'ket_booking'          => 'nullable',
            'diskon_cash'          => 'nullable|numeric',
            'pembayaran_cash'      => 'nullable|numeric',
            'sisa_bayar_ajb'       => 'nullable|numeric',
            'an_surat_cash'        => 'nullable',
            'dp_cash_b'            => 'nullable|numeric',
            'termin_x_cash_b'      => 'nullable',
            'dp_kredit'            => 'nullable|numeric',
            'lama_cicilan_kredit'  => 'nullable|numeric',
            'cicilan_kredit'       => 'nullable|numeric',
            'tgl_tempo_cicilan_1'  => 'nullable|date',
            'an_surat_kredit'      => 'nullable',
        ], [
            'tgl_terima.required'         => 'Tanggal Terima wajib diisi!',
            'tgl_terima.date'             => 'Tanggal Terima harus berupa tanggal yang valid!',
            'id_lokasi.required'          => 'Lokasi wajib diisi!',
            'id_kavling.required'         => 'Kavling wajib diisi!',
            'id_status_progres.required'  => 'Status progres wajib diisi!',
            'nama_lengkap.required'       => 'Nama lengkap wajib diisi!',
            'no_ktp.required'             => 'Nomor KTP wajib diisi!',
            'tgl_lahir.date'              => 'Tanggal Lahir harus berupa tanggal yang valid!',
            'alamat.required'             => 'Alamat wajib diisi!',
            'no_telp.required'            => 'Nomor telepon wajib diisi!',
            'no_wa.numeric'               => 'Nomor WhatsApp harus berupa angka!',
            'email.email'                 => 'Email harus berupa alamat email yang valid!',
            'id_freelance.integer'        => 'Freelance harus berupa angka bulat!',
            'hrg_rumah.required'          => 'Harga rumah wajib diisi!',
            'hrg_rumah.numeric'           => 'Harga rumah harus berupa angka!',
            'cashback.numeric'            => 'Cashback harus berupa angka!',
            'harga_bersih.numeric'        => 'Harga bersih harus berupa angka!',
            'referal_fee.numeric'         => 'Referral fee harus berupa angka!',
            'jenis_pembelian.required'    => 'Jenis pembelian wajib diisi!',
            'pembayaran_booking.numeric'  => 'Pembayaran booking harus berupa angka!',
            'tgl_batas_booking.date'      => 'Tanggal batas booking harus berupa tanggal yang valid!',
            'diskon_cash.numeric'         => 'Diskon cash harus berupa angka!',
            'pembayaran_cash.numeric'     => 'Pembayaran cash harus berupa angka!',
            'sisa_bayar_ajb.numeric'      => 'Sisa bayar AJB harus berupa angka!',
            'dp_cash_b.numeric'           => 'DP cash harus berupa angka!',
            'dp_kredit.numeric'           => 'DP kredit harus berupa angka!',
            'lama_cicilan_kredit.numeric' => 'Lama cicilan kredit harus berupa angka!',
            'cicilan_kredit.numeric'      => 'Cicilan kredit harus berupa angka!',
            'tgl_tempo_cicilan_1.date'    => 'Tanggal tempo cicilan pertama harus berupa tanggal yang valid!',
        ]);

        $db = [
            'tgl_terima'           => $request->tgl_terima,
            'id_lokasi'            => $request->id_lokasi,
            'id_kavling'           => $request->id_kavling,
            'id_status_progres'    => $request->id_status_progres,
            'nama_lengkap'         => $request->nama_lengkap,
            'no_ktp'               => $request->no_ktp,
            'no_ktp_p'             => $request->no_ktp_p ?? '',
            'no_kk'                => $request->no_kk ?? '',
            'jenis_kelamin'        => $request->jenis_kelamin ?? '',
            'tempat_lahir'         => $request->tempat_lahir ?? '',
            'tgl_lahir'            => $request->tgl_lahir ?? null,
            'warga_negara'         => $request->warga_negara ?? '',
            'alamat'               => $request->alamat,
            'alamat_domisili'      => $request->alamat_domisili ?? '',
            'no_telp'              => $request->no_telp ?? '',
            'no_wa'                => $request->no_telp ?? '',
            'email'                => $request->email ?? '',
            'npwp'                 => $request->npwp ?? '',
            'pekerjaan'            => $request->pekerjaan ?? '',
            'penghasilan'          => $request->penghasilan ?? 0,
            'nama_saudara'         => $request->nama_saudara ?? '',
            'no_telp_saudara'      => $request->no_telp_saudara ?? '',
            'kode_token'           => $request->kode_token ?? '',
            'id_bank'              => $request->id_bank ?? 0,
            'id_marketing'         => $request->id_marketing ?? 0,
            'id_admin_pemberkasan' => $request->id_admin_pemberkasan ?? 0,
            'keterangan_stt'       => $request->keterangan_stt ?? '',
            'ket_reject'           => $request->ket_reject ?? '',
            'id_freelance'         => $request->id_freelance ?? 0,
            'hrg_rumah'            => $request->hrg_rumah,
            'cashback'             => $request->cashback ?? 0,
            'harga_bersih'         => $request->harga_bersih ?? 0,
            'referal_fee'          => $request->referal_fee ?? 0,
            'jenis_pembelian'      => $request->jenis_pembelian,
            'pembayaran_booking'   => $request->pembayaran_booking ?? 0,
            'tgl_batas_booking'    => $request->tgl_batas_booking ?? null,
            'ket_booking'          => $request->ket_booking ?? '',
            'diskon_cash'          => $request->diskon_cash ?? 0,
            'pembayaran_cash'      => $request->pembayaran_cash ?? 0,
            'sisa_bayar_ajb'       => $request->sisa_bayar_ajb ?? 0,
            'an_surat_cash'        => $request->an_surat_cash ?? '',
            'dp_cash_b'            => $request->dp_cash_b ?? 0,
            'termin_x_cash_b'      => $request->termin_x_cash_b ?? 0,
            'dp_kredit'            => $request->dp_kredit ?? 0,
            'lama_cicilan_kredit'  => $request->lama_cicilan_kredit ?? 0,
            'cicilan_kredit'       => $request->cicilan_kredit ?? 0,
            'tgl_tempo_cicilan_1'  => $request->tgl_tempo_cicilan_1 ?? null,
            'an_surat_kredit'      => $request->an_surat_kredit ?? '',
        ];

        $data->update($db);

        return response()->json(['status' => 'success']);
    }

    public function destroy($id)
    {
        $data = Customer::findOrFail($id);

        ArsipNasabah::create([
            'id_customer'       => $data->id_customer,
            'tgl_terima'        => $data->tgl_terima,
            'id_progres'        => $data->id_status_progres,
            'id_lokasi'         => $data->id_lokasi,
            'id_kavling'        => $data->id_kavling,
            'id_status_progres' => $data->id_status_progres,
            'kode_customer'     => $data->kode_customer,
            'nama_lengkap'      => $data->nama_lengkap,
            'no_ktp'            => $data->no_ktp,
            'no_ktp_p'          => $data->no_ktp_p,
            'jenis_kelamin'     => $data->jenis_kelamin,
            'tempat_lahir'      => $data->tempat_lahir,
            'tgl_lahir'         => $data->tgl_lahir,
            'alamat'            => $data->alamat,
            'alamat_domisili'   => $data->alamat_domisili,
            'no_telp'           => $data->no_telp,
            'no_wa'             => $data->no_wa,
            'pekerjaan'         => $data->pekerjaan,
            'id_marketing'      => $data->id_marketing,
            'ket_reject'        => $data->ket_reject,
            'id_freelance'      => $data->id_freelance,
        ]);

        $data->delete();

        return response()->json(['status' => 'success']);
    }

    public function cetakData(Request $request)
    {
        try {
            $query = Customer::with([
                'marketing',
                'lokasi',
                'kavling',
                'progres',
            ]);

            if ($request->lokasi) {
                $query->where('id_lokasi', $request->lokasi);
            }

            $data = $query->get();

            if ($request->tipe == 1) {
                return $this->cetakExcel($data);
            } else {
                return $this->cetakPdf($data, $request->lokasi);
            }

        } catch (\Exception $e) {
            Log::error('Gagal cetak data customer: ' . $e->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan saat mencetak data. Silakan cek log server.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function cetakPdf($data, $lokasi = null)
    {
        $namaPerusahaan = DB::table('konfigurasi')->value('nama_perusahaan');
        $namaKavling    = '';

        if ($lokasi) {
            $namaKavling = DB::table('lokasi_kavling')
                ->where('id_lokasi', $lokasi)
                ->value('nama_kavling') ?? '';
        }

        $pdf = new TCPDF('P', 'mm', 'A4');
        $pdf->SetTitle('Data Customer');
        $pdf->AddPage();

        $logoPath = public_path('assets/img/header.png');
        if (file_exists($logoPath)) {
            $pdf->Image($logoPath, 20, 5, 30);
        }

        $pdf->SetFont('Times', 'B', 16);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetXY(85, 10);
        $pdf->Cell(0, 7, strtoupper($namaPerusahaan), 0, 2, 'L');

        $pdf->SetFont('', 'B', 12);
        $pdf->SetXY(95, 15);
        $pdf->Cell(0, 7, strtoupper($namaKavling), 0, 2, 'L');

        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetLineWidth(0.7);
        $pdf->Line(10, 30, 200, 30);

        $pdf->SetLineWidth(0.3);
        $pdf->Line(10, 31, 200, 31);

        $pdf->Ln(10);
        $pdf->SetFont('Times', 'B', 10);
        $pdf->SetTextColor(218, 0, 0);
        $pdf->Cell(190, 8, 'DATA CUSTOMER', 0, 1, 'C');
        $pdf->Ln(3);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Times', '', 9);

        $pdf->Ln(5);
        $pdf->SetFont('Times', 'B', 9);
        $pdf->SetFillColor(252, 203, 53);

        $pdf->Cell(10, 7, 'NO', 1, 0, 'C', true);
        $pdf->Cell(45, 7, 'PEMOHON', 1, 0, 'L', true);
        $pdf->Cell(15, 7, 'UNIT', 1, 0, 'C', true);
        $pdf->Cell(25, 7, 'NO. TELP', 1, 0, 'C', true);
        $pdf->Cell(40, 7, 'MARKETING', 1, 0, 'L', true);
        $pdf->Cell(25, 7, 'BANK', 1, 0, 'C', true);
        $pdf->Cell(30, 7, 'STATUS', 1, 1, 'C', true);

        $pdf->SetFont('Times', '', 8);
        $no = 1;

        if ($data->count() > 0) {
            foreach ($data as $d) {

                $akadDetail = $d->akadDetail?->where('status', 2)->first();
                $statusText = $d->progres->status_progres ?? '-';

                if (($d->id_status_progres ?? null) == 3 && $akadDetail && $akadDetail->akad) {
                    $statusText .= ' (' . date('d/m/Y', strtotime($akadDetail->akad->tgl_akad)) . ')';
                }

                $pdf->Cell(10, 7, $no++, 1, 0, 'C');
                $pdf->Cell(45, 7, $d->nama_lengkap, 1, 0, 'L');
                $pdf->Cell(15, 7, ($d->kavling->kode_kavling ?? '-'), 1, 0, 'C');
                $pdf->Cell(25, 7, (! empty($d->no_wa) ? $d->no_wa : ($d->no_telp ?? '-')), 1, 0, 'C');
                $pdf->Cell(40, 7, ($d->marketing->nama_marketing ?? '-'), 1, 0, 'L');
                $pdf->Cell(25, 7, ($d->wawancara?->wawancaraSp3k?->bank?->bank ?? $d->wawancara?->bank?->bank ?? '-'), 1, 0, 'C');
                $pdf->Cell(30, 7, $statusText, 1, 1, 'C');
            }
        } else {
            $pdf->Cell(190, 7, 'Tidak ada data ditemukan', 1, 1, 'C');
        }

        $pdf->Output();
        exit;
    }

    private function cetakExcel($data)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'LAPORAN DATA CUSTOMER');
        $sheet->mergeCells('A1:R1');

        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'fill'      => [
                'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2E86AB'],
            ],
        ]);

        $sheet->setCellValue('A2', 'Tanggal Export: ' . date('d/m/Y H:i:s'));
        $sheet->mergeCells('A2:R2');
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['italic' => true, 'size' => 10],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);

        $headers = [
            'A4' => 'NO',
            'B4' => 'LOKASI',
            'C4' => 'BLOK/KAV',
            'D4' => 'LUAS TANAH',
            'E4' => 'LUAS BANGUNAN',
            'F4' => 'DAYA LISTRIK',
            'G4' => 'HARGA JUAL',
            'H4' => 'ID RUMAH',
            'I4' => 'NO SERTIFIKAT',
            'J4' => 'NO REKENING LISTRIK',
            'K4' => 'NO REKENING AIR',
            'L4' => 'NAMA LENGKAP',
            'M4' => 'NO KTP',
            'N4' => 'TEMPAT/TGL LAHIR',
            'O4' => 'ALAMAT KTP',
            'P4' => 'NO TELP',
            'Q4' => 'PEKERJAAN',
            'R4' => 'STATUS PENJUALAN',
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        $sheet->getStyle('A4:R4')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'fill'      => [
                'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4CAF50'],
            ],
            'borders'   => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color'       => ['rgb' => '000000'],
                ],
            ],
        ]);

        $row = 5;
        $no  = 1;

        foreach ($data as $d) {

            $akadDetail = $d->akadDetail?->where('status', 2)->first();
            $statusText = $d->progres->status_progres ?? '';

            if (($d->id_status_progres ?? null) == 3 && $akadDetail && $akadDetail->akad) {
                $statusText .= ' (' . date('d/m/Y', strtotime($akadDetail->akad->tgl_akad)) . ')';
            }

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $d->lokasi->nama_kavling ?? '');
            $sheet->setCellValue('C' . $row, $d->kavling->kode_kavling ?? '');
            $sheet->setCellValue('D' . $row, $d->kavling->luas_tanah ?? '');
            $sheet->setCellValue('E' . $row, $d->kavling->luas_bangunan ?? '');
            $sheet->setCellValue('F' . $row, $d->kavling->daya_listrik ?? '');
            $sheet->setCellValue('G' . $row, $d->kavling->hrg_jual ?? '');
            $sheet->setCellValue('H' . $row, $d->kavling->id_rumah_sikumbang ?? '');
            $sheet->setCellValue('I' . $row, $d->kavling->no_sertifikat ?? '');
            $sheet->setCellValue('J' . $row, $d->kavling->no_rekening_listrik ?? '');
            $sheet->setCellValue('K' . $row, $d->kavling->no_rekening_air ?? '');
            $sheet->setCellValue('L' . $row, $d->nama_lengkap ?? '');
            $sheet->setCellValue('M' . $row, $d->nik ?? '');
            $sheet->setCellValue('N' . $row, ($d->tempat_lahir ?? '') . ' / ' . ($d->tgl_lahir ? date('d/m/Y', strtotime($d->tgl_lahir)) : ''));
            $sheet->setCellValue('O' . $row, $d->alamat_ktp ?? '');
            $sheet->setCellValue('P' . $row, (! empty($d->no_wa) ? $d->no_wa : ($d->no_telp ?? '')));
            $sheet->setCellValue('Q' . $row, $d->pekerjaan ?? '');
            $sheet->setCellValue('R' . $row, $statusText);

            $fillColor = ($no % 2 == 0) ? 'F9F9F9' : 'FFFFFF';

            $sheet->getStyle('A' . $row . ':R' . $row)->applyFromArray([
                'fill'    => [
                    'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $fillColor],
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color'       => ['rgb' => 'CCCCCC'],
                    ],
                ],
            ]);

            $row++;
        }

        foreach (range('A', 'R') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->getRowDimension('1')->setRowHeight(30);
        $sheet->getRowDimension('4')->setRowHeight(25);

        $totalRow = $row;
        $sheet->setCellValue('A' . $totalRow, 'Total Data: ' . ($no - 1) . ' record');
        $sheet->mergeCells('A' . $totalRow . ':R' . $totalRow);
        $sheet->getStyle('A' . $totalRow)->applyFromArray([
            'font'      => ['bold' => true, 'italic' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'fill'      => [
                'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E3F2FD'],
            ],
        ]);

        $writer   = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'data_customer_' . date('Y-m-d_H-i-s') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type'  => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'no-cache, must-revalidate',
            'Pragma'        => 'no-cache',
            'Expires'       => '0',
        ]);
    }

    public function cetakFormSubsidi($id_customer)
    {
        $customer = Customer::with(['kavlingPeta.lokasi', 'lokasiKavling'])
            ->findOrFail($id_customer);

        $kavling = $customer->kavlingPeta;
        $lokasi  = $kavling?->lokasi ?? $customer->lokasiKavling;

        $templatePath      = public_path('templates/form_subsidi.docx');
        $templateProcessor = new TemplateProcessor($templatePath);

        $templateProcessor->setValue('tgl_sekarang', Carbon::now()->translatedFormat('d F Y'));

        // Data Nasabah
        $templateProcessor->setValue('nama_lengkap', $customer->nama_lengkap);
        $templateProcessor->setValue('id_kavling', $customer->id_kavling);
        $templateProcessor->setValue('jenis_kelamin', $customer->jenis_kelamin);
        $templateProcessor->setValue('tempat_lahir', $customer->tempat_lahir);
        $templateProcessor->setValue('tgl_lahir', Carbon::parse($customer->tgl_lahir)->translatedFormat('d F Y'));
        $templateProcessor->setValue('warga_negara', $customer->warga_negara);
        $templateProcessor->setValue('pekerjaan', $customer->pekerjaan);
        $templateProcessor->setValue('no_ktp', $customer->no_ktp);
        $templateProcessor->setValue('alamat', $customer->alamat);
        $templateProcessor->setValue('no_telp', $customer->no_wa);
        $templateProcessor->setValue('email', $customer->email);

        // Data Pasangan (optional)
        $templateProcessor->setValue('nama_lengkap_p', $customer->nama_lengkap_p ?? '-');
        $templateProcessor->setValue('no_ktp_p', $customer->no_ktp_p ?? '-');
        $templateProcessor->setValue('tanggal_lahir_p', $customer->tanggal_lahir_p
                ? Carbon::parse($customer->tanggal_lahir_p)->translatedFormat('d F Y') : '-');
        $templateProcessor->setValue('tempat_lahir_p', $customer->tempat_lahir_p ?? '-');
        $templateProcessor->setValue('alamat_p', $customer->alamat_p ?? '-');
        $templateProcessor->setValue('pekerjaan_p', $customer->pekerjaan_p ?? '-');

        // Developer & Kavling
        $templateProcessor->setValue('nama_perumahan', $lokasi->nama_kavling ?? '-');
        $templateProcessor->setValue('no_rumah', $kavling->kode_kavling ?? '-');
        $templateProcessor->setValue('nama_developer', $lokasi->nama_perusahaan ?? '-');
        $templateProcessor->setValue('alamat_perusahaan', $lokasi->alamat_perusahaan ?? '-');
        $templateProcessor->setValue('form_nama_penanda_tangan', $lokasi->nama_penandatangan ?? '-');
        $templateProcessor->setValue('form_no_ktp', $lokasi->form_no_ktp ?? '-');
        $templateProcessor->setValue('form_jabatan', $lokasi->form_jabatan ?? '-');

        // Detail Rumah
        $templateProcessor->setValue('lokasi_rumah', $lokasi->nama_kavling ?? '-');
        $templateProcessor->setValue('kode_kavling', $kavling->kode_kavling ?? '-');
        $templateProcessor->setValue('tipe_rumah', $kavling->tipe_bangunan ?? '-');
        $templateProcessor->setValue('luas_tanah', $kavling->luas_tanah ?? '-');
        $templateProcessor->setValue('luas_bangunan', $kavling->luas_bangunan ?? '-');
        $templateProcessor->setValue('daya_listrik', $kavling->daya_listrik ?? '-');
        $templateProcessor->setValue('harga_jual', number_format($kavling->hrg_jual ?? 0, 0, ',', '.'));
        $templateProcessor->setValue('harga_jual_terbilang', $this->terbilang($kavling->hrg_jual ?? 0));

        // Save file
        $fileName = 'subsidi_' . $id_customer . '_' . now()->format('Ymd_His') . '.docx';
        $savePath = storage_path('app/public/' . $fileName);
        $templateProcessor->saveAs($savePath);

        return response()->download($savePath)->deleteFileAfterSend(true);
    }
    private function terbilang($angka)
    {
        $f = new \NumberFormatter("id", \NumberFormatter::SPELLOUT);
        return ucwords($f->format($angka)) . ' Rupiah';
    }
}
