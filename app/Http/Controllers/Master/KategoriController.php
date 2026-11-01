<?php
namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Pengaturan\HakAksesController;
use App\Models\KategoriModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class KategoriController extends Controller
{
    public function index(Request $request)
    {
        Carbon::setLocale('id');

        $permissions = HakAksesController::getUserPermissions();

        if ($request->ajax()) {
            $data = KategoriModel::orderBy('kategori_id', 'desc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) use ($permissions) {$showUrl = route('kategori.show', $row->kategori_id);
                    $deleteUrl                          = route('kategori.destroy', $row->kategori_id);

                    $btn = '<div class="d-flex justify-content-center">';
                    if ($permissions['edit']) {
                        $btn .= '<button class="btn btn-primary btn-sm edit-button" data-id="' . e($row->kategori_id) . '" data-url="' . e($showUrl) . '">Edit</button>';
                    }

                    if ($permissions['hapus']) {
                        $btn .= '<form action="' . e($deleteUrl) . '" method="POST" style="display:inline;">' . csrf_field() . method_field('DELETE') . '<button type="submit" class="delete-button btn btn-danger btn-sm ml-2">Hapus</button></form>';
                    }
                    $btn .= '</div>';
                    return $btn;})
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.master.kategori.index', compact('permissions'));
    }

    public function show($id)
    {
        $data = KategoriModel::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data'   => $data,
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'kategori'       => 'required|unique:kategori,kategori',
            'jenis_kategori' => 'required',
        ];

        $messages = [
            'kategori.required'       => 'Kategori wajib diisi.',
            'kategori.unique'         => 'Kategori sudah digunakan.',
            'jenis_kategori.required' => 'Jenis kategori wajib diisi.',
        ];

        $request->validate($rules, $messages);

        DB::beginTransaction();
        try {

            $db = [
                'kategori'       => $request->kategori,
                'jenis_kategori' => $request->jenis_kategori,
                'stt_fix'        => 0,
            ];

            KategoriModel::create($db);

            DB::commit();

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan kategori: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'error'  => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $data = KategoriModel::findOrFail($id);

        $rules = [
            'kategori'       => 'required|unique:kategori,kategori,' . $data->kategori_id . ',kategori_id',
            'jenis_kategori' => 'required',
        ];

        $messages = [
            'kategori.required'       => 'Kategori wajib diisi.',
            'kategori.unique'         => 'Kategori sudah digunakan.',
            'jenis_kategori.required' => 'Jenis kategori wajib diisi.',
        ];
        $request->validate($rules, $messages);

        DB::beginTransaction();
        try {

            $db = [
                'kategori'       => $request->kategori,
                'jenis_kategori' => $request->jenis_kategori,
            ];

            $data->update($db);

            DB::commit();

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal mengupdate kategori: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'error'  => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $data = KategoriModel::findOrFail($id);
            $data->delete();

            DB::commit();

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menghapus kategori: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'error'  => $e->getMessage(),
            ], 500);
        }
    }
}
