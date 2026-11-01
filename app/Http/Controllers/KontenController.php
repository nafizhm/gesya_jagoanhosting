<?php

namespace App\Http\Controllers;

use App\Models\Konten;
use Illuminate\Http\Request;
use App\Models\Marketing;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Pengaturan\HakAksesController;

class KontenController extends Controller
{
    public function index(Request $request)
    {
        $permissions = HakAksesController::getUserPermissions();

        if ($request->ajax()) {
            $opd = Konten::query();

            return DataTables::of($opd)
                ->addIndexColumn()
                ->addColumn('icon', function ($row) {
                    return $row->icon ? '<i class="' . e($row->icon) . '" style="font-size:14px;"></i>' : '';
                })

                 ->editColumn('jenis_content', function ($row) {
                $jenisContentList = [
                    1 => 'Slider',
                    2 => 'About Us',
                    3 => 'Primary Content',
                    4 => 'Secondary Content',
                    5 => 'Tertiary Content',
                    6 => 'Fasility',
                    7 => 'Footer',
                    8 => 'Navbar Item',
                    9 => 'Navbar Logo',
                ];
                return $jenisContentList[$row->jenis_content] ?? 'Tidak Diketahui';
            })

                ->addColumn('action', function ($row) use ($permissions) {
                    $editUrl = route('konten.edit', $row->id_content);
                    $deleteUrl = route('konten.destroy', $row->id_content);

                    $btn = '<div class="d-flex justify-content-center">';

                    if ($permissions['edit'] == 1) {
                    $btn .= '<button class="btn btn-primary btn-sm mx-1" data-id="' . e($row->id_content) . '"
                         data-url="' . e($editUrl) . '" data-toggle="modal" data-target="#modalForm" id="edit-button">
                         Edit
                     </button>';
                    }

                    if ($permissions['hapus'] == 1) {
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
                ->rawColumns(['action', 'icon'])
                ->make(true);

        }

        return view('admin.konten.index', compact('permissions'));
    }

public function store(Request $request)
{
    $jenis = $request->jenis_content;

    $baseRules = [
        'jenis_content' => 'required|string|max:45',
    ];

    $messages = [
        'jenis_content.required' => 'Jenis Konten wajib diisi.',
        'jenis_content.string' => 'Jenis Konten harus berupa teks.',
        'jenis_content.max' => 'Jenis Konten tidak boleh lebih dari 45 karakter.',
    ];

    if (in_array($jenis, ['6', '7'])) {
        $baseRules['judul'] = 'required|string|max:255';
        $baseRules['artikel'] = 'required|string';
        $baseRules['icon'] = 'required';
        $baseRules['nama_file'] = 'nullable|file|max:2048';

        $messages += [
            'judul.required' => 'Judul wajib diisi.',
            'artikel.required' => 'Artikel wajib diisi.',
            'icon.required' => 'Icon wajib dipilih.',
            'nama_file.mimes' => 'File harus berupa gambar (jpg, jpeg, png) atau PDF.',
            'nama_file.max' => 'Ukuran file maksimal 2MB.',
        ];
    } elseif ($jenis == '8') {
        $baseRules['judul'] = 'required|string|max:255';
        $baseRules['url_item'] = 'required|string';
        $baseRules['nama_file'] = 'nullable|file|max:2048';

        $messages += [
            'judul.required' => 'Judul wajib diisi.',
            'url_item.required' => 'Url wajib diisi.',
        ];
    } elseif ($jenis == '9') {
        $baseRules['nama_file'] = 'required|file|max:2048';

        $messages += [
            'nama_file.required' => 'File Konten wajib diisi.',
            'nama_file.file' => 'File yang diunggah tidak valid.',
            'nama_file.mimes' => 'File harus berupa gambar (jpg, jpeg, png) atau PDF.',
            'nama_file.max' => 'Ukuran file maksimal 2MB.',
        ];
    } elseif(in_array($jenis, ['4', '5'])) {
        $baseRules['judul'] = 'required|string|max:255';
        $baseRules['url_item'] = 'required|string';
        $baseRules['artikel'] = 'required|string';

        $messages += [
            'judul.required' => 'judul Konten wajib diisi.',
            'url_item.required' => 'Url wajib diisi.',
            'artikel.required' => 'Artikel wajib diisi.',
        ];
    } else {
        $baseRules['judul'] = 'required|string|max:255';
        $baseRules['url_item'] = 'required|string';
        $baseRules['artikel'] = 'required|string';
        $baseRules['icon'] = 'required';
        $baseRules['nama_file'] = 'required|file|max:2048';

        $messages += [
            'judul.required' => 'Judul wajib diisi.',
            'url_item.required' => 'Url wajib diisi.',
            'artikel.required' => 'Artikel wajib diisi.',
            'icon.required' => 'Icon wajib dipilih.',
            'nama_file.required' => 'File Konten wajib diisi.',
            'nama_file.mimes' => 'File harus berupa gambar (jpg, jpeg, png) atau PDF.',
            'nama_file.max' => 'Ukuran file maksimal 2MB.',
        ];
    }

    $validated = $request->validate($baseRules, $messages);

    $data = [
        'jenis_content' => $jenis,
        'judul' => $request->judul,
        'url_item' => $request->url_item,
        'artikel' => $request->artikel,
        'icon' => $request->icon,
        'tanggal' => now(),
    ];

    if ($request->hasFile('nama_file')) {
        $file = $request->file('nama_file');
        $ext = strtolower($file->getClientOriginalExtension());
        $filenameWithoutExt = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $destinationPath = public_path('konten');

        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
            $imagePath = $file->getRealPath();
            $webpName = $filenameWithoutExt . '.webp';
            $outputPath = $destinationPath . '/' . $webpName;

            if ($ext === 'png') {
                $img = @imagecreatefrompng($imagePath);
            } else {
                $img = imagecreatefromjpeg($imagePath);
            }

            imagepalettetotruecolor($img);
            imagewebp($img, $outputPath, 75);
            imagedestroy($img);

            $data['nama_file'] = $webpName;
        } else {
            $originalName = $file->getClientOriginalName();
            $file->move($destinationPath, $originalName);
            $data['nama_file'] = $originalName;
        }
    }

    Konten::create($data);

    return response()->json(['success' => true, 'message' => 'Data berhasil ditambahkan']);
}

    public function edit($id)
    {
        $list = Konten::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $list,
        ]);
    }


