<?php
namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Pengaturan\HakAksesController;
use App\Models\Marketing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class MarketingOfflineController extends Controller
{
    public function index(Request $request)
    {
        $permissions = HakAksesController::getUserPermissions();
        if ($request->ajax()) {
            $program = Marketing::query();

            return DataTables::of($program)
                ->addIndexColumn()

                ->addColumn('action', function ($row) use ($permissions) {
                    $actions = '<div class="text-center">';

                    if ($permissions['edit']) {
                        $actions .= '<button type="button" onclick="modalAction(\'' . route('marketing-offline.edit', $row->id_marketing) . '\')" class=" btn btn-primary btn-sm">Edit</button> ';
                    }

                    if ($permissions['hapus']) {
                        $actions .= '
                    <form action="' . route('marketing-offline.destroy', $row->id_marketing) . '" method="POST" style="display:inline;">
                        ' . csrf_field() . method_field('DELETE') . '
                        <button type="submit" class="btn btn-danger btn-sm delete-button">Delete</button>
                    </form>';
                    }

                    $actions .= '</div>';
                    return $actions;
                })

                ->rawColumns(['action'])
                ->make(true);
        }

        $permissions = HakAksesController::getUserPermissions();
        $data        = Marketing::first();

        return view('admin.marketing.marketing_offline.index', compact('data', 'permissions'));
    }

    public function create()
    {
        return view('admin.marketing.marketing_offline.index', compact('title'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'nama_marketing' => 'required|string',
            'alamat'         => 'required|string|max:50',
            'jenis_kelamin'  => 'required|max:100',
            'pekerjaan'      => 'nullable',
            'no_telp'        => 'required',
            'foto'           => 'nullable',
            'status'         => 'nullable',
            'nama_bank'      => 'required',
            'no_rekening'    => 'nullable',
            'atas_nama'      => 'nullable',
        ], [
            'nama_marketing.required' => 'Nama marketing wajib diisi.',
            'nama_marketing.string'   => 'Nama marketing harus berupa teks.',

            'alamat.required'         => 'Alamat wajib diisi.',
            'alamat.string'           => 'Alamat harus berupa teks.',
            'alamat.max'              => 'Alamat tidak boleh lebih dari 50 karakter.',

            'jenis_kelamin.required'  => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.max'       => 'Jenis kelamin tidak valid.',

            'no_telp.required'        => 'Nomor telepon wajib diisi.',

            'foto.mimes'              => 'Foto harus berformat jpg, jpeg, atau png.',
            'foto.max'                => 'Ukuran foto maksimal 2MB.',

            'nama_bank.required'      => 'Nama bank wajib diisi.',
        ]);

        $lastMarketing = DB::table('marketing')
            ->where('kode_marketing', 'like', 'M-%')
            ->orderByDesc('id_marketing')
            ->first();

        $nextNumber = ($lastMarketing && preg_match('/M-(\d+)/', $lastMarketing->kode_marketing, $matches))
            ? intval($matches[1]) + 1
            : 1;

        $kodeMarketing = 'M-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        $fileName = null;
        if ($request->hasFile('foto')) {
            $file         = $request->file('foto');
            $extension    = strtolower($file->getClientOriginalExtension());
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $cleanName    = preg_replace('/[^a-zA-Z0-9-_\.]/', '_', $originalName);
            $webpName     = time() . '_' . $cleanName . '.webp';

            $destinationPath = public_path('photo');
            if (! file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
                $image = null;
                if ($extension === 'jpg' || $extension === 'jpeg') {
                    $image = imagecreatefromjpeg($file->getPathname());
                } elseif ($extension === 'png') {
                    $image = imagecreatefrompng($file->getPathname());
                }

                if ($image) {
                    imagewebp($image, $destinationPath . '/' . $webpName, 80);
                    imagedestroy($image);
                    $fileName = $webpName;
                }
            }
        }

        Marketing::create([
            'kode_marketing' => $kodeMarketing,
            'nama_marketing' => $request->nama_marketing,
            'alamat'         => $request->alamat,
            'jenis_kelamin'  => $request->jenis_kelamin,
            'pekerjaan'      => $request->pekerjaan,
            'no_telp'        => $request->no_telp,
            'foto'           => $fileName,
            'status'         => $request->status,
            'nama_bank'      => $request->nama_bank,
            'no_rekening'    => $request->no_rekening,
            'atas_nama'      => $request->atas_nama,
            'id_level'       => $request->id_level ?? '0',
            'stt_marketing'  => $request->stt_marketing ?? '0',
        ]);

        return response()->json(['status' => 'success']);
    }

    public function edit($id_marketing)
    {
        $data = Marketing::find($id_marketing);
        return view('admin.marketing.marketing_offline.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_marketing' => 'required|string',
            'alamat'         => 'required|string|max:50',
            'jenis_kelamin'  => 'required|max:100',
            'pekerjaan'      => 'nullable',
            'no_telp'        => 'required',
            'foto'           => 'nullable|mimes:jpg,jpeg,png|max:2048',
            'status'         => 'nullable',
            'nama_bank'      => 'required',
            'no_rekening'    => 'nullable',
            'atas_nama'      => 'nullable',
        ]);

        $data     = Marketing::findOrFail($id);
        $fileName = $data->foto;

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $ext  = strtolower($file->getClientOriginalExtension());

            if (! in_array($ext, ['jpg', 'jpeg', 'png'])) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Format gambar tidak valid.',
                ], 400);
            }

            $destinationPath = public_path('photo');
            if (! file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $newName  = time() . '_' . uniqid() . '.webp';
            $fullPath = $destinationPath . '/' . $newName;
            $image    = null;

            if ($ext === 'jpg' || $ext === 'jpeg') {
                $image = imagecreatefromjpeg($file->getRealPath());
            } elseif ($ext === 'png') {
                $image = imagecreatefrompng($file->getRealPath());
            }

            if ($image) {
                if (imagewebp($image, $fullPath, 80)) {
                    if ($fileName && file_exists(public_path('photo/' . $fileName))) {
                        unlink(public_path('photo/' . $fileName));
                    }

                    imagedestroy($image);
                    $fileName = $newName;
                } else {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Gagal menyimpan gambar WebP.',
                    ], 500);
                }
            } else {
                return response()->json([
                    'status'  => false,
                    'message' => 'Gagal memproses gambar.',
                ], 500);
            }
        }

        $data->update([
            'nama_marketing' => $request->nama_marketing,
            'alamat'         => $request->alamat,
            'jenis_kelamin'  => $request->jenis_kelamin,
            'pekerjaan'      => $request->pekerjaan,
            'no_telp'        => $request->no_telp,
            'foto'           => $fileName,
            'status'         => $request->status,
            'nama_bank'      => $request->nama_bank,
            'no_rekening'    => $request->no_rekening,
            'atas_nama'      => $request->atas_nama,
        ]);

        return response()->json(['status' => 'success', 'message' => 'Data berhasil diperbarui!']);

    }

    public function destroy($id_marketing)
    {
        try {
            $user = Marketing::findOrFail($id_marketing);
            $user->delete();

            return response()->json(['status' => 'success', 'message' => 'Data berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal menghapus data'], 500);
        }
    }
}
