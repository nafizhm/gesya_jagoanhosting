@extends('admin.layout_admin')
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
                                    <h3 class="font-weight-bold text-xl">Data Media Aplikasi</h3>
                                    <div class="d-flex align-items-center" style="gap: 8px;">
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
                                                <th>Jenis Data</th>
                                                <th>Keterangan</th>
                                                <th>File</th>
                                                <th>Nama File</th>
                                                <th class="text-center" width="10%">Action</th>
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
            </div>
        </section>
    </div>

    <div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="uploadModalForm" enctype="multipart/form-data">
                <div class="modal-content">
                    <div class="modal-header bg-success">
                        <h5 class="modal-title" id="uploadModalLabel">Upload Gambar</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" id="media_id">

                        <div id="fileAlert" class="alert alert-danger d-none" role="alert">
                            Ukuran file tidak boleh lebih dari 3MB.
                        </div>

                        <div class="form-group">
                            <label for="nama_file">Pilih Gambar</label>
                            <input type="file" name="nama_file" id="nama_file" required>
                        </div>
                    </div>


                    <div class="modal-footer">
                        <button type="submit" id="submitBtn" class="btn btn-primary">
                            <span class="spinner-border spinner-border-sm mx-1 d-none" role="status"
                                aria-hidden="true"></span>
                            <span class="button-text">Upload</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title" id="previewModalLabel">Preview Gambar</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <img id="previewImage" src="" alt="Preview Gambar" class="img-fluid mb-3"
                        style="max-height: 400px;">
                    <br>
                    <a id="downloadBtn" href="#" download class="btn btn-success">Download Gambar</a>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        var audio = new Audio('{{ asset('audio/notification.ogg') }}');

        function reloadTable() {
            $('#table').DataTable().ajax.reload();
        }

        $(document).on('click', '.preview-button', function() {
            var imageUrl = $(this).data('file');
            var namaFile = $(this).data('nama');

            $('#previewImage').attr('src', imageUrl);
            $('#downloadBtn').attr('href', imageUrl);
            $('#downloadBtn').attr('download', namaFile);

            $('#previewModal').modal('show');
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).on('click', '.upload-button', function() {
            var id = $(this).data('id');
            $('#media_id').val(id);
        });

        $('#uploadModal').on('hidden.bs.modal', function() {
            $('#uploadModalForm')[0].reset();
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            let submitBtn = $('#submitBtn');
            let spinner = submitBtn.find('.spinner-border');
            let btnText = submitBtn.find('.button-text');

            spinner.addClass('d-none');
            btnText.text('Simpan');
            submitBtn.prop('disabled', false);
        });

        $('#uploadModalForm').on('submit', function(e) {
            e.preventDefault();

            var id = $('#media_id').val();
            var fileInput = $('#nama_file')[0];
            $('#fileAlert').addClass('d-none');

            if (fileInput.files.length === 0) {
                toastr.warning("Silakan pilih file terlebih dahulu.");
                return;
            }

            var file = fileInput.files[0];

            if (file.size > 3 * 1024 * 1024) {
                $('#fileAlert').removeClass('d-none');
                return;
            }

            var formData = new FormData();
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
            formData.append('_method', 'PUT');
            formData.append('nama_file', file);

            let submitBtn = $('#submitBtn');
            let spinner = submitBtn.find('.spinner-border');
            let btnText = submitBtn.find('.button-text');

            spinner.removeClass('d-none');
            btnText.text('Menyimpan...');
            submitBtn.prop('disabled', true);

            $.ajax({
                url: "{{ route('pengaturan-media.update', ':id') }}".replace(':id', id),
                type: 'POST',
                headers: {
                    'X-HTTP-Method-Override': 'PUT'
                },
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    $('#uploadModal').modal('hide');
                    $('#uploadModalForm')[0].reset();
                    $('#fileAlert').addClass('d-none');
                    audio.play();
                    toastr.success(response.success, "BERHASIL", {
                        progressBar: true,
                        timeOut: 3500,
                        positionClass: "toast-bottom-right",
                    });

                    $('.data-table').DataTable().ajax.reload();
                },
                error: function(xhr, status, error) {
                    audio.play();
                    console.error(error);
                    toastr.error('Terjadi kesalahan saat mengupdate foto', "GAGAL!", {
                        progressBar: true,
                        timeOut: 3500,
                        positionClass: "toast-bottom-right",
                    });

                    spinner.addClass('d-none');
                    btnText.text('Simpan');
                    submitBtn.prop('disabled', false);
                }
            });
        });

        $('#nama_file').on('change', function() {
            $('#fileAlert').addClass('d-none');
        });

        @if (session('success'))
            audio.play();
            toastr.success("{{ session('success') }}", "BERHASIL", {
                progressBar: true,
                timeOut: 3500,
                positionClass: "toast-bottom-right",
            });
        @elseif (session('error'))
            audio.play();
            toastr.error("{{ session('error') }}", "GAGAL!", {
                progressBar: true,
                timeOut: 3500,
                positionClass: "toast-bottom-right",
            });
        @endif

        $(function() {
            var permissions = @json($permissions);
            var showActionColumn = (permissions['edit'] == 1);

            var table = $('.data-table').DataTable({
                processing: false,
                serverSide: true,
                ajax: "{{ route('pengaturan-media.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'jenis_data',
                        name: 'jenis_data',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'gambar',
                        name: 'gambar',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nama_file',
                        name: 'nama_file',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
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
                }],
                searching: false
            });

            $("input[data-bootstrap-switch]").each(function() {
                $(this).bootstrapSwitch('state', $(this).prop('checked'));
            });
        });
    </script>
@endpush
