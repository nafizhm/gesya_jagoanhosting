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
                                    <h3 class="font-weight-bold text-xl">Upload Data Lampiran {{ $customer->nama_lengkap }}
                                    </h3>
                                    <div class="d-flex align-items-center" style="gap: 3px">
                                        <button type="button" class="btn btn-sm btn-success" data-toggle="modal"
                                            data-target="#modalForm">
                                            <i class="fas fa-plus"></i> Tambah Lampiran
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered table-striped data-table">
                                    <thead>
                                        <tr>
                                            <th width="50">No</th>
                                            <th>Nama File</th>
                                            <th>File Lampiran</th>
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
            </div>
        </section>
    </div>

    <div class="modal fade" id="modalForm" tabIndex={-1} role="dialog" data-focus="false" aria-labelledby="modalFormLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title text-white font-weight-bold" id="modalFormLabel"></h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formData" encType="multipart/form-data">
                    @csrf
                    <input type="hidden" id="primary_id" name="primary_id" />
                    <input type="hidden" id="nasabah" name="nasabah" value="{{ $customer->id_customer }}" />
                    <div class="modal-body">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Tanggal Upload</label>
                            <div class="col-sm-8">
                                <input type="date" class="form-control" id="tanggal" name="tanggal"
                                    value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Nama File</label>
                            <div class="col-sm-8">
                                <select name="nama_file" id="nama_file" class="form-control select-nama-file">
                                    <option value="">-- Pilih Nama File --</option>
                                    <option value="KTP">Foto KTP</option>
                                    <option value="KK">Foto Kartu Keluarga</option>
                                    <option value="Pemohon">Foto Pemohon</option>
                                    <option value="KTP Pasangan">Foto KTP Pasangan</option>
                                    <option value="NPWP">Foto NPWP</option>
                                    <option value="BPJS">Foto BPJS</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Image</label>
                            <div class="col-sm-8">
                                <div class="image-preview-container"
                                    style="width: 200px; height: 200px; border: 1px dashed #ccc; position: relative; cursor: pointer; overflow: hidden;">
                                    <div class="no-image-placeholder"
                                        style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background-color: #f8f9fa;">
                                        <i class="fas fa-image" style="font-size: 24px; color: #6c757d;"></i>
                                    </div>

                                    <img id="image-preview" src="" alt="Preview"
                                        style="width: 100%; height: 100%; object-fit: cover; display: none;" />

                                    <button type="button" id="remove-image-btn" class="btn btn-danger btn-xs"
                                        style="position: absolute; top: 2px; right: 2px; display: none;">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <input type="file" name="lampiran" id="lampiran" accept=".jpg, .jpeg, .png"
                                    style="display: none;" />
                                <input type="hidden" name="remove_image" id="remove_image" value="0" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Description</label>
                            <div class="col-sm-8">
                                <textarea class="form-control" name="keterangan"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary ms-1" id="submitBtn">
                            <span class="spinner-border spinner-border-sm mx-1 d-none" role="status"
                                aria-hidden="true"></span>
                            <span class="button-text">Save</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        var customerId = {{ $customer->id_customer }};

        $(document).ready(function() {
            $('.select-nama-file').select2({
                theme: "bootstrap4",
                minimumResultsForSearch: Infinity,
                placeholder: "Pilih Nama File",
            });
        });

        var audio = new Audio('{{ asset('audio/notification.ogg') }}');

        $(function() {
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ordering: false,
                ajax: "{{ route('rekap-lampiran.show', $customer->id_customer) }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'lampiran',
                        name: 'lampiran',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'nama_file',
                        name: 'nama_file',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ],
                columnDefs: [{
                    targets: 0,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                }]
            });

            $(document).on('click', '.view-file-btn', function() {
                var filename = $(this).data('filename');
                var namaFile = $(this).data('nama-file');

                viewFile(filename, namaFile);
            });
        });

        function viewFile(filename, keterangan) {
            var fileExt = filename.split('.').pop().toLowerCase();
            var fileUrl = '/berkas_user/' + filename;

            const isImage = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'].includes(fileExt);
            const isPDF = fileExt === 'pdf';

            if (!isImage && !isPDF) {
                Swal.fire('Info', 'Hanya file gambar dan PDF yang didukung', 'info');
                return;
            }

            Swal.fire({
                title: isPDF ? 'File PDF' : keterangan,
                html: isPDF ? `
                    <div class="text-center">
                        <i class="fas fa-file-pdf fa-4x text-danger mb-3"></i>
                        <p>${keterangan}</p>
                        <a href="${fileUrl}" target="_blank" class="btn btn-danger btn-sm">
                            Buka PDF
                        </a>
                    </div>
                ` : `<img src="${fileUrl}" class="img-fluid" style="max-height: 300px;">`,
                width: isPDF ? '400px' : 'auto',
                showConfirmButton: false,
                showCancelButton: true,
                cancelButtonText: 'Tutup'
            });
        }

        $(document).on('click', '.image-preview-container', function() {
            $('#lampiran').click();
        });

        $('#lampiran').change(function(e) {
            var input = this;
            var preview = $('#image-preview');
            var placeholder = $('.no-image-placeholder');
            var removeBtn = $('#remove-image-btn');

            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    preview.attr('src', e.target.result);
                    preview.show();
                    placeholder.hide();
                    removeBtn.show();
                    $('#remove_image').val(0);
                }

                reader.readAsDataURL(input.files[0]);
            }
        });

        $('#remove-image-btn').click(function(e) {
            e.stopPropagation();
            $('#image-preview').attr('src', '').hide();
            $('.no-image-placeholder').show();
            $('#lampiran').val('');
            $('#remove_image').val(1);
            $(this).hide();
        });

        $(document).on('click', '[data-target="#modalForm"]', function() {
            $('#modalFormLabel').text('Tambah Data Lampiran');
        });

        $('#modalForm').on('hidden.bs.modal', function() {
            $('#formData')[0].reset();
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();
            $('#is_active').val('').trigger('change');
            $('#image-preview').attr('src', '').hide();
            $('.no-image-placeholder').show();
            $('#remove-image-btn').hide();
            $('#remove_image').val(0);

            let submitBtn = $('#submitBtn');
            let spinner = submitBtn.find('.spinner-border');
            let btnText = submitBtn.find('.button-text');

            spinner.addClass('d-none');
            btnText.text('Save');
            submitBtn.prop('disabled', false);
        });

        $('#formData').on('submit', function(e) {
            e.preventDefault();

            let submitBtn = $('#submitBtn');
            let spinner = submitBtn.find('.spinner-border');
            let btnText = submitBtn.find('.button-text');

            spinner.removeClass('d-none');
            btnText.text('Saving...');
            submitBtn.prop('disabled', true);

            let id = $('#primary_id').val();
            let url = id ? '{{ route('rekap-lampiran.update', ['rekap_lampiran' => ':id']) }}'.replace(':id', id) :
                '{{ route('rekap-lampiran.store') }}';
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
                    let msg = response.message;
                    toastr.success(msg, "SUCCESS", {
                        progressBar: true,
                        timeOut: 3500,
                        positionClass: "toast-bottom-right",
                    });
                    $('.data-table').DataTable().ajax.reload();
                },
                error: function(response) {
                    if (response.status === 422) {
                        audio.play();
                        toastr.error("There are errors in your input!", "ERROR", {
                            progressBar: true,
                            timeOut: 3500,
                            positionClass: "toast-bottom-right",
                        });

                        let errors = response.responseJSON.errors;
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
                        btnText.text('Save');
                        submitBtn.prop('disabled', false);
                    }

                    audio.play();
                    let msg = response.message;
                    toastr.error(msg, "ERROR", {
                        progressBar: true,
                        timeOut: 3500,
                        positionClass: "toast-bottom-right",
                    });
                }
            });
        });

        $(document).on('click', '.delete-button', function(e) {
            e.preventDefault();

            const form = $(this).closest('form');

            Swal.fire({
                title: 'Are you sure?',
                text: 'This product will be permanently deleted!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '<span class="swal-btn-text">Yes, Delete</span>',
                cancelButtonText: 'Cancel',
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
                            `<span class="spinner-border spinner-border-sm mx-2" role="status" aria-hidden="true"></span> Deleting...`;
                        confirmBtn.disabled = true;

                        $.ajax({
                            url: form.attr('action'),
                            method: 'POST',
                            data: form.serialize(),
                            success: function(response) {
                                audio.play();
                                let msg = response.message
                                toastr.success(msg, "SUCCESS", {
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
                                let msg = response.message
                                toastr.error(msg, "ERROR", {
                                    progressBar: true,
                                    timeOut: 3500,
                                    positionClass: "toast-bottom-right"
                                });

                                btnText.innerHTML = `Yes, Delete`;
                                confirmBtn.disabled = false;
                            }
                        });
                    });
                }
            });
        });
    </script>
@endpush
