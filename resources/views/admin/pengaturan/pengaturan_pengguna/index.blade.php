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
                                    <h3 class="font-weight-bold text-xl">Pengaturan Pengguna</h3>
                                    <div class="d-flex align-items-center">
                                        @if (isset($permissions['tambah']) && $permissions['tambah'] == 1)
                                            <button class="btn btn-info btn-sm" data-toggle="modal"
                                                data-target="#modalForm"><i class="fas fa-plus"></i>
                                                Tambah Pengguna</button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered table-striped data-table">
                                    <thead>
                                        <tr>
                                            <th width="5%">No</th>
                                            <th>Nama Lengkap</th>
                                            <th>Username</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th class="text-center" width="15%">Action</th>
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
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalForm" tabindex="-1" role="dialog" aria-labelledby="modalFormLabel" aria-hidden="true"
        data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title" id="modalFormLabel">Form Pengguna</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formData">
                    @csrf
                    <input type="hidden" id="primary_id" name="primary_id">
                    <div class="modal-body">
                        <!-- Nama Lengkap Field -->
                        <div class="form-group row mb-3">
                            <label for="nama_lengkap" class="col-sm-3 col-form-label">Nama Lengkap</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="surname" name="surname">
                            </div>
                        </div>

                        <!-- Username Field -->
                        <div class="form-group row mb-3">
                            <label for="username" class="col-sm-3 col-form-label">Username</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="username" name="username">
                            </div>
                        </div>

                        <!-- Email Field -->
                        <div class="form-group row mb-3">
                            <label for="email" class="col-sm-3 col-form-label">Email</label>
                            <div class="col-sm-9">
                                <input type="email" class="form-control" id="email" name="email">
                            </div>
                        </div>

                        <!-- Password Field -->
                        <div class="form-group row mb-3">
                            <label for="password" class="col-sm-3 col-form-label">Password</label>
                            <div class="col-sm-9">
                                <input type="password" class="form-control" id="password" name="password">
                            </div>
                        </div>

                        <!-- Status Field -->
                        <div class="form-group row mb-3">
                            <label for="status" class="col-sm-3 col-form-label">Status</label>
                            <div class="col-sm-9">
                                <select name="status" id="status" class="form-select select-status">
                                    <option value=""></option>
                                    <option value="AKTIF">AKTIF</option>
                                    <option value="BLOKIR">BLOKIR</option>
                                </select>
                            </div>
                        </div>

                        <!-- Role Field -->
                        <div class="form-group row mb-3">
                            <label for="role" class="col-sm-3 col-form-label">Role</label>
                            <div class="col-sm-9">
                                <select name="role" id="role" class="form-select select-role">
                                    <option value=""></option>
                                    @foreach ($roles as $r)
                                        <option value="{{ $r->id }}">{{ $r->level_user }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
                        <button type="submit" id="submitBtn" class="btn btn-primary">
                            <span class="spinner-border spinner-border-sm mx-1 d-none" role="status"
                                aria-hidden="true"></span>
                            <span class="button-text">Simpan</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script></script>
    <script>
        $(document).ready(function() {
            $('.select-status').select2({
                theme: 'bootstrap4',
                placeholder: 'Pilih Status',
                minimumResultsForSearch: Infinity,
            });
            $('.select-role').select2({
                theme: 'bootstrap4',
                placeholder: 'Pilih Role',
            });
        });

        var permissions = @json($permissions);
        var showActionColumn = (permissions['edit'] == 1 || permissions['hapus'] == 1);
        var audio = new Audio('{{ asset('audio/notification.ogg') }}');

        $(function() {
            var table = $('.data-table').DataTable({
                processing: false,
                serverSide: true,
                ordering: false,
                responsive: true,
                ajax: "{{ route('pengaturan-pengguna.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'surname',
                        name: 'surname',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'username',
                        name: 'username',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'email',
                        name: 'email',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'role',
                        name: 'role',
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

        // Tombol edit
        $(document).on('click', '#edit-button', function() {
            var url = $(this).data('url');
            $.get(url, function(response) {
                if (response.status === 'success') {
                    $('#modalFormLabel').text('Edit Data');
                    $('#primary_id').val(response.data.id);
                    $('#surname').val(response.data.surname);
                    $('#username').val(response.data.username);
                    $('#role').val(response.data.role).trigger('change');
                    $('#email').val(response.data.email);
                    $('#status').val(response.data.status).trigger('change');
                }
            });
        });

        $('#modalForm').on('hidden.bs.modal', function() {
            $('#formData')[0].reset();
            $('#primary_id').val('');
            $('#status').val('').trigger('change');
            $('#role').val('').trigger('change');
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

            let id = $('#primary_id').val();
            let url = id ? '{{ route('pengaturan-pengguna.update', ['pengaturan_pengguna' => ':id']) }}'.replace(':id', id) :
                '{{ route('pengaturan-pengguna.store') }}';
            let method = id ? 'PUT' : 'POST';

            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            let submitBtn = $('#submitBtn');
            let spinner = submitBtn.find('.spinner-border');
            let btnText = submitBtn.find('.button-text');

            spinner.removeClass('d-none');
            btnText.text('Menyimpan...');
            submitBtn.prop('disabled', true);

            let formData = new FormData(this);
            formData.append('_method', method);

            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
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
                            let input = $('#' + key);
                            input.addClass('is-invalid');
                            input.parent().find('.invalid-feedback').remove();
                            input.parent().append(
                                '<span class="invalid-feedback" role="alert"><strong>' +
                                val[0] + '</strong></span>'
                            );
                        });
                    }

                    spinner.addClass('d-none');
                    btnText.text('Simpan');
                    submitBtn.prop('disabled', false);
                }
            });
        });

        // Hapus data
        $(document).on('submit', 'form', function(e) {
            if ($(this).has('button.delete-button').length) {
                e.preventDefault();

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function() {
                        audio.play();
                        toastr.success("Data telah dihapus!", "BERHASIL", {
                            progressBar: true,
                            timeOut: 3500,
                            positionClass: "toast-bottom-right",
                        });
                        $('.data-table').DataTable().ajax.reload();
                    },
                    error: function() {
                        audio.play();
                        toastr.error("Gagal menghapus data.", "GAGAL!", {
                            progressBar: true,
                            timeOut: 3500,
                            positionClass: "toast-bottom-right",
                        });
                    }
                });
            }
        });

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
@endpush
