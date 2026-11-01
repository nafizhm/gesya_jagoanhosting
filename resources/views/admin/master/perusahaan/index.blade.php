@extends('admin.layout_admin')
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header p-3">
                                <div class="d-flex align-content-center justify-content-between">
                                    <h3 class="font-weight-bold text-lg">Data Perusahaan</h3>
                                    <div class="d-flex align-items-center">
                                        @if ($permissions['tambah'])
                                            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal"
                                                data-target="#modalForm">
                                                <i class="fas fa-plus"></i> Tambah Perusahaan
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered table-striped data-table w-100">
                                    <thead>
                                        <tr>
                                            <th width="30px">No</th>
                                            <th>Nama Perusahaan</th>
                                            <th>Alamat</th>
                                            <th>Kawasan</th>
                                            <th>Telepon</th>
                                            <th>Kota</th>
                                            <th width="100px">Action</th>
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

    <div class="modal fade" id="modalForm" tabindex="-1" role="dialog" data-focus="false" aria-labelledby="modalFormLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title text-white font-weight-bold" id="modalFormLabel"></h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formData" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="primary_id" name="primary_id">
                    <div class="modal-body">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Nama Perusahaan</label>
                            <div class="col-sm-8">
                                <input type="text" name="nama_perusahaan" id="nama_perusahaan" class="form-control">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Alamat Perusahaan</label>
                            <div class="col-sm-8">
                                <input type="text" name="alamat_perusahaan" id="alamat_perusahaan" class="form-control">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Kawasan</label>
                            <div class="col-sm-8">
                                <input type="text" name="kawasan" id="kawasan" class="form-control">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Telepon</label>
                            <div class="col-sm-8">
                                <input type="text" name="telp_perusahaan" id="telp_perusahaan" class="form-control">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Kota Penandatangan</label>
                            <div class="col-sm-8">
                                <input type="text" name="kota_penandatangan" id="kota_penandatangan"
                                    class="form-control">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Nama Penandatangan</label>
                            <div class="col-sm-8">
                                <input type="text" name="nama_penandatangan" id="nama_penandatangan"
                                    class="form-control">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Jabatan Penandatangan</label>
                            <div class="col-sm-8">
                                <input type="text" name="jabatan_penandatangan" id="jabatan_penandatangan"
                                    class="form-control">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Nama Mengetahui</label>
                            <div class="col-sm-8">
                                <input type="text" name="nama_mengetahui" id="nama_mengetahui" class="form-control">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Kwitansi</label>
                            <div class="col-sm-8">
                                <input type="file" name="bg_kwitansi" id="bg_kwitansi"
                                    accept=".jpg, .jpeg, .png, .pdf">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label"></label>
                            <div class="col-sm-8">
                                <div class="img-thumbnail mb-2 d-flex align-items-center justify-content-center"
                                    id="previewKwitansi"
                                    style="max-width: 140px; height: 150px; background-color: #f8f9fa; border: 1px solid #dee2e6; overflow: hidden;">
                                    <span style="color: #6c757d;">Tidak ada Kwitansi</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Kop Surat</label>
                            <div class="col-sm-8">
                                <input type="file" name="kop_surat" id="kop_surat" accept=".jpg, .jpeg, .png, .pdf">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label"></label>
                            <div class="col-sm-8">
                                <div class="img-thumbnail mb-2 d-flex align-items-center justify-content-center"
                                    id="previewKopSurat"
                                    style="max-width: 140px; height: 150px; background-color: #f8f9fa; border: 1px solid #dee2e6; overflow: hidden;">
                                    <span style="color: #6c757d;">Tidak ada Kop Surat</span>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
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
@endsection
@push('scripts')
    <script>
        var audio = new Audio('{{ asset('audio/notification.ogg') }}');

        var permissions = @json($permissions);
        var showActionColumn = (permissions['edit'] == 1 || permissions['hapus'] == 1);

        $(function() {
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ordering: false,
                ajax: "{{ route('perusahaan.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'nama_perusahaan',
                        name: 'nama_perusahaan',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'alamat_perusahaan',
                        name: 'alamat_perusahaan',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'kawasan',
                        name: 'kawasan',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'telp_perusahaan',
                        name: 'telp_perusahaan',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'kota_penandatangan',
                        name: 'kota_penandatangan',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
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
            $('#modalFormLabel').text('Tambah Perusahaan');
        });

        $(document).on('click', '.edit-button', function() {
            var url = $(this).data('url');

            $.get(url, function(response) {
                if (response.status === 'success') {
                    $('#modalFormLabel').text('Edit Perusahaan');
                    $('#primary_id').val(response.data.id_perusahaan);
                    $('#nama_perusahaan').val(response.data.nama_perusahaan);
                    $('#alamat_perusahaan').val(response.data.alamat_perusahaan);
                    $('#kawasan').val(response.data.kawasan);
                    $('#telp_perusahaan').val(response.data.telp_perusahaan);
                    $('#kota_penandatangan').val(response.data.kota_penandatangan);
                    $('#nama_penandatangan').val(response.data.nama_penandatangan);
                    $('#jabatan_penandatangan').val(response.data.jabatan_penandatangan);
                    $('#nama_mengetahui').val(response.data.nama_mengetahui);
                    $('#modalForm').modal('show');
                }
            });
        });

        $('#kop_surat').on('change', function() {
            const file = this.files[0];
            const previewDiv = $('#previewKopSurat');

            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewDiv.html(
                        `<img src="${e.target.result}" style="max-width: 100%; max-height: 100%;">`);
                };
                reader.readAsDataURL(file);
            } else {
                previewDiv.html('<span style="color: #6c757d;">Tidak ada Kop Surat</span>');
            }
        });

        $('#bg_kwitansi').on('change', function() {
            const file = this.files[0];
            const previewDiv = $('#previewKwitansi');

            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewDiv.html(
                        `<img src="${e.target.result}" style="max-width: 100%; max-height: 100%;">`);
                };
                reader.readAsDataURL(file);
            } else {
                previewDiv.html('<span style="color: #6c757d;">Tidak ada Kwitansi</span>');
            }
        });

        $('#modalForm').on('hidden.bs.modal', function() {
            $('#formData')[0].reset();
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();
            $('#previewKwitansi').html('<span style="color: #6c757d;">Tidak ada Kwitansi</span>');
            $('#previewKopSurat').html('<span style="color: #6c757d;">Tidak ada Kop Surat</span>');

            let submitBtn = $('#submitBtn');
            let spinner = submitBtn.find('.spinner-border');
            let btnText = submitBtn.find('.button-text');

            spinner.addClass('d-none');
            btnText.text('Simpan');
            submitBtn.prop('disabled', false);
        });

        $('#formData').on('submit', function(e) {
            e.preventDefault();

            let submitBtn = $('#submitBtn');
            let spinner = submitBtn.find('.spinner-border');
            let btnText = submitBtn.find('.button-text');

            spinner.removeClass('d-none');
            btnText.text('Menyimpan...');
            submitBtn.prop('disabled', true);

            let id = $('#primary_id').val();
            let url = id ? '{{ route('perusahaan.update', ['perusahaan' => ':id']) }}'.replace(':id',
                    id) :
                '{{ route('perusahaan.store') }}';
            let method = id ? 'PUT' : 'POST';

            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

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
                    let msg = id ? "Perusahaan berhasil diupdate!" : "Perusahaan berhasil ditambahkan!";
                    toastr.success(msg, "BERHASIL", {
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
