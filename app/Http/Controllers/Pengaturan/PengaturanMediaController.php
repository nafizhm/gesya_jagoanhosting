<?php

namespace App\Http\Controllers\Pengaturan;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PengaturanMedia;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\Pengaturan\HakAksesController;

class PengaturanMediaController extends Controller
{
   public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = PengaturanMedia::query();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('gambar', function ($row) {
                    $url = asset('config_media/' . $row->nama_file);

                    return '<img src="' . $url . '" height="50">';
                })
                ->addColumn('action', function ($row) {
                    $fileUrl = asset('config_media/' . $row->nama_file);

                    $previewBtn = '<button class="btn btn-xs btn-warning preview-button"
                                        data-id="' . $row->id . '"
                                        data-file="' . $fileUrl . '"
                                        data-nama="' . $row->nama_file . '"
                                        data-toggle="modal"
                                        data-target="#previewModal">
                                    Preview
                                </button>';

                    $uploadBtn = '<button class="btn btn-xs btn-primary upload-button"
                                        data-id="' . $row->id . '"
                                        data-toggle="modal"
                                        data-target="#uploadModal">
                                        Update File
                                </button>
                                <form id="upload-form-' . $row->id . '"
                                        enctype="multipart/form-data"
                                        style="display:none;">
                                    <input type="file" name="nama_file"
                                            class="file-input" data-id="' . $row->id . '">
                                </form>';

                    return $previewBtn . ' ' . $uploadBtn;
                })
                ->rawColumns(['gambar', 'action'])
                ->make(true);
        }

        $data = PengaturanMedia::first();
        $permissions = HakAksesController::getUserPermissions();

        return view('admin.pengaturan.pengaturan_media.index', compact('data', 'permissions'));
    }



    public function update(Request $request, $id)
    {
        $konfigurasiMedia = PengaturanMedia::findOrFail($id);

        $request->validate([
            'nama_file' => 'required|image|max:3048',
        ]);

        if ($request->hasFile('nama_file')) {
            $file = $request->file('nama_file');
            $extension = strtolower($file->getClientOriginalExtension());

            $imagePath = $file->getPathname();
            $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $webpName = $filename . '.webp';

            $destinationPath = public_path('config_media');
            $fullSavePath = $destinationPath . '/' . $webpName;

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            $oldFilePath = $destinationPath . '/' . $konfigurasiMedia->nama_file;
            if ($konfigurasiMedia->nama_file && file_exists($oldFilePath)) {
                unlink($oldFilePath);
            }

            switch ($extension) {
                case 'jpeg':
                case 'jpg':
                    $img = imagecreatefromjpeg($imagePath);
                    break;
                case 'png':
                    $img = @imagecreatefrompng($imagePath);
                    imagepalettetotruecolor($img);
                    imagealphablending($img, true);
                    imagesavealpha($img, true);
                    break;
                default:
                    return response()->json(['error' => 'Format gambar tidak didukung'], 422);
            }

            imagewebp($img, $fullSavePath, 80);
            imagedestroy($img);

            $konfigurasiMedia->update([
                'nama_file' => $webpName,
            ]);
        }

        return response()->json(['success' => 'Foto berhasil dikompres & diupdate'], 200);
    }



}
