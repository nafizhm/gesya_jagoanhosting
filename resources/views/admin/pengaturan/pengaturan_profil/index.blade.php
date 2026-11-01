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
                                    <h3 class="font-weight-bold text-xl">Pengaturan Aplikasi</h3>
                                </div>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('pengaturan-profil.update', $data->id) }}" method="POST" id="konfigurasi-form">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="id" value="{{ $data->id }}">

                                    <div class="form-group row mb-3">
                                        <label for="nama_perusahaan" class="col-sm-3 col-form-label">Nama Perusahaan</label>
                                        <div class="col-sm-9">
                                            <input type="text"
                                                class="form-control"
                                                id="nama_perusahaan" name="nama_perusahaan" placeholder="Nama Perusahaan"
                                                value="{{ old('nama_perusahaan', $data->nama_perusahaan) }}"
                                                @if ($permissions['edit'] == 0) readonly @endif>
                                        </div>
                                    </div>

                                    <div class="form-group row mb-3">
                                        <label for="alamat" class="col-sm-3 col-form-label">Alamat</label>
                                        <div class="col-sm-9">
                                            <textarea class="form-control" id="alamat" name="alamat" placeholder="Alamat"
                                                rows="2" @if ($permissions['edit'] == 0) readonly @endif>{{ old('alamat', $data->alamat) }}</textarea>
                                        </div>
                                    </div>

                                    <div class="form-group row mb-3">
                                        <label for="email" class="col-sm-3 col-form-label">Email</label>
                                        <div class="col-sm-9">
                                            <input type="email" class="form-control"
                                                id="email" name="email" placeholder="Email"
                                                value="{{ old('email', $data->email) }}"
                                                @if ($permissions['edit'] == 0) readonly @endif>
                                        </div>
                                    </div>

                                    <div class="form-group row mb-3">
                                        <label for="telp" class="col-sm-3 col-form-label">No. Telp</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control"
                                                id="telp" name="telp" placeholder="Telepon"
                                                value="{{ old('telp', $data->telp) }}"
                                                @if ($permissions['edit'] == 0) readonly @endif>
                                        </div>
                                    </div>

                                    <div class="form-group row mb-3">
                                        <label for="hape" class="col-sm-3 col-form-label">No. HP</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control"
                                                id="hape" name="hape" placeholder="Nomor Handphone"
                                                value="{{ old('hape', $data->hape) }}"
                                                @if ($permissions['edit'] == 0) readonly @endif>
                                        </div>
                                    </div>

                                    <div class="form-group row mb-5">
                                        <label for="npwp_perusahaan" class="col-sm-3 col-form-label">NPWP</label>
                                        <div class="col-sm-9">
                                            <input type="text"
                                                class="form-control"
                                                id="npwp_perusahaan" name="npwp_perusahaan" placeholder="NPWP Perusahaan"
                                                value="{{ old('npwp_perusahaan', $data->npwp_perusahaan) }}"
                                                @if ($permissions['edit'] == 0) readonly @endif>
                                        </div>
                                    </div>

                                    <div class="form-group row mb-3">
                                        <label for="folderSVG" class="col-sm-3 col-form-label">Folder SVG
                                        </label>
                                        <div class="col-sm-9">
                                            <input type="text"
                                                class="form-control"
                                                id="folderSVG" name="folder_svg" placeholder="Folder SVG"
                                                value="{{ old('folder_svg', $data->folder_svg) }}"
                                                @if ($permissions['edit'] == 0) readonly @endif>
                                        </div>
                                    </div>

                                    @if ($permissions['edit'] == 1)
                                        <div class="modal-footer justify-content-between">
                                            <button type="submit" class="btn btn-success ml-auto" id="submit-btn">SIMPAN
                                                PENGATURAN</button>
                                        </div>
                                    @endif
                                </form>
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
    <style>
        input[readonly],
        textarea[readonly] {
            background-color: white !important;
            color: #495057;
            /* Warna teks agar tetap terlihat jelas */
        }
    </style>
@endsection
@push('scripts')
    <script>
        var audio = new Audio('{{ asset('audio/notification.ogg') }}');

        document.getElementById('submit-btn').addEventListener('click', function(event) {
            event.preventDefault();

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data akan di update!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '<span class="swal-btn-text">Ya, Simpan</span>',
                cancelButtonText: 'Tidak, Batalkan',
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'btn btn-primary mx-2',
                    cancelButton: 'btn btn-secondary'
                },
                preConfirm: () => {
                    return new Promise((resolve) => {
                        const confirmBtn = Swal.getConfirmButton();
                        const btnText = confirmBtn.querySelector('.swal-btn-text');

                        btnText.innerHTML = `<span class="spinner-border spinner-border-sm mx-2" role="status" aria-hidden="true"></span> Simpan...`;
                        confirmBtn.disabled = true;

                        let form = document.getElementById('konfigurasi-form');
                        let formData = new FormData(form);
                        formData.append('_method', 'PUT');

                        $.ajax({
                            url: form.action,
                            method: 'POST',
                            data: formData,
                            contentType: false,
                            processData: false,
                            success: function(response) {
                                audio.play();
                                toastr.success(response.message || "Data Berhasil Diubah!", "BERHASIL", {
                                    progressBar: true,
                                    timeOut: 3500,
                                    positionClass: "toast-bottom-right",
                                });
                                Swal.close();
                            },
                            error: function(xhr) {
                                audio.play();
                                if (xhr.status === 422) {
                                    let errors = xhr.responseJSON.errors;
                                    $.each(errors, function(key, val) {
                                        let input = $('#' + key);
                                        input.addClass('is-invalid');
                                        input.parent().find('.invalid-feedback').remove();
                                        input.parent().append('<span class="invalid-feedback" role="alert"><strong>' + val[0] + '</strong></span>');
                                    });
                                    toastr.error("Ada inputan yang salah!", "GAGAL!", {
                                        progressBar: true,
                                        timeOut: 3500,
                                        positionClass: "toast-bottom-right",
                                    });
                                } else {
                                    toastr.error("Gagal menyimpan data.", "GAGAL!", {
                                        progressBar: true,
                                        timeOut: 3500,
                                        positionClass: "toast-bottom-right",
                                    });
                                }
                                btnText.innerHTML = 'Ya, Simpan';
                                confirmBtn.disabled = false;
                            }
                        });
                    });
                }
            });
        });
    </script>
@endpush
