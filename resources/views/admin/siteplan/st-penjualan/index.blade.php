@extends('admin.layout_admin')
@section('content')
    <style>
        .legend {
            position: fixed;
            top: 80px;
            right: 30px;
            padding: 10px;
            font-size: 14px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 1px 5px rgba(0, 0, 0, 0.4);
            transition: right 0.3s ease;
            width: 200px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            margin-bottom: 5px;
        }

        .legend-color {
            width: 20px;
            height: 20px;
            margin-right: 5px;
            border-radius: 50%;
            border: 2px solid #000;
        }

        .toggle-btn {
            width: 100%;
            padding: 5px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            margin-top: 10px;
        }

        .show-btn {
            position: fixed;
            top: 100px;
            right: 30px;
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            box-shadow: 0 1px 5px rgba(0, 0, 0, 0.4);
            cursor: pointer;
            display: none;
        }

        .svg-container {
            width: 100%;
            height: 100vh;
            overflow: hidden;
            border: 2px solid #d1cfcf;
            position: relative;
        }

        .svg-container svg {
            width: 100%;
            height: 100%;
            cursor: grab;
            transition: transform 0.1s ease-out;
        }
    </style>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 font-weight-bold text-lg text-dark">ST Penjualan</h1>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12 col-12">

                        <div class="card card-primary card-outline card-outline-tabs">
                            <div class="card-header p-0 border-bottom-0">
                                <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                                    @foreach ($lokasiKavling as $index => $kav)
                                        <li class="nav-item">
                                            <a class="nav-link {{ $index == 0 ? 'active' : '' }}"
                                                id="custom-tabs-four-{{ $kav->id_lokasi }}-tab" data-toggle="pill"
                                                href="#custom-tabs-four-{{ $kav->id_lokasi }}" role="tab">
                                                {{ $kav->nama_kavling }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <div class="card-body">
                                <div class="tab-content" id="custom-tabs-four-tabContent">
                                    @foreach ($lokasiKavling as $index => $kav)
                                        <div class="tab-pane fade {{ $index == 0 ? 'show active' : '' }}"
                                            id="custom-tabs-four-{{ $kav->id_lokasi }}" role="tabpanel">

                                            <a href="{{ route('siteplan-penjualan.cetak.pdf', $kav->id_lokasi) }}"
                                                target="_blank" class="btn btn-danger btn-sm mr-1">
                                                <i class="fas fa-file-pdf mr-1"></i> Cetak Denah PDF
                                            </a>
                                            <a href="{{ route('siteplan-penjualan.cetak.jpg', $kav->id_lokasi) }}"
                                                target="_blank" class="btn btn-primary btn-sm">
                                                <i class="fas fa-file-image mr-1"></i> Download Denah JPG
                                            </a>

                                            <div class="svg-container mt-3">
                                                <button class="reset-button btn btn-success btn-sm"
                                                    style="position: absolute; top: 10px; left: 10px; z-index: 10;">
                                                    Reset Siteplan
                                                </button>

                                                @if ($kav->masterSvg)
                                                    {!! str_replace(['[[lebar]]', '[[tinggi]]'], ['100%', '100%'], $kav->masterSvg->header_svg) !!}
                                                @endif

                                                @foreach ($kav->kavlingPeta as $pt)
                                                    @php
                                                        $warna = '#ffffff';

                                                        if ($pt->customer && $pt->customer->progres) {
                                                            $warna = $pt->customer->progres->warna ?? '#ffffff';
                                                        } elseif ($pt->memiliki_hold_aktif) {
                                                            $warna = '#00ffff';
                                                        }
                                                    @endphp

                                                    @php $svg_code = $pt->jenis_map === 'polygon' ? $kav->masterSvg->polygon_svg : $kav->masterSvg->path_svg; @endphp

                                                    <a href="javascript:void(0);" class="detail-button"
                                                        data-id="{{ $pt->id_kavling }}"
                                                        data-url="{{ route('siteplan-penjualan.show', $pt->id_kavling) }}">
                                                        {!! str_replace(
                                                            ['[[1]]', '[[2]]', '[[3]]', '[[4]]'],
                                                            [$pt->map, $warna, $pt->matrik, $pt->kode_kavling],
                                                            $svg_code,
                                                        ) !!}
                                                    </a>
                                                @endforeach

                                                @if ($kav->masterSvg)
                                                    {!! $kav->masterSvg->footer_svg !!}
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <button class="show-btn btn-xs" id="show-btn" onclick="toggleLegend()">Show</button>

                    <div class="legend" id="legend">
                        @foreach ($legend as $item)
                            <div class="legend-item">
                                <div class="legend-color" style="background-color: {{ $item->warna }}"></div>
                                {{ $item->status_progres }}
                            </div>
                        @endforeach
                        <button class="toggle-btn" onclick="toggleLegend()">Hide</button>
                    </div>

                </div>
            </div>
        </section>
    </div>

    <div class="modal fade" id="modalDetail" data-focus="false" tabindex="-1" role="dialog"
        aria-labelledby="modalDetailLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success d-flex align-items-center">
                    <h5 class="modal-title text-white font-weight-bold" id="modalDetailLabel">Detail Data Kavling</h5>
                    <div class="ml-auto d-flex align-items-center">
                        <button type="button" class="btn btn-primary btn-sm mr-2" id="btn-cetak">
                            <i class="fas fa-print"></i> Cetak Data
                        </button>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>

                <form id="formData">
                    @csrf
                    <input type="hidden" id="id_lokasi" name="id_lokasi" value="">
                    <div class="modal-body">
                        <div class="card card-primary card-outline card-outline-tabs">
                            <div class="card-header p-0 border-bottom-0">
                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-toggle="pill"
                                            href="#tab-penj-101">Data Unit Rumah</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="pill"
                                            href="#tab-penj-102">Data User</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="pill"
                                            href="#tab-penj-104">Tagihan & Pembayaran</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="pill"
                                            href="#tab-penj-103">Foto Unit</a>
                                    </li>
                                </ul>
                            </div>

                            <div class="card-body">
                                <div class="tab-content">
                                    <div class="tab-pane fade show active" id="tab-penj-101">
                                        <div class="form-group row mb-3">
                                            <label class="col-sm-3 col-form-label">Lokasi Kavling</label>
                                            <div class="col-sm-3">
                                                <input id="lokasi_id" class="form-control" type="text" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group row mb-3">
                                            <label class="col-sm-3 col-form-label">Lokasi Rumah</label>
                                            <div class="col-sm-3">
                                                <input id="kode_kavling_id" class="form-control" type="text" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group row mb-3">
                                            <label class="col-sm-3 col-form-label">Panjang Tanah</label>
                                            <div class="col-sm-3">
                                                <input id="panjang_kanan_id" class="form-control" type="text" readonly>
                                            </div>
                                            <label class="col-sm-2 col-form-label">Lebar Tanah</label>
                                            <div class="col-sm-2">
                                                <input id="lebar_depan_id" class="form-control" type="text" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group row mb-3">
                                            <label class="col-sm-3 col-form-label">Luas Tanah</label>
                                            <div class="col-sm-3">
                                                <input id="luas_tanah_id" class="form-control" type="text" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group row mb-3">
                                            <label class="col-sm-3 col-form-label">Tipe Bangunan</label>
                                            <div class="col-sm-3">
                                                <input id="tipe_bangunan_id" class="form-control" type="text" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group row mb-3">
                                            <label class="col-sm-3 col-form-label">Luas Bangunan</label>
                                            <div class="col-sm-3">
                                                <input id="luas_bangunan_id" class="form-control" type="text" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group row mb-3">
                                            <label class="col-sm-3 col-form-label">Harga Jual</label>
                                            <div class="col-sm-3">
                                                <input id="harga_id" class="form-control" type="text" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group row mb-3">
                                            <label class="col-sm-3 col-form-label">Daya Listrik</label>
                                            <div class="col-sm-3">
                                                <input id="daya_listrik_id" class="form-control" type="text" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group row mb-3">
                                            <label class="col-sm-3 col-form-label">Keterangan</label>
                                            <div class="col-sm-3">
                                                <input id="keterangan_id" class="form-control" type="text" readonly>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="form-group row mb-3">
                                            <label class="col-sm-3 col-form-label">ID Rumah</label>
                                            <div class="col-sm-3">
                                                <input id="id_rumah_sikumbang_id" class="form-control" type="text" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group row mb-3 align-items-center">
                                            <label class="col-sm-3 col-form-label">Norek Listrik</label>
                                            <div class="col-sm-3">
                                                <input id="norek_listrik_id" class="form-control" type="text" readonly>
                                            </div>
                                            <div class="col-sm-6">
                                                <a href="#" class="mr-4" target="_blank" id="fotoListrik1Link" style="display: none;">
                                                    <i class="fas fa-external-link-alt"></i> Lihat Foto Listrik 1
                                                </a>
                                                <a href="#" class="mr-4" target="_blank" id="fotoListrik2Link" style="display: none;">
                                                    <i class="fas fa-external-link-alt"></i> Lihat Foto Listrik 2
                                                </a>
                                            </div>
                                        </div>
                                        <div class="form-group row mb-3 align-items-center">
                                            <label class="col-sm-3 col-form-label">Norek Air</label>
                                            <div class="col-sm-3">
                                                <input id="norek_air_id" class="form-control" type="text" readonly>
                                            </div>
                                            <div class="col-sm-6">
                                                <a href="#" class="mr-4" target="_blank" id="fotoAir1Link" style="display: none;">
                                                    <i class="fas fa-external-link-alt"></i> Lihat Foto Air 1
                                                </a>
                                                <a href="#" class="mr-4" target="_blank" id="fotoAir2Link" style="display: none;">
                                                    <i class="fas fa-external-link-alt"></i> Lihat Foto Air 2
                                                </a>
                                            </div>
                                        </div>
                                        <div class="form-group row mb-3">
                                            <label class="col-sm-3 col-form-label">No. Sertipikat</label>
                                            <div class="col-sm-3">
                                                <input id="no_sertifikat_id" class="form-control" type="text" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="tab-penj-102">
                                        <div class="form-group row mb-3">
                                            <label class="col-sm-3 col-form-label">Nama Lengkap</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" id="nama_lengkap_id" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group row mb-3">
                                            <label class="col-sm-3 col-form-label">No. KTP</label>
                                            <div class="col-sm-3">
                                                <input id="no_ktp_id" class="form-control" type="text" readonly>
                                            </div>
                                            <label class="col-sm-2 col-form-label">No. KTP Pasangan</label>
                                            <div class="col-sm-3">
                                                <input id="no_ktp_p_id" class="form-control" type="text" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group row mb-3">
                                            <label class="col-sm-3 col-form-label">Tempat Lahir</label>
                                            <div class="col-sm-3">
                                                <input id="tempat_lahir_id" class="form-control" type="text" readonly>
                                            </div>
                                            <label class="col-sm-2 col-form-label">Tanggal Lahir</label>
                                            <div class="col-sm-3">
                                                <input id="tgl_lahir_id" class="form-control" type="date" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group row mb-3">
                                            <label class="col-sm-3 col-form-label">Jenis Kelamin</label>
                                            <div class="col-sm-3">
                                                <input type="text" class="form-control" id="jenis_kelamin_id" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group row mb-3">
                                            <label class="col-sm-3 col-form-label">Alamat KTP</label>
                                            <div class="col-sm-8">
                                                <textarea id="alamat_id" class="form-control" rows="2" readonly></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group row mb-3">
                                            <label class="col-sm-3 col-form-label">Alamat Domisili</label>
                                            <div class="col-sm-8">
                                                <textarea id="alamat_domisili_id" class="form-control" rows="2" readonly></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group row mb-3">
                                            <label class="col-sm-3 col-form-label">No. Telp / WA</label>
                                            <div class="col-sm-3">
                                                <input id="no_telp_id" class="form-control" type="text" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group row mb-3">
                                            <label class="col-sm-3 col-form-label">NPWP</label>
                                            <div class="col-sm-3">
                                                <input id="npwp_id" class="form-control" type="text" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group row mb-3">
                                            <label class="col-sm-3 col-form-label">Jenis Pembelian</label>
                                            <div class="col-sm-3">
                                                <input id="jenis_pembelian_id" class="form-control" type="text" readonly>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="form-group row mb-3">
                                            <label class="col-sm-3 col-form-label">Foto KTP</label>
                                            <div class="col-sm-3">
                                                <a href="#" class="view-photo-link" id="fotoKtpLink" target="_blank" style="display: none;">
                                                    <i class="fas fa-external-link-alt"></i> Lihat Foto KTP
                                                </a>
                                                <span id="noFotoKtp" style="display: none;">-</span>
                                            </div>
                                            <label class="col-sm-3 col-form-label">Foto Pemohon</label>
                                            <div class="col-sm-3">
                                                <a href="#" class="view-photo-link" id="fotoPemohonLink" target="_blank" style="display: none;">
                                                    <i class="fas fa-external-link-alt"></i> Lihat Foto Pemohon
                                                </a>
                                                <span id="noFotoPemohon" style="display: none;">-</span>
                                            </div>
                                        </div>
                                        <div class="form-group row mb-3">
                                            <label class="col-sm-3 col-form-label">Foto KK</label>
                                            <div class="col-sm-3">
                                                <a href="#" class="view-photo-link" id="fotoKkLink" target="_blank" style="display: none;">
                                                    <i class="fas fa-external-link-alt"></i> Lihat Foto KK
                                                </a>
                                                <span id="noFotoKk" style="display: none;">-</span>
                                            </div>
                                            <label class="col-sm-3 col-form-label">Foto KTP Pasangan</label>
                                            <div class="col-sm-3">
                                                <a href="#" class="view-photo-link" id="fotoKtpPasanganLink" target="_blank" style="display: none;">
                                                    <i class="fas fa-external-link-alt"></i> Lihat Foto KTP Pasangan
                                                </a>
                                                <span id="noFotoKtpPasangan" style="display: none;">-</span>
                                            </div>
                                        </div>
                                        <div class="form-group row mb-3">
                                            <label class="col-sm-3 col-form-label">Foto NPWP</label>
                                            <div class="col-sm-3">
                                                <a href="#" class="view-photo-link" id="fotoNpwpLink" target="_blank" style="display: none;">
                                                    <i class="fas fa-external-link-alt"></i> Lihat Foto NPWP
                                                </a>
                                                <span id="noFotoNpwp" style="display: none;">-</span>
                                            </div>
                                            <label class="col-sm-3 col-form-label">Foto BPJS</label>
                                            <div class="col-sm-3">
                                                <a href="#" class="view-photo-link" id="fotoBpjsLink" target="_blank" style="display: none;">
                                                    <i class="fas fa-external-link-alt"></i> Lihat Foto BPJS
                                                </a>
                                                <span id="noFotoBpjs" style="display: none;">-</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="tab-penj-104">
                                        <h5>- Tagihan - </h5>
                                        <table class="table table-bordered table-sm">
                                            <thead>
                                                <tr class="table-primary">
                                                    <th>No</th>
                                                    <th>Jenis Tagihan</th>
                                                    <th>Harga</th>
                                                    <th>Catatan</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbody-tagihan"></tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="2" class="text-right">Total</th>
                                                    <th id="total-tagihan">Rp 0</th>
                                                    <th></th>
                                                </tr>
                                            </tfoot>
                                        </table>

                                        <h5>- Pemasukan - </h5>
                                        <table class="table table-bordered table-sm mt-3">
                                            <thead>
                                                <tr class="table-success">
                                                    <th>No</th>
                                                    <th>Tanggal</th>
                                                    <th>Jenis</th>
                                                    <th>Harga</th>
                                                    <th>Keterangan</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbody-pembayaran"></tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="3" class="text-right">Total</th>
                                                    <th id="total-pembayaran">Rp 0</th>
                                                    <th></th>
                                                </tr>
                                            </tfoot>
                                        </table>

                                        <h5>- Pengeluaran - </h5>
                                        <table class="table table-bordered table-sm mt-3">
                                            <thead>
                                                <tr class="table-danger">
                                                    <th>No</th>
                                                    <th>Tanggal</th>
                                                    <th>Jenis</th>
                                                    <th>Harga</th>
                                                    <th>Keterangan</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbody-pengeluaran"></tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="3" class="text-right">Total</th>
                                                    <th id="total-pengeluaran">Rp 0</th>
                                                    <th></th>
                                                </tr>
                                            </tfoot>
                                        </table>

                                        <div id="sisa-bayar-wrapper" class="mt-3">
                                            <table class="table table-bordered table-sm">
                                                <tr class="table-warning">
                                                    <th colspan="2" class="text-center">Sisa Bayar</th>
                                                </tr>
                                                <tr>
                                                    <td><strong id="sisa-bayar">Rp 0</strong></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="tab-penj-103">
                                        <h3>Foto Unit</h3>
                                        <hr>
                                        <div id="foto"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Keluar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function toggleLegend() {
            var legend = document.getElementById('legend');
            var showBtn = document.getElementById('show-btn');

            if (legend.style.right === '30px' || legend.style.right === '') {
                legend.style.right = '-300px';
                showBtn.style.display = 'block';
            } else {
                legend.style.right = '30px';
                showBtn.style.display = 'none';
            }
        }

        $(document).on('click', '#btn-cetak', function() {
            const data = {
                _token: $('meta[name="csrf-token"]').attr('content'),
                kode_kavling: $('#kode_kavling_id').val(),
                status: 'Terjual',
                lokasi: $('#lokasi_id').val(),
                blok: $('#kode_kavling_id').val(),
                luas_tanah: $('#luas_tanah_id').val(),
                luas_bangunan: $('#luas_bangunan_id').val(),
                daya_listrik: $('#daya_listrik_id').val(),
                harga: $('#harga_id').val()?.replace(/[^0-9]/g, ''),
                no_sertifikat: $('#no_sertifikat_id').val(),
                no_rek_listrik: $('#norek_listrik_id').val(),
                no_rek_air: $('#norek_air_id').val(),
                nama_customer: $('#nama_lengkap_id').val(),
                no_ktp: $('#no_ktp_id').val(),
                tempat_lahir: $('#tempat_lahir_id').val(),
                tgl_lahir: $('#tgl_lahir_id').val(),
                alamat: $('#alamat_id').val(),
                no_hp: $('#no_telp_id').val(),
                pekerjaan: $('#pekerjaan_id').val() || $('#jenis_pembelian_id').val(),
                marketing: '-',
                foto: '',
            };

            if (!data.kode_kavling || !data.nama_customer) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Data tidak lengkap. Pastikan detail sudah dimuat.',
                });
                return;
            }

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route('penjualan.cetak') }}';
            form.target = '_blank';

            for (const key in data) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = data[key] || '';
                form.appendChild(input);
            }

            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        });

        $(document).on('click', '.detail-button', function() {
            var url = $(this).data('url');
            var id_kavling = $(this).data('id');
            $('#modalDetail').data('id-kavling', id_kavling);

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'GET',
                success: function(response) {
                    if (response.status === 'success') {
                        $('#id_kavling').val(id_kavling);
                        $('#kode_kavling_id').val(response.kavling.kode_kavling ?? '-');
                        $('#panjang_kanan_id').val(response.kavling.panjang_kanan ?? '-');
                        $('#lebar_depan_id').val(response.kavling.lebar_depan ?? '-');
                        $('#luas_tanah_id').val(response.kavling.luas_tanah ?? '-');
                        $('#tipe_bangunan_id').val(response.kavling.tipe_bangunan ?? '-');
                        $('#luas_bangunan_id').val(response.kavling.luas_bangunan ?? '-');
                        $('#harga_id').val(response.kavling.hrg_jual ?? '-');
                        $('#daya_listrik_id').val(response.kavling.daya_listrik ?? '-');
                        $('#keterangan_id').val(response.kavling.keterangan ?? '-');
                        $('#id_rumah_sikumbang_id').val(response.kavling.id_rumah_sikumbang ?? '-');
                        $('#no_sertifikat_id').val(response.kavling.no_sertifikat ?? '-');

                        if (response.customer) {
                            $('#nama_lengkap_id').val(response.customer.nama_lengkap ?? '-');
                            $('#no_ktp_id').val(response.customer.no_ktp ?? '-');
                            $('#no_ktp_p_id').val(response.customer.no_ktp_p ?? '-');
                            $('#tempat_lahir_id').val(response.customer.tempat_lahir ?? '-');
                            $('#tgl_lahir_id').val(response.customer.tgl_lahir ?? '-');
                            $('#jenis_kelamin_id').val(response.customer.jenis_kelamin ?? '-');
                            $('#alamat_id').val(response.customer.alamat ?? '-');
                            $('#alamat_domisili_id').val(response.customer.alamat_domisili ?? '-');
                            $('#no_telp_id').val(response.customer.no_telp ?? '-');
                            $('#npwp_id').val(response.customer.npwp ?? '-');
                            $('#jenis_pembelian_id').val(response.customer.jenis_pembelian ?? '-');
                        }

                        $('#lokasi_id').val(response.lokasi);

                        if (response.listrikAir) {
                            $('#norek_listrik_id').val(response.listrikAir.norek_listrik ?? '-');
                            $('#norek_air_id').val(response.listrikAir.norek_air ?? '-');
                            updatePhotoLinks(response.listrikAir);
                        } else {
                            $('#norek_listrik_id').val('-');
                            $('#norek_air_id').val('-');
                            updatePhotoLinks({});
                        }

                        if (response.files) {
                            updateCustomerPhotoLinks(response.files);
                        } else {
                            hideAllCustomerPhotoLinks();
                        }

                        $('#modalDetail').modal('show');
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan. Silakan coba lagi.');
                },
            });
        });

        function updatePhotoLinks(listrikAir) {
            if (listrikAir.foto_listrik_url) {
                $('#fotoListrik1Link').attr('href', listrikAir.foto_listrik_url).show();
            } else {
                $('#fotoListrik1Link').hide();
            }
            if (listrikAir.foto_listrik_2_url) {
                $('#fotoListrik2Link').attr('href', listrikAir.foto_listrik_2_url).show();
            } else {
                $('#fotoListrik2Link').hide();
            }
            if (listrikAir.foto_air_url) {
                $('#fotoAir1Link').attr('href', listrikAir.foto_air_url).show();
            } else {
                $('#fotoAir1Link').hide();
            }
            if (listrikAir.foto_air_2_url) {
                $('#fotoAir2Link').attr('href', listrikAir.foto_air_2_url).show();
            } else {
                $('#fotoAir2Link').hide();
            }
        }

        function updateCustomerPhotoLinks(files) {
            $('.view-photo-link').hide();
            $('[id^="noFoto"]').hide();

            if (files.ktp) {
                $('#fotoKtpLink').attr('href', files.ktp).show();
            } else {
                $('#noFotoKtp').show();
            }
            if (files.pemohon) {
                $('#fotoPemohonLink').attr('href', files.pemohon).show();
            } else {
                $('#noFotoPemohon').show();
            }
            if (files.kk) {
                $('#fotoKkLink').attr('href', files.kk).show();
            } else {
                $('#noFotoKk').show();
            }
            if (files.ktp_pasangan) {
                $('#fotoKtpPasanganLink').attr('href', files.ktp_pasangan).show();
            } else {
                $('#noFotoKtpPasangan').show();
            }
            if (files.npwp) {
                $('#fotoNpwpLink').attr('href', files.npwp).show();
            } else {
                $('#noFotoNpwp').show();
            }
            if (files.bpjs) {
                $('#fotoBpjsLink').attr('href', files.bpjs).show();
            } else {
                $('#noFotoBpjs').show();
            }
        }

        function hideAllCustomerPhotoLinks() {
            $('.view-photo-link').hide();
            $('[id^="noFoto"]').show();
        }

        $('#modalDetail').on('shown.bs.modal', function() {
            const id_kavling = $(this).data('id-kavling');
            if (!id_kavling) return;

            loadFotoKavling(id_kavling);
            loadTagihan(id_kavling);
        });

        function loadFotoKavling(id_kavling) {
            $.ajax({
                url: '{{ url("/admin/siteplan/siteplan-penjualan/foto") }}/' + id_kavling,
                type: 'GET',
                success: function(response) {
                    if (response.status === 'success') {
                        $('#foto').html('<div class="row">' + response.html + '</div>');
                    }
                },
                error: function() {
                    toastr.error('Gagal memuat foto kavling.');
                }
            });
        }

        function loadTagihan(id_kavling) {
            $.ajax({
                url: '{{ url("/admin/siteplan/siteplan-penjualan/tagihan") }}/' + id_kavling,
                type: 'GET',
                success: function(res) {
                    if (res.status === 'success') {
                        let htmlTagihan = '', htmlPembayaran = '', htmlPengeluaran = '';
                        let totalTagihan = 0, totalPembayaran = 0, totalPengeluaran = 0;

                        if (res.tagihan && res.tagihan.length) {
                            res.tagihan.forEach((item, i) => {
                                const jumlah = Number(item.jumlah ?? item.jumlah_tagihan ?? 0);
                                totalTagihan += jumlah;
                                htmlTagihan += `<tr><td>${i + 1}</td><td>${item.jenis ?? item.jenis_tagihan ?? '-'}</td><td>Rp ${numberFormat(jumlah)}</td><td>${item.catatan ?? ''}</td></tr>`;
                            });
                        } else {
                            htmlTagihan = '<tr><td colspan="4" class="text-center">Tidak ada tagihan.</td></tr>';
                        }

                        if (res.pembayaran && res.pembayaran.length) {
                            res.pembayaran.forEach((item, i) => {
                                const jumlah = Number(item.jumlah ?? 0);
                                totalPembayaran += jumlah;
                                htmlPembayaran += `<tr><td>${i + 1}</td><td>${formatTanggalIndo(item.tanggal)}</td><td>${item.jenis_pembayaran?.jenis ?? '-'}</td><td>Rp ${numberFormat(jumlah)}</td><td>${item.keterangan ?? '-'}</td></tr>`;
                            });
                        } else {
                            htmlPembayaran = '<tr><td colspan="5" class="text-center">Tidak ada pemasukan.</td></tr>';
                        }

                        if (res.pengeluaran && res.pengeluaran.length) {
                            res.pengeluaran.forEach((item, i) => {
                                const jumlah = Number(item.jumlah ?? 0);
                                totalPengeluaran += jumlah;
                                htmlPengeluaran += `<tr><td>${i + 1}</td><td>${formatTanggalIndo(item.tanggal)}</td><td>${item.jenis_pembayaran?.jenis ?? '-'}</td><td>Rp ${numberFormat(jumlah)}</td><td>${item.keterangan ?? '-'}</td></tr>`;
                            });
                        } else {
                            htmlPengeluaran = '<tr><td colspan="5" class="text-center">Tidak ada pengeluaran.</td></tr>';
                        }

                        $('#tbody-tagihan').html(htmlTagihan);
                        $('#total-tagihan').html(`Rp ${numberFormat(totalTagihan)}`);
                        $('#tbody-pembayaran').html(htmlPembayaran);
                        $('#total-pembayaran').html(`Rp ${numberFormat(totalPembayaran)}`);
                        $('#tbody-pengeluaran').html(htmlPengeluaran);
                        $('#total-pengeluaran').html(`Rp ${numberFormat(totalPengeluaran)}`);
                        $('#sisa-bayar').html(`Rp ${numberFormat(totalTagihan - totalPembayaran + totalPengeluaran)}`);
                    }
                },
                error: function() {
                    toastr.error('Gagal memuat data tagihan.');
                }
            });
        }

        function numberFormat(num) {
            return new Intl.NumberFormat('id-ID').format(num);
        }

        function formatTanggalIndo(tanggal) {
            if (!tanggal) return '-';
            const bulanIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            let d = new Date(tanggal);
            return `${d.getDate()} ${bulanIndo[d.getMonth()]} ${d.getFullYear()}`;
        }
    </script>
    <script src="{{ asset('assets/svg_1.js') }}"></script>
@endpush
