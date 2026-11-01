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
                        <h1 class="m-0 font-weight-bold text-lg text-dark">ST Proyek</h1>
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

                                            <a href="{{ route('siteplan-proyek.cetak.pdf', $kav->id_lokasi) }}"
                                                target="_blank" class="btn btn-danger btn-sm mr-1">
                                                <i class="fas fa-file-pdf mr-1"></i> Cetak Denah PDF
                                            </a>
                                            <a href="{{ route('siteplan-proyek.cetak.jpg', $kav->id_lokasi) }}"
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
                                                        $warna = $legend->firstWhere('id', 1)->warna;

                                                        if (isset($units[$pt->kode_kavling])) {
                                                            $unit = $units[$pt->kode_kavling]->first();
                                                            $proyek = $unit->proyekBangunan;

                                                            if ($proyek && $proyek->id_lokasi == $kav->id_lokasi) {
                                                                $persen = $proyek->proyekBangunanDetail
                                                                    ->whereNotNull('tanggal')
                                                                    ->sum('persen') ?? 0;

                                                                if ($persen > 0) {
                                                                    if ($persen < 30) {
                                                                        $warna = $legend->firstWhere('id', 2)->warna;
                                                                    } elseif ($persen < 60) {
                                                                        $warna = $legend->firstWhere('id', 3)->warna;
                                                                    } elseif ($persen < 82) {
                                                                        $warna = $legend->firstWhere('id', 4)->warna;
                                                                    } elseif ($persen == 100) {
                                                                        $warna = $legend->firstWhere('id', 5)->warna;
                                                                    }
                                                                }
                                                            }
                                                        }

                                                        if ($pt->customer && optional($pt->customer)->id_status_progres == 5) {
                                                            $warna = $legend->firstWhere('id', 6)->warna;
                                                        }
                                                    @endphp

                                                    @if ($pt->jenis_map == 'polygon')
                                                        <a href="javascript:void(0);" class="detail-button"
                                                            data-id="{{ $pt->id_kavling }}"
                                                            data-url="{{ route('siteplan-proyek.show', $pt->id_kavling) }}">
                                                            {!! str_replace(
                                                                ['[[1]]', '[[2]]', '[[3]]', '[[4]]'],
                                                                [$pt->map, $warna, $pt->matrik, $pt->kode_kavling],
                                                                $kav->masterSvg->polygon_svg,
                                                            ) !!}
                                                        </a>
                                                    @elseif ($pt->jenis_map == 'path')
                                                        <a href="javascript:void(0);" class="detail-button"
                                                            data-id="{{ $pt->id_kavling }}"
                                                            data-url="{{ route('siteplan-proyek.show', $pt->id_kavling) }}">
                                                            {!! str_replace(
                                                                ['[[1]]', '[[2]]', '[[3]]', '[[4]]'],
                                                                [$pt->map, $warna, $pt->matrik, $pt->kode_kavling],
                                                                $kav->masterSvg->path_svg,
                                                            ) !!}
                                                        </a>
                                                    @endif
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
                                {{ $item->keterangan }}
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
                <div class="modal-header bg-success">
                    <h5 class="modal-title text-white font-weight-bold" id="modalDetailLabel">Detail Data Kavling</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card card-primary card-outline card-outline-tabs">
                        <div class="card-header p-0 border-bottom-0">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="pill"
                                        href="#tab-proyek-101">Data Unit Rumah</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="pill"
                                        href="#tab-proyek-103">Foto Unit</a>
                                </li>
                            </ul>
                        </div>

                        <div class="card-body">
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="tab-proyek-101">
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Perumahan</label>
                                        <div class="col-sm-3">
                                            <input type="text" id="nama_kavling" class="form-control" readonly>
                                        </div>
                                        <label class="col-sm-2 col-form-label">Kode Kavling</label>
                                        <div class="col-sm-2">
                                            <input type="text" id="kode_kavling" class="form-control" readonly>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Panjang Kanan</label>
                                        <div class="col-sm-3">
                                            <div class="input-group">
                                                <input type="text" id="panjang_kanan" class="form-control" readonly>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">m</span>
                                                </div>
                                            </div>
                                        </div>
                                        <label class="col-sm-2 col-form-label">Panjang Kiri</label>
                                        <div class="col-sm-3">
                                            <div class="input-group">
                                                <input type="text" id="panjang_kiri" class="form-control" readonly>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">m</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Lebar Depan</label>
                                        <div class="col-sm-3">
                                            <div class="input-group">
                                                <input type="text" id="lebar_depan" class="form-control" readonly>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">m</span>
                                                </div>
                                            </div>
                                        </div>
                                        <label class="col-sm-2 col-form-label">Lebar Belakang</label>
                                        <div class="col-sm-3">
                                            <div class="input-group">
                                                <input type="text" id="lebar_belakang" class="form-control" readonly>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">m</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Luas Tanah</label>
                                        <div class="col-sm-3">
                                            <div class="input-group">
                                                <input type="text" id="luas_tanah" class="form-control" readonly>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">m²</span>
                                                </div>
                                            </div>
                                        </div>
                                        <label class="col-sm-2 col-form-label">Luas Bangunan</label>
                                        <div class="col-sm-3">
                                            <div class="input-group">
                                                <input type="text" id="luas_bangunan" class="form-control" readonly>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">m²</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Harga Jual</label>
                                        <div class="col-sm-3">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Rp.</span>
                                                </div>
                                                <input type="text" id="hrg_jual" class="form-control" readonly>
                                            </div>
                                        </div>
                                        <label class="col-sm-2 col-form-label">Daya Listrik</label>
                                        <div class="col-sm-3">
                                            <input type="text" id="daya_listrik" class="form-control" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Keterangan</label>
                                        <div class="col-sm-3">
                                            <textarea id="keterangan" class="form-control" rows="3" readonly></textarea>
                                        </div>
                                        <label class="col-sm-2 col-form-label">No. Sertif</label>
                                        <div class="col-sm-3">
                                            <input type="text" id="no_sertifikat" class="form-control" readonly>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="tab-proyek-103">
                                    <div class="form-group row">
                                        <div class="col-sm-12">
                                            <div class="img-thumbnail mb-2 d-flex align-items-center justify-content-center mx-auto"
                                                id="previewFoto"
                                                style="max-width: 800px; height: 500px; background-color: #f8f9fa; border: 1px solid #dee2e6; overflow: hidden;">
                                                <span style="color: #6c757d;">Tidak ada foto</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Keluar</button>
                </div>
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

        $(document).on('click', '.detail-button', function() {
            var url = $(this).data('url');

            $.get(url, function(response) {
                if (response.status === 'success') {
                    const formatNumber = (number) => {
                        let num = parseFloat(number);
                        return isNaN(num) ? 0 : num.toLocaleString('id-ID');
                    };

                    $('#nama_kavling').val(response.data.lokasi.nama_kavling);
                    $('#kode_kavling').val(response.data.kode_kavling);
                    $('#panjang_kanan').val(response.data.panjang_kanan);
                    $('#panjang_kiri').val(response.data.panjang_kiri);
                    $('#lebar_depan').val(response.data.lebar_depan);
                    $('#lebar_belakang').val(response.data.lebar_belakang);
                    $('#luas_tanah').val(response.data.luas_tanah);
                    $('#luas_bangunan').val(response.data.luas_bangunan);
                    $('#hrg_jual').val(formatNumber(response.data.hrg_jual));
                    $('#daya_listrik').val(formatNumber(response.data.daya_listrik));
                    $('#keterangan').val(response.data.keterangan);
                    $('#no_sertifikat').val(response.data.no_sertifikat);

                    let foto = response.data.foto;
                    let preview = $('#previewFoto');
                    if (foto) {
                        let imageUrl = '/assets/foto_kavling/' + foto;
                        preview.html(`<img src="${imageUrl}" alt="Foto Kavling" style="max-height: 100%; max-width: 100%;">`);
                    } else {
                        preview.html(`<span style="color: #6c757d;">Tidak ada foto</span>`);
                    }

                    $('#modalDetail').modal('show');
                }
            });
        });
    </script>
    <script src="{{ asset('assets/svg_1.js') }}"></script>
@endpush
