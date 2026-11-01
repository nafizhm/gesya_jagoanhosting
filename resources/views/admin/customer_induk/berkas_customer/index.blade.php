@extends('admin.layout_admin')
@section('content')
    <style>
        .select2-container .select2-selection--single {
            height: 38px;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            padding: 6px 12px;
            font-size: 1rem;
            background-color: #fff;
        }

        .select2-container .select2-selection__arrow {
            display: none;
        }

        .select2-container .select2-selection--single:focus {
            outline: none;
        }

        .select2-container .select2-selection__rendered {
            line-height: 30px;
        }
    </style>


    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                </h1>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header p-3">
                                <div class="d-flex align-content-center justify-content-between">
                                    <h3 class="font-weight-bold text-xl">Data Lampiran File Nasabah</h3>
                                </div>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('berkas-customer.index') }}" method="POST" id="konfigurasi-form">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="id" value="{{ $data ? $data->id : '' }}">

                                    <div class="form-group row mb-3">
                                        <label class="col-sm-3 col-form-label">Nama Nasabah</label>
                                        <div class="col-sm-3">
                                            <select class="form-control select2" id="nama_nasabah" name="nama_nasabah">
                                                <option value="">Pilih Nasabah</option>
                                                @foreach ($customers as $item)
                                                    <option value="{{ $item->id_customer }}">{{ $item->kode_customer }} - {{ $item->nama_lengkap }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>


                                    <div class="form-group row mb-3">
                                        <label class="col-sm-3 col-form-label">No. KTP</label>
                                        <div class="col-sm-3">
                                            <input type="number" name="no_ktp" class="form-control" disabled>
                                        </div>
                                        <label class="col-sm-2 col-form-label">No. Telp</label>
                                        <div class="col-sm-3">
                                            <input type="number" name="no_ktp_p" class="form-control" disabled>
                                        </div>
                                    </div>

                                    <div class="form-group row mb-3">
                                        <label for="lokasi_perumahan" class="col-sm-3 col-form-label">Lokasi
                                            Perumahan</label>
                                        <div class="col-sm-3">
                                            <input type="text" class="form-control" name="lokasi_perumahan" disabled>
                                        </div>
                                        <label class="col-sm-2 col-form-label">Lokasi Kav/Blok</label>
                                        <div class="col-sm-3">
                                            <input type="text" name="kode_kavling" class="form-control" disabled>
                                        </div>
                                    </div>



                                </form>
                                @if ($permissions['edit'] == 1)
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-info" id="submit-btn" data-toggle="modal"
                                            data-target="#uploadModal">
                                            <i class="fas fa-upload" style="margin-right: 8px;"></i>Upload File
                                        </button>
                                    </div>
                                @endif


                                <table id="table-file-nasabah" class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama File</th>
                                            <th>File Lampiran</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.card-body -->
                        </div>


                        <div class="modal fade" id="uploadModal" tabindex="-1" role="dialog"
                            aria-labelledby="modalUploadFileLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <form id="upload-file-form" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="nasabah" id="modal_nasabah_id">
                                    <div class="modal-content">
                                        <div class="modal-header bg-success">
                                            <h5 class="modal-title">Upload File Nasabah</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">

                                            <div class="form-group">
                                                <label for="tanggal">Tanggal Upload</label>
                                                <input type="date" class="form-control" id="tanggal" name="tanggal"
                                                    value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                                            </div>

                                            <div class="form-group">
                                                <label for="nama_file">Nama File</label>
                                                <select name="nama_file" id="nama_file" class="form-control nama-file">
                                                    <option value="">-- Pilih Nama File --</option>
                                                    <option value="KTP">Foto KTP</option>
                                                    <option value="KK">Foto Kartu Keluarga</option>
                                                    <option value="Pemohon">Foto Pemohon</option>
                                                    <option value="KTP Pasangan">Foto KTP Pasangan</option>
                                                    <option value="NPWP">Foto NPWP</option>
                                                    <option value="BPJS">Foto BPJS</option>
                                                </select>
                                                {{-- <input type="text" class="form-control" id="nama_file" name="nama_file"> --}}
                                            </div>

                                            <div class="form-group">
                                                <label for="lampiran">File Lampiran</label>
                                                <input type="file" is="lampiran" name="lampiran">
                                            </div>

                                            <div class="form-group">
                                                <label for="keterangan">Keterangan</label>
                                                <textarea class="form-control" name="keterangan"></textarea>
                                            </div>

                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary ms-1" id="submitBtn">
                                                <span class="spinner-border spinner-border-sm mx-1 d-none" role="status"
                                                    aria-hidden="true"></span>
                                                <span class="button-text">Simpan</span>
                                            </button>
                                        </div>
                                    </div>
                                </form>
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
        var nasabahId = null;
        var audio = new Audio('{{ asset('audio/notification.ogg') }}');

        function loadTableFiles(nasabahId) {
            $('#table-file-nasabah tbody').empty();

            $.ajax({
                url: '/admin/customer/' + nasabahId + '/file-nasabah',
                method: 'GET',
                success: function(data) {
                    if (data.length === 0) {
                        $('#table-file-nasabah tbody').html(
                            '<tr><td colspan="4" class="text-center">Belum ada file.</td></tr>');
                    } else {
                        data.forEach(function(file, index) {
                            $('#table-file-nasabah tbody').append(`
                        <tr>
                            <td>${index + 1}</td>
                            <td>${file.nama_file}</td>
                            <td>
                                <button class="btn btn-sm btn-info" onclick="viewFile('${file.folder}', '${file.lampiran}', '${file.nama_file}')">View</button>
                                <a href="/berkas_user/${file.lampiran}" class="btn btn-sm btn-success" download>Download</a>
                            </td>
                            <td>
                                @if ($permissions['hapus'] == 1)
                                <button class="btn btn-sm btn-danger" onclick="deleteFile(${file.id_file})">Hapus</button>
                                @endif
                            </td>
                        </tr>
                    `);
                        });
                    }
                },
                error: function() {
                    console.error('Gagal memuat file nasabah.');
                }
            });
        }

        $('#nama_nasabah').on('change', function() {
            nasabahId = $(this).val();
            $('#modal_nasabah_id').val(nasabahId);
            loadTableFiles(nasabahId);
        });

        $('#submit-btn').on('click', function(event) {
            event.preventDefault();

            var namaNasabah = $('#nama_nasabah').val();

            if (namaNasabah === "") {
                alert("Pilih nama nasabah terlebih dahulu");
            } else {
                $('#uploadModal').modal('show');
            }
        });

        $('#upload-file-form').submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);

            let submitBtn = $('#submitBtn');
            let spinner = submitBtn.find('.spinner-border');
            let btnText = submitBtn.find('.button-text');

            spinner.removeClass('d-none');
            btnText.text('Menyimpan...');
            submitBtn.prop('disabled', true);

            $.ajax({
                url: '/admin/customer/file-nasabah/upload',
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function() {
                    $('#upload-file-form')[0].reset();
                    $('#uploadModal').modal('hide');
                    loadTableFiles(nasabahId);

                    audio.play();
                    toastr.success('File berhasil diupload!', 'BERHASIL', {
                        progressBar: true,
                        timeOut: 3500,
                        positionClass: "toast-bottom-right"
                    });
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

        function viewFile(folder, filename, nama_file) {
            var fileExt = filename.split('.').pop().toLowerCase();
            var fileUrl = '/berkas_user/' + filename;

            let content = '';

            if (['pdf'].includes(fileExt)) {
                content = `<iframe src="${fileUrl}" width="100%" height="500px"></iframe>`;
            } else {
                content = `<img src="${fileUrl}" class="img-fluid" />`;;
            }

            Swal.fire({
                title: 'Preview File',
                html: content,
                width: 600
            });
        }

        function deleteFile(id_file) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'File akan dihapus secara permanen!',
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
                            url: '/admin/customer/file-nasabah/' + id_file,
                            method: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function() {
                                loadTableFiles(nasabahId);

                                audio.play();
                                toastr.success('File berhasil dihapus!', 'BERHASIL', {
                                    progressBar: true,
                                    timeOut: 3500,
                                    positionClass: "toast-bottom-right"
                                });
                                Swal.close();
                            },
                            error: function() {
                                audio.play();
                                toastr.error('Gagal menghapus file.', 'GAGAL!', {
                                    progressBar: true,
                                    timeOut: 3500,
                                    positionClass: "toast-bottom-right"
                                });

                                btnText.innerHTML = 'Ya, Hapus';
                                confirmBtn.disabled = false;
                            }
                        });
                    });
                }
            });
        }


        $('#uploadModal').on('hidden.bs.modal', function() {
            $('#upload-file-form')[0].reset();
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


        $(document).ready(function() {
            $('#nama_nasabah').select2({
                placeholder: 'Pilih Nasabah',
                allowClear: true
            });

            $('#nama_nasabah').on('change', function() {
                var nasabahId = $(this).val();

                if (nasabahId) {
                    $.ajax({
                        url: '/admin/customer/' + nasabahId + '/details',
                        method: 'GET',
                        success: function(data) {
                            $('input[name="no_ktp"]').val(data.no_ktp);
                            $('input[name="no_ktp_p"]').val(data.no_telp);
                            $('input[name="lokasi_perumahan"]').val(data.lokasi_perumahan);
                            $('input[name="kode_kavling"]').val(data.lokasi_kav_blok);
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Data nasabah tidak ditemukan!',
                            });
                        }
                    });
                }
            });

        });

        document.getElementById('submit').addEventListener('click', function(event) {
            event.preventDefault();

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data akan di update!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Simpan',
                cancelButtonText: 'Tidak, Batalkan'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('konfigurasi-form').submit();
                }
            });
        });
    </script>
@endpush
