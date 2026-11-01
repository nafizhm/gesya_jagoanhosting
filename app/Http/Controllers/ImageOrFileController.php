<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\File;

class ImageOrFileController extends Controller
{
    public function getLegalFotoListrik($image)
    {
        $path = storage_path('app/private/legal/listrik_air/foto_listrik/' . $image);

        if (file_exists($path)) {
            return response()->file($path);
        }

        abort(404, 'Foto listrik tidak ditemukan.');
    }

    public function getLegalFotoListrik2($image)
    {
        $path = storage_path('app/private/legal/listrik_air/foto_listrik_2/' . $image);

        if (file_exists($path)) {
            return response()->file($path);
        }

        abort(404, 'Foto listrik 2 tidak ditemukan.');
    }

    public function getLegalFotoAir($image)
    {
        $path = storage_path('app/private/legal/listrik_air/foto_air/' . $image);

        if (file_exists($path)) {
            return response()->file($path);
        }

        abort(404, 'Foto air tidak ditemukan.');
    }

    public function getLegalFotoAir2($image)
    {
        $path = storage_path('app/private/legal/listrik_air/foto_air_2/' . $image);

        if (file_exists($path)) {
            return response()->file($path);
        }

        abort(404, 'Foto air 2 tidak ditemukan.');
    }

    public function getPercakapanWa($image)
    {
        $path = storage_path('app/private/legal/pengajuan_berkas/percakapan_wa/' . $image);

        if (file_exists($path)) {
            return response()->file($path);
        }

        abort(404, 'Percakapan WA tidak ditemukan.');
    }
}
