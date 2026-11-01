<?php
namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Pengaturan\HakAksesController;
use App\Models\Freelance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class MarketingFreelanceController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $program     = Freelance::query();
            $permissions = HakAksesController::getUserPermissions();

            return DataTables::of($program)
                ->addIndexColumn()

                ->addColumn('action', function ($row) use ($permissions) {
                    $actions = '<div class="text-center">';

                    if ($permissions['edit']) {
                        $actions .= '<button type="button" onclick="modalAction(\'' . route('marketing-freelance.edit', $row->id_freelance) . '\')" class="edit btn btn-primary btn-sm">Edit</button> ';
                    }

                    if ($permissions['hapus']) {
                        $actions .= '
                        <form action="' . route('marketing-freelance.destroy', $row->id_freelance) . '" method="POST" style="display:inline;">
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
        $data        = Freelance::first();

        return view('admin.marketing.marketing_freelance.index', compact('data', 'permissions'));
    }

    public function create()
    {
        return view('admin.marketing.marketing_freelance.index', compact('title'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'nama_freelance'    => 'required|string|max:20',
            'alamat'            => 'required|string|max:50',
            'jenis_kelamin'     => 'required|max:100',
            'pekerjaan'         => 'required',
            'no_telp_freelance' => 'required',
            'foto'              => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'status'            => 'nullable',
            'nama_bank'         => 'nullable',
            'no_rekening'       => 'nullable',
            'atas_nama'         => 'nullable',
        ], [
            'nama_freelance.required'    => 'Nama freelance wajib diisi.',
            'nama_freelance.string'      => 'Nama freelance harus berupa teks.',
            'nama_freelance.max'         => 'Nama freelance maksimal 20 karakter.',

            'alamat.required'            => 'Alamat wajib diisi.',
            'alamat.string'              => 'Alamat harus berupa teks.',
            'alamat.max'                 => 'Alamat maksimal 50 karakter.',

            'jenis_kelamin.required'     => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.max'          => 'Jenis kelamin tidak valid.',

            'pekerjaan.required'         => 'Pekerjaan wajib diisi.',

            'no_telp_freelance.required' => 'Nomor telepon wajib diisi.',

            'foto.file'                  => 'Foto harus berupa file.',
            'foto.mimes'                 => 'Format foto harus jpg, jpeg, atau png.',
            'foto.max'                   => 'Ukuran foto maksimal 2MB.',
        ]);

        $lastfreelance = DB::table('freelance')
            ->where('kode_freelance', 'like', 'FR-%')
            ->orderByDesc('id_freelance')
            ->first();

        if ($lastfreelance && preg_match('/FR-(\d+)/', $lastfreelance->kode_freelance, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }

        $kodeFreelance = 'FR-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

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
                $imageResource = null;
                if ($extension === 'jpg' || $extension === 'jpeg') {
                    $imageResource = imagecreatefromjpeg($file->getPathname());
                } elseif ($extension === 'png') {
                    $imageResource = imagecreatefrompng($file->getPathname());
                }

                if ($imageResource) {
                    imagewebp($imageResource, $destinationPath . '/' . $webpName, 80);
                    imagedestroy($imageResource);
                    $fileName = $webpName;
                }
            } else {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move($destinationPath, $fileName);
            }
        }

        Freelance::create([
            'kode_freelance'    => $kodeFreelance,
            'nama_freelance'    => $request->nama_freelance,
            'alamat'            => $request->alamat,
            'jenis_kelamin'     => $request->jenis_kelamin,
            'pekerjaan'         => $request->pekerjaan,
            'no_telp_freelance' => $request->no_telp_freelance,
            'foto'              => $fileName,
            'status'            => $request->status,
            'nama_bank'         => $request->nama_bank,
            'no_rekening'       => $request->no_rekening,
            'atas_nama'         => $request->atas_nama,
            'id_level'          => $request->id_level ?? '0',
            'stt_freelance'     => $request->stt_freelance ?? '0',
            'tempat_lahir'      => $request->tempat_lahir ?? '',
        ]);

        return response()->json(['status' => 'success']);
    }

    public function edit($id_marketing)
    {
        $data = Freelance::find($id_marketing);
        return view('admin.marketing.marketing_freelance.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_freelance'    => 'required|string|max:20',
            'alamat'            => 'required|string|max:50',
            'jenis_kelamin'     => 'required|max:100',
            'pekerjaan'         => 'required',
            'no_telp_freelance' => 'required',
            'foto'              => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'status'            => 'nullable',
            'nama_bank'         => 'nullable',
            'no_rekening'       => 'nullable',
            'atas_nama'         => 'nullable',
        ], [
            'nama_freelance.required'    => 'Nama freelance wajib diisi.',
            'nama_freelance.string'      => 'Nama freelance harus berupa teks.',
            'nama_freelance.max'         => 'Nama freelance maksimal 20 karakter.',

            'alamat.required'            => 'Alamat wajib diisi.',
            'alamat.string'              => 'Alamat harus berupa teks.',
            'alamat.max'                 => 'Alamat maksimal 50 karakter.',

            'jenis_kelamin.required'     => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.max'          => 'Jenis kelamin tidak valid.',

            'pekerjaan.required'         => 'Pekerjaan wajib diisi.',

            'no_telp_freelance.required' => 'Nomor telepon wajib diisi.',

            'foto.file'                  => 'Foto harus berupa file.',
            'foto.mimes'                 => 'Format foto harus jpg, jpeg, atau png.',
            'foto.max'                   => 'Ukuran foto maksimal 2MB.',
        ]);

        $freelance = Freelance::findOrFail($id);
        $fileName  = $freelance->foto;

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

            if ($fileName && file_exists($destinationPath . '/' . $fileName)) {
                unlink($destinationPath . '/' . $fileName);
            }

            if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
                $imageResource = null;
                if ($extension === 'jpg' || $extension === 'jpeg') {
                    $imageResource = imagecreatefromjpeg($file->getPathname());
                } elseif ($extension === 'png') {
                    $imageResource = imagecreatefrompng($file->getPathname());
                }

                if ($imageResource) {
                    imagewebp($imageResource, $destinationPath . '/' . $webpName, 80);
                    imagedestroy($imageResource);
                    $fileName = $webpName;
                }
            }
        }

        $freelance->update([
            'nama_freelance'    => $request->nama_freelance,
            'alamat'            => $request->alamat,
            'jenis_kelamin'     => $request->jenis_kelamin,
            'pekerjaan'         => $request->pekerjaan,
            'no_telp_freelance' => $request->no_telp_freelance,
            'foto'              => $fileName,
            'status'            => $request->status,
            'nama_bank'         => $request->nama_bank,
            'no_rekening'       => $request->no_rekening,
            'atas_nama'         => $request->atas_nama,
        ]);

        return response()->json(['status' => 'success']);
    }

    public function destroy($id_freelance)
    {
        try {
            $user = Freelance::findOrFail($id_freelance);
            $user->delete();

            return response()->json(['status' => 'success', 'message' => 'Data berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal menghapus data'], 500);
        }
    }
}
