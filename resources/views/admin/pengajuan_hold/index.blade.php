@extends('admin.layout_admin')
@section('content')
    <x-toast />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
            </div><!-- /.container-fluid -->
        </section>
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header p-3">
                        <div class="d-flex align-content-center justify-content-between">
                            <h3 class="font-weight-bold text-xl">Data Pengajuan Hold</h3>
                            <div class="d-flex align-items-center" style="gap: 3px">
                                <button id="toggleArchiveBtn" class="btn btn-success btn-sm">
                                    <i class="fas fa-book mr-1"></i>
                                    <span id="archiveBtnText">Tampilkan Arsip</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="alert alert-info" id="archiveAlert" style="display: none;">
                            <i class="fas fa-info-circle mr-2"></i> Sedang menampilkan data arsip (status Disetujui)
                        </div>
                        <div class="table-responsive">
                            <table id="table" class="table w-100 small table-bordered table-striped data-table">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th >Tanggal</th>
                                        <th >Customer</th>
                                        <th >No. Telp</th>
                                        <th >Nama Marketing</th>
                                        <th >Lokasi</th>
                                        <th >Status</th>
                                        <th width="10%" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="modalForm" tabindex="-1" role="dialog" data-focus="false"
                    aria-labelledby="modalFormLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                    <div class="modal-dialog modal-xl" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-success">
                                <h5 class="modal-title" id="modalFormLabel">Edit Pengajuan Hold</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form id="formData">
                                @csrf
                                <input type="hidden" id="primary_id" name="primary_id">
                                <div class="modal-body">
                                    <div class="form-group row">
                                        <label for="tgl_registrasi" class="col-sm-3 col-form-label">Tanggal</label>
                                        <div class="col-sm-3">
                                            <input type="text" name="tgl_registrasi" id="tgl_registrasi" disabled
                                                class="form-control">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="nama_lengkap" class="col-sm-3 col-form-label">Nama Lengkap</label>
                                        <div class="col-sm-5">
                                            <input type="text" name="nama_lengkap" id="nama_lengkap"
                                                class="form-control">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="nik" class="col-sm-3 col-form-label">No. KTP</label>
                                        <div class="col-sm-3">
                                            <input type="number" name="nik" id="nik" class="form-control">
                                        </div>
                                        <label for="nik_p" class="col-sm-2 col-form-label">No. KTP Pasangan</label>
                                        <div class="col-sm-3">
                                            <input type="number" name="nik_p" id="nik_p" class="form-control">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="tempat_lahir" class="col-sm-3 col-form-label">Tempat Lahir</label>
                                        <div class="col-sm-3">
                                            <input type="text" name="tempat_lahir" id="tempat_lahir"
                                                class="form-control">
                                        </div>

                                        <label for="tgl_lahir" class="col-sm-2 col-form-label">Tanggal Lahir</label>
                                        <div class="col-sm-3">
                                            <input type="date" name="tgl_lahir" id="tgl_lahir" class="form-control">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="jenis_kelamin" class="col-sm-3 col-form-label">Jenis Kelamin</label>
                                        <div class="col-sm-3">
                                            <select name="jenis_kelamin" id="jenis_kelamin"
                                                class="form-control select-jk">
                                                <option value=""></option>
                                                <option value="Laki-laki">Laki-laki</option>
                                                <option value="Perempuan">Perempuan</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="no_telp_customer" class="col-sm-3 col-form-label">No. Telp /
                                            WA</label>
                                        <div class="col-sm-3">
                                            <input type="number" name="no_telp_customer" id="no_telp_customer"
                                                class="form-control">
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
                                        <label for="no_bpjs_kes" class="col-sm-3 col-form-label">No. BPJS Kes</label>
                                        <div class="col-sm-3">
                                            <input type="text" name="no_bpjs_kes" id="no_bpjs_kes"
                                                class="form-control">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="alamat" class="col-sm-3 col-form-label">Alamat</label>
                                        <div class="col-sm-5">
                                            <input type="text" name="alamat" id="alamat" class="form-control">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="pekerjaan" class="col-sm-3 col-form-label">Pekerjaan</label>
                                        <div class="col-sm-3">
                                            <input type="text" name="pekerjaan" id="pekerjaan" class="form-control">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="status" class="col-sm-3 col-form-label">Status Pernikahan</label>
                                        <div class="col-sm-3">
                                            <select name="status" id="status" class="form-control select-sp">
                                                <option value=""></option>
                                                <option value="Belum Menikah">Belum Menikah</option>
                                                <option value="Menikah">Menikah</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="nama_p" class="col-sm-3 col-form-label">Nama Pasangan</label>
                                        <div class="col-sm-3">
                                            <input type="text" name="nama_p" id="nama_p" class="form-control">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="nama_saudara" class="col-sm-3 col-form-label">Nama Saudara</label>
                                        <div class="col-sm-3">
                                            <input type="text" name="nama_saudara" id="nama_saudara"
                                                class="form-control">
                                        </div>
                                        <label for="no_telp_saudara" class="col-sm-2 col-form-label">No. Telp
                                            Saudara</label>
                                        <div class="col-sm-3">
                                            <input type="number" name="no_telp_saudara" id="no_telp_saudara"
                                                class="form-control">
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
                                        <label for="id_kavling" class="col-sm-3 col-form-label">Blok / Kode
                                            Kavling</label>
                                        <div class="col-sm-3">
                                            <select name="id_kavling" id="id_kavling"
                                                class="form-control select-kavling"></select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="hrg_jual" class="col-sm-3 col-form-label">Harga Jual</label>
                                        <div class="col-sm-3">
                                            <input type="text" id="hrg_jual" name="hrg_jual_display"
                                                class="form-control" disabled>
                                            <input type="text" name="hrg_jual" id="hrg_jual_hidden" hidden>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="id_marketing" class="col-sm-3 col-form-label">Marketing</label>
                                        <div class="col-sm-3">
                                            <select name="id_marketing" id="id_marketing"
                                                class="form-control select-marketing">
                                                <option value=""></option>
                                                @foreach ($marketing as $m)
                                                    <option value="{{ $m->id_marketing }}">{{ $m->nama_marketing }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="id_freelance" class="col-sm-3 col-form-label">Freelance</label>
                                        <div class="col-sm-3">
                                            <select name="id_freelance" id="id_freelance"
                                                class="form-control select-freelance">
                                                <option value=""></option>
                                                @foreach ($freelance as $f)
                                                    <option value="{{ $f->id_freelance }}">{{ $f->nama_freelance }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="jenis_perumahan" class="col-sm-3 col-form-label">Jenis
                                            Perumahan</label>
                                        <div class="col-sm-3">
                                            <select name="jenis_perumahan" id="jenis_perumahan"
                                                class="form-control select-jenis-perumahan">
                                                <option value=""></option>
                                                <option value="Subsidi">Subsidi</option>
                                                <option value="Komersial">Komersial</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="jenis_pembelian" class="col-sm-3 col-form-label">Jenis
                                            Pembelian</label>
                                        <div class="col-sm-3">
                                            <select name="jenis_pembelian" id="jenis_pembelian"
                                                class="form-control select-jenis-pembelian">
                                                <option value=""></option>
                                                <option value="Cash Keras">Cash Keras</option>
                                                <option value="Cash Bertahap">Cash Bertahap</option>
                                                <option value="KPR">KPR</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="booking_fee" class="col-sm-3 col-form-label">Booking Fee</label>
                                        <div class="col-sm-3">
                                            <input type="text" name="booking_fee" id="booking_fee"
                                                class="form-control">
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
                                        <button type="submit" id="submitBtn" class="btn btn-primary">
                                            <span class="spinner-border spinner-border-sm me-2 d-none" role="status"
                                                aria-hidden="true"></span>
                                            <span class="button-text">Simpan</span>
                                        </button>
                                    </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>
@endsection

@push('scripts')
    <script>
        var audio = new Audio('{{ asset('audio/notification.ogg') }}');
        var isArchiveView = false;
        var table;

        $(document).ready(function() {
            let successMessage = sessionStorage.getItem('success');
            if (successMessage) {
                audio.play();
                toastr.success(successMessage, "SUKSES!", {
                    progressBar: true,
                    timeOut: 3500,
                    positionClass: "toast-bottom-right",
                });

                sessionStorage.removeItem('success');
            }
        });

        $(function() {
            var audio = new Audio('{{ asset('audio/notification.ogg') }}');
            $(document).ready(function() {
                @if (session('success'))
                    audio.play();
                    toastr.success("{{ session('success') }}", "BERHASIL", {
                        progressBar: true,
                        timeOut: 3500,
                        positionClass: "toast-bottom-right",
                    });
                    if ($.fn.DataTable.isDataTable('.data-table')) {
                        $('.data-table').DataTable().ajax.reload();
                    }
                @endif

                @if (session('error'))
                    audio.play();
                    toastr.error("{{ session('error') }}", "GAGAL!", {
                        progressBar: true,
                        timeOut: 3500,
                        positionClass: "toast-bottom-right",
                    });
                @endif
            });

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

                $('.select-sp').select2({
                    theme: "bootstrap4",
                    placeholder: "Pilih Status",
                    minimumResultsForSearch: Infinity,
                });

                $('.select-jenis-perumahan').select2({
                    theme: "bootstrap4",
                    placeholder: "Pilih Perumahan",
                    minimumResultsForSearch: Infinity,
                });

                $('.select-jenis-pembelian').select2({
                    theme: "bootstrap4",
                    placeholder: "Pilih Jenis Pembelian",
                    minimumResultsForSearch: Infinity,
                });
            });

            initializeDataTable();

            $('#toggleArchiveBtn').on('click', function() {
                toggleArchiveView();
            });
        });

        function initializeDataTable() {
            table = $('#table').DataTable({
                processing: false,
                serverSide: true,
                responsive: true,
                ordering: false,
                ajax: {
                    url: "{{ route('pengajuan-hold.index') }}",
                    data: function(d) {
                        if (isArchiveView) {
                            d.archive = true;
                        }
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
                        data: 'tgl_registrasi',
                        name: 'tgl_registrasi',
                    },
                    {
                        data: 'nama_lengkap',
                        name: 'nama_lengkap',
                    },
                    {
                        data: 'no_telp_customer',
                        name: 'no_telp_customer',
                    },
                    {
                        data: 'nama_marketing',
                        name: 'nama_marketing',
                    },
                    {
                        data: 'kode_kavling',
                        name: 'kode_kavling',
                    },
                    {
                        data: 'stt_reg',
                        name: 'stt_reg',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '25%'
                    }
                ],
                pageLength: 10,
                language: {
                    emptyTable: "Tidak ada data pengajuan"
                }
            });
        }

        function toggleArchiveView() {
            isArchiveView = !isArchiveView;

            if ($.fn.DataTable.isDataTable('#table')) {
                table.destroy();
            }

            initializeDataTable();

            updateArchiveUI();
        }

        function updateArchiveUI() {
            const archiveBtn = $('#toggleArchiveBtn');
            const archiveBtnText = $('#archiveBtnText');
            const archiveAlert = $('#archiveAlert');

            if (isArchiveView) {
                // Mode arsip aktif
                archiveBtn.removeClass('btn-success').addClass('btn-info');
                archiveBtnText.text('Tampilkan Data Utama');
                archiveAlert.show();

                audio.play();
                toastr.info("Menampilkan data arsip (status Disetujui)", "INFO", {
                    progressBar: true,
                    timeOut: 3000,
                    positionClass: "toast-bottom-right",
                });
            } else {
                // Mode arsip non-aktif
                archiveBtn.removeClass('btn-info').addClass('btn-success');
                archiveBtnText.text('Tampilkan Arsip');
                archiveAlert.hide();

                audio.play();
                toastr.info("Menampilkan data utama", "INFO", {
                    progressBar: true,
                    timeOut: 3000,
                    positionClass: "toast-bottom-right",
                });
            }
        }

        $(document).ready(function() {
            $('.select-lokasi').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Lokasi",
            });

            $('.select-kavling').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Kavling",
            });

            const routeGetKavling = "{{ route('booking.getKavling', ':id') }}";
            const routeGetHarga = "{{ route('booking.getHargaKavling', ':id') }}";

            $('#id_lokasi').on('change', function() {
                let idLokasi = $(this).val();
                let currentId = $('#id_kavling').val();
                $('#id_kavling').html('<option value="">Loading...</option>').trigger('change');
                $('#hrg_jual').val('');
                $('#hrg_jual_hidden').val('');

                if (idLokasi) {
                    let url = `/get-kavling/${idLokasi}`;
                    if (currentId) {
                        url += `/${currentId}`;
                    }
                    $.get(url, function(data) {
                        let options = '<option value=""></option>';
                        data.forEach(function(item) {
                            options +=
                                `<option value="${item.id_kavling}">${item.kode_kavling}</option>`;
                        });
                        $('#id_kavling').html(options).trigger('change');
                    });
                }
            });

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

        var formData = $('#formData').serializeArray();

        $(document).on('click', '#edit-button', function() {
            var url = $(this).data('url');
            $.get(url, function(response) {
                if (response.status === 'success') {
                    const data = response.data;

                    console.log(data);

                    // Basic Information
                    $('#primary_id').val(data.id_reg);
                    $('#tgl_registrasi').val(data.tgl_registrasi);
                    $('#nama_lengkap').val(data.nama_lengkap);
                    $('#nik').val(data.nik);
                    $('#nik_p').val(data.nik_p);

                    // Personal Details
                    $('#tempat_lahir').val(data.tempat_lahir);
                    $('#tgl_lahir').val(data.tgl_lahir);
                    $('#jenis_kelamin').val(data.jenis_kelamin).trigger('change');

                    // Contact Information
                    $('#no_telp_customer').val(data.no_telp_customer);
                    $('#email').val(data.email);

                    // Document Numbers
                    $('#npwp').val(data.npwp);
                    $('#no_bpjs_kes').val(data.no_bpjs_kes);

                    // Address & Work
                    $('#alamat').val(data.alamat);
                    $('#pekerjaan').val(data.pekerjaan);

                    // Family Information
                    $('#status').val(data.status).trigger('change');
                    $('#nama_p').val(data.nama_p);
                    $('#nama_saudara').val(data.nama_saudara);
                    $('#no_telp_saudara').val(data.no_telp_saudara);

                    // Property Information
                    $('#id_lokasi').val(data.id_lokasi).trigger('change');
                    loadKavling(data.id_lokasi, data.id_kavling).then(() => {
                        $('#id_kavling').val(data.id_kavling).trigger('change');
                    });

                    $('#id_marketing').val(data.id_marketing).trigger('change');
                    $('#id_freelance').val(data.id_freelance).trigger('change');
                    $('#jenis_perumahan').val(data.jenis_perumahan).trigger('change');
                    $('#jenis_pembelian').val(data.jenis_pembelian).trigger('change');

                    // Pricing & Sales
                    $('#hrg_jual').val(formatAngkaRibuan(data.hrg_jual.toString()));
                    $('#hrg_jual_hidden').val(data.hrg_jual);
                    $('#booking_fee').val(data.booking_fee ? formatAngkaRibuan(data.booking_fee
                        .toString()) : '');


                    $('#modalForm').modal('show');
                }
            });
        });

        function loadKavling(id_lokasi, currentId = null) {
            let url = `/get-kavling/${id_lokasi}`;
            if (currentId) {
                url += `/${currentId}`;
            }
            return $.get(url, function(res) {
                let $kavling = $('#id_kavling');
                $kavling.empty().append('<option value="">Pilih Kavling</option>');
                res.forEach(k => {
                    $kavling.append(`<option value="${k.id_kavling}">${k.kode_kavling}</option>`);
                });
            });
        }

        $('#modalForm').on('hidden.bs.modal', function() {
            $('#formData')[0].reset();
            $('#primary_id').val('');
            $('.jenis_kelamin').val('').trigger('change');
            $('.status').val('').trigger('change');
            $('.id_lokasi').val('').trigger('change');
            $('.id_kavling').val('').trigger('change');
            $('.id_marketing').val('').trigger('change');
            $('.id_freelance').val('').trigger('change');
            $('.jenis_perumahan').val('').trigger('change');
            $('.jenis_pembelian').val('').trigger('change');
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            let submitBtn = $('#submitBtn');
            let spinner = submitBtn.find('.spinner-border');
            let btnText = submitBtn.find('.button-text');

            spinner.addClass('d-none');
            btnText.text('Simpan');
            submitBtn.prop('disabled', false);
        });

        $('#formData').on('submit', function(e) {
            e.preventDefault();

            let id = $('#primary_id').val();
            let url = '{{ route('pengajuan-hold.update', ':id') }}'.replace(':id', id);
            let method = 'PUT';

            let submitBtn = $('#submitBtn');
            let spinner = submitBtn.find('.spinner-border');
            let btnText = submitBtn.find('.button-text');

            spinner.removeClass('d-none');
            btnText.text('Menyimpan...');
            submitBtn.prop('disabled', true);

            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            // Pastikan mengambil nilai dari select2 yang benar
            let formData = {
                _token: $('meta[name="csrf-token"]').attr('content'),
                _method: method,
                tgl_registrasi: $('#tgl_registrasi').val(),
                nama_lengkap: $('#nama_lengkap').val(),
                nik: $('#nik').val(),
                nik_p: $('#nik_p').val(),
                tempat_lahir: $('#tempat_lahir').val(),
                tgl_lahir: $('#tgl_lahir').val(),
                jenis_kelamin: $('#jenis_kelamin').val(),
                no_telp_customer: $('#no_telp_customer').val(),
                email: $('#email').val(),
                npwp: $('#npwp').val(),
                no_bpjs_kes: $('#no_bpjs_kes').val(),
                booking_fee: $('#booking_fee').val().replace(/\./g, ''),
                alamat: $('#alamat').val(),
                pekerjaan: $('#pekerjaan').val(),
                status: $('#status').val(),
                nama_p: $('#nama_p').val(),
                nama_saudara: $('#nama_saudara').val(),
                no_telp_saudara: $('#no_telp_saudara').val(),
                id_lokasi: $('#id_lokasi').val(),
                id_kavling: $('#id_kavling').val(),
                hrg_jual: $('#hrg_jual_hidden').val(),
                id_marketing: $('#id_marketing').val(), // Pastikan ini ada
                id_freelance: $('#id_freelance').val(),
                jenis_perumahan: $('#jenis_perumahan').val(), // Pastikan ini ada
                jenis_pembelian: $('#jenis_pembelian').val(), // Pastikan ini ada
            };

            console.log('Data yang dikirim:', formData); // Debugging

            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                success: function() {
                    $('#modalForm').modal('hide');
                    audio.play();
                    toastr.success("Data telah disimpan!", "BERHASIL", {
                        progressBar: true,
                        timeOut: 3500,
                        positionClass: "toast-bottom-right",
                    });
                    table.ajax.reload();
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
                            let input = $('#' + key);
                            input.addClass('is-invalid');

                            // Hapus pesan error sebelumnya agar tidak dobel
                            input.parent().find('.invalid-feedback').remove();

                            input.parent().append(
                                '<span class="invalid-feedback" role="alert"><strong>' +
                                val[0] + '</strong></span>'
                            );
                        });

                        spinner.addClass('d-none');
                        btnText.text('Simpan');
                        submitBtn.prop('disabled', false);
                    }
                }
            });
        });

        function formatRupiah(angka) {
            if (!angka) return '';
            return angka.toString().replace(/\D/g, '')
                .replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        $('#status').on('change', function() {
            if ($(this).val() === 'Menikah') {
                $('#pasangan').show();
                $('#nama_p, #nik_p').prop('required', true);
            } else {
                $('#pasangan').hide();
                $('#nama_p, #nik_p').prop('required', false);
            }
        }).trigger('change');

        $(document).on('click', '.delete-button', function(e) {
            e.preventDefault();

            const form = $(this).closest('form');

            // Cek dulu kalau form-nya valid (punya tombol delete-button)
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

                                table.ajax.reload();
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
