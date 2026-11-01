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
                                    <h3 class="font-weight-bold text-xl">Data Notaris</h3>
                                    <div class="d-flex align-items-center" style="gap: 3px">
                                        @if (isset($permissions['tambah']) && $permissions['tambah'] == 1)
                                            <button class="btn btn-info btn-sm" data-toggle="modal"
                                                data-target="#modalForm"><i class="fas fa-plus"></i>
                                                Tambah Notaris</button>
                                        @endif

                                        <button type="button" class="btn border px-3 py-1 btn-xs" onclick="reloadTable()">
                                            Reload
                                        </button>

                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered table-striped data-table">
                                    <thead>
                                        <tr>
                                            <th width="5%">No</th>
                                            <th>Nama Notaris</th>
                                            <th>Alamat</th>
                                            <th>No Telp</th>
                                            <th>Keterangan</th>
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
                    <h5 class="modal-title" id="modalFormLabel">Form Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formData">
                    @csrf
                    <input type="hidden" id="primary_id" name="primary_id">
                    <div class="modal-body">
                        <div class="form-group row mb-3">
                            <label for="nama_notaris" class="col-sm-4 col-form-label">Nama Notaris</label>
                            <div class="col-sm-8">
                                <input type="text" id="nama_notaris" class="form-control" name="nama_notaris">
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="alamat_notaris" class="col-sm-4 col-form-label">Alamat</label>
                            <div class="col-sm-8">
                                <input type="text" id="alamat_notaris" class="form-control" name="alamat_notaris">
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="telp_notaris" class="col-sm-4 col-form-label">No Telp</label>
                            <div class="col-sm-8">
                                <input type="text" id="telp_notaris" class="form-control" name="telp_notaris">
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="keterangan_notaris" class="col-sm-4 col-form-label">Keterangan</label>
                            <div class="col-sm-8">
                                <input type="text" id="keterangan_notaris" class="form-control"
                                    name="keterangan_notaris">
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
    <script>
        function reloadTable() {
            $('#table').DataTable().ajax.reload();
        }

        var audio = new Audio('{{ asset('audio/notification.ogg') }}');

        var permissions = @json($permissions);
        var showActionColumn = (permissions['edit'] == 1 || permissions['hapus'] == 1);

        $(function() {
            var table = $('.data-table').DataTable({
                processing: false,
                serverSide: true,
                ordering: false,
                responsive: true,
                ajax: "{{ route('notaris.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'nama_notaris',
                        name: 'nama_notaris',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'alamat_notaris',
                        name: 'alamat_notaris',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'telp_notaris',
                        name: 'telp_notaris',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'keterangan_notaris',
                        name: 'keterangan_notaris',
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
                }, ]
            });
        });

        $(document).on('click', '[data-target="#modalForm"]', function() {
            $('#modalFormLabel').text('Tambah Data');
        });

        // Tombol edit
        $(document).on('click', '#edit-button', function() {
            var url = $(this).data('url');
            $.get(url, function(response) {
                if (response.status === 'success') {
                    $('#modalFormLabel').text('Edit Data');
                    $('#primary_id').val(response.data.id_notaris);
                    $('#nama_notaris').val(response.data.nama_notaris);
                    $('#alamat_notaris').val(response.data.alamat_notaris);
                    $('#telp_notaris').val(response.data.telp_notaris);
                    $('#keterangan_notaris').val(response.data.keterangan_notaris);
                }
            });
        });

        $('#modalForm').on('hidden.bs.modal', function() {
            $('#formData')[0].reset();
            $('#primary_id').val('');
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
            let url = id ? '{{ route('notaris.update', ['notari' => ':id']) }}'.replace(':id', id) :
                '{{ route('notaris.store') }}';
            let method = id ? 'PUT' : 'POST';

            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            let submitBtn = $('#submitBtn');
            let spinner = submitBtn.find('.spinner-border');
            let btnText = submitBtn.find('.button-text');

            spinner.removeClass('d-none');
            btnText.text('Menyimpan...');
            submitBtn.prop('disabled', true);

            let formData = new FormData(this); // Ambil semua data input dalam form
            formData.append('_method', method); // Laravel butuh method override via _method

            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                contentType: false, // Penting untuk FormData
                processData: false, // Penting untuk FormData
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
