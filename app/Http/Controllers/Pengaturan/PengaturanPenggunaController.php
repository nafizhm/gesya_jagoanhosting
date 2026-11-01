<?php

namespace App\Http\Controllers\Pengaturan;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\HakAkses;
use Illuminate\Http\Request;
use App\Models\PengaturanPengguna;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\Pengaturan\HakAksesController;
use App\Models\LevelUser;
use Illuminate\Support\Facades\Auth;

class PengaturanPenggunaController extends Controller
{
    public function index(Request $request)
    {
        $permissions = HakAksesController::getUserPermissions();

        if ($request->ajax()) {
            $data = PengaturanPengguna::where('user_from', 1)
                ->when(Auth::user()->username != 'dev', function ($query) {
                    $query->where('username', '!=', 'dev');
                })
                ->orderBy('id', 'desc');

            return DataTables::of($data)
                ->addIndexColumn()

                ->editColumn('role', function ($row) {
                    switch ($row->role) {
                        case 1:
                            return '<span class="badge bg-info text-dark">Marketing</span>';
                        case 2:
                            return '<span class="badge bg-success">Admin</span>';
                        case 3:
                            return '<span class="badge bg-warning text-dark">Manager</span>';
                        case 4:
                            return '<span class="badge bg-warning text-dark">Super Admin</span>';
                        default:
                            return '<span class="badge bg-secondary">Tidak Diketahui</span>';
                    }
                })

                ->addColumn('action', function ($row) use ($permissions) {
                    $editUrl = route('pengaturan-pengguna.edit', $row->id);
                    $deleteUrl = route('pengaturan-pengguna.destroy', $row->id);

                    $btn = '<div class="d-flex justify-content-center">';
                    if ($permissions['edit']) {
                        $btn .= '<button class="btn btn-primary btn-sm mx-1" data-id="' . e($row->id) . '"
                                    data-url="' . e($editUrl) . '" data-toggle="modal" data-target="#modalForm" id="edit-button">
                                    Edit
                                </button>';
                    }

                    if ($permissions['hapus']) {
                        $btn .= '<form action="' . e($deleteUrl) . '" method="POST" style="display:inline;">
                        ' . csrf_field() . method_field('DELETE') . '
                        <button type="submit" class="delete-button btn btn-danger btn-sm mx-1">
                            Hapus
                        </button>
                        </form>';
                    }

                    $btn .= '</div>';
                    return $btn;
                })

                ->rawColumns(['role', 'action'])
                ->make(true);
        }

        $roles = LevelUser::select('id', 'level_user')->get();

        return view('admin.pengaturan.pengaturan_pengguna.index', compact('roles', 'permissions'));
    }

    public function edit($id)
    {
        $list = PengaturanPengguna::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $list,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'surname' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'nullable|string|min:6',
            'status' => 'required|in:AKTIF,BLOKIR',
            'role' => 'required',
        ], [
            'surname.required' => 'Nama lengkap wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'password.min' => 'Password minimal 6 karakter.',
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status tidak valid.',
            'role.required' => 'Role wajib dipilih.',
        ]);

        $db = [
            'surname' => $request->surname,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => $request->status,
            'role' => $request->role,
        ];

        $user = PengaturanPengguna::create($db);

        $menus = Menu::all();

        foreach ($menus as $menu) {
            $akses = [
                'id_user' => $user->id,
                'id_menu' => $menu->id,
            ];

            if ($user->role == 4) {
                $akses['lihat'] = $menu->lihat;
                $akses['tambah'] = $menu->tambah;
                $akses['edit'] = $menu->edit;
                $akses['hapus'] = $menu->hapus;
            } elseif ($user->role == 2) {
                $akses['lihat'] = $menu->lihat;
                $akses['tambah'] = $menu->tambah;
                $akses['edit'] = $menu->edit;
                $akses['hapus'] = $menu->hapus;
            } elseif ($user->role == 3) {
                $akses['lihat'] = 0;
                $akses['tambah'] = 0;
                $akses['edit'] = 0;
                $akses['hapus'] = 0;
            } else {
                $akses['lihat'] = 0;
                $akses['tambah'] = 0;
                $akses['edit'] = 0;
                $akses['hapus'] = 0;
            }

            HakAkses::create($akses);
        }

        return response()->json(['status' => 'success']);
    }

    public function update(Request $request, $id)
    {
        $data = PengaturanPengguna::findOrFail($id);

        $request->validate([
            'surname' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'nullable|string|min:6',
            'status' => 'required|in:AKTIF,BLOKIR',
            'role' => 'required',
        ], [
            'surname.required' => 'Nama lengkap wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'password.min' => 'Password minimal 6 karakter.',
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status tidak valid.',
            'role.required' => 'Role wajib dipilih.',
        ]);

        $db = [
            'surname' => $request->surname,
            'username' => $request->username,
            'email' => $request->email,
            'status' => $request->status,
            'role' => $request->role,
        ];

        if (!empty($request->password)) {
            $db['password'] = Hash::make($request->password);
        }

        $data->update($db);

        return response()->json(['status' => 'success']);
    }

    public function destroy($id)
    {
        $data = PengaturanPengguna::findOrFail($id);
        $hakAkses = HakAkses::where('id_user', $data->id)->delete();
        $data->delete();

        return response()->json(['status' => 'success']);
    }
}
