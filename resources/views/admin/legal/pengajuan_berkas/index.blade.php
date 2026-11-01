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
                                    <h3 class="font-weight-bold text-xl">Data Kelengkapan Berkas</h3>
                                    <div class="d-flex align-items-center" style="gap: 3px">
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
                                            <th width="4%">No</th>
                                            <th width="25%">Nama Customer</th>
                                            <th width="25%">Lokasi</th>
                                            <th width="7%">IPH</th>
                                            <th width="7%">SHGB</th>
                                            <th width="7%">PAJAK</th>
                                            <th width="40%">Catatan</th>
                                            <th class="text-center" width="10%">Action</th>
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
                    <h5 class="modal-title" id="modalFormLabel">Form Kelengkapan Berkas</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formData" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="primary_id" name="primary_id">
                    <div class="modal-body">
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Nama Customer</label>
                            <div class="col-sm-8">
                                <input name="id_customer" type="text" class="form-control" id="id_customer" readonly>

                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="IPH" class="col-sm-3 col-form-label">IPH</label>
                            <div class="col-sm-8">
                                <select name="IPH" id="IPH" class="form-control select-iph">
                                    <option value=""></option>
                                    <option value="0">Belum Ada</option>
                                    <option value="1">Ada</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="SHGB" class="col-sm-3 col-form-label">SHGB</label>
                            <div class="col-sm-8">
                                <select name="SHGB" id="SHGB" class="form-control select-shgb">
                                    <option value=""></option>
                                    <option value="0">Belum Ada</option>
                                    <option value="1">Ada</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="pajak" class="col-sm-3 col-form-label">Pajak</label>
                            <div class="col-sm-8">
                                <select name="pajak" id="pajak" class="form-control select-pajak">
                                    <option value=""></option>
                                    <option value="0">Belum Ada</option>
                                    <option value="1">Ada</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Percakapan WA</label>
                            <div class="col-md-8">
                                <input id="percakapan_wa" name="percakapan_wa" type="file" class="form-control-file">
                                <a href="#" target="_blank" id="link_percakapan_wa" style="display: none;">Lihat file
                                    anda</a>
                            </div>
                        </div>


                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Catatan Berkas</label>
                            <div class="col-md-8">
                                <textarea name="catatan_kekurangan" class="form-control" id="catatan_kekurangan" rows="3"></textarea>
                            </div>
                        </div>

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
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $('.select-iph').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Status IPH",
                minimumResultsForSearch: Infinity,
            });
            $('.select-shgb').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Status SHGB",
                minimumResultsForSearch: Infinity,
            });
            $('.select-pajak').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Status Pajak",
                minimumResultsForSearch: Infinity,
            });
        });

        function reloadTable() {
            $('.data-table').DataTable().ajax.reload();
        }

        var audio = new Audio('{{ asset('audio/notification.ogg') }}');

        $(function() {
            var permissions = @json($permissions);
            var showActionColumn = (permissions['edit'] == 1 || permissions['hapus'] == 1);

            var table = $('.data-table').DataTable({
                processing: false,
                serverSide: true,
                ordering: false,
                responsive: true,
                ajax: "{{ route('pengajuan-berkas.index') }}",
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
                        data: 'lokasi',
                        name: 'lokasi',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'IPH',
                        name: 'IPH',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'SHGB',
                        name: 'SHGB',
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
                        data: 'catatan_kekurangan',
                        name: 'catatan_kekurangan',
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

        // Tombol edit
        $(document).on('click', '#edit-button', function() {
            var url = $(this).data('url');
            $.get(url, function(response) {
                if (response.status === 'success') {
                    const d = response.data;
                    const nama = response.nama_customer;

                    $('#primary_id').val(d.id);
                    $('#id_customer').val(nama);
                    $('#IPH').val(d.IPH).trigger('change');
                    $('#SHGB').val(d.SHGB).trigger('change');
                    $('#pajak').val(d.pajak).trigger('change');
                    $('#catatan_kekurangan').val(d.catatan_kekurangan);

                    if (response.percakapan_wa_url) {
                        $('#link_percakapan_wa')
                            .attr('href', response.percakapan_wa_url)
                            .show();
                    } else {
                        $('#link_percakapan_wa').hide();
                    }
                }
            });
        });


        $('#modalForm').on('hidden.bs.modal', function() {
            $('#formData')[0].reset();
            $('#primary_id').val('');
            $('.select-iph').val('').trigger('change');
            $('.select-shgb').val('').trigger('change');
            $('.select-pajak').val('').trigger('change');
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();
            $('#link_percakapan_wa').hide().attr('href', '#');

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
            let url = id ? '{{ route('pengajuan-berkas.update', ['pengajuan_berka' => ':id']) }}'.replace(':id',
                id) :
                '{{ route('pengajuan-berkas.store') }}';
            let method = id ? 'PUT' : 'POST';

            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

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
                        spinner.addClass('d-none');
                        btnText.text('Simpan');
                        submitBtn.prop('disabled', false);
                    }
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
