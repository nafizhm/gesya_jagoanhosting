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
                            <h3 class="font-weight-bold text-xl">Data Freelance</h3>
                            <div class="d-flex align-items-center" style="gap: 8px;">
                                @if ($permissions['tambah'])
                                    <button type="button" class="btn btn-sm btn-info" data-toggle="modal"
                                        data-target="#modalTambah">
                                        <i class="fas fa-plus"></i> Tambah Freelance
                                    </button>
                                @endif
                                <button type="button" class="btn border px-3 py-1 btn-xs" onclick="reloadTable()">
                                    Reload
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table" class="table table-bordered table-striped data-table">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Nama Freelance</th>
                                        <th>Alamat</th>
                                        <th>Rekening</th>
                                        <th>Status</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Modal Tambah  -->
        <div class="modal fade" id="modalTambah" tabindex="-1" role="dialog" aria-labelledby="modalFormLabel"
            aria-hidden="true" data-backdrop="static" data-keyboard="false" data-focus="false">
            @csrf
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-success">
                        <h5 class="modal-title" id="modalTambahLabel">Form Freelance</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="form" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="primary_id" name="primary_id">
                        <div class="modal-body">
                            <div class="form-group row">
                                <label for="nama_freelance" class="col-sm-3 col-form-label">Nama Freelance</label>
                                <div class="col-sm-6">
                                    <input type="text" name="nama_freelance" id="nama_freelance" class="form-control">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="alamat" class="col-sm-3 col-form-label">Alamat</label>
                                <div class="col-sm-6">
                                    <input type="text" name="alamat" id="alamat" class="form-control">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="jenis_kelamin" class="col-sm-3 col-form-label">Jenis Kelamin</label>
                                <div class="col-sm-3">
                                    <select name="jenis_kelamin" id="jenis_kelamin" class="form-control select-jk">
                                        <option value="Laki-laki">Laki-Laki</option>
                                        <option value="Perempuan">Perempuan</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="pekerjaan" class="col-sm-3 col-form-label">Pekerjaan</label>
                                <div class="col-sm-6">
                                    <input type="text" name="pekerjaan" id="pekerjaan" class="form-control">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="no_telp_freelance" class="col-sm-3 col-form-label">No. Telp</label>
                                <div class="col-sm-6">
                                    <input type="number" name="no_telp_freelance" id="no_telp_freelance"
                                        class="form-control">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="foto" class="col-sm-3 col-form-label">Foto</label>
                                <div class="col-sm-6">
                                    <input type="file" name="foto" id="foto">
                                    <small id="error-foto" class="error-text text-danger"></small>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="status" class="col-sm-3 col-form-label">Status</label>
                                <div class="col-sm-3">
                                    <select name="status" id="status" class="form-control select-status">
                                        <option value="1">Aktif</option>
                                        <option value="0">Tidak Aktif</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="nama_bank" class="col-sm-3 col-form-label">Nama Bank</label>
                                <div class="col-sm-6">
                                    <input type="text" name="nama_bank" id="nama_bank" class="form-control">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="no_rekening" class="col-sm-3 col-form-label">No. Rekening</label>
                                <div class="col-sm-6">
                                    <input type="number" name="no_rekening" id="no_rekening" class="form-control">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="atas_nama" class="col-sm-3 col-form-label">Atas Nama</label>
                                <div class="col-sm-6">
                                    <input type="text" name="atas_nama" id="atas_nama" class="form-control">
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
        <!-- /.content -->
    </div>
    <div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" aria-labelledby="modalFormLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false" data-focus="false">
    </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script type="text/javascript">
        var audio = new Audio('{{ asset('audio/notification.ogg') }}');

        function reloadTable() {
            $('#table').DataTable().ajax.reload();
        }

        function modalAction(url = '') {
            $('#myModal').load(url, function() {
                $('#myModal').modal('show');
                $(document).ready(function() {
                    $('.select-jk').select2({
                        theme: 'bootstrap4',
                        placeholder: "Pilih Jenis Kelamin"
                    });
                    $('.select-status').select2({
                        theme: 'bootstrap4',
                        placeholder: "Pilih Status"
                    });
                });

                $(document).ready(function() {
                    $('#form-edit').on('submit', function(e) {
                        e.preventDefault();
                        $('.is-invalid').removeClass('is-invalid');
                        $('.invalid-feedback').remove();

                        let submitBtn = $('#submitBtn');
                        let spinner = submitBtn.find('.spinner-border');
                        let btnText = submitBtn.find('.button-text');

                        spinner.removeClass('d-none');
                        btnText.text('Menyimpan...');
                        submitBtn.prop('disabled', true);

                        let form = this;
                        let formData = new FormData(form);

                        $.ajax({
                            url: $(form).attr('action'),
                            type: $(form).attr('method'),
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                $('.error-text').text('');

                                if (response.status) {
                                    $('#myModal').modal('hide');
                                    audio.play();

                                    toastr.success(response.message, "Berhasil!", {
                                        closeButton: true,
                                        progressBar: true,
                                        timeOut: 3000,
                                        positionClass: 'toast-bottom-right'
                                    });

                                    $('#table').DataTable().ajax.reload(null, false);
                                } else {
                                    $.each(response.msgField, function(prefix, val) {
                                        $('#error-' + prefix).text(val[0]);
                                    });

                                    audio.play();
                                    toastr.error(response.message, "Gagal!", {
                                        closeButton: true,
                                        progressBar: true,
                                        timeOut: 3000,
                                        positionClass: 'toast-bottom-right'
                                    });

                                    spinner.addClass('d-none');
                                    btnText.text('Simpan');
                                    submitBtn.prop('disabled', false);
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
                                        let input = $('.' + key);
                                        input.addClass('is-invalid');
                                        input.parent().find('.invalid-feedback')
                                            .remove();
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
                });
            });
        }

        $(document).ready(function() {
            $('.select-jk').select2({
                theme: 'bootstrap4',
                placeholder: "Pilih Jenis Kelamin"
            });
            $('.select-status').select2({
                theme: 'bootstrap4',
                placeholder: "Pilih Status"
            });

            $('#modalTambah').on('hidden.bs.modal', function() {
                $('#form')[0].reset();
                $('#primary_id').val('');

                $('.select-jk').val('').trigger('change');
                $('.select-status').val('').trigger('change');

                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();

                let submitBtn = $('#submitBtn');
                let spinner = submitBtn.find('.spinner-border');
                let btnText = submitBtn.find('.button-text');

                spinner.addClass('d-none');
                btnText.text('Simpan');
                submitBtn.prop('disabled', false);
            });

            $('#form').on('submit', function(e) {
                e.preventDefault();

                let submitBtn = $('#submitBtn');
                let spinner = submitBtn.find('.spinner-border');
                let btnText = submitBtn.find('.button-text');

                spinner.removeClass('d-none');
                btnText.text('Menyimpan...');
                submitBtn.prop('disabled', true);

                let id = $('#primary_id').val();
                let url = id ? '{{ route('marketing-freelance.update', ['marketing_freelance' => ':id']) }}'.replace(':id',
                        id) :
                    '{{ route('marketing-freelance.store') }}';
                let method = id ? 'PUT' : 'POST';

                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();

                let formData = new FormData(this);
                formData.append('_method', method);

                $.ajax({
                    url: url,
                    method: method,
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function() {
                        $('#modalTambah').modal('hide');
                        audio.play();
                        toastr.success("Data telah disimpan!", "BERHASIL", {
                            progressBar: true,
                            timeOut: 3500,
                            positionClass: "toast-bottom-right",
                        });
                        reloadTable();
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
        });

        $(function() {
            var permissions = @json($permissions);
            var showActionColumn = (permissions['edit'] == 1 || permissions['hapus'] == 1);

            var table = $('#table').DataTable({
                processing: false,
                serverSide: true,
                responsive: true,
                ajax: "{{ route('marketing-freelance.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'nama_freelance',
                        name: 'nama_freelance',
                        width: "150px"
                    },
                    {
                        data: null,
                        name: 'alamat',
                        render: function(data, type, row) {
                            return row.alamat +
                                '<br> Telp: ' + row.no_telp_freelance;
                        },
                        width: "180px"
                    },
                    {
                        data: null,
                        name: 'rekening',
                        render: function(data, type, row) {
                            return row.nama_bank +
                                '<br>' + row.no_rekening +
                                '<br>' + row.atas_nama;
                        },
                        width: "160px"
                    },
                    {
                        data: 'status',
                        name: 'status',
                        render: function(data) {
                            if (data == 1) {
                                return '<span class="badge badge-success">Aktif</span>';
                            }
                            return '<span class="badge badge-secondary">Tidak Aktif</span>';
                        },
                        width: "90px"
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        visible: showActionColumn,
                        width: "120px"
                    }
                ],
                pageLength: 10,
            });
        });

        // Konfirmasi hapus
       $(document).on('click', '.delete-button', function(e) {
            e.preventDefault();

            const form = $(this).closest('form');

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

                        btnText.innerHTML =
                            `<span class="spinner-border spinner-border-sm mx-2" role="status" aria-hidden="true"></span> Menghapus...`;
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
