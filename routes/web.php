<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Pengaturan\HakAksesController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\ImageOrFileController;
use App\Http\Controllers\KontenController;
use App\Http\Controllers\BerandaController;
use App\Models\Customer;

use App\Http\Controllers\Siteplan\SiteplanUnitReadyController;
use App\Http\Controllers\Siteplan\SiteplanPenjualanController;
use App\Http\Controllers\Siteplan\SiteplanProyekController;

use App\Http\Controllers\Transaksi\WawancaraController;
use App\Http\Controllers\Transaksi\AccBankController;
use App\Http\Controllers\Transaksi\AkadController;
use App\Http\Controllers\Transaksi\PindahUnitController;
use App\Http\Controllers\Transaksi\PembelianCancelController;
use App\Http\Controllers\Transaksi\GantiNamaController;
use App\Http\Controllers\Transaksi\BASTController;

use App\Http\Controllers\Legal\ListrikAirController;
use App\Http\Controllers\Legal\BerkasPengajuanController;

use App\Http\Controllers\Keuangan\KeuanganController;

use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\Customer\ProspekController;
use App\Http\Controllers\Customer\BerkasCustomerController;
use App\Http\Controllers\Customer\ArsipNasabahController;
use App\Http\Controllers\Customer\RekapLampiranController;

use App\Http\Controllers\Marketing\MarketingOfflineController;
use App\Http\Controllers\Marketing\MarketingFreelanceController;

use App\Http\Controllers\Master\LokasiKavlingController;
use App\Http\Controllers\Master\KavlingController;
use App\Http\Controllers\Master\NotarisController;
use App\Http\Controllers\Master\KategoriController;
use App\Http\Controllers\Master\BankController;
use App\Http\Controllers\Master\PerusahaanController;
use App\Http\Controllers\Pengaturan\pengaturanProfilController;
use App\Http\Controllers\Pengaturan\PengaturanMediaController;
use App\Http\Controllers\Pengaturan\PengaturanPenggunaController;
use App\Http\Controllers\Pengaturan\ListPenjualanController;
use App\Http\Controllers\Pengaturan\ListPembangunanController;
use App\Http\Controllers\PengaturanWa\PengaturanPesanController;
use App\Http\Controllers\PengaturanWa\PengaturanKoneksiController;

// ========== PUBLIC ROUTES ==========

Route::get('/', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $menus = session('getmenus');

    if (!$menus || $menus->isEmpty()) {
        Auth::logout();
        return redirect()->route('login')->withErrors(['msg' => 'Anda tidak punya akses menu.']);
    }

    $beranda = $menus->firstWhere('route_name', 'beranda.index');

    if ($beranda) {
        return redirect()->route('beranda.index');
    }

    $firstMenu = $menus->first();

    if ($firstMenu) {
        if ($firstMenu->children && $firstMenu->children->isNotEmpty()) {
            return redirect()->route($firstMenu->children->first()->route_name);
        }

        return redirect()->route($firstMenu->route_name);
    }

    return redirect()->route('login');
});

Route::get('/booking', [RegisterController::class, 'booking'])->name('booking');
Route::get('/get-kavling/{id_lokasi}/{currentId?}', [RegisterController::class, 'getKavling'])->name('booking.getKavling');
Route::get('/get-harga-kavling/{id_kavling}', [RegisterController::class, 'getHargaKavling'])->name('booking.getHargaKavling');
Route::post('booking/store', [RegisterController::class, 'bookingStore'])->name('store.booking');
Route::get('/booking/sukses', [RegisterController::class, 'bookingSukses'])->name('booking.sukses');

Route::group(['middleware' => 'guest'], function () {
    Route::get('admin/login', [AuthController::class, 'getLogin'])->name('login');
    Route::post('admin/post-login', [AuthController::class, 'postLogin'])->name('admin.loginPost');
});

Route::get('/file/{filename}', function ($filename) {
    $path = storage_path('app/private/' . $filename);
    if (file_exists($path)) {
        return response()->file($path);
    }
    abort(404);
});

Route::get('/refresh-csrf', function () {
    return response()->json(['token' => csrf_token()]);
})->name('refresh.csrf');

Route::get('/paksa-logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/')->with('success', 'Anda telah logout.');
});

// ========== AUTH ROUTES (prefix admin) ==========

