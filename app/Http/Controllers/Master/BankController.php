<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Pengaturan\HakAksesController;
use App\Models\BankModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class BankController extends Controller
{
    public function index(Request $request)
    {
        Carbon::setLocale('id');

        $permissions = HakAksesController::getUserPermissions();

        if ($request->ajax()) {
            $opd = BankModel::orderBy('id_bank', 'desc');

            return DataTables::of($opd)
                ->addIndexColumn()
                ->addColumn('action', function ($row) use ($permissions) {
                    $editUrl = route('bank.edit', $row->id_bank);
                    $deleteUrl = route('bank.destroy', $row->id_bank);

                    $btn = '<div class="d-flex justify-content-center">';
                    if ($permissions['edit']) {
                        $btn .= '<button class="btn btn-primary btn-sm edit-button" data-id="' . e($row->id_bank) . '" data-url="' . e($editUrl) . '">Edit</button>';
                    }

                    if ($permissions['hapus']) {
                        $btn .= '<form action="' . e($deleteUrl) . '" method="POST" style="display:inline;">' . csrf_field() . method_field('DELETE') . '<button type="submit" class="delete-button btn btn-danger btn-sm ml-2">Hapus</button></form>';
                    }
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.master.bank.index', compact('permissions'));
    }

    public function edit($id)
    {
        $data = BankModel::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data'   => $data,
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'kode_bank'       => 'required|unique:bank,kode_bank',
            'bank'            => 'required|unique:bank,bank',
            'keterangan_bank' => 'required',
        ];

        $messages = [
            'kode_bank.required'       => 'Kode Bank wajib diisi.',
            'kode_bank.unique'         => 'Kode Bank sudah terdaftar.',
            'bank.required'            => 'Nama Bank wajib diisi.',
            'bank.unique'              => 'Nama Bank sudah terdaftar.',
            'keterangan_bank.required' => 'Keterangan Bank wajib diisi.',
        ];

        $request->validate($rules, $messages);

        DB::beginTransaction();
        try {
            $db = [
                'kode_bank'       => $request->kode_bank,
                'bank'            => $request->bank,
                'keterangan_bank' => $request->keterangan_bank,
            ];

            BankModel::create($db);

            DB::commit();

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan bank: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'error'  => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $data = BankModel::findOrFail($id);

        $rules = [
            'kode_bank'       => 'required|unique:bank,kode_bank,' . $data->id_bank . ',id_bank',
            'bank'            => 'required|unique:bank,bank,' . $data->id_bank . ',id_bank',
            'keterangan_bank' => 'required',
        ];

        $messages = [
            'kode_bank.required'       => 'Kode Bank wajib diisi.',
            'kode_bank.unique'         => 'Kode Bank sudah terdaftar.',
            'bank.required'            => 'Nama Bank wajib diisi.',
            'bank.unique'              => 'Nama Bank sudah terdaftar.',
            'keterangan_bank.required' => 'Keterangan Bank wajib diisi.',
        ];

        $request->validate($rules, $messages);

        DB::beginTransaction();
        try {
            $db = [
                'kode_bank'       => $request->kode_bank,
                'bank'            => $request->bank,
                'keterangan_bank' => $request->keterangan_bank,
            ];

            $data->update($db);

            DB::commit();

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal mengupdate bank: ' . $e->getMessage());
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
            $data = BankModel::findOrFail($id);
            $data->delete();

            DB::commit();

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menghapus bank: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'error'  => $e->getMessage(),
            ], 500);
        }
    }

    public function getBankList(Request $request)
    {
        $query = BankModel::query();

        if ($request->has('q')) {
            $query->where('bank', 'like', '%' . $request->q . '%')
                ->orWhere('kode_bank', 'like', '%' . $request->q . '%');
        }

        $banks = $query->limit(10)->get(['id_bank', 'kode_bank', 'bank']);

        return response()->json($banks);
    }
}
