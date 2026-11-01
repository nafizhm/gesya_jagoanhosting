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
                                    <h3 class="font-weight-bold text-xl">Data Acc Bank</h3>
                                    <div class="d-flex align-items-center" style="gap: 3px">
                                        <a href="{{ route('acc-bank.arsip') }}" class="btn btn-dark btn-sm">Arsip SP3K</a>

                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered w-100 small table-striped data-table">
                                    <thead>
                                        <tr>
                                            <th width="3%">No</th>
                                            <th>Nama Customer</th>
                                            <th>Lokasi Rumah</th>
                                            <th width="10%">Bank</th>
                                            <th>Harga Jual</th>
                                            <th>ACC Plafon</th>
                                            <th>Tgl Wawancara</th>
                                            <th>Tgl Expired</th>
                                            <th>Status</th>
                                            <th class="text-center" width="17%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
@push('scripts')
    <script>
        function reloadTable() {
            $('#table').DataTable().ajax.reload();
        }

        var audio = new Audio('{{ asset('audio/notification.ogg') }}');

        $(function() {
            var table = $('.data-table').DataTable({
                processing: false,
                serverSide: true,
                ordering: false,
                responsive: true,
                ajax: "{{ route('acc-bank.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'wawancara.nasabah.nama_lengkap',
                        name: 'wawancara.nasabah.nama_lengkap',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'kode_kavling',
                        name: 'kode_kavling',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'bank',
                        name: 'bank',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'harga_jual',
                        name: 'harga_jual',
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
                        data: 'tgl_terbit_sp3k',
                        name: 'tgl_terbit_sp3k',
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
                        data: 'sisa_hari',
                        name: 'sisa_hari',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
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

        $(document).on('click', '.arsip-button', function(e) {
            e.preventDefault();

            const form = $(this).closest('form');

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Data ini akan diarsipkan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '<span class="swal-btn-text">Ya, Arsipkan</span>',
                cancelButtonText: 'Batal',
                showLoaderOnConfirm: false,
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'btn btn-primary mx-2',
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
                                toastr.success("Data telah diarsipkan!", "BERHASIL", {
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

                                btnText.innerHTML = `Ya, Arsipkan`;
                                confirmBtn.disabled = false;
                            }
                        });
                    });
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
