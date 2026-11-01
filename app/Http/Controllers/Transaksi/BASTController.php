<?php


namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;

use Carbon\Carbon;
use App\Models\BAST;
use App\Models\Customer;
use App\Models\KavlingPeta;
use Illuminate\Http\Request;
use App\Models\LokasiKavling;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Pengaturan\HakAksesController;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Str;

class BASTController extends Controller
{
    public function index(Request $request)
    {
        $permissions = HakAksesController::getUserPermissions();

        if ($request->ajax()) {
            $opd = BAST::orderBy('id_bast', 'desc');

            return DataTables::of($opd)
                ->addIndexColumn()
                ->editColumn('kode_kavling', function ($row) {
                    return $row->kode_kavling . '<br><small class="text-muted">' . $row->lokasi_rumah . '</small>';
                })
                ->addColumn('action', function ($row) {
                    $cetakUrl = route('bast.cetak', $row->id_customer);
                    $deleteUrl = route('bast.destroy', $row->id_bast);

                    $btn = '<div class="d-flex justify-content-center">';
                    $btn .= '<a href="' . e($cetakUrl) . '" target="_blank" class="btn btn-warning btn-sm ml-1">Cetak</a>';
                    $btn .= '<form action="' . e($deleteUrl) . '" method="POST" style="display:inline;">' . csrf_field() . method_field('DELETE') . '<button type="submit" class="delete-button btn btn-danger btn-sm ml-1">Hapus</button></form>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['kode_kavling', 'action'])
                ->make(true);
        }

        return view('admin.transaksi.bast.index', compact('permissions'));
    }

    public function generateNoBAST(Request $request)
    {
        $tanggalSekarang = Carbon::now('Asia/Jakarta');
        $bulanRomawi = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII'
        ];

        $lokasiRumah = $request->get('lokasi_rumah');

        $lokasi = LokasiKavling::where('nama_kavling', $lokasiRumah)->first();
        $namaSingkat = $lokasi ? $lokasi->nama_singkat : 'ADM';

        $lastBAST = BAST::where('lokasi_rumah', $lokasiRumah)
            ->orderByDesc('id_bast')
            ->first();

        if ($lastBAST && preg_match('/^(\d{3})\//', $lastBAST->no_bast, $matches)) {
            $lastNumber = intval($matches[1]);
            $nextNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '001';
        }

        $nextMonth = $bulanRomawi[intval($tanggalSekarang->format('n'))];

        $no_bast = $nextNumber . '/ADM/' . $namaSingkat . '/' . $nextMonth . '/' . $tanggalSekarang->format('Y');

        return response()->json(['no_bast' => $no_bast]);
    }

    public function create()
    {
        $tanggalSekarang = Carbon::now('Asia/Jakarta')->format('Y-m-d');

        $usedCustomerIds = BAST::pluck('id_customer')->toArray();

        $customers = Customer::whereNotIn('id_customer', $usedCustomerIds)
            ->get(['id_customer', 'nama_lengkap']);

        return view('admin.transaksi.bast.create', compact('tanggalSekarang', 'customers'));
    }

    public function getCustomerData($id_customer)
    {
        $customer = Customer::findOrFail($id_customer);

        $kavling = KavlingPeta::where('id_kavling', $customer->id_kavling)->first();
        $lokasi = LokasiKavling::where('id_lokasi', $customer->id_lokasi)->first();

        return response()->json([
            'no_ktp'         => $customer->no_ktp,
            'jenis_kelamin'  => $customer->jenis_kelamin,
            'tempat_lahir'   => $customer->tempat_lahir,
            'tanggal_lahir'  => $customer->tgl_lahir,
            'domisili'       => $customer->alamat_domisili,
            'alamat_ktp'     => $customer->alamat,
            'pekerjaan'      => $customer->pekerjaan,
            'no_npwp'      => $customer->npwp,
            'warga_negara'   => $customer->warga_negara,
            'no_telp'        => $customer->no_telp,
            'email'          => $customer->email,
            'lokasi_rumah'   => $lokasi->nama_kavling,
            'blok'           => $kavling->kode_kavling,
            'tipe_rumah'     => $kavling->tipe_bangunan,
            'luas_tanah'     => $kavling->luas_tanah,
            'luas_bangunan'  => $kavling->luas_bangunan,
            'daya_listrik'   => $kavling->daya_listrik,
            'harga_jual'     => $kavling->hrg_jual,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'hari' => 'required',
            'tanggal_bast' => 'required|date',
            'no_bast' => 'required',
            'nama_customer' => 'required',
        ], [
            'hari.required' => 'Hari BAST harus diisi.',
            'tanggal_bast.required' => 'Tanggal BAST harus diisi.',
            'tanggal_bast.date' => 'Tanggal BAST harus berupa tanggal yang valid.',
            'no_bast.required' => 'Nomor BAST harus diisi.',
            'nama_customer.required' => 'Nama customer harus diisi.',
        ]);

        $customer = Customer::findOrFail($request->nama_customer);
        $kavling = KavlingPeta::where('id_kavling', $customer->id_kavling)->first();
        $lokasi = LokasiKavling::where('id_lokasi', $customer->id_lokasi)->first();

        BAST::create([
            'id_customer' => $customer->id_customer,
            'id_kavling' => $kavling->id_kavling,
            'tanggal_bast' => $request->tanggal_bast,
            'hari_bast' => $request->hari,
            'no_bast' => $request->no_bast,
            'nama_customer' => $customer->nama_lengkap,
            'jenis_kelamin' => $customer->jenis_kelamin,
            'tempat_lahir' => $customer->tempat_lahir,
            'tanggal_lahir' => ($customer->tgl_lahir == '0000-00-00' || empty($customer->tgl_lahir))
                ? '1900-01-01'
                : $customer->tgl_lahir,
            'domisili' => $customer->alamat_domisili,
            'no_ktp' => $customer->no_ktp,
            'alamat_ktp' => $customer->alamat,
            'no_telp' => $customer->no_telp ?? '',
            'email' => $customer->email ?? '',
            'npwp' => $customer->npwp ?? '',
            'pekerjaan_cust' => $customer->pekerjaan,
            'lokasi_rumah' => $lokasi->nama_kavling,
            'blok' => $kavling->kode_kavling,
            'kode_kavling' => $kavling->kode_kavling,
            'tipe_rumah' => $kavling->tipe_bangunan,
            'luas_tanah' => $kavling->luas_tanah,
            'luas_bangunan' => !empty($kavling->luas_bangunan) ? $kavling->luas_bangunan : 0,
            'daya_listrik' => $kavling->daya_listrik,
            'nomor_sertifikat' => $kavling->no_sertifikat,
            'id_rumah_sikumbang' => $kavling->id_rumah_sikumbang,
            'stt_delete' => 0,
        ]);

        return response()->json(['status' => 'success', 'message' => 'BAST Berhasil Ditambahkan!']);
    }

    public function cetakBast($id_customer)
    {
        $customer = Customer::findOrFail($id_customer);
        $bast = Bast::where('id_customer', $id_customer)->firstOrFail();
        $kavling = KavlingPeta::with(['perusahaan', 'lokasi.perusahaan'])->where('id_kavling', $customer->id_kavling)->first();

        $tanggal = Carbon::parse($bast->tanggal_bast)->locale('id');
        $tanggalTerbilang = ucwords($this->numberToWords($tanggal->day));
        $hari = $bast->hari_bast ?? $tanggal->translatedFormat('l');
        $bulan = $tanggal->translatedFormat('F');
        $tahun = $tanggal->format('Y');
        $atas_nama = $kavling->perusahaan ? $kavling->perusahaan->nama_penandatangan : 'JOEL PARLINDUNGAN';
        $nama_perusahaan = null;

        if ($kavling->perusahaan) {
            $nama_perusahaan = Str::upper($kavling->perusahaan->nama_perusahaan);
        } elseif ($kavling->lokasi && $kavling->lokasi->perusahaan->isNotEmpty()) {
            $nama_perusahaan = Str::upper($kavling->lokasi->perusahaan->first()->nama_perusahaan);
        }

        $nama_perusahaan = $nama_perusahaan ?? 'PT. PRIMA KARYA ASIH';
        $templatePath = public_path('templates/master_bast.docx');

        $templateProcessor = new TemplateProcessor($templatePath);

        $tahunTerbilang = ucwords($this->numberToWords($tahun));

        $kawasan = '';
        if ($kavling->perusahaan && $kavling->perusahaan->kawasan) {
            $kawasan = $kavling->perusahaan->kawasan;
        } elseif ($kavling->lokasi && $kavling->lokasi->perusahaan->isNotEmpty()) {
            $kawasan = $kavling->lokasi->perusahaan->first()->kawasan ?? '';
        }

        $tanggalBast = $tanggal->translatedFormat('j F Y');

        $templateProcessor->setValues([
            'no_bast'       => $bast->no_bast,
            'hari_bast'     => $hari,
            'tanggal_bast'  => $tanggalBast,
            'tanggal'       => $tanggalTerbilang,
            'bulan'         => $bulan,
            'tahun'         => $tahunTerbilang,
            'nama_customer' => $customer->nama_lengkap,
            'pekerjaan_cust' => $customer->pekerjaan,
            'alamat_ktp'    => $customer->alamat,
            'kode_kavling'  => $kavling->kode_kavling ?? '',
            'tipe_rumah'    => $kavling->tipe_bangunan ?? '',
            'tipe_bangunan' => $kavling->tipe_bangunan ?? '',
            'luas_tanah'    => $kavling->luas_tanah ?? '',
            'luas_bangunan' => $kavling->luas_bangunan ?? '',
            'norek_listrik' => $customer->norek_listrik ?? '',
            'norek_air'     => $customer->norek_air ?? '',
            'nama_perumahan' => $kavling->lokasi ? $kavling->lokasi->nama_kavling : '',
            'kawasan'       => $kawasan,
            'atas_nama'     => $atas_nama,
            'nama_perusahaan' => $nama_perusahaan
        ]);

        $fileName = 'BAST_' . Str::slug($customer->nama_lengkap) . '.docx';
        $tempFile = tempnam(sys_get_temp_dir(), 'phpword');
        $templateProcessor->saveAs($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }

    private function numberToWords($number)
    {
        $f = new \NumberFormatter("id", \NumberFormatter::SPELLOUT);
        return $f->format($number);
    }

    public function destroy($id)
    {
        $data = BAST::findOrFail($id);

        $data->delete();

        return response()->json(['status' => 'success', 'message' => 'BAST Berhasil Dihapus!']);
    }
}
