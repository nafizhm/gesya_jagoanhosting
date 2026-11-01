@extends('admin.layout_admin')

@section('content')
    <div class="content-wrapper">

        <section class="content">
            <div class="container-fluid mt-2">
                <div class="card">
                    <div class="card-header px-3">
                        <div class="d-flex w-100 justify-content-between align-items-center">
                            <h3 class="font-weight-bold text-xl">Detail Acc Bank</h3>
                            {{-- <span>Data Wawancara</span> --}}
                            <a href="{{ route('acc-bank.index') }}" class="btn btn-sm btn-secondary">Kembali</a>
                        </div>
                    </div>


                    <div class="card-body">

                        @php
                            $w = $dataSp3k->wawancara;
                            $tglWawancara = $w->tgl_wawancara ?? null;
                        @endphp

                        <div class="form-group row">
                            <label for="hari_wawancara" class="col-sm-2 col-form-label">Hari</label>
                            <div class="col-sm-3">
                                <input type="text" name="hari_wawancara" id="hari_wawancara" class="form-control"
                                    readonly>
                            </div>
                            <label for="tgl_wawancara" class="col-sm-2 col-form-label">Tanggal</label>
                            <div class="col-sm-3">
                                <input type="text" name="tgl_wawancara" id="tgl_wawancara" class="form-control" disabled>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="id_customer" class="col-sm-2 col-form-label">Nama Customer</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="nama_nasabah"
                                    value="{{ $w->nasabah->nama_lengkap ?? '-' }}" disabled>
                            </div>
                        </div>


                        <div class="form-group row">
                            <label for="no_ktp" class="col-sm-2 col-form-label">No Ktp</label>
                            <div class="col-sm-3">
                                <input type="text" name="no_ktp" id="no_ktp" class="form-control"
                                    value="{{ $w->nasabah->no_ktp ?? '-' }}" disabled>
                            </div>
                            <label for="alamat" class="col-sm-2 col-form-label">Alamat Ktp</label>
                            <div class="col-sm-3">
                                <input type="text" name="alamat" id="alamat" class="form-control"
                                    value="{{ $w->nasabah->alamat ?? '-' }}" disabled>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="kode_kavling" class="col-sm-2 col-form-label">Klaster/Blok</label>
                            <div class="col-sm-3">
                                <input type="text" name="kode_kavling" id="kode_kavling" class="form-control"
                                    value="{{ ($w->kavling->kode_kavling ?? '-') . ' / ' . ($w->kavling->lokasi->nama_kavling ?? '-') }}"
                                    disabled>
                                <input type="hidden" name="id_kavling" id="id_kavling"
                                    value="{{ $w->kavling->id_kavling ?? '' }}">
                            </div>
                            <label for="tipe_bangunan" class="col-sm-2 col-form-label">Tipe Rumah</label>
                            <div class="col-sm-3">
                                <input type="text" name="tipe_bangunan" id="tipe_bangunan" class="form-control"
                                    value="{{ $w->kavling->tipe_bangunan ?? '-' }}" disabled>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="luas_tanah" class="col-sm-2 col-form-label">Luas Tanah</label>
                            <div class="col-sm-3">
                                <input type="text" name="luas_tanah" id="luas_tanah" class="form-control"
                                    value="{{ $w->kavling->luas_tanah ?? '-' }}" disabled>
                            </div>
                            <label for="luas_bangunan" class="col-sm-2 col-form-label">LB</label>
                            <div class="col-sm-3">
                                <input type="text" name="luas_bangunan" id="luas_bangunan" class="form-control"
                                    value="{{ $w->kavling->luas_bangunan ?? '-' }}" disabled>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="id_marketing" class="col-sm-2 col-form-label">Marketing</label>
                            <div class="col-sm-8">
                                <input type="text" id="nama_marketing" name="nama_marketing" class="form-control"
                                    value="{{ $w->marketing->nama_marketing ?? '-' }}" disabled>
                                <input type="hidden" id="id_marketing" name="id_marketing"
                                    value="{{ $w->marketing->id_marketing ?? '' }}">
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="catatan_kekurangan" class="col-sm-2 col-form-label">Catatan Wawancara</label>
                            <div class="col-sm-8">
                                <textarea type="text" id="catatan_kekurangan" class="form-control" name="catatan_kekurangan" disabled>{{ $w->catatan_kekurangan ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">Detail SP3K</div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped data-table" id="sp3k-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Customer</th>
                                    <th>ACC Plafon</th>
                                    <th>Tenor</th>
                                    <th>Tgl SP3K</th>
                                    <th>Tgl Expired</th>
                                    <th>Notaris</th>
                                    <th>Durasi</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </section>
    </div>
@endsection
@push('scripts')
    <script>
        $(function() {
            const idWawancara = "{{ $dataSp3k->id_wawancara ?? '' }}";

            $('#sp3k-table').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                paging: false,
                info: false,
                lengthChange: false,
                ajax: {
                    url: "{{ route('sp3k.data') }}",
                    data: function(d) {
                        d.id_wawancara = idWawancara;
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'nama_customer',
                        name: 'nasabah.nama_lengkap',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'acc_plafon',
                        name: 'acc_plafon',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'tenor',
                        name: 'tenor',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'tgl_wawancara',
                        name: 'wawancara.tgl_wawancara',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'tgl_expired',
                        name: 'tgl_expired',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nama_notaris',
                        name: 'notaris.nama_notaris',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'sisa_hari',
                        name: 'sisa_hari',
                        orderable: false,
                        searchable: false,
                    },
                ]
            });
        });


        document.addEventListener('DOMContentLoaded', function() {
            const tglWawancara = "{{ $tglWawancara }}";

            if (tglWawancara) {
                const tanggal = new Date(tglWawancara);

                const hariIndonesia = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                const bulanIndonesia = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                ];

                const namaHari = hariIndonesia[tanggal.getDay()];
                const tanggalFormatted = tanggal.getDate() + ' ' + bulanIndonesia[tanggal.getMonth()] + ' ' +
                    tanggal.getFullYear();

                document.getElementById('hari_wawancara').value = namaHari;
                document.getElementById('tgl_wawancara').value = tanggalFormatted;
            } else {
                document.getElementById('hari_wawancara').value = '-';
                document.getElementById('tgl_wawancara').value = '-';
            }
        });
    </script>
@endpush
