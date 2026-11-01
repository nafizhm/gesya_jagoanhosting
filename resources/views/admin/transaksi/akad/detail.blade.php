@extends('admin.layout_admin')
@section('content')
    <style>
        table th .form-check {
            margin: 0;
            padding: 0;
            height: 100%;
        }

        table th .form-check-input {
            margin: 0;
            transform: scale(1.1);
            cursor: pointer;
        }
    </style>
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
                                    <h3 class="font-weight-bold text-xl">Detail Akad</h3>

                                    <div class="d-flex align-items-center" style="gap: 3px">
                                        <a href="{{ route('akad.index') }}" class="btn btn-secondary btn-sm">
                                            Kembali
                                        </a>
                                        <button class="btn btn-primary btn-sm" id="seleksiButton">
                                            <i class="fas fa-plus"></i>
                                            Seleksi Customer
                                        </button>
                                        <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#modalForm"><i
                                                class="fas fa-plus"></i>
                                            Tambah Akad</button>

                                        <a href="{{ route('akad.detail.pdf', $data->id_akad) }}" target="_blank"
                                            class="btn btn-danger btn-sm">
                                            <i class="fas fa-file-pdf"></i> Cetak PDF
                                        </a>

                                        <a href="{{ route('akad.detail.excel', $data->id_akad) }}"
                                            class="btn btn-success btn-sm">
                                            <i class="fas fa-file-excel"></i> Cetak Excel
                                        </a>


                                        <button type="button" class="btn border px-3 py-1 btn-xs" onclick="reloadTable()">
                                            Reload
                                        </button>

                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="dataTableWrapper">
                                    <table class="table table-bordered table-striped data-table">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Lengkap</th>
                                                <th>Lokasi Rumah</th>
                                                <th>IPH</th>
                                                <th>SHGB</th>
                                                <th>Pajak</th>
                                                <th>Tanggal</th>
                                                <th>Keterangan</th>
                                                <th class="text-center" width="15%">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>

                                <div id="seleksiTableWrapper" class="d-none">
                                    <form id="formSeleksi">
                                        @csrf
                                        <table id="seleksi-table" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th class="text-center">
                                                        <div class="form-check m-6 p-0 d-flex justify-content-center align-items-center"
                                                            style="height: 100%;">
                                                            <input type="checkbox" id="checkAll" class="form-check-input">
                                                        </div>
                                                    </th>
                                                    <th>Nama</th>
                                                    <th>Kavling</th>
                                                    <th>IPH</th>
                                                    <th>SHGB</th>
                                                    <th>Pajak</th>
                                                </tr>
                                            </thead>
                                        </table>
                                        <div class="mt-3 text-right">
                                            <button type="button" class="btn btn-secondary btn-sm"
                                                id="batalSeleksi">Batal</button>
                                            <button type="submit" class="btn btn-primary btn-sm" id="submitSeleksi"><span
                                                    class="spinner-border spinner-border-sm me-2 d-none" role="status"
                                                    aria-hidden="true"></span>
                                                <span class="button-text">Simpan</span></button>
                                        </div>
                                    </form>
                                </div>
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
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalForm" tabindex="-1" role="dialog" data-focus="false" aria-labelledby="modalFormLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title" id="modalFormLabel">Form Akad</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formData">
                    @csrf
                    <input type="hidden" id="primary_id" name="primary_id">
                    <div class="modal-body">
                        <input type="hidden" name="id_persyaratan" id="id_persyaratan">


                        <div class="form-group row mb-3">
                            <label for="id_customer" class="col-sm-2 col-form-label">Customer</label>
                            <div class="col-sm-4">
                                <select name="id_customer" id="id_customer" class="form-control select-customer">
                                    <option value=""></option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id_customer }}">
                                            {{ $customer->nama_lengkap }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label class="col-sm-2 col-form-label">Alamat</label>
                            <div class="col-sm-6">
                                <input type="text" id="alamat" readonly class="form-control">
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label class="col-sm-2 col-form-label">No Telp</label>
                            <div class="col-sm-3">
                                <input type="text" id="no_wa" readonly class="form-control">
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label class="col-sm-2 col-form-label">Kavling</label>
                            <div class="col-sm-5">
                                <input type="text" id="nama_kavling" readonly class="form-control"
                                    placeholder="Lokasi Kavling">
                            </div>
                            <div class="col-sm-3">
                                <input type="text" id="kode_kavling" readonly class="form-control"
                                    placeholder="Kode Kavling">
                            </div>
                        </div>

                        <table class="table table-bordered mt-3" id="table-legal">
                            <thead>
                                <tr>
                                    <th>IPH</th>
                                    <th>SHGB</th>
                                    <th>Pajak</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td id="icon-iph" class="text-center">-</td>
                                    <td id="icon-shgb" class="text-center">-</td>
                                    <td id="icon-pajak" class="text-center">-</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary " id="submitBtn">
                            <span class="spinner-border spinner-border-sm me-2 d-none" role="status"
                                aria-hidden="true"></span>
                            <span class="button-text">Simpan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Keterangan Hadir -->
    <div class="modal fade" id="modalHadir" tabindex="-1" aria-labelledby="modalHadirLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalHadirLabel">Keterangan Kehadiran</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="hadirId">
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan (Opsional)</label>
                        <textarea id="keterangan" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary " id="btnSimpanHadir">
                        <span class="spinner-border spinner-border-sm me-2 d-none" role="status"
                            aria-hidden="true"></span>
                        <span class="button-text">Simpan</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $('.select-customer').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Customer",
            });
        });

        function reloadTable() {
            $('.data-table').DataTable().ajax.reload();
        }

        var audio = new Audio('{{ asset('audio/notification.ogg') }}');

        $(function() {
            var table = $('.data-table').DataTable({
                processing: false,
                serverSide: true,
                ordering: false,
                responsive: true,
                ajax: "{{ route('akad.detail.data', $data->id_akad) }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'id_customer',
                        name: 'id_customer',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'nama_kavling',
                        name: 'nama_kavling',
                    },
                    {
                        data: 'iph',
                        name: 'iph',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'shgb',
                        name: 'shgb',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'pajak',
                        name: 'pajak',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'tgl_masuk',
                        name: 'tgl_masuk',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                    }
                ],
                columnDefs: [{
                    targets: 0,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                }, ]
            });
        });

        var audio = new Audio('{{ asset('audio/notification.ogg') }}');

        $('#formData').on('submit', function(e) {
            e.preventDefault();
            let submitBtn = $('#submitBtn');
            let spinner = submitBtn.find('.spinner-border');
            let btnText = submitBtn.find('.button-text');

            spinner.removeClass('d-none');
            btnText.text('Menyimpan...');
            submitBtn.prop('disabled', true);

            const formData = $(this).serializeArray();
            formData.push({
                name: 'id_akad',
                value: '{{ $data->id_akad }}'
            });

            $.ajax({
                url: "{{ route('akad.detail.store') }}",
                method: 'POST',
                data: $.param(formData),
                success: function(res) {
                    if (res.status === 'success') {
                        $('#modalForm').modal('hide');
                        audio.play();
                        toastr.success(res.message ?? "Data telah disimpan!", "BERHASIL", {
                            progressBar: true,
                            timeOut: 3500,
                            positionClass: "toast-bottom-right",
                        });
                        $('.data-table').DataTable().ajax.reload();
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        audio.play();
                        toastr.error("Ada inputan yang salah!", "GAGAL!", {
                            progressBar: true,
                            timeOut: 3500,
                            positionClass: "toast-bottom-right",
                        });

                        $('.is-invalid').removeClass('is-invalid');
                        $('.invalid-feedback').remove();

                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, val) {
                            let input = $('#' + key);
                            input.addClass('is-invalid');
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

        $('#id_customer').on('change', function() {
            var id = $(this).val();

            if (id) {
                $.ajax({
                    url: '/admin/transaksi/akad/get-customer-detail/' + id,
                    type: 'GET',
                    success: function(res) {
                        if (res.status === 'success') {
                            $('#alamat').val(res.alamat);
                            $('#no_wa').val(res.no_wa);
                            $('#nama_kavling').val(res.nama_kavling);
                            $('#kode_kavling').val(res.kode_kavling);

                            $('#id_persyaratan').val(res.id_persyaratan);

                            const iconCheck = '<i class="fas fa-check-circle text-success"></i>';
                            const iconTimes = '<i class="fas fa-times-circle text-danger"></i>';

                            $('#icon-iph').html(res.iph == 1 ? iconCheck : iconTimes);
                            $('#icon-shgb').html(res.shgb == 1 ? iconCheck : iconTimes);
                            $('#icon-pajak').html(res.pajak == 1 ? iconCheck : iconTimes);
                        }
                    },
                    error: function() {
                        toastr.error('Gagal mengambil data customer');
                    }
                });
            } else {
                $('#alamat, #no_wa, #nama_kavling, #kode_kavling').val('');
                $('#icon-iph, #icon-shgb, #icon-pajak').html('-');
            }
        });

        $(document).on('click', '#edit-button', function() {
            var url = $(this).data('url');
            $.get(url, function(response) {
                if (response.status === 'success') {
                    $('#primary_id').val(response.data.id_akad);
                    $('#tgl_akad').val(response.data.tgl_akad);
                    $('#keterangan').val(response.data.keterangan);
                }
            });
        });

        $('#modalForm').on('hidden.bs.modal', function() {
            $('#formData')[0].reset();
            $('#id_customer').val(null).trigger('change');
            $('#alamat, #no_wa, #nama_kavling, #kode_kavling').val('');
            $('#icon-iph, #icon-shgb, #icon-pajak').html('-');

            let submitBtn = $('#submitBtn');
            let spinner = submitBtn.find('.spinner-border');
            let btnText = submitBtn.find('.button-text');

            spinner.addClass('d-none');
            btnText.text('Simpan');
            submitBtn.prop('disabled', false);
        });

        $('#seleksiButton').on('click', function() {
            $('#dataTableWrapper').addClass('d-none');
            $('#seleksiTableWrapper').removeClass('d-none');

            if (!$.fn.DataTable.isDataTable('#seleksi-table')) {
                $('#seleksi-table').DataTable({
                    ajax: '{{ route('akad.detail.seleksi', $data->id_akad) }}',
                    processing: false,
                    serverSide: true,
                    ordering: false,
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false,
                            className: 'text-center'
                        },
                        {
                            data: 'pilih',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'nama_lengkap'
                        },
                        {
                            data: 'nama_kavling'
                        },
                        {
                            data: 'iph'
                        },
                        {
                            data: 'shgb'
                        },
                        {
                            data: 'pajak'
                        },
                    ]
                });
            }
        });

        $('#batalSeleksi').on('click', function() {
            $('#seleksiTableWrapper').addClass('d-none');
            $('#dataTableWrapper').removeClass('d-none');
        });

        $(document).on('change', '#checkAll', function() {
            $('input[name="id_customer[]"]').prop('checked', this.checked);
        });

        $('#formSeleksi').on('submit', function(e) {
            e.preventDefault();

            let submitBtn = $('#submitSeleksi');
            let spinner = submitBtn.find('.spinner-border');
            let btnText = submitBtn.find('.button-text');

            spinner.removeClass('d-none');
            btnText.text('Menyimpan...');
            submitBtn.prop('disabled', true);

            const selected = $('input[name="id_customer[]"]:checked');
            if (selected.length === 0) {
                toastr.warning("Pilih minimal satu customer terlebih dahulu!", "PERINGATAN", {
                    progressBar: true,
                    timeOut: 3000,
                    positionClass: "toast-bottom-right"
                });

                spinner.addClass('d-none');
                btnText.text('Simpan');
                submitBtn.prop('disabled', false);
                return;
            }

            $.ajax({
                url: "{{ route('akad.detail.store') }}",
                method: 'POST',
                data: $(this).serialize() + '&id_akad={{ $data->id_akad }}',
                success: function(res) {
                    if (res.status === 'success') {
                        audio.play();
                        toastr.success(res.message ?? "Data telah disimpan!", "BERHASIL", {
                            progressBar: true,
                            timeOut: 3500,
                            positionClass: "toast-bottom-right",
                        });

                        // Reset form dan reload data seleksi
                        $('#formSeleksi')[0].reset();
                        $('#seleksi-table').DataTable().ajax.reload();

                        // Balik ke data utama
                        $('#seleksiTableWrapper').addClass('d-none');
                        $('#dataTableWrapper').removeClass('d-none');
                        reloadTable();
                    }
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
                            input.parent().append(
                                '<span class="invalid-feedback" role="alert"><strong>' +
                                val[0] + '</strong></span>'
                            );
                        });
                    }
                },
                complete: function() {
                    // Selalu reset tombol, sukses maupun error
                    spinner.addClass('d-none');
                    btnText.text('Simpan');
                    submitBtn.prop('disabled', false);
                }
            });
        });

        $(document).on('click', '.delete-button', function(e) {
            e.preventDefault();

            let url = $(this).data('url');

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
                    return new Promise((resolve, reject) => {
                        const confirmBtn = Swal.getConfirmButton();
                        const btnText = confirmBtn.querySelector('.swal-btn-text');

                        btnText.innerHTML =
                            `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Menghapus...`;
                        confirmBtn.disabled = true;

                        $.ajax({
                            url: url,
                            method: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
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
                                resolve();
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
                                reject();
                            }
                        });
                    });
                }
            });
        });

        $(document).on('click', '.hadir-button', function() {
            let id = $(this).data('id');
            $('#hadirId').val(id);
            $('#keterangan').val('');
            $('#modalHadir').modal('show');
        });

        $('#btnSimpanHadir').click(function(e) {
            e.preventDefault();

            let submitBtn = $('#btnSimpanHadir');
            let spinner = submitBtn.find('.spinner-border');
            let btnText = submitBtn.find('.button-text');

            spinner.removeClass('d-none');
            btnText.text('Menyimpan...');
            submitBtn.prop('disabled', true);

            let id = $('#hadirId').val();
            let keterangan = $('#keterangan').val();

            $.ajax({
                url: `{{ route('akad.detail.hadir', ':id') }}`.replace(':id', id),
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    keterangan: keterangan
                },
                success: function(res) {
                    if (res.status === 'success') {
                        $('#modalHadir').modal('hide');
                        audio.play();
                        toastr.success(res.message, "SUKSES", {
                            progressBar: true,
                            timeOut: 3500,
                            positionClass: "toast-bottom-right",
                        });
                        $('.data-table').DataTable().ajax.reload();
                    }

                    spinner.addClass('d-none');
                    btnText.text('Simpan');
                    submitBtn.prop('disabled', false);
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        audio.play();
                        toastr.error("Gagal menyimpan data.", "ERROR", {
                            progressBar: true,
                            timeOut: 3500,
                            positionClass: "toast-bottom-right",
                        });

                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, val) {
                            let input = $('#' + key);
                            input.addClass('is-invalid');
                            input.parent().find('.invalid-feedback').remove();
                            input.parent().append(
                                '<span class="invalid-feedback" role="alert"><strong>' +
                                val[0] + '</strong></span>'
                            );
                        });
                    } else {
                        audio.play();
                        toastr.error('Terjadi Kesalahan Pada Server!', "Terjadi Kesalahan!", {
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
    </script>
@endpush
