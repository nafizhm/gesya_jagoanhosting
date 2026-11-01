@extends('admin.layout_admin')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
            </div><!-- /.container-fluid -->
        </section>
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header p-3">
                                <div class="d-flex align-content-center justify-content-between">
                                    <h3 class="font-weight-bold text-xl">Data Customer</h3>
                                    <div class="d-flex align-items-center">
                                        @if ($permissions['tambah'])
                                            <button type="button" class="btn btn-sm btn-info" data-toggle="modal"
                                                data-target="#modalForm">
                                                <i class="fas fa-plus"></i> Tambah Customer
                                            </button>
                                        @endif
                                        <button type="button" class="btn btn-sm ml-1 btn-dark" data-toggle="modal"
                                            data-target="#modalFilterCetak">
                                            <i class="fas fa-file mr-1"></i> Cetak Data
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body">
                                <table class="table table-bordered table-striped data-table">
                                    <thead>
                                        <tr>
                                            <th width="40px">No</th>
                                            <th>Tanggal</th>
                                            <th>Nama Nasabah</th>
                                            <th>Marketing</th>
                                            <th>Perumahan</th>
                                            <th>Status Progres</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </section>


        <!-- /.content -->
        <div class="modal fade" id="modalFilterCetak" tabindex="-1" role="dialog" data-focus="false">
            <div class="modal-dialog" role="document">
                <form action="{{ route('customer.cetak') }}" method="GET" target="_blank">
                    <div class="modal-content">
                        <div class="modal-header bg-success">
                            <h5 class="modal-title">Filter Cetak Data</h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Lokasi</label>
                                <select name="lokasi" class="form-control select-lokasi">
                                    <option value=""></option>
                                    @foreach ($lokasi as $l)
                                        <option value="{{ $l->id_lokasi }}">{{ $l->nama_kavling }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Tipe Cetak</label>
                                <select name="tipe" id="tipe" class="form-control select-tipe-cetak">
                                    <option value=""></option>
                                    <option value="1">Excel</option>
                                    <option value="2">PDF</option>
                                </select>
                            </div>
                            <!-- Tambah filter lain kalau perlu -->
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Cetak</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>


        <!-- Modal -->
        <div class="modal fade" id="modalForm" tabindex="-1" role="dialog" data-focus="false"
            aria-labelledby="modalFormLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-success">
                        <h5 class="modal-title" id="modalFormLabel">Form Customer</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="formData">
                        @csrf
                        <input type="hidden" id="primary_id" name="primary_id">
                        <div class="modal-body">
                            <div class="form-group row">
                                <label for="tgl_terima" class="col-sm-3 col-form-label">Tanggal</label>
                                <div class="col-sm-3">
                                    <input type="date" name="tgl_terima" id="tgl_terima" class="form-control"
                                        value="{{ \Carbon\Carbon::now('Asia/Jakarta')->format('Y-m-d') }}">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="nama_lengkap" class="col-sm-3 col-form-label">Nama Lengkap</label>
                                <div class="col-sm-5">
                                    <input type="text" name="nama_lengkap" id="nama_lengkap" class="form-control">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="no_ktp" class="col-sm-3 col-form-label">No. KTP</label>
                                <div class="col-sm-3">
                                    <input type="number" name="no_ktp" id="no_ktp" class="form-control">
                                </div>
                                <label for="no_ktp_p" class="col-sm-2 col-form-label">No. KTP Pasangan</label>
                                <div class="col-sm-3">
                                    <input type="number" name="no_ktp_p" id="no_ktp_p" class="form-control">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="tempat_lahir" class="col-sm-3 col-form-label">Tempat Lahir</label>
                                <div class="col-sm-3">
                                    <input type="text" name="tempat_lahir" id="tempat_lahir" class="form-control">
                                </div>

                                <label for="tgl_lahir" class="col-sm-2 col-form-label">Tanggal Lahir</label>
                                <div class="col-sm-3">
                                    <input type="date" name="tgl_lahir" id="tgl_lahir" class="form-control">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="jenis_kelamin" class="col-sm-3 col-form-label">Jenis Kelamin</label>
                                <div class="col-sm-3">
                                    <select name="jenis_kelamin" id="jenis_kelamin" class="form-control select-jk">
                                        <option value=""></option>
                                        <option value="Laki-laki">Laki-laki</option>
                                        <option value="Perempuan">Perempuan</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="no_telp" class="col-sm-3 col-form-label">No. Telp / WA</label>
                                <div class="col-sm-3">
                                    <input type="number" name="no_telp" id="no_telp" class="form-control">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="email" class="col-sm-3 col-form-label">Email</label>
                                <div class="col-sm-3">
                                    <input type="email" name="email" id="email" class="form-control">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="npwp" class="col-sm-3 col-form-label">NPWP</label>
                                <div class="col-sm-3">
                                    <input type="text" name="npwp" id="npwp" class="form-control">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="alamat" class="col-sm-3 col-form-label">Alamat KTP</label>
                                <div class="col-sm-5">
                                    <input type="text" name="alamat" id="alamat" class="form-control">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="alamat_domisili" class="col-sm-3 col-form-label">Alamat Domisili</label>
                                <div class="col-sm-5">
                                    <input type="text" name="alamat_domisili" id="alamat_domisili"
                                        class="form-control">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="pekerjaan" class="col-sm-3 col-form-label">Pekerjaan</label>
                                <div class="col-sm-3">
                                    <input type="text" name="pekerjaan" id="pekerjaan" class="form-control">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="warga_negara" class="col-sm-3 col-form-label">Warga Negara</label>
                                <div class="col-sm-3">
                                    <input type="text" name="warga_negara" id="warga_negara" class="form-control">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="id_lokasi" class="col-sm-3 col-form-label">Lokasi Perumahan</label>
                                <div class="col-sm-3">
                                    <select name="id_lokasi" id="id_lokasi" class="form-control select-lokasi">
                                        <option value=""></option>
                                        @foreach ($lokasi as $l)
                                            <option value="{{ $l->id_lokasi }}">{{ $l->nama_kavling }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="id_kavling" class="col-sm-3 col-form-label">Blok / Kode Kavling</label>
                                <div class="col-sm-3">
                                    <select name="id_kavling" id="id_kavling"
                                        class="form-control select-kavling"></select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="hrg_jual" class="col-sm-3 col-form-label">Harga Jual</label>
                                <div class="col-sm-3">
                                    <input type="text" id="hrg_jual" name="hrg_jual_display" class="form-control"
                                        disabled>
                                    <input type="text" name="hrg_jual" id="hrg_jual_hidden" hidden>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="id_marketing" class="col-sm-3 col-form-label">Marketing</label>
                                <div class="col-sm-3">
                                    <select name="id_marketing" id="id_marketing" class="form-control select-marketing">
                                        <option value=""></option>
                                        @foreach ($marketing as $m)
                                            <option value="{{ $m->id_marketing }}">{{ $m->nama_marketing }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="id_freelance" class="col-sm-3 col-form-label">Freelance</label>
                                <div class="col-sm-3">
                                    <select name="id_freelance" id="id_freelance" class="form-control select-freelance">
                                        <option value=""></option>
                                        @foreach ($freelance as $f)
                                            <option value="{{ $f->id_freelance }}">{{ $f->nama_freelance }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="id_status_progres" class="col-sm-3 col-form-label">Status Progres</label>
                                <div class="col-sm-3">
                                    <select name="id_status_progres" id="id_status_progres"
                                        class="form-control select-status-progres">
                                        <option value=""></option>
                                        @foreach ($progres as $p)
                                            <option value="{{ $p->id_status_progres }}">{{ $p->status_progres }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="ket_cashback" class="col-sm-3 col-form-label">Keterangan Cashback</label>
                                <div class="col-sm-5">
                                    <input type="text" name="ket_cashback" id="ket_cashback" class="form-control">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="jenis_pembelian" class="col-sm-3 col-form-label">Jenis Pembelian</label>
                                <div class="col-sm-3">
                                    <select name="jenis_pembelian" id="jenis_pembelian"
                                        class="form-control select-jenis-pembelian">
                                        <option value=""></option>
                                        <option value="Booking">Booking</option>
                                        <option value="Cash Keras">Cash Keras</option>
                                        <option value="Cash Bertahap">Cash Bertahap</option>
                                        <option value="Kredit Inhouse">Kredit Inhouse</option>
                                        <option value="KPR">KPR</option>
                                    </select>
                                </div>
                            </div>

                            <!-- BOOKING ==================================> -->
                            <hr class="hr-transaksi" style="display: none;">
                            <div id="trx_booking" style="display: none;">

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Pembayaran Booking</label>
                                    <div class="col-sm-3">
                                        <input name="pembayaran_booking" id="pembayaran_booking" class="form-control"
                                            type="text">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Tanggal Batas Booking</label>
                                    <div class="col-sm-3">
                                        <input name="tgl_batas_booking" id="tgl_batas_booking" class="form-control"
                                            type="date">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Keterangan</label>
                                    <div class="col-sm-3">
                                        <input name="ket_booking" id="ket_booking" class="form-control" type="text">
                                    </div>
                                </div>
                            </div>

                            <!-- CASH ==================================> -->
                            <hr class="hr-transaksi" style="display: none;">
                            <div id="trx_cash" style="display: none;">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Diskon</label>
                                    <div class="col-sm-3">
                                        <input name="diskon_cash" id="diskon_cash" class="form-control" type="text"
                                            value="0">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Pembayaran Cash</label>
                                    <div class="col-sm-3">
                                        <input name="pembayaran_cash" id="pembayaran_cash" class="form-control"
                                            type="text" value="0">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Sisa Pembayaran (AJB)</label>
                                    <div class="col-sm-3">
                                        <input name="sisa_bayar_ajb" id="sisa_bayar_ajb" class="form-control"
                                            type="text" value="0">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Atas Nama Surat</label>
                                    <div class="col-sm-3">
                                        <input name="an_surat_cash" id="an_surat_cash" class="form-control"
                                            type="text">
                                    </div>
                                </div>
                            </div>

                            <!-- CASH BERTAHAP ==================================> -->
                            <hr class="hr-transaksi" style="display: none;">
                            <div id="trx_cash_bertahap" style="display: none;">

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">DP</label>
                                    <div class="col-sm-3">
                                        <input name="dp_cash_b" id="dp_cash_b" class="form-control" type="text"
                                            value="0">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Termin (x)</label>
                                    <div class="col-sm-3">
                                        <input name="termin_x_cash_b" id="termin_x_cash_b" class="form-control"
                                            type="number" value="0">
                                    </div>
                                </div>
                            </div>

                            <!-- KREDIT ==================================> -->
                            <hr class="hr-transaksi" style="display: none;">
                            <div id="trx_kredit" style="display: none;">

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">DP / Uang Muka</label>
                                    <div class="col-sm-3">
                                        <input name="dp_kredit" id="dp_kredit" class="form-control" type="text">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Lama Cicilan</label>
                                    <div class="col-sm-3">
                                        <input name="lama_cicilan_kredit" id="lama_cicilan_kredit" class="form-control"
                                            type="text">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Cicilan Per Bulan</label>
                                    <div class="col-sm-3">
                                        <input name="cicilan_kredit" id="cicilan_kredit" class="form-control"
                                            type="text">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Tanggal Tempo Cicilan Pertama</label>
                                    <div class="col-sm-3">
                                        <input name="tgl_tempo_cicilan_1" id="tgl_tempo_cicilan_1" class="form-control"
                                            type="date">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Atas Nama Surat</label>
                                    <div class="col-sm-3">
                                        <input name="an_surat_kredit" id="an_surat_kredit" class="form-control"
                                            type="text">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <span class="spinner-border spinner-border-sm mx-1 d-none" role="status"
                                    aria-hidden="true"></span>
                                <span class="button-text">Simpan</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        var audio = new Audio('{{ asset('audio/notification.ogg') }}');

        $(document).ready(function() {
            $('.select-lokasi').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Lokasi",
            });
            $('.select-jk').select2({
                theme: "bootstrap4",
                minimumResultsForSearch: Infinity,
                placeholder: "Pilih Jenis Kelamin",
            });
            $('.select-kavling').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Kavling",
            });
            $('.select-marketing').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Marketing",
            });

            $('.select-freelance').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Freelance",
            });

            $('.select-status-progres').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Status Progres",
            });

            $('.select-bank').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Bank KPR",
            });

            $('.select-jenis-pembelian').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Jenis Pembelian",
                minimumResultsForSearch: Infinity,
            });

            $('.select-tipe-cetak').select2({
                theme: "bootstrap4",
                minimumResultsForSearch: Infinity,
                placeholder: "Pilih Tipe Cetak",
            });

            function hideAllTransactionForms() {
                $('#trx_booking').hide();
                $('#trx_cash').hide();
                $('#trx_cash_bertahap').hide();
                $('#trx_kredit').hide();
                $('.hr-transaksi').hide();
            }

            $('#modalForm').on('hidden.bs.modal', function() {
                hideAllTransactionForms();
                $('#formData')[0].reset();
                $('#jenis_pembelian').val('').trigger('change');

                let submitBtn = $('#submitBtn');
                let spinner = submitBtn.find('.spinner-border');
                let btnText = submitBtn.find('.button-text');

                spinner.addClass('d-none');
                btnText.text('Simpan');
                submitBtn.prop('disabled', false);
            });

            // Ketika modal dibuka
            $('#modalForm').on('shown.bs.modal', function() {
                hideAllTransactionForms();
            });

            $('#jenis_pembelian').on('change', function() {
                let selected = $(this).val();
                hideAllTransactionForms();

                switch (selected) {
                    case 'Booking':
                        $('#trx_booking').prev('.hr-transaksi').show();
                        $('#trx_booking').show();
                        break;
                    case 'Pembelian Cash':
                        $('#trx_cash').prev('.hr-transaksi').show();
                        $('#trx_cash').show();
                        break;
                    case 'Cash Bertahap':
                        $('#trx_cash_bertahap').prev('.hr-transaksi').show();
                        $('#trx_cash_bertahap').show();
                        break;
                    case 'Kredit':
                        $('#trx_kredit').prev('.hr-transaksi').show();
                        $('#trx_kredit').show();
                        break;
                }
            });

        });


        $(document).ready(function() {
            // Inisialisasi Select2
            $('.select-lokasi').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Lokasi",
            });

            $('.select-kavling').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Kavling",
            });

            const routeGetKavling = "{{ route('customer.getKavling', ':id') }}";
            const routeGetHarga = "{{ route('customer.getHargaKavling', ':id') }}";

            $('#id_lokasi').on('change', function() {
                let idLokasi = $(this).val();
                $('#id_kavling').html('<option value="">Loading...</option>').trigger('change');
                $('#hrg_jual').val('');
                $('#hrg_jual_hidden').val('');

                if (idLokasi) {
                    const urlKavling = routeGetKavling.replace(':id', idLokasi);
                    $.get(urlKavling, function(data) {
                        let options = '<option value=""></option>';
                        data.forEach(function(item) {
                            options +=
                                `<option value="${item.id_kavling}">${item.kode_kavling}</option>`;
                        });
                        $('#id_kavling').html(options).trigger('change');
                    });
                }
            });

            // Event saat kavling berubah
            $('#id_kavling').on('change', function() {
                let idKavling = $(this).val();

                if (idKavling) {
                    const urlHarga = routeGetHarga.replace(':id', idKavling);
                    $.get(urlHarga, function(data) {
                        $('#hrg_jual').val(data.formatted);
                        $('#hrg_jual_hidden').val(data.hrg_jual);
                    });
                } else {
                    $('#hrg_jual').val('');
                    $('#hrg_jual_hidden').val('');
                }
            });
        });

        function formatRupiah(angka) {
            if (!angka) return '';
            return angka.toString().replace(/\D/g, '') // hanya angka
                .replace(/\B(?=(\d{3})+(?!\d))/g, '.'); // ribuan
        }

        const uangFields = [
            '#pembayaran_booking',
            '#diskon_cash',
            '#pembayaran_cash',
            '#sisa_bayar_ajb',
            '#dp_cash_b',
            '#dp_kredit',
            '#cicilan_kredit'
        ];

        uangFields.forEach(function(selector) {
            $(document).on('input', selector, function() {
                let val = $(this).val().replace(/\./g, '');
                $(this).val(formatRupiah(val));
            });
        });

        $(document).on('click', '#edit-button', function() {
            var url = $(this).data('url');
            $.get(url, function(response) {
                if (response.status === 'success') {
                    const data = response.data;
                    const user = response.user;

                    $('#primary_id').val(data.id_customer);
                    $('#tgl_terima').val(data.tgl_terima);
                    $('#jenis_kelamin').val(data.jenis_kelamin).trigger('change');
                    $('#nama_lengkap').val(data.nama_lengkap);
                    $('#no_ktp').val(data.no_ktp);
                    $('#no_ktp_p').val(data.no_ktp_p);
                    $('#tempat_lahir').val(data.tempat_lahir);
                    $('#tgl_lahir').val(data.tgl_lahir);
                    $('#no_telp').val(data.no_wa ?? data.no_telp);
                    $('#email').val(data.email);
                    $('#npwp').val(data.npwp);
                    $('#alamat').val(data.alamat);
                    $('#alamat_domisili').val(data.alamat_domisili);
                    $('#pekerjaan').val(data.pekerjaan);
                    $('#warga_negara').val(data.warga_negara);

                    $('#id_lokasi').val(data.id_lokasi).trigger('change');
                    setTimeout(function() {
                        $('#id_kavling').val(data.id_kavling).trigger('change');
                    }, 300);

                    $('#hrg_jual').val(data.hrg_jual);
                    $('#hrg_jual_hidden').val(data.hrg_jual);
                    $('#id_marketing').val(data.id_marketing).trigger('change');
                    $('#id_freelance').val(data.id_freelance).trigger('change');

                    if (user.role !== 4) {
                        $('#id_status_progres').val(data.id_status_progres).trigger('change').prop(
                            'readonly', true);
                    } else {
                        $('#id_status_progres').val(data.id_status_progres).trigger('change').prop(
                            'readonly', false);
                    }

                    $('#ket_cashback').val(data.ket_cashback);
                    $('#jenis_pembelian').val(data.jenis_pembelian);

                    // Booking
                    $('#pembayaran_booking').val(formatRupiah(data.pembayaran_booking));
                    $('#pembayaran_booking_int').val(data.pembayaran_booking); // hidden tetap angka mentah
                    $('#tgl_batas_booking').val(data.tgl_batas_booking);
                    $('#ket_booking').val(data.ket_booking);

                    // Cash
                    $('#diskon_cash').val(formatRupiah(data.diskon_cash));
                    $('#pembayaran_cash').val(formatRupiah(data.pembayaran_cash));
                    $('#sisa_bayar_ajb').val(formatRupiah(data.sisa_bayar_ajb));
                    $('#an_surat_cash').val(data.an_surat_cash); // bukan angka

                    // Cash Bertahap
                    $('#dp_cash_b').val(formatRupiah(data.dp_cash_b));
                    $('#termin_x_cash_b').val(data.termin_x_cash_b); // ini jumlah termin, bukan uang

                    // Kredit
                    $('#dp_kredit').val(formatRupiah(data.dp_kredit));
                    $('#lama_cicilan_kredit').val(data.lama_cicilan_kredit); // jumlah bulan
                    $('#cicilan_kredit').val(formatRupiah(data.cicilan_kredit));
                    $('#tgl_tempo_cicilan_1').val(data.tgl_tempo_cicilan_1);
                    $('#an_surat_kredit').val(data.an_surat_kredit);


                    $('#modalForm').modal('show');
                    $('#modalForm').one('shown.bs.modal', function() {
                        $('#jenis_pembelian').trigger('change');
                    });
                }
            });
        });


        $('#modalForm').on('hidden.bs.modal', function() {
            $('#formData')[0].reset();
            $('#primary_id').val('');
            $('.select-jk').val('').trigger('change');
            $('.select-lokasi').val('').trigger('change');
            $('.select-kavling').val('').trigger('change');
            $('.select-marketing').val('').trigger('change');
            $('.select-freelance').val('').trigger('change');
            $('.select-status-progres').val('').trigger('change');
            $('.select-jenis-pembelian').val('').trigger('change');
            $('.select-bank').val('').trigger('change');
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            let submitBtn = $('#submitBtn');
            let spinner = submitBtn.find('.spinner-border');
            let btnText = submitBtn.find('.button-text');

            spinner.addClass('d-none');
            btnText.text('Simpan');
            submitBtn.prop('disabled', false);
        });

        // Simpan / Update data
        $('#formData').on('submit', function(e) {
            e.preventDefault();

            let submitBtn = $('#submitBtn');
            let spinner = submitBtn.find('.spinner-border');
            let btnText = submitBtn.find('.button-text');

            spinner.removeClass('d-none');
            btnText.text('Menyimpan...');
            submitBtn.prop('disabled', true);

            let id = $('#primary_id').val();
            let url = id ? '{{ route('customer.update', ['customer' => ':id']) }}'.replace(':id', id) :
                '{{ route('customer.store') }}';
            let method = id ? 'PUT' : 'POST';

            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            $.ajax({
                url: url,
                method: method,
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    _method: method,
                    tgl_terima: $('#tgl_terima').val(),
                    jenis_kelamin: $('#jenis_kelamin').val(),
                    nama_lengkap: $('#nama_lengkap').val(),
                    no_ktp: $('#no_ktp').val(),
                    no_ktp_p: $('#no_ktp_p').val(),
                    tempat_lahir: $('#tempat_lahir').val(),
                    tgl_lahir: $('#tgl_lahir').val(),
                    no_telp: $('#no_telp').val(),
                    email: $('#email').val(),
                    npwp: $('#npwp').val(),
                    alamat: $('#alamat').val(),
                    alamat_domisili: $('#alamat_domisili').val(),
                    pekerjaan: $('#pekerjaan').val(),
                    warga_negara: $('#warga_negara').val(),
                    id_lokasi: $('#id_lokasi').val(),
                    id_kavling: $('#id_kavling').val(),
                    hrg_rumah: $('#hrg_jual_hidden').val(),
                    id_marketing: $('#id_marketing').val(),
                    id_freelance: $('#id_freelance').val(),
                    id_status_progres: $('#id_status_progres').val(),
                    ket_cashback: $('#ket_cashback').val(),
                    jenis_pembelian: $('#jenis_pembelian').val(),
                    pembayaran_booking: $('#pembayaran_booking').val().replace(/\./g, ''),
                    tgl_batas_booking: $('#tgl_batas_booking').val(),
                    ket_booking: $('#ket_booking').val(),
                    diskon_cash: $('#diskon_cash').val().replace(/\./g, ''),
                    pembayaran_cash: $('#pembayaran_cash').val().replace(/\./g, ''),
                    sisa_bayar_ajb: $('#sisa_bayar_ajb').val().replace(/\./g, ''),
                    an_surat_cash: $('#an_surat_cash').val(),
                    dp_cash_b: $('#dp_cash_b').val().replace(/\./g, ''),
                    termin_x_cash_b: $('#termin_x_cash_b').val(),
                    dp_kredit: $('#dp_kredit').val().replace(/\./g, ''),
                    lama_cicilan_kredit: $('#lama_cicilan_kredit').val(),
                    cicilan_kredit: $('#cicilan_kredit').val().replace(/\./g, ''),
                    tgl_tempo_cicilan_1: $('#tgl_tempo_cicilan_1').val(),
                    an_surat_kredit: $('#an_surat_kredit').val()
                },
                success: function() {
                    $('#modalForm').modal('hide');
                    audio.play();
                    toastr.success("Data telah disimpan!", "BERHASIL", {
                        progressBar: true,
                        timeOut: 3500,
                        positionClass: "toast-bottom-right",
                    });
                    $('.data-table').DataTable().ajax.reload();
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        audio.play();
                        toastr.error("Ada inputan yang salah!", "GAGAL!", {
                            progressBar: true,
                            timeOut: 3500,
                            positionClass: "toast-bottom-right",
                        });

                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, val) {
                            console.log("Error di field:", key, "-", val[
                                0]); // <-- Tambahkan baris ini

                            let input = $('#' + key);
                            input.addClass('is-invalid');

                            // Hapus pesan error sebelumnya agar tidak dobel
                            input.parent().find('.invalid-feedback').remove();

                            input.parent().append(
                                '<span class="invalid-feedback" role="alert"><strong>' +
                                val[0] + '</strong></span>'
                            );
                        });
                    } else {
                        audio.play();
                        toastr.error('Periksa koneksi atau input Anda.',
                            "Terjadi Kesalahan!", {
                                closeButton: true,
                                progressBar: true,
                                timeOut: 3000,
                                positionClass: 'toast-bottom-right'
                            });
                    }

                    spinner.addClass('d-none');
                    btnText.text('Simpan');
                    submitBtn.prop('disabled', false);
                }
            });
        });


        $(function() {
            var permissions = @json($permissions);
            var showActionColumn = (permissions['edit'] == 1 || permissions['hapus'] == 1);

            var table = $('.data-table').DataTable({
                processing: false,
                serverSide: true,
                ordering: false,
                responsive: true,
                ajax: "{{ route('customer.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'tgl_terima',
                        name: 'tgl_terima',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nama_lengkap',
                        name: 'nama_lengkap',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'id_marketing',
                        name: 'id_marketing',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'id_lokasi',
                        name: 'id_lokasi',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'id_status_progres',
                        name: 'id_status_progres',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        visible: showActionColumn
                    }
                ],
                columnDefs: [{
                    targets: 0,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                }]
            });
        });

        $(document).on('click', '.delete-button', function(e) {
            e.preventDefault();

            const form = $(this).closest('form');

            if (!form.has('button.delete-button').length) return;

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Data ini akan dihapus secara permanen!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '<span class="swal-btn-text">Ya, Hapus</span>',
                cancelButtonText: 'Batal',
                showLoaderOnConfirm: false,
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'btn btn-danger mx-2',
                    cancelButton: 'btn btn-secondary'
                },
                preConfirm: () => {
                    return new Promise((resolve) => {
                        const confirmBtn = Swal.getConfirmButton();
                        const btnText = confirmBtn.querySelector('.swal-btn-text');

                        btnText.innerHTML = `
                    <span class="spinner-border spinner-border-sm mx-2" role="status" aria-hidden="true"></span>
                    Menghapus...`;
                        confirmBtn.disabled = true;

                        $.ajax({
                            url: form.attr('action'),
                            method: 'POST',
                            data: form.serialize(),
                            success: function() {
                                audio.play();
                                toastr.success("Data telah dihapus!", "BERHASIL", {
                                    progressBar: true,
                                    timeOut: 3500,
                                    positionClass: "toast-bottom-right"
                                });

                                $('.data-table').DataTable().ajax.reload(null,
                                    false);
                                Swal.close();
                            },
                            error: function() {
                                audio.play();
                                toastr.error("Gagal menghapus data.", "GAGAL!", {
                                    progressBar: true,
                                    timeOut: 3500,
                                    positionClass: "toast-bottom-right"
                                });

                                btnText.innerHTML = `Ya, Hapus`;
                                confirmBtn.disabled = false;
                            }
                        });
                    });
                }
            });
        });
    </script>
@endpush
