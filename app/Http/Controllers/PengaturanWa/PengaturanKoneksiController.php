<?php

namespace App\Http\Controllers\PengaturanWa;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KonfigurasiWa;
use App\Http\Controllers\Pengaturan\HakAksesController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
class PengaturanKoneksiController extends Controller
{
    public function index()
    {
        $data        = KonfigurasiWa::first();
        $permissions = HakAksesController::getUserPermissions();

        return view('admin.pengaturan_wa.pengaturan_koneksi.index', compact('data', 'permissions'));
    }

    public function store(Request $request)
    {
        $rules = [
            'api_key'    => 'required',
            'number_key' => 'required',
        ];

        $messages = [
            'api_key.required'    => 'Api key wajib diisi.',
            'number_key.required' => 'Number key wajib diisi.',
        ];

        $request->validate($rules, $messages);

        DB::beginTransaction();
        try {
            $kredensial = KonfigurasiWa::first();

            $kredensial->update([
                'api_key'    => $request->api_key,
                'number_key' => $request->number_key,
            ]);

            DB::commit();

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'error'  => $e->getMessage(),
            ], 500);
        }
    }

    public function kirimPesanWa(Request $request)
    {
        $request->validate([
            'no_wa'   => 'required',
            'message' => 'required',
        ], [
            'no_wa.required'   => 'Nomor WhatsApp wajib diisi.',
            'message.required' => 'Pesan wajib diisi.',
        ]);

        $kredensial = KonfigurasiWa::first();

        Http::post('https://api.watzap.id/v1/send_message', [
            'api_key'    => $kredensial->api_key,
            'number_key' => $kredensial->number_key,
            'phone_no'   => $request->no_wa,
            'message'    => $request->message,
        ]);

        return response()->json(['status' => 'success']);
    }
}
