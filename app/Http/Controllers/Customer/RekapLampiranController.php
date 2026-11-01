<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Pengaturan\HakAksesController;
use App\Models\Customer;
use App\Models\UploadFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class RekapLampiranController extends Controller
{
    public function index(Request $request)
    {
        $permissions = HakAksesController::getUserPermissions();
        $user = auth()->user();

        if ($request->ajax()) {
            $customers = Customer::with('lokasiKavling', 'kavlingPeta', 'marketing')
                ->orderBy('id_customer', 'desc');

            if ($user->role == 1) {
                $marketingName = $user->surname;

                $customers->whereHas('marketing', function ($query) use ($marketingName) {
                    $query->where('nama_marketing', $marketingName);
                });
            }

            return DataTables::of($customers)
                ->addIndexColumn()
                ->addColumn('marketing', function ($row) {
                    return $row->marketing->nama_marketing;
                })
                ->addColumn('nama_customer', function ($row) {
                    return $row->nama_lengkap;
                })
                ->addColumn('KTP', function ($row) {
                    $exists = UploadFile::where('nasabah', $row->id_customer)
                        ->where('nama_file', 'KTP')
                        ->exists();
                    return $exists
                        ? '<i class="fas fa-check-circle text-success"></i>'
                        : '<i class="fas fa-times-circle text-danger"></i>';
                })
                ->addColumn('NPWP', function ($row) {
                    $exists = UploadFile::where('nasabah', $row->id_customer)
                        ->where('nama_file', 'NPWP')
                        ->exists();
                    return $exists
                        ? '<i class="fas fa-check-circle text-success"></i>'
                        : '<i class="fas fa-times-circle text-danger"></i>';
                })
                ->addColumn('KK', function ($row) {
                    $exists = UploadFile::where('nasabah', $row->id_customer)
                        ->where('nama_file', 'KK')
                        ->exists();
                    return $exists
                        ? '<i class="fas fa-check-circle text-success"></i>'
                        : '<i class="fas fa-times-circle text-danger"></i>';
                })
                ->addColumn('Pemohon', function ($row) {
                    $exists = UploadFile::where('nasabah', $row->id_customer)
                        ->where('nama_file', 'Pemohon')
                        ->exists();
                    return $exists
                        ? '<i class="fas fa-check-circle text-success"></i>'
                        : '<i class="fas fa-times-circle text-danger"></i>';
                })
                ->addColumn('KTP_Pasangan', function ($row) {
                    $exists = UploadFile::where('nasabah', $row->id_customer)
                        ->where('nama_file', 'KTP Pasangan')
                        ->exists();
                    return $exists
                        ? '<i class="fas fa-check-circle text-success"></i>'
                        : '<i class="fas fa-times-circle text-danger"></i>';
                })
                ->addColumn('BPJS', function ($row) {
                    $exists = UploadFile::where('nasabah', $row->id_customer)
                        ->where('nama_file', 'BPJS')
                        ->exists();
                    return $exists
                        ? '<i class="fas fa-check-circle text-success"></i>'
                        : '<i class="fas fa-times-circle text-danger"></i>';
                })
                ->addColumn('action', function ($row) {
                    $showUrl = route('rekap-lampiran.show', $row->id_customer);

                    $btn = '<div class="d-flex justify-content-center">';
                    $btn .= '<a href="' . $showUrl . '" class="btn btn-primary btn-sm upload-button"><i class="fas fa-upload"></i> Upload</a>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->filterColumn('nama_customer', function ($query, $keyword) {
                    $query->where('nama_lengkap', 'like', "%{$keyword}%");
                })
                ->filterColumn('marketing', function ($query, $keyword) {
                    $query->whereHas('marketing', function ($q) use ($keyword) {
                        $q->where('nama_marketing', 'like', "%{$keyword}%");
                    });
                })
                ->rawColumns(['KTP', 'NPWP', 'KK', 'Pemohon', 'KTP_Pasangan', 'BPJS', 'action'])
                ->make(true);
        }

        return view('admin.customer_induk.rekap_lampiran.index', compact('permissions'));
    }

    public function show(Request $request, $id)
    {
        $customer = Customer::find($id);
        $permissions = HakAksesController::getUserPermissions();

        if ($request->ajax()) {
            $files = UploadFile::where('nasabah', $customer->id_customer)->get();

            return DataTables::of($files)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $deleteUrl = route('rekap-lampiran.destroy', $row->id_file);

                    $btn = '<div class="d-flex justify-content-center">';
                    $btn .= '<button class="btn btn-primary btn-sm view-file-btn" 
                          data-keterangan="' . e($row->keterangan) . '"
                          data-filename="' . e($row->lampiran) . '"
                          data-nama-file="' . e($row->keterangan ?: $row->lampiran) . '">
                          Detail
                        </button>';
                    $btn .= '<form action="' . e($deleteUrl) . '" method="POST" style="display:inline;">' . csrf_field() . method_field('DELETE') . '<button type="submit" class="delete-button btn btn-danger btn-sm ml-2">Hapus</button></form>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.customer_induk.rekap_lampiran.upload', compact('customer', 'permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal'    => 'required|date',
            'nama_file'  => 'required|string',
            'lampiran'   => 'required|file|mimes:jpg,jpeg,png,webp,pdf',
        ], [
            'tanggal.required'      => 'Tanggal wajib diisi.',
            'tanggal.date'          => 'Tanggal tidak valid.',
            'nama_file.required'    => 'Nama file wajib diisi.',
            'lampiran.required'     => 'Lampiran wajib diunggah.',
            'lampiran.file'         => 'Lampiran harus berupa file.',
            'lampiran.mimes'        => 'Hanya mendukung jpg, jpeg, png, webp, atau pdf.',
        ]);

        $folder = 'berkas_user';
        $destination = public_path($folder);

        if (!file_exists($destination)) {
            mkdir($destination, 0755, true);
        }

        $file = $request->file('lampiran');
        $originalExtension = strtolower($file->getClientOriginalExtension());

        $cleanName = preg_replace('/[^a-zA-Z0-9-_\.]/', '_', pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $finalName = time() . '_' . $cleanName . '.' . $originalExtension;

        $file->move($destination, $finalName);

        UploadFile::create([
            'tanggal'     => $request->tanggal,
            'nasabah'     => $request->nasabah,
            'folder'      => $folder,
            'nama_file'   => $request->nama_file,
            'keterangan'  => $request->keterangan,
            'lampiran'    => $finalName,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Lampiran berhasil diunggah.',
        ]);
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $data = UploadFile::findOrFail($id);

            $filePath = public_path($data->folder . '/' . $data->lampiran);

            if (!empty($data->lampiran) && file_exists($filePath)) {
                unlink($filePath);
            }

            $data->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Lampiran berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus lampiran.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
