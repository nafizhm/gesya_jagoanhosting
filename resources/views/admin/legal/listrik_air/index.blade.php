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
                                    <h3 class="font-weight-bold text-xl">Data Rekening Listrik & Air</h3>
                                    <div class="d-flex align-items-center" style="gap: 3px">
                                        @if ($permissions['tambah'])
                                            <button class="btn btn-info btn-sm" data-toggle="modal"
                                                data-target="#modalForm"><i class="fas fa-plus"></i>
                                                Tambah Data Rekening</button>
                                        @endif
                                        <button type="button" class="btn border px-3 py-1 btn-xs" onclick="reloadTable()">
                                            Reload
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <table id="table" class="table table-bordered table-striped data-table">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Lokasi Rumah</th>
                                            <th>No. Rek. Listrik</th>
                                            <th>No. Rek. Air</th>
                                            <th class="text-center" width="100px">Action</th>
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
    <div class="modal fade" id="modalForm" tabindex="-1" role="dialog"data-focus="false"
            aria-labelledby="modalFormLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title" id="modalFormLabel">Form Listrik & Air</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formData" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="primary_id" name="primary_id">
                    <div class="modal-body">
                        <div class="form-group row">
                            <label for="id_lokasi" class="col-sm-3 col-form-label">Lokasi Perumahan</label>
                            <div class="col-sm-4">
                                <select name="id_lokasi" id="id_lokasi" class="form-control select-lokasi">
                                    <option value=""></option>
                                    @foreach ($lokasi as $l)
                                        <option value="{{ $l->id_lokasi }}">{{ $l->nama_kavling }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="id_kavling" class="col-sm-3 col-form-label">Lokasi Blok/Kav</label>
                            <div class="col-sm-4">
                                <select name="id_kavling" id="id_kavling" class="form-control select-kavling"></select>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="nama" class="col-sm-3 col-form-label">No. Rekening Listrik</label>
                            <div class="col-sm-8">
                                <input type="text" id="norek_listrik" class="form-control" name="norek_listrik">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label" for="foto_listrik">Foto Meteran Listrik</label>
                            <div class="col-sm-8">
                                <input id="foto_listrik" name="foto_listrik" type="file" class="form-control-file">
                                <a href="#" target="_blank" id="link_foto_listrik" style="display: none;">Lihat file
                                    anda</a>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label" for="foto_listrik_2">Foto Meteran Listrik 2</label>
                            <div class="col-sm-8">
                                <input id="foto_listrik_2" name="foto_listrik_2" type="file" class="form-control-file">
                                <a href="#" target="_blank" id="link_foto_listrik_2" style="display: none;">Lihat file
                                    anda</a>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label" for="norek_air">No. Rekening Air</label>
                            <div class="col-sm-8">
                                <input id="norek_air" name="norek_air" type="text" class="form-control">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label" for="foto_air">Foto Meteran Air</label>
                            <div class="col-sm-8">
                                <input id="foto_air" name="foto_air" type="file" class="form-control-file">
                                <a href="#" target="_blank" id="link_foto_air" style="display: none;">Lihat file
                                    anda</a>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label" for="foto_air_2">Foto Meteran Air 2</label>
                            <div class="col-sm-8">
                                <input id="foto_air_2" name="foto_air_2" type="file" class="form-control-file">
                                <a href="#" target="_blank" id="link_foto_air_2" style="display: none;">Lihat file
                                    anda</a>
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
            $('.select-lokasi').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Lokasi",
            });
            $('.select-kavling').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Kavling",
            });
        });

        function reloadTable() {
            $('.data-table').DataTable().ajax.reload();
        }

        var audio = new Audio('{{ asset('audio/notification.ogg') }}');

        const routeGetKavling = "{{ route('customer.getKavling', ':id') }}";

        $('#id_lokasi').on('change', function() {
            let idLokasi = $(this).val();
            $('#id_kavling').html('<option value="">Loading...</option>').trigger('change');

            if (idLokasi) {
                const urlKavling = routeGetKavling.replace(':id', idLokasi);
                $.get(urlKavling, function(data) {
                    let options = '<option value=""></option>';
                    data.forEach(function(item) {
                        options +=
                            `<option value="${item.id_kavling}">${item.kode_kavling}</option>`;
                    });
                    $('#id_kavling').html(options).trigger('change');
                });
            }
        });

        $(function() {
            var permissions = @json($permissions);
            var showActionColumn = (permissions['edit'] == 1 || permissions['hapus'] == 1);

            var table = $('.data-table').DataTable({
                processing: false,
                serverSide: true,
                ordering: false,
                responsive: true,
                ajax: "{{ route('listrik-air.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'lokasi_rumah',
                        name: 'lokasi_rumah',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'norek_listrik',
                        name: 'norek_listrik',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'norek_air',
                        name: 'norek_air',
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

                    $('#primary_id').val(d.id);
                    $('#id_lokasi').val(d.id_lokasi).trigger('change');

                    setTimeout(function() {
                        $('#id_kavling').val(d.id_kavling).trigger('change');
                    }, 300);

                    $('#norek_listrik').val(d.norek_listrik);
                    $('#norek_air').val(d.norek_air);

                    // foto_listrik
                    if (response.foto_listrik_url) {
                        $('#link_foto_listrik')
                            .attr('href', response.foto_listrik_url)
                            .show();
                    } else {
                        $('#link_foto_listrik').hide();
                    }

                    // foto_listrik_2
                    if (response.foto_listrik_2_url) {
                        $('#link_foto_listrik_2')
                            .attr('href', response.foto_listrik_2_url)
                            .show();
                    } else {
                        $('#link_foto_listrik_2').hide();
                    }

                    // foto_air
                    if (response.foto_air_url) {
                        $('#link_foto_air')
                            .attr('href', response.foto_air_url)
                            .show();
                    } else {
                        $('#link_foto_air').hide();
                    }

                    // foto_air_2
                    if (response.foto_air_2_url) {
                        $('#link_foto_air_2')
                            .attr('href', response.foto_air_2_url)
                            .show();
                    } else {
                        $('#link_foto_air_2').hide();
                    }
                }
            });
        });


        $('#modalForm').on('hidden.bs.modal', function() {
            $('#formData')[0].reset();
            $('#primary_id').val('');
            $('.select-lokasi').val('').trigger('change');
            $('.select-kavling').val('').trigger('change');
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();
            $('#link_foto_listrik').hide().attr('href', '#');
            $('#link_foto_listrik_2').hide().attr('href', '#');
            $('#link_foto_air').hide().attr('href', '#');
            $('#link_foto_air_2').hide().attr('href', '#');

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
            let url = id ? '{{ route('listrik-air.update', ['listrik_air' => ':id']) }}'.replace(':id', id) :
                '{{ route('listrik-air.store') }}';
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
