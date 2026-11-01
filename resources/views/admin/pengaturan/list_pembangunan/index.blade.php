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
                            <h3 class="font-weight-bold text-xl" style="color: #343a40;">Data List Pembangunan</h3>
                            <div class="d-flex align-items-center" style="gap: 8px">
                                @if ($permissions['tambah'])
                                    <button type="button" class="btn btn-sm btn-info me-2" data-toggle="modal"
                                        onclick="modalAction('{{ route('list-pembangunan.create') }}')"
                                        data-target="#modalTambah">
                                        <i class="fas fa-plus"></i> Tambah List Progres Pembangunan
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
                                        <th>Nama Progres</th>
                                        <th>Warna</th>
                                        <th width="text-center" class="text-center">Action</th>
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


        <!-- /.content -->
    </div>



    <div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data-backdrop="static"
        data-keyboard="false" aria-hidden="true"></div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>



    <script type="text/javascript">
        function reloadTable() {
            $('#table').DataTable().ajax.reload();
        }



        function modalAction(url = '') {
            $('#myModal').load(url, function() {
                $('#myModal').modal('show');
            });
        }

        $(document).on('submit', '#formTambah', function(e) {
            e.preventDefault();

            var form = $(this);
            var url = form.attr('action');
            var data = form.serialize();

            $.ajax({
                url: url,
                method: 'POST',
                data: data,
                success: function(response) {
                    if (response.status) {
                        // Menampilkan toast sukses menggunakan toastr
                        toastr.success(response.message, 'Berhasil!', {
                            closeButton: true,
                            progressBar: true,
                            timeOut: 3000,
                            positionClass: 'toast-bottom-right',
                        });

                        // Menutup modal setelah sukses
                        $('#myModal').modal('hide');

                        // Memperbarui data di tabel
                        $('#table').DataTable().ajax.reload();
                    } else {
                        toastr.error('Terjadi kesalahan saat menyimpan data.', 'Kesalahan!', {
                            closeButton: true,
                            progressBar: true,
                            timeOut: 3000,
                            positionClass: 'toast-bottom-right',
                        });
                    }
                },
                error: function(xhr) {
                    toastr.error('Terjadi kesalahan saat menyimpan data.', 'Kesalahan!', {
                        closeButton: true,
                        progressBar: true,
                        timeOut: 3000,
                        positionClass: 'toast-bottom-right',
                    });
                }
            });
        });

        $(function() {
            var permissions = @json($permissions);
            var showActionColumn = (permissions['edit'] == 1 || permissions['hapus'] == 1);

            var table = $('.data-table').DataTable({
                processing: false,
                serverSide: true,
                responsive: true,
                ordering: false,
                ajax: "{{ route('list-pembangunan.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan'
                    },
                    {
                        data: 'warna_display',
                        name: 'warna_display'
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