Route::middleware(['auth'])->prefix('admin')->group(function () {

    // --- Beranda ---
    Route::resource('beranda', BerandaController::class);

    // --- Dashboard ---
    Route::controller(DashboardController::class)->group(function () {
        Route::get('/dashboard', 'dashboard')->name('dashboard');
        Route::get('/dashboard/lokasi-penjualan/{id}', 'showLokasiPenjualan')->name('dashboard.lokasi-penjualan-show');
        Route::get('/dashboard/customer-status-progres/{id}', 'showCustomer')->name('dashboard.customer-status-progres-show');
        Route::get('/dashboard/customer-bank/{id}', 'showCustomer')->name('dashboard.customer-bank-show');
        Route::get('/dashboard/customer-marketing/{id}', 'showCustomer')->name('dashboard.customer-marketing-show');
        Route::get('/dashboard/customer-freelance/{id}', 'showCustomer')->name('dashboard.customer-freelance-show');
        Route::get('/total-unit', 'totalUnit')->name('total.unit');
        Route::get('/booking-unit', 'booking')->name('booking.unit');
        Route::get('/wawancara-unit', 'wawancara')->name('wawancara.unit');
        Route::get('/akad-unit', 'akad')->name('akad.unit');
    });

    // --- Siteplan ---
    Route::prefix('siteplan')->group(function () {
        // siteplan-unit-ready
        Route::prefix('siteplan-unit-ready')->group(function () {
            Route::get('cetak/pdf/{id_lokasi}', [SiteplanUnitReadyController::class, 'cetakPDF'])->name('siteplan-unit-ready.cetak.pdf');
            Route::get('cetak/jpg/{id_lokasi}', [SiteplanUnitReadyController::class, 'cetakJPG'])->name('siteplan-unit-ready.cetak.jpg');
        });
        Route::resource('siteplan-unit-ready', SiteplanUnitReadyController::class)->names('siteplan-unit-ready');

        // siteplan-penjualan
        Route::prefix('siteplan-penjualan')->group(function () {
            Route::get('cetak/pdf/{id_lokasi}', [SiteplanPenjualanController::class, 'cetakPDF'])->name('siteplan-penjualan.cetak.pdf');
            Route::get('cetak/jpg/{id_lokasi}', [SiteplanPenjualanController::class, 'cetakJPG'])->name('siteplan-penjualan.cetak.jpg');
            Route::get('tagihan/{id_customer}', [SiteplanPenjualanController::class, 'tagihan'])->name('st-penjualan.tagihan');
            Route::post('cetak', [SiteplanPenjualanController::class, 'cetak'])->name('penjualan.cetak');
            Route::get('foto/{id}', [SiteplanPenjualanController::class, 'foto'])->name('st-penjualan.foto');
        });
        Route::resource('siteplan-penjualan', SiteplanPenjualanController::class)->names('siteplan-penjualan');

        // siteplan-proyek
        Route::prefix('siteplan-proyek')->group(function () {
            Route::get('cetak/pdf/{id_lokasi}', [SiteplanProyekController::class, 'cetakPDF'])->name('siteplan-proyek.cetak.pdf');
            Route::get('cetak/jpg/{id_lokasi}', [SiteplanProyekController::class, 'cetakJPG'])->name('siteplan-proyek.cetak.jpg');
        });
        Route::resource('siteplan-proyek', SiteplanProyekController::class)->only(['index', 'show'])->names('siteplan-proyek');
    });

    // --- Pengajuan Hold ---
    Route::resource('pengajuan-hold', RegisterController::class);
    Route::post('/pengajuan-hold/{id}/upload', [RegisterController::class, 'upload'])->name('pengajuan-hold.upload');
    Route::post('/pengajuan-hold/{id}/delete-file', [RegisterController::class, 'deleteFile'])->name('pengajuan-hold.delete-file');
    Route::get('/file-preview/{filename}', function ($filename) {
        $path = public_path("bookingPhoto/{$filename}");
        if (!file_exists($path)) {
            abort(404);
        }
        return response()->file($path);
    })->where('filename', '.*')->name('file.preview');
    Route::get('/pengajuan-hold/{id}/verifikasi', [RegisterController::class, 'verifikasi'])->name('pengajuan-hold.verifikasi');
    Route::post('/pengajuan-hold/{id}/verifikasi', [RegisterController::class, 'simpanVerifikasi'])->name('pengajuan-hold.verifikasi.simpan');

    // --- Pembayaran ---
    Route::controller(PembayaranController::class)->prefix('pembayaran')->group(function () {
        Route::get('get-customer-kpr', 'getCustomerKPR')->name('pembayaran.get-customer-kpr');
        Route::get('/', 'index')->name('pembayaran');
        Route::get('{id}/detail', 'detail')->name('pembayaran.detail');
        Route::get('detail-tagihan/{id}', 'detailTagihan')->name('pembayaran.detail-tagihan');
        Route::get('detail-pemasukan/{id}', 'detailPemasukan')->name('pembayaran.detail-pemasukan');
        Route::put('update-harga-rumah/{id}', 'UpdateHargaRumah')->name('pembayaran.update-harga-rumah');
        Route::get('get-data-kavling/{id}', 'getDataKavling')->name('pembayaran.getDataKavling');
        Route::get('get-data-pemasukan/{id}', 'getDataPemasukan')->name('pembayaran.getDataPemasukan');
        Route::put('update-pemasukan/{id}', 'updatePemasukan')->name('pembayaran.updatePemasukan');
        Route::get('rekap-pembayaran', 'rekapPembayaran')->name('pembayaran.rekap');
        Route::get('jatuh-tempo', 'jatuhTempo')->name('pembayaran.jatuhTempo');
        Route::post('tambah-tagihan/{id}', 'tambahTagihan')->name('pembayaran.tambah-tagihan');
        Route::post('tambah-pemasukan/{id}', 'tambahPemasukan')->name('pembayaran.tambah-pemasukan');
        Route::put('edit-jatuh-tampu/{id}', 'editJatuhTempo')->name('pembayaran.editTempo');
        Route::delete('DeleteTagihan/{id}', 'DeleteTagihan')->name('pembayaran.DeleteTagihan');
        Route::delete('DeletePemasukan/{id}', 'DeletePemasukan')->name('pembayaran.DeletePemasukan');
        Route::get('cetak/{id}', [PembayaranController::class, 'cetak'])->name('pembayaran.Cetak');
        Route::get('print/{id}', [PembayaranController::class, 'print'])->name('pembayaran.Print');
    });
    Route::get('customer/cetak-rekap/{id}', [PembayaranController::class, 'cetakRekap'])->name('customer.cetak_rekap');
    Route::prefix('pengeluaran')->group(function () {
        Route::get('cetak/{id}', [PembayaranController::class, 'cetakPengeluaran'])->name('pengeluaran.Cetak');
    });

    // --- Transaksi ---
    Route::prefix('transaksi')->group(function () {
        // wawancara
        Route::get('wawancara/search', [WawancaraController::class, 'getNasabah'])->name('cari.nasabah');
        Route::get('wawancara/{id}/details', [WawancaraController::class, 'getNasabahDetails'])->name('nasabah.details');
        Route::put('wawancara/{id}/sp3k', [WawancaraController::class, 'simpanSp3k'])->name('wawancara.sp3k');
        Route::get('wawancara/{id}/acc', [WawancaraController::class, 'acc'])->name('wawancara.acc');

        // acc bank
        Route::get('sp3k/data', [AccBankController::class, 'getDataSp3k'])->name('sp3k.data');
        Route::get('acc-bank/arsip', [AccBankController::class, 'arsip'])->name('acc-bank.arsip');
        Route::resource('acc-bank', AccBankController::class);

        // akad
        Route::get('akad/detail-excel/{id}', [AkadController::class, 'cetakDetailExcel'])->name('akad.detail.excel');
        Route::get('akad/detail-pdf/{id}', [AkadController::class, 'cetakDetailPDF'])->name('akad.detail.pdf');
        Route::post('akad/detail/hadir/{id}', [AkadController::class, 'updateHadir'])->name('akad.detail.hadir');
        Route::delete('akad/detail/{id}', [AkadController::class, 'destroyDetail'])->name('akad.detail.destroy');
        Route::post('akad/detail/store', [AkadController::class, 'storeDetailAkad'])->name('akad.detail.store');
        Route::get('akad/{id}/seleksi', [AkadController::class, 'seleksi'])->name('akad.detail.seleksi');
        Route::get('akad/get-customer-detail/{id}', [AkadController::class, 'getCustomerDetail'])->name('akad.getCustomerDetail');
        Route::get('akad/detail/{akad}', [AkadController::class, 'detail'])->name('akad.detail');
        Route::get('akad/detail/data/{id}', [AkadController::class, 'detailData'])->name('akad.detail.data');
        Route::get('akad/download/{id}', [AkadController::class, 'downloadWord'])->name('akad.download');
        Route::resource('akad', AkadController::class);

        // pindah unit
        Route::get('pindah-unit/kwitansi/{id}', [PindahUnitController::class, 'cetakKwitansi'])->name('pindah-unit.kwitansi');
        Route::get('pindah-unit/cetak-word/{id}', [PindahUnitController::class, 'cetakWord'])->name('pindah-unit.cetak-word');
        Route::get('pindah-unit/get-customer-data/{id_customer}', [PindahUnitController::class, 'getCustomerData'])->name('pindah-unit.getCustomerData');
        Route::get('pindah-unit/get-kavling-baru/{id_customer}', [PindahUnitController::class, 'getKavlingBaru'])->name('pindah-unit.getKavlingBaru');
        Route::resource('pindah-unit', PindahUnitController::class);

        // pembelian cancel
        Route::get('pembatalan/get-customer-data/{id_customer}', [PembelianCancelController::class, 'getCustomerData'])->name('pembatalan.getCustomerData');
        Route::resource('pembelian-cancel', PembelianCancelController::class);

        // ganti nama
        Route::get('gantiNama/{id}/detail', [GantiNamaController::class, 'detail'])->name('admin.ganti-nama.detail');
        Route::get('ganti-nama/cetak/{id}', [GantiNamaController::class, 'cetak'])->name('admin.ganti-nama.cetak');
        Route::get('get-customer/{id}', [GantiNamaController::class, 'getCustomer'])->name('get.customer');
        Route::get('get-customers', function () {
            return response()->json(
                Customer::select('id_customer', 'nama_lengkap')->get()
            );
        });
        Route::resource('gantiNama', GantiNamaController::class);

        // bast
        Route::get('bast/generate-no', [BASTController::class, 'generateNoBAST'])->name('generateNoBAST');
        Route::get('get-customer-data/{id_customer}', [BASTController::class, 'getCustomerData'])->name('bast.getCustomerData');
        Route::get('cetak-bast/{id_customer}', [BASTController::class, 'cetakBast'])->name('bast.cetak');
        Route::resource('bast', BASTController::class);

        // wawancara resource
        Route::resource('wawancara', WawancaraController::class);
    });

    // --- Legal ---
    Route::prefix('legal')->group(function () {
        Route::resource('listrik-air', ListrikAirController::class);
        Route::resource('pengajuan-berkas', BerkasPengajuanController::class);
        Route::get('listrik-air/foto-listrik/{image}', [ImageOrFileController::class, 'getLegalFotoListrik'])->name('listrik-air.foto-listrik');
        Route::get('listrik-air/foto-listrik-2/{image}', [ImageOrFileController::class, 'getLegalFotoListrik2'])->name('listrik-air.foto-listrik-2');
        Route::get('listrik-air/foto-air/{image}', [ImageOrFileController::class, 'getLegalFotoAir'])->name('listrik-air.foto-air');
        Route::get('listrik-air/foto-air-2/{image}', [ImageOrFileController::class, 'getLegalFotoAir2'])->name('listrik-air.foto-air-2');
        Route::get('pengajuan-berkas/percakapan-wa/{image}', [ImageOrFileController::class, 'getPercakapanWa'])->name('pengajuan-berkas.percakapan-wa');
    });

    // --- Keuangan ---
    Route::prefix('keuangan')->controller(KeuanganController::class)->group(function () {
        Route::get('keuanganData', 'index')->name('keuanganData');
        Route::post('keuanganData/store', 'store')->name('keuanganData.store');
        Route::delete('keuanganData/delete/{transaksi_id}', 'destroy')->name('keuanganData.destroy');
        Route::get('cetak_pdf/cetak_transaksi', 'cetakTransaksi')->name('cetak.transaksi');
        Route::get('kategori/search', 'search')->name('kategori.search');
    });

    // --- Customer ---
    Route::prefix('customer')->group(function () {
        // customer
        Route::get('/get-kavling/{idLokasi}', [CustomerController::class, 'getKavling'])->name('customer.getKavling');
        Route::get('/get-harga-kavling/{id_kavling}', [CustomerController::class, 'getHargaKavling'])->name('customer.getHargaKavling');
        Route::get('/customer/cetak', [CustomerController::class, 'cetakData'])->name('customer.cetak');
        Route::get('customer-data/{id_customer}/subsidi-cetak', [CustomerController::class, 'cetakFormSubsidi'])->name('subsidi.cetak');
        Route::resource('customer', CustomerController::class);

        // prospek
        Route::get('prospekData/{idLokasi}/get-kavling', [ProspekController::class, 'getKavling'])->name('prospekData.getKavling');
        Route::get('prospekData/{id_prospek}/utj', [ProspekController::class, 'utj'])->name('prospekData.utj');
        Route::post('prospekData/createUtj', [ProspekController::class, 'createUtj'])->name('prospekData.createUtj');
        Route::resource('prospek', ProspekController::class);

        // berkas customer (sebelumnya uploud-file)
        Route::get('/search', [BerkasCustomerController::class, 'getNasabah'])->name('nasabah.search');
        Route::get('/{id}/details', [BerkasCustomerController::class, 'getNasabahDetails'])->name('nasabah.details');
        Route::get('/{id}/files', [BerkasCustomerController::class, 'getFileNasabah'])->name('nasabah.files');
        Route::delete('/file/{id}', [BerkasCustomerController::class, 'deleteFile'])->name('nasabah.file.delete');
        Route::get('/{id}/file-nasabah', [BerkasCustomerController::class, 'getFileNasabah']);
        Route::post('/file-nasabah/upload', [BerkasCustomerController::class, 'uploadFile']);
        Route::delete('/file-nasabah/{id}', [BerkasCustomerController::class, 'deleteFile']);
        Route::resource('berkas-customer', BerkasCustomerController::class);

        // rekap lampiran
        Route::resource('rekap-lampiran', RekapLampiranController::class);

        // arsip nasabah
        Route::resource('arsipNasabah', ArsipNasabahController::class);
    });

    // --- Marketing ---
    Route::prefix('marketing')->group(function () {
        Route::resource('marketing-offline', MarketingOfflineController::class);
        Route::resource('marketing-freelance', MarketingFreelanceController::class);
    });

    // --- Master Data ---
    Route::prefix('master')->group(function () {
        Route::resource('perusahaan', PerusahaanController::class);

        Route::get('lokasi-kavling/detail/{id}', [LokasiKavlingController::class, 'detail'])->name('lokasi-kavling.detail');
        Route::get('lokasi-kavling/export/{id}', [LokasiKavlingController::class, 'exportDetail'])->name('lokasi-kavling.export');
        Route::get('lokasi-kavling/perusahaan/{id_lokasi}', [LokasiKavlingController::class, 'getPerusahaanByLokasi'])->name('lokasi-kavling.getPerusahaanByLokasi');
        Route::resource('lokasi-kavling', LokasiKavlingController::class);

        Route::post('kavling/uploud', [KavlingController::class, 'uploud'])->name('kavling.uploud');
        Route::get('kavling/{id}/lampiran', [KavlingController::class, 'lampiran'])->name('kavling.lampiran');
        Route::post('kavling/{id}/lampiran/upload', [KavlingController::class, 'uploadLampiran'])->name('kavling.lampiran.upload');
        Route::get('kavling/perusahaan/{id_lokasi}', [KavlingController::class, 'getPerusahaanByLokasi'])->name('kavling.getPerusahaanByLokasi');
        Route::resource('kavling', KavlingController::class);

        Route::resource('notaris', NotarisController::class);
        Route::resource('kategori', KategoriController::class);
        Route::resource('bank', BankController::class);
        Route::get('bank/data/list', [BankController::class, 'getBankList'])->name('bank.list');
    });

    // --- Pengaturan ---
    Route::prefix('pengaturan')->group(function () {
        Route::resource('pengaturan-profil', PengaturanProfilController::class);
        Route::resource('pengaturan-media', PengaturanMediaController::class);
        Route::resource('konten', KontenController::class);
        Route::resource('pengaturan-pengguna', PengaturanPenggunaController::class);
        Route::resource('list-penjualan', ListPenjualanController::class);
        Route::resource('list-pembangunan', ListPembangunanController::class);
    });

    // --- Pengaturan WA ---
    Route::prefix('pengaturan-wa')->group(function () {
        Route::resource('template-pesan', PengaturanPesanController::class);
        Route::post('kirim-pesan-wa', [PengaturanKoneksiController::class, 'kirimPesanWa'])->name('kirimPesanWa');
        Route::resource('pengaturan-koneksi', PengaturanKoneksiController::class);
    });

    // --- Hak Akses & Auth ---
    Route::put('updateHakAkses', [HakAksesController::class, 'updateHakAkses'])->name('admin.updateHakAkses');
    Route::get('hak-akses', [HakAksesController::class, 'hak_akses'])->name('hakakses');
    Route::get('get-hak-akses', [HakAksesController::class, 'getHakAkses'])->name('admin.getHakAkses');
    Route::post('logout', [AuthController::class, 'logout'])->name('admin.logout');

    // --- Menu Navigation ---
    Route::get('pengaturan', [AuthController::class, 'pengaturan'])->name('pengaturan');
    Route::get('pengaturanWa', [AuthController::class, 'pengaturanWa'])->name('pengaturanWa');
    Route::get('customer', [AuthController::class, 'customer'])->name('customer');
    Route::get('master', [AuthController::class, 'master'])->name('master');
    Route::get('transaksi', [AuthController::class, 'transaksi'])->name('transaksi');
    Route::get('keuangan', [AuthController::class, 'keuangan'])->name('keuangan');
    Route::get('marketing', [AuthController::class, 'Marketing'])->name('marketing');
});
