<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Pengaturan\HakAksesController;
use App\Models\BankModel;
use App\Models\Customer;
use App\Models\Freelance;
use App\Models\KavlingPeta;
use App\Models\LokasiKavling;
use App\Models\Marketing;
use App\Models\ProgresListPenjualan;
use App\Models\Registrasi;
use App\Models\Tagihan;
use App\Models\UploadFile;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class RegisterController extends Controller
{
    public function index(Request $request)
    {
        $permissions = HakAksesController::getUserPermissions();

        Carbon::setLocale('id');

        if ($request->ajax()) {
            $surname   = Auth::user()->surname;
            $role      = Auth::user()->role;
            $isArchive = $request->has('archive');

            $program = Registrasi::with(['marketing', 'freelance', 'lokasi', 'kavling'])
                ->when($role == 1, function ($query) use ($surname) {
                    return $query->whereHas('marketing', function ($q) use ($surname) {
                        $q->where('nama_marketing', $surname);
                    });
                })
                ->when($isArchive, function ($query) {
                    return $query->where('stt_reg', 2);
                }, function ($query) {
                    return $query->where('stt_reg', 1);
                })
                ->orderByDesc('id_reg');

            return DataTables::of($program)
                ->addIndexColumn()
                ->editColumn('tgl_registrasi', function ($row) {
                    $tgl = $row->tgl_registrasi ? Carbon::parse($row->tgl_registrasi)->translatedFormat('d F Y') : '-';
                    return "$tgl";
                })
                ->filterColumn('kode_kavling', function ($query, $keyword) {
                    $query->whereHas('kavling', function ($q) use ($keyword) {
                        $q->where('kode_kavling', 'like', "%{$keyword}%");
                    })->orWhereHas('lokasi', function ($q) use ($keyword) {
                        $q->where('nama_kavling', 'like', "%{$keyword}%");
                    });
                })
                ->addColumn('stt_reg', function ($row): string {
                    switch ($row->stt_reg) {
                        case 1:
                            return '<span class="badge bg-warning">Pending</span>';
                        case 2:
                            return '<span class="badge bg-success">Disetujui</span>';
                        case 3:
                            return '<span class="badge bg-danger">Ditolak</span>';
                        default:
                            return '<span class="badge bg-secondary">Unknown</span>';
                    }
                })
                ->addColumn('nama_marketing', function ($row) {
                    return $row->marketing->nama_marketing ?? '-';
                })
                ->addColumn('kode_kavling', function ($row) {
                    $kode       = $row->kavling->kode_kavling ?? '-';
                    $namaLokasi = $row->lokasi->nama_kavling ?? '-';
                    return " $kode <br> $namaLokasi";
                })
                ->addColumn('action', function ($row) use ($permissions): string {
                    $editUrl     = route('pengajuan-hold.edit', $row->id_reg);
                    $deleteUrl   = route('pengajuan-hold.destroy', $row->id_reg);
                    $lampiranUrl = route('pengajuan-hold.show', $row->id_reg);
                    $verifUrl    = route('pengajuan-hold.verifikasi', $row->id_reg);

                    $btn = '<div class="text-center">';

                    $btn .= '<button class="btn btn-warning btn-xs" data-id="' . e($row->id_reg) . '"
                                data-url="' . e($editUrl) . '" data-toggle="modal" data-target="#modalForm" id="edit-button">
                                Edit
                            </button> ';

                    $btn .= '<a href="' . e($lampiranUrl) . '" class="btn btn-success btn-xs" >
                                Lampiran
                            </a> ';

                    if ($row->stt_reg != 2 && $permissions['edit']) {
                        $btn .= '<a href="' . e($verifUrl) . '" class="btn btn-primary btn-xs">
                                    Verifikasi
                                </a> ';
                    }

                    if ($permissions['hapus']) {
                        $btn .= '<form action="' . e($deleteUrl) . '" method="POST" style="display:inline;">
                                    ' . csrf_field() . method_field('DELETE') . '
                                    <button type="submit" class="delete-button btn btn-danger btn-xs">
                                        Hapus
                                    </button>
                                </form>';
                    }

                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns([
                    'nama_marketing',
                    'kode_kavling',
                    'stt_reg',
                    'action',
                    'kode_kavling',
                ])
                ->make(true);
        }

        $marketing = Marketing::all();
        $freelance = Freelance::all();
        $lokasi    = LokasiKavling::all();

        return view('admin.pengajuan_hold.index', compact('permissions', 'marketing', 'freelance', 'lokasi'));
    }

    public function edit($id_reg)
    {
        $list = Registrasi::findOrFail($id_reg);

        return response()->json([
            'status' => 'success',
            'data'   => $list,
        ]);
    }

    public function update(Request $request, $id_reg)
    {
        $data = Registrasi::findOrFail($id_reg);

        $request->validate([
            'tgl_registrasi'   => 'required|date',
            'nama_lengkap'     => 'required',
            'nik'              => 'required|numeric',
            'tempat_lahir'     => 'required',
            'tgl_lahir'        => 'required|date',
            'jenis_kelamin'    => 'required',
            'no_telp_customer' => 'required|numeric',
            'email'            => 'nullable|email',
            'npwp'             => 'nullable|numeric',
            'alamat'           => 'required',
            'pekerjaan'        => 'required',
            'status'           => 'required',
            'nama_p'           => 'nullable',
            'nik_p'            => 'nullable|numeric',
            'nama_saudara'     => 'nullable',
            'no_telp_saudara'  => 'nullable|numeric',
            'booking_fee'      => 'nullable|numeric',
            'no_bpjs_kes'      => 'nullable|numeric',
            'id_lokasi'        => 'required',
            'id_kavling'       => 'required',
            'hrg_jual'         => 'required',
            'id_marketing'     => 'required',
            'id_freelance'     => 'nullable',
            'jenis_perumahan'  => 'required',
            'jenis_pembelian'  => 'required',
        ], [
            'required'                  => ':attribute wajib diisi.',
            'date'                      => ':attribute harus berupa tanggal yang valid.',
            'numeric'                   => ':attribute harus berupa angka.',
            'email'                     => 'Format :attribute tidak valid.',
            'mimes'                     => ':attribute harus berupa file dengan format jpg, jpeg, atau png.',
            'max'                       => 'Ukuran :attribute tidak boleh lebih dari 2MB.',

            'tgl_registrasi.required'   => 'Tanggal registrasi wajib diisi.',
            'nama_lengkap.required'     => 'Nama lengkap wajib diisi.',
            'nik.required'              => 'NIK wajib diisi.',
            'tempat_lahir.required'     => 'Tempat lahir wajib diisi.',
            'tgl_lahir.required'        => 'Tanggal lahir wajib diisi.',
            'jenis_kelamin.required'    => 'Jenis kelamin wajib dipilih.',
            'no_telp_customer.required' => 'Nomor telepon wajib diisi.',
            'email.required'            => 'Email wajib diisi.',
            'alamat.required'           => 'Alamat wajib diisi.',
            'pekerjaan.required'        => 'Pekerjaan wajib diisi.',
            'status.required'           => 'Status wajib diisi.',
            'nama_saudara.required'     => 'Nama saudara wajib diisi.',
            'id_lokasi.required'        => 'Lokasi wajib dipilih.',
            'id_kavling.required'       => 'Kavling wajib dipilih.',
            'hrg_jual.required'         => 'Harga jual wajib diisi.',
            'id_marketing.required'     => 'Marketing wajib dipilih.',
            'jenis_perumahan.required'  => 'Jenis perumahan wajib dipilih.',
            'jenis_pembelian.required'  => 'Jenis pembelian wajib dipilih.',
        ]);

        $db = [
            'tgl_registrasi'   => $request->tgl_registrasi,
            'nama_lengkap'     => $request->nama_lengkap,
            'nik'              => $request->nik,
            'nik_p'            => $request->nik_p,
            'tempat_lahir'     => $request->tempat_lahir,
            'tgl_lahir'        => $request->tgl_lahir,
            'jenis_kelamin'    => $request->jenis_kelamin,
            'no_telp_customer' => $request->no_telp_customer,
            'email'            => $request->email,
            'npwp'             => $request->npwp,
            'no_bpjs_kes'      => $request->no_bpjs_kes,
            'booking_fee'      => $request->booking_fee,
            'alamat'           => $request->alamat,
            'pekerjaan'        => $request->pekerjaan,
            'status'           => $request->status,
            'nama_p'           => $request->nama_p,
            'nama_saudara'     => $request->nama_saudara,
            'no_telp_saudara'  => $request->no_telp_saudara,
            'id_lokasi'        => $request->id_lokasi,
            'id_kavling'       => $request->id_kavling,
            'hrg_jual'         => $request->hrg_jual,
            'id_marketing'     => $request->id_marketing,
            'id_freelance'     => $request->id_freelance,
            'jenis_pembelian'  => $request->jenis_pembelian,
            'jenis_perumahan'  => $request->jenis_perumahan,
        ];

        $data->update($db);

        return response()->json(['status' => 'success']);
    }

    public function show($id_reg)
    {
        $data = Registrasi::findOrFail($id_reg);

        $folder = public_path('bookingPhoto');

        $files = [];

        if (file_exists($folder)) {
            $scan = scandir($folder);

            $files = array_filter($scan, function ($file) use ($data) {
                return ! in_array($file, ['.', '..']) && Str::startsWith($file, $data->id . '_');
            });
        }

        return view('admin.pengajuan_hold.lampiran', compact('data', 'files'));
    }

    public function booking(Request $request)
    {
        $marketing = Marketing::all();
        $freelance = Freelance::all();
        $bank      = BankModel::all();
        $progres   = ProgresListPenjualan::all();
        $lokasi    = LokasiKavling::all();
        return view('frontend.booking.index', compact('marketing', 'freelance', 'lokasi', 'progres', 'bank'));
    }

    public function bookingSukses()
    {
        $data = session()->get('booking_sukses');

        if (! $data) {
            return redirect()->route('booking');
        }

        return view('frontend.booking.sukses', $data);
    }

    public function upload(Request $request, $id)
    {
        $field = $request->input('field');
        $file  = $request->file('file');

        $allowed = ['foto_pemohon', 'foto_ktp_p', 'file_bukti', 'foto_ktp', 'foto_npwp', 'foto_kk', 'foto_bpjs'];

        if (! in_array($field, $allowed) || ! $file) {
            return response()->json(['success' => false]);
        }

        $data = Registrasi::findOrFail($id);

        $filename    = $field . '_' . time() . '.webp';
        $destination = public_path('bookingPhoto');

        if (! file_exists($destination)) {
            mkdir($destination, 0777, true);
        }

        $originalPath = $file->getPathname();
        $extension    = strtolower($file->getClientOriginalExtension());
        $image        = null;

        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                $image = imagecreatefromjpeg($originalPath);
                break;
            case 'png':
                $image = imagecreatefrompng($originalPath);
                break;
            case 'gif':
                $image = imagecreatefromgif($originalPath);
                break;
            default:
                return response()->json(['success' => false, 'message' => 'Format gambar tidak didukung']);
        }

        if (! $image) {
            return response()->json(['success' => false, 'message' => 'Gagal convert gambar']);
        }

        $outputPath = $destination . '/' . $filename;
        imagewebp($image, $outputPath, 80);

        imagedestroy($image);

        if ($data->$field && file_exists($destination . '/' . $data->$field)) {
            unlink($destination . '/' . $data->$field);
        }

        $data->$field = $filename;
        $data->save();

        return response()->json(['success' => true, 'filename' => $filename]);
    }

    public function deleteFile(Request $request, $id)
    {
        $field   = $request->input('field');
        $allowed = ['foto_pemohon', 'foto_ktp_p', 'file_bukti', 'foto_ktp', 'foto_npwp', 'foto_kk', 'foto_bpjs'];

        if (! in_array($field, $allowed)) {
            return response()->json(['success' => false]);
        }

        $data = Registrasi::findOrFail($id);

        if ($data->$field) {
            $filePath = public_path("bookingPhoto/" . $data->$field);
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            $data->$field = null;
            $data->save();
        }

        return response()->json(['success' => true]);
    }

    public function getKavling($id_lokasi, $currentId = null)
    {
        $bookingAktif = Registrasi::query()
            ->select('id_kavling')
            ->whereIn('stt_reg', [1, 2])
            ->whereNotExists(function ($query) {
                $query->selectRaw('1')
                    ->from('pembatalan')
                    ->whereColumn('pembatalan.id_kavling', 'registrasi.id_kavling')
                    ->whereColumn('pembatalan.no_ktp', 'registrasi.nik')
                    ->whereColumn('pembatalan.tgl_batal', '>=', 'registrasi.tgl_registrasi');
            })
            ->whereNotExists(function ($query) {
                $query->selectRaw('1')
                    ->from('pindah_rumah')
                    ->whereColumn('pindah_rumah.id_kavling_lama', 'registrasi.id_kavling')
                    ->whereColumn('pindah_rumah.tgl_pindah', '>=', 'registrasi.tgl_registrasi')
                    ->where('pindah_rumah.stt_delete', 0)
                    ->where(function ($query) {
                        $query->whereColumn('pindah_rumah.no_ktp', 'registrasi.nik')
                            ->orWhereColumn('pindah_rumah.nama_customer', 'registrasi.nama_lengkap')
                            ->orWhereIn('pindah_rumah.id_customer', function ($query) {
                                $query->select('id_customer')
                                    ->from('nasabah')
                                    ->whereColumn('nasabah.id_reg', 'registrasi.id_reg');
                            });
                    });
            })
            ->whereNotExists(function ($query) {
                $query->selectRaw('1')
                    ->from('nasabah as nasabah_basi')
                    ->where('registrasi.stt_reg', 2)
                    ->where(function ($query) {
                        $query->whereColumn('nasabah_basi.id_reg', 'registrasi.id_reg')
                            ->orWhereColumn('nasabah_basi.no_ktp', 'registrasi.nik');
                    })
                    ->whereColumn('nasabah_basi.id_kavling', '<>', 'registrasi.id_kavling')
                    ->whereNotExists(function ($query) {
                        $query->selectRaw('1')
                            ->from('nasabah as nasabah_sendiri')
                            ->whereColumn('nasabah_sendiri.id_reg', 'registrasi.id_reg')
                            ->whereColumn('nasabah_sendiri.id_kavling', 'registrasi.id_kavling');
                    });
            });

        $kavling = KavlingPeta::where('id_lokasi', $id_lokasi)
            ->where(function ($query) use ($currentId, $bookingAktif) {
                $query->whereDoesntHave('customer', function ($query) {
                    $query->whereDoesntHave('pindahUnitKeluar', function ($query) {
                        $query->whereColumn('pindah_rumah.id_kavling_lama', 'nasabah.id_kavling')
                            ->whereColumn('pindah_rumah.tgl_pindah', '>=', 'nasabah.tgl_terima');
                    });
                })->whereNotIn('id_kavling', $bookingAktif);

                if ($currentId) {
                    $query->orWhere('id_kavling', $currentId);
                }
            })
            ->get(['id_kavling', 'kode_kavling']);

        return response()->json($kavling);
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

    public function bookingStore(Request $request)
    {
        $request->merge([
            'hrg_jual'    => str_replace(['.', ','], '', $request->hrg_jual),
            'booking_fee' => str_replace(['.', ','], '', $request->booking_fee),
        ]);

        $request->validate([
            'tgl_registrasi'   => 'required|date',
            'nama_lengkap'     => 'required',
            'nik'              => 'required|numeric',
            'tempat_lahir'     => 'required',
            'tgl_lahir'        => 'required|date',
            'jenis_kelamin'    => 'required',
            'no_telp_customer' => 'required|numeric',
            'npwp'             => 'nullable|numeric',
            'alamat'           => 'nullable',
            'pekerjaan'        => 'nullable',
            'status'           => 'nullable',
            'nama_p'           => 'nullable',
            'nik_p'            => 'nullable|numeric',
            'nama_saudara'     => 'nullable',
            'no_telp_saudara'  => 'nullable|numeric',
            'booking_fee'      => 'nullable|numeric',
            'no_bpjs_kes'      => 'nullable|numeric',
            'id_lokasi'        => 'required',
            'id_kavling'       => 'required',
            'hrg_jual'         => 'required',
            'id_marketing'     => 'required',
            'id_freelance'     => 'nullable',
            'jenis_perumahan'  => 'nullable',
            'jenis_pembelian'  => 'nullable',
            'foto_ktp'         => 'nullable|mimes:jpg,jpeg,png|max:2048',
            'foto_npwp'        => 'nullable|mimes:jpg,jpeg,png|max:2048',
            'foto_kk'          => 'nullable|mimes:jpg,jpeg,png|max:2048',
            'foto_bpjs'        => 'nullable|mimes:jpg,jpeg,png|max:2048',
            'foto_ktp_p'       => 'nullable|mimes:jpg,jpeg,png|max:2048',
            'file_bukti'       => 'nullable|mimes:jpg,jpeg,png|max:2048',
            'foto_pemohon'     => 'nullable|mimes:jpg,jpeg,png|max:2048',
        ], [
            'required'                  => ':attribute wajib diisi.',
            'date'                      => ':attribute harus berupa tanggal yang valid.',
            'numeric'                   => ':attribute harus berupa angka.',
            'email'                     => 'Format :attribute tidak valid.',
            'mimes'                     => ':attribute harus berupa file dengan format jpg, jpeg, atau png.',
            'max'                       => 'Ukuran :attribute tidak boleh lebih dari 2MB.',

            'tgl_registrasi.required'   => 'Tanggal registrasi wajib diisi.',
            'nama_lengkap.required'     => 'Nama lengkap wajib diisi.',
            'nik.required'              => 'NIK wajib diisi.',
            'tempat_lahir.required'     => 'Tempat lahir wajib diisi.',
            'tgl_lahir.required'        => 'Tanggal lahir wajib diisi.',
            'jenis_kelamin.required'    => 'Jenis kelamin wajib dipilih.',
            'no_telp_customer.required' => 'Nomor telepon wajib diisi.',
            'id_lokasi.required'        => 'Lokasi wajib dipilih.',
            'id_kavling.required'       => 'Kavling wajib dipilih.',
            'hrg_jual.required'         => 'Harga jual wajib diisi.',
            'id_marketing.required'     => 'Marketing wajib dipilih.',
        ]);

        $folder = public_path('bookingPhoto');

        if (! file_exists($folder)) {
            mkdir($folder, 0777, true);
        }

        $nama_file_ktp     = $this->simpanFile($request->file('foto_ktp'), $folder);
        $nama_file_npwp    = $this->simpanFile($request->file('foto_npwp'), $folder);
        $nama_file_kk      = $this->simpanFile($request->file('foto_kk'), $folder);
        $nama_file_bpjs    = $this->simpanFile($request->file('foto_bpjs'), $folder);
        $nama_file_bukti   = $this->simpanFile($request->file('file_bukti'), $folder);
        $nama_file_pemohon = $this->simpanFile($request->file('foto_pemohon'), $folder);

        $nama_file_ktp_p = null;
        if ($request->hasFile('foto_ktp_p')) {
            $nama_file_ktp_p = $this->simpanFile($request->file('foto_ktp_p'), $folder);
        }
        $no_registrasi = $this->generateNoRegistrasi();

        Registrasi::create([
            'no_registrasi'    => $no_registrasi,
            'tgl_registrasi'   => $request->tgl_registrasi,
            'nama_lengkap'     => $request->nama_lengkap,
            'nik'              => $request->nik,
            'kartu_keluarga'   => $request->nik,
            'tempat_lahir'     => $request->tempat_lahir,
            'tgl_lahir'        => $request->tgl_lahir,
            'jenis_kelamin'    => $request->jenis_kelamin,
            'no_telp_customer' => $request->no_telp_customer,
            'email'            => $request->email,
            'npwp'             => $request->npwp,
            'alamat'           => $request->alamat,
            'pekerjaan'        => $request->pekerjaan,
            'status'           => $request->status,
            'nama_p'           => $request->nama_p,
            'nik_p'            => $request->nik_p,
            'nama_saudara'     => $request->nama_saudara,
            'no_telp_saudara'  => $request->no_telp_saudara,
            'id_lokasi'        => $request->id_lokasi,
            'id_kavling'       => $request->id_kavling,
            'hrg_jual'         => $request->hrg_jual,
            'no_bpjs_kes'      => $request->no_bpjs_kes,
            'booking_fee'      => $request->booking_fee,
            'id_marketing'     => $request->id_marketing,
            'id_freelance'     => $request->id_freelance,
            'jenis_perumahan'  => $request->jenis_perumahan,
            'jenis_pembelian'  => $request->jenis_pembelian,
            'foto_ktp'         => $nama_file_ktp,
            'foto_npwp'        => $nama_file_npwp,
            'foto_kk'          => $nama_file_kk,
            'foto_bpjs'        => $nama_file_bpjs,
            'foto_ktp_p'       => $nama_file_ktp_p,
            'file_bukti'       => $nama_file_bukti,
            'foto_pemohon'     => $nama_file_pemohon,
            'stt_reg'          => 1,
        ]);

        session()->flash('booking_sukses', [
            'nama'   => $request->nama_lengkap,
            'lokasi' => LokasiKavling::find($request->id_lokasi)->nama_kavling ?? '-',
            'blok'   => KavlingPeta::find($request->id_kavling)->kode_kavling ?? '-',
        ]);

        return response()->json([
            'status'   => 'success',
            'redirect' => route('booking.sukses'),
        ]);
    }

    private function simpanFile($file, $folder)
    {
        if (! $file) {
            return null;
        }

        $filename   = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $filename   = preg_replace('/[^a-zA-Z0-9-_]/', '_', $filename);
        $filename   = $filename . '_' . time() . '.webp';
        $outputPath = $folder . '/' . $filename;

        $ext    = strtolower($file->getClientOriginalExtension());
        $source = null;

        switch ($ext) {
            case 'jpg':
            case 'jpeg':
                $source = imagecreatefromjpeg($file->getPathname());
                break;
            case 'png':
                $source = imagecreatefrompng($file->getPathname());
                break;
            default:
                return null;
        }

        if ($source) {
            imagewebp($source, $outputPath, 80);
            imagedestroy($source);
            return $filename;
        }

        return null;
    }

    private function generateNoRegistrasi()
    {
        $latest = Registrasi::orderBy('no_registrasi', 'desc')->first();

        if (! $latest || ! preg_match('/^\d{3}$/', $latest->no_registrasi)) {
            return '001';
        }

        $lastNumber = intval($latest->no_registrasi);
        return str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
    }

    public function verifikasi($id)
    {
        $data     = Registrasi::findOrFail($id);
        $bankList = BankModel::all();
        return view('admin.pengajuan_hold.verif', compact('data', 'bankList'));
    }

    public function simpanVerifikasi(Request $request, $id_reg)
    {
        $request->validate([
            'stt_reg'         => 'required',
            'jenis_pembelian' => 'nullable|in:Cash Keras,Cash Bertahap,Kredit Inhouse,KPR',
        ]);

        try {
            DB::beginTransaction();
            $data                  = Registrasi::findOrFail($id_reg);
            $data->stt_reg         = $request->stt_reg;
            $data->jenis_pembelian = $request->jenis_pembelian;

            if ($request->jenis_pembelian == 'Cash Keras') {
                $data->jumlah_pembayaran = $request->jumlah_pembayaran;
                $data->jumlah_bulan_x    = null;
                $data->cicil_perbulan    = null;
                $data->tenor_cicilan     = null;
                $data->jatuh_tempo       = null;
            } elseif ($request->jenis_pembelian == 'Cash Bertahap') {
                $data->jumlah_bulan_x    = $request->jumlah_bulan_x;
                $data->jumlah_pembayaran = null;
                $data->cicil_perbulan    = null;
                $data->tenor_cicilan     = null;
                $data->jatuh_tempo       = null;
            } elseif ($request->jenis_pembelian == 'Kredit Inhouse') {
                $data->cicil_perbulan    = $request->cicil_perbulan;
                $data->tenor_cicilan     = $request->tenor_cicilan;
                $data->jatuh_tempo       = $request->jatuh_tempo;
                $data->jumlah_pembayaran = null;
                $data->jumlah_bulan_x    = null;
            } elseif ($request->jenis_pembelian == 'KPR') {
                $data->jumlah_pembayaran = null;
                $data->jumlah_bulan_x    = null;
                $data->cicil_perbulan    = null;
                $data->tenor_cicilan     = null;
                $data->jatuh_tempo       = null;
            }

            $data->save();

            if ($data->stt_reg == 3 && $data->id_kavling) {
            }

            if ($data->stt_reg == 2) {
                $prefix = $data->lokasi->nama_singkat;

                $latest = Customer::where('id_lokasi', $data->id_lokasi)
                    ->where('kode_customer', 'like', $prefix . '-%')
                    ->orderByDesc('id_customer')
                    ->first();

                if ($latest && preg_match('/\d+$/', $latest->kode_customer, $match)) {
                    $number  = (int) $match[0] + 1;
                    $newKode = $prefix . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
                } else {
                    $newKode = $prefix . '-0001';
                }

                $existing = Customer::where('id_reg', $data->id_reg)->first();

                if (! $existing) {
                    $customer = Customer::create([
                        'id_reg'            => $data->id_reg,
                        'tgl_terima'        => $data->tgl_registrasi,
                        'nama_lengkap'      => $data->nama_lengkap,
                        'kode_customer'     => $newKode,
                        'no_ktp'            => $data->nik,
                        'tempat_lahir'      => $data->tempat_lahir,
                        'jenis_kelamin'     => $data->jenis_kelamin,
                        'no_wa'             => $data->no_telp_customer,
                        'email'             => $data->email ?? '',
                        'npwp'              => $data->npwp ?? null,
                        'alamat'            => $data->alamat,
                        'alamat_domisili'   => $data->alamat,
                        'pekerjaan'         => $data->pekerjaan,
                        'warga_negara'      => 'WNI',
                        'hrg_rumah'         => $data->hrg_jual,
                        'id_marketing'      => $data->id_marketing,
                        'id_status_progres' => $request->jenis_pembelian == 'Cash Keras' ? 21 : 1,
                        'jenis_pembelian'   => $data->jenis_pembelian,
                        'nik_p'             => $data->nik_p,
                        'id_lokasi'         => $data->id_lokasi,
                        'id_kavling'        => $data->id_kavling,
                        'tgl_lahir'         => $data->tgl_lahir,
                        'id_freelance'      => $data->id_freelance,
                        'no_kk'             => $data->kartu_keluarga,
                        'jumlah_bulan'      => $data->jumlah_bulan_x,
                        'id_bank'           => $data->id_bank,
                    ]);

                    Tagihan::create([
                        'nasabah'        => $customer->id_customer,
                        'jenis_tagihan'  => 'Harga Rumah Tipe ' . $data->kavling->tipe_bangunan . ' ' . $data->lokasi->nama_kavling . ' Blok ' . $data->kavling->kode_kavling,
                        'jumlah_tagihan' => $data->hrg_jual,
                        'utama'          => 1,
                        'catatan'        => '',
                    ]);

                    $this->copyLampiranNasabah($data, $customer->id_customer);
                } else {
                    Tagihan::create([
                        'nasabah'        => $existing->id_customer,
                        'jenis_tagihan'  => 'Harga Rumah Tipe ' . $data->kavling->tipe_bangunan . ' ' . $data->lokasi->nama_kavling . ' Blok ' . $data->kavling->kode_kavling,
                        'jumlah_tagihan' => $data->hrg_jual,
                        'utama'          => 1,
                        'catatan'        => '',
                    ]);
                }
            }

            DB::commit();
            return response()->json([
                'status'  => 'success',
                'message' => 'Data berhasil diupdate!',
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function copyLampiranNasabah($data, $customerId)
    {
        $map = [
            'foto_ktp'     => ['nama_file' => 'KTP', 'keterangan' => 'Foto KTP'],
            'foto_npwp'    => ['nama_file' => 'NPWP', 'keterangan' => 'Foto NPWP'],
            'foto_kk'      => ['nama_file' => 'KK', 'keterangan' => 'Foto Kartu Keluarga'],
            'foto_bpjs'    => ['nama_file' => 'BPJS', 'keterangan' => 'Foto BPJS'],
            'foto_pemohon' => ['nama_file' => 'PEMOHON', 'keterangan' => 'Foto Pemohon'],
            'foto_ktp_p'   => ['nama_file' => 'KTP PASANGAN', 'keterangan' => 'Foto KTP Pasangan'],
            'file_bukti'   => ['nama_file' => 'BUKTI', 'keterangan' => 'File Bukti'],
        ];

        foreach ($map as $field => $info) {
            if (! empty($data->$field)) {
                $sourcePath = public_path('bookingPhoto/' . $data->$field); // asal file
                $destDir    = public_path('berkas_user');                   // tujuan folder
                $destPath   = $destDir . '/' . $data->$field;               // tujuan file

                // Pastikan folder tujuan ada
                if (! File::exists($destDir)) {
                    File::makeDirectory($destDir, 0777, true);
                }

                // Copy file jika ada di sumber
                if (File::exists($sourcePath)) {
                    File::copy($sourcePath, $destPath);
                }

                // Simpan record ke tabel
                UploadFile::create([
                    'tanggal'    => now(),
                    'nasabah'    => $customerId,
                    'folder'     => 'berkas_user',
                    'nama_file'  => $info['nama_file'],
                    'keterangan' => $info['keterangan'],
                    'lampiran'   => $data->$field, // simpan nama file
                ]);
            }
        }
    }

    public function destroy($pengajuan_hold)
    {
        $user = Registrasi::findOrFail($pengajuan_hold);

        $idKavling = $user->id_kavling;

        $user->delete();

        return response()->json(['status' => 'success', 'message' => 'Data berhasil dihapus']);
    }
}