  public function update(Request $request, $id)
    {
        $konten = Konten::findOrFail($id);
        $jenis = $request->jenis_content;

        $rules = [
            'jenis_content' => 'required|string|max:255',
        ];

        $messages = [
            'jenis_content.required' => 'Jenis Konten wajib diisi.',
        ];

        if (in_array($jenis, ['6', '7'])) {
            $rules['judul'] = 'required|string|max:255';
            $rules['artikel'] = 'required|string';
            $rules['icon'] = 'required|string';
            $rules['nama_file'] = 'nullable|file|max:2048';

            $messages += [
                'judul.required' => 'Judul wajib diisi.',
                'artikel.required' => 'Artikel wajib diisi.',
                'icon.required' => 'Icon wajib dipilih.',
            ];
        } elseif(in_array($jenis, ['8', '4', '5'])) {
            $rules['judul'] = 'required|string|max:255';
            $rules['url_item'] = 'required|string|max:255';
            $rules['nama_file'] = 'nullable|file|max:2048';
            $rules['artikel'] = 'nullable|string';
            $rules['icon'] = 'nullable|string';

            $messages += [
                'url_item.required' => 'Url wajib diisi.',
                'judul.required' => 'Judul wajib diisi.',
            ];
        } elseif ($jenis == '9') {
            $rules['nama_file'] = 'nullable|file|max:2048';
            $rules['judul'] = 'nullable|string|max:255';
            $rules['artikel'] = 'nullable|string';
            $rules['icon'] = 'nullable|string';
        } else {
            $rules += [
                'url_item' => 'required|string|max:255',
                'judul' => 'required|string|max:255',
                'nama_file' => 'nullable|file|max:2048',
                'artikel' => 'required|string',
            ];

            $messages += [
                'url_item.required' => 'Url wajib diisi.',
                'judul.required' => 'Judul wajib diisi.',
                'artikel.required' => 'Artikel wajib diisi.',
            ];
        }

        $request->validate($rules, $messages);

        $konten->jenis_content = $request->jenis_content;
        $konten->judul = $request->judul;
        $konten->url_item = $request->url_item;
        $konten->artikel = $request->artikel;
        $konten->icon = $request->icon;

        if ($request->hasFile('nama_file')) {
            if ($konten->nama_file && file_exists(public_path('konten/' . $konten->nama_file))) {
                unlink(public_path('konten/' . $konten->nama_file));
            }

            $file = $request->file('nama_file');
            $ext = strtolower($file->getClientOriginalExtension());
            $filenameWithoutExt = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $destinationPath = public_path('konten');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                $imagePath = $file->getRealPath();
                $webpName = time() . '_' . preg_replace('/[^a-zA-Z0-9-_]/', '_', $filenameWithoutExt) . '.webp';
                $outputPath = $destinationPath . '/' . $webpName;

                if ($ext === 'png') {
                    $img = @imagecreatefrompng($imagePath);
                } else {
                    $img = imagecreatefromjpeg($imagePath);
                }

                imagepalettetotruecolor($img);
                imagewebp($img, $outputPath, 75);
                imagedestroy($img);

                $konten->nama_file = $webpName;
            } else {
                $originalName = time() . '_' . $file->getClientOriginalName();
                $file->move($destinationPath, $originalName);
                $konten->nama_file = $originalName;
            }
        }

        $konten->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil diperbarui.'
        ]);
    }

    public function homepage()
    {
        $sliders = Konten::where('jenis_content', 1)->whereNotNull('nama_file')->get();
        $aboutUs = Konten::where('jenis_content', 2)->first();


        $primaryContent = Konten::where('jenis_content', 3)->get();
        $secondaryContent = Konten::where('jenis_content', 4)->first();
        $tertiaryContent = Konten::where('jenis_content', 5)->first();
        $facilities = Konten::where('jenis_content', 6)
            ->whereRaw('LENGTH(artikel) > 20')
            ->get();
        $footer = Konten::where('jenis_content', 7)->get();
        $logo = Konten::where('jenis_content', '9')->first();
        $navItems = Konten::where('jenis_content', '8')->get();

        $beranda = Konten::where('jenis_content', 1)->first();
        $fasilitas = Konten::where('jenis_content', 6)->first();
        $product = Konten::where('jenis_content', 3)->first();

        $marketing = Marketing::where('status', 1)->get();


        return view('homepage', compact(
            'sliders',
            'aboutUs',
            'primaryContent',
            'secondaryContent',
            'tertiaryContent',
            'facilities',
            'footer',
            'navItems',
            'logo',
            'beranda',
            'fasilitas',
            'product',
            'marketing',
        ));
    }


    public function destroy($id)
    {
        $data = Konten::findOrFail($id);

        $data->delete();

        return response()->json(['status' => 'success', 'message' => 'Akad Berhasil Dihapus!']);
    }


}
