@extends('admin.layout_admin')
@section('content')
    <x-toast />

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
            </div>
        </section>
        <section class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header p-3">
                        <div class="d-flex align-content-center justify-content-between">
                            <h3 class="font-weight-bold text-xl">Data List Penjualan</h3>
                            <div class="d-flex align-items-center" style="gap: 8px;">
                                @if ($permissions['tambah'])
                                    <button type="button" class="btn btn-sm btn-info" data-toggle="modal"
                                        data-target="#modalform">
                                        <i class="fas fa-plus"></i> Tambah List Progres Penjualan
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
                                        <th>Kode</th>
                                        <th>Progres</th>
                                        <th>Keterangan</th>
                                        <th>Status</th>
                                        <th>Warna</th>
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
        <div class="modal fade" id="modalform" tabindex="-1" role="dialog" aria-labelledby="modalTambahLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-success">
                        <h5 class="modal-title" id="modalTambahLabel">Form List Penjualan</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="form" method="POST" action="{{ route('list-penjualan.store') }}">
                        @csrf
                        <input type="hidden" id="primary_id" name="primary_id">


                        <div class="modal-body">
                            <div class="form-group row">
                                <label for="status_progres" class="col-sm-4 col-form-label">Progres Penjualan</label>
                                <div class="col-sm-8">
                                    <input type="text" name="status_progres" id="status_progres" class="form-control">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="short_name" class="col-sm-4 col-form-label">Nama Singkat</label>
                                <div class="col-sm-8">
                                    <input type="text" name="short_name" id="short_name" class="form-control">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="keterangan" class="col-sm-4 col-form-label">Keterangan</label>
                                <div class="col-sm-8">
                                    <input type="text" name="keterangan" id="keterangan" class="form-control">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="urutan" class="col-sm-4 col-form-label">urutan</label>
                                <div class="col-sm-8">
                                    <input type="number" name="urutan" id="urutan" class="form-control">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="warna" class="col-sm-4 col-form-label">warna</label>
                                <div class="col-sm-8">
                                    <input type="color" name="warna" id="warna" class="form-control">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="stt_tampil" class="col-sm-4 col-form-label">Status Tampil</label>
                                <div class="col-sm-8">
                                    <select name="stt_tampil" id="stt_tampil" class="form-control stt_tampil">
                                        <option></option>
                                        <option value="1" {{ old('stt_tampil') == '1' ? 'selected' : '' }}>Aktif
                                        </option>
                                        <option value="0" {{ old('stt_tampil') == '0' ? 'selected' : '' }}>Tidak Aktif
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="modal-footer ">
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




        <!-- /.content -->
    </div>
    </div>

    <div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data-backdrop="static"
        data-keyboard="false" aria-hidden="true"></div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>



    <script type="text/javascript">
        function reloadTable() {
            $('#table').DataTable().ajax.reload();
        }

        function modalAction(url = '') {
            $('#myModal').load(url, function() {
                $('#myModal').modal('show');
            });
        }
        $(document).ready(function() {
            $('.stt_tampil').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Status",
                minimumResultsForSearch: Infinity,
            });

            $('#modalform').on('hidden.bs.modal', function() {
                $('#form')[0].reset();
                $('#primary_id').val('');
                $('#stt_tampil').val('').trigger('change');

                // Hapus validasi error
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

                let id = $('#primary_id').val();
                let url = id ? '{{ route('list-penjualan.update', ['list_penjualan' => ':id']) }}'
                    .replace(':id', id) :
                    '{{ route('list-penjualan.store') }}';
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
                        $('#modalform').modal('hide');
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
                ajax: "{{ route('list-penjualan.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'short_name',
                        name: 'short_name'
                    },
                    {
                        data: 'status_progres',
                        name: 'status_progres'
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan'
                    },
                    {
                        data: 'stt_tampil',
                        name: 'stt_tampil'
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
