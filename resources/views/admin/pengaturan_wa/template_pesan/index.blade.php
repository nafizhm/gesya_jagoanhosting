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
                            <h3 class="font-weight-bold text-xl">Data Template Pesan</h3>
                            <div class="d-flex align-items-center" style="gap: 8px;">
                                @if ($permissions['tambah'])
                                    <button type="button" class="btn btn-sm btn-info" data-toggle="modal"
                                        data-target="#modalTambahPesan">
                                        <i class="fas fa-plus"></i> Tambah Template
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
                                        <th>Nama Template</th>
                                        <th>Isi Pesan</th>
                                        <th>Jenis Pesan</th>
                                        <th width="15%" class="text-center">Action</th>
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

        <!-- Modal pengaturan pesan -->
        <div class="modal fade" id="modalTambahPesan" tabindex="-1" role="dialog" aria-labelledby="modalTambahLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form id="formTambahPesan">
                        @csrf
                        <div class="modal-header bg-success">
                            <h5 class="modal-title" id="modalTambahLabel">Form Template Pesan</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">
                            <div class="form-group row">
                                <label for="nama_template" class="col-sm-4 col-form-label">Nama Template</label>
                                <div class="col-sm-8">
                                    <input type="text" name="nama_template" id="nama_template" class="form-control">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="isi_template" class="col-sm-4 col-form-label">Isi Pesan</label>
                                <div class="col-sm-8">
                                    <textarea name="isi_template" id="isi_template" class="form-control" rows="4"></textarea>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="jenis_pesan" class="col-sm-4 col-form-label">Jenis Pesan</label>
                                <div class="col-sm-8">
                                    <input type="text" name="jenis_pesan" id="jenis_pesan" class="form-control">
                                </div>
                            </div>

                            <div class="modal-footer ">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary ms-1" id="submitBtn">
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
    </div>

    <div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data-backdrop="static"
        data-keyboard="false" aria-hidden="true"></div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

    <script type="text/javascript">
        var audio = new Audio('{{ asset('audio/notification.ogg') }}');

        function modalAction(url = '') {
            $('#myModal').load(url, function() {
                $('#myModal').modal('show');
            });
        }

        function reloadTable() {
            $('#table').DataTable().ajax.reload();
        }

        $(document).ready(function() {
            $('#modalTambahPesan').on('hidden.bs.modal', function() {
                $('#formTambahPesan')[0].reset();


                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();

                let submitBtn = $('#submitBtn');
                let spinner = submitBtn.find('.spinner-border');
                let btnText = submitBtn.find('.button-text');

                spinner.addClass('d-none');
                btnText.text('Simpan');
                submitBtn.prop('disabled', false);
            });

            $('#formTambahPesan').on('submit', function(e) {
                e.preventDefault();

                let formData = $(this).serialize();
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();

                let submitBtn = $('#submitBtn');
                let spinner = submitBtn.find('.spinner-border');
                let btnText = submitBtn.find('.button-text');

                spinner.removeClass('d-none');
                btnText.text('Menyimpan...');
                submitBtn.prop('disabled', true);

                $.ajax({
                    url: "{{ route('template-pesan.store') }}",
                    method: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#modalTambahPesan').modal('hide');
                            $('.data-table').DataTable().ajax.reload();

                            audio.play(); // play suara
                            toastr.success(response.message, "Berhasil!", {
                                closeButton: true,
                                progressBar: true,
                                timeOut: 3000,
                                positionClass: 'toast-bottom-right'
                            });
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

            var table = $('.data-table').DataTable({
                processing: false,
                serverSide: true,
                responsive: true,
                ajax: "{{ route('template-pesan.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'nama_template',
                        name: 'nama_template'
                    },
                    {
                        data: 'isi_template',
                        name: 'isi_template',
                        width: '40%'
                    },
                    {
                        data: 'jenis_pesan',
                        name: 'jenis_pesan'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        visible: showActionColumn
                    }
                ],
                pageLength: 25,
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
