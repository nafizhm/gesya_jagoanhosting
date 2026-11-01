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
                                    <h3 class="font-weight-bold text-xl">Konfigurasi</h3>
                                </div>
                            </div>
                            <div class="card-body">
                                <form id="formData">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $data->id }}">

                                    <div class="form-group row mb-3">
                                        <label for="api_key" class="col-sm-3 col-form-label">API Key</label>
                                        <div class="col-sm-3">
                                            <input type="text" class="form-control" id="api_key" name="api_key"
                                                value="{{ $data->api_key }}"
                                                @if ($permissions['edit'] == 0) readonly @endif>
                                        </div>
                                    </div>

                                    <div class="form-group row mb-3">
                                        <label for="number_key" class="col-sm-3 col-form-label">Number Key</label>
                                        <div class="col-sm-3">
                                            <input type="text" class="form-control" id="number_key" name="number_key"
                                                value="{{ $data->number_key }}"
                                                @if ($permissions['edit'] == 0) readonly @endif>
                                        </div>
                                    </div>


                                    <div class="mt-2 mb-3">
                                        <label class="col-sm-3 col-form-label"></label>
                                        <button type="button" class="btn btn-primary" data-toggle="modal"
                                            data-target="#modalWa">
                                            Test Pesan
                                        </button>
                                    </div>


                                    @if ($permissions['edit'] == 1)
                                        <div class="modal-footer justify-content-between">
                                            <button type="submit" class="btn btn-primary ml-auto" id="submitBtn">
                                                <span class="spinner-border spinner-border-sm mx-1 d-none" role="status"
                                                    aria-hidden="true"></span>
                                                <span class="button-text">Simpan Pengaturan</span>
                                            </button>
                                        </div>
                                    @endif
                                </form>
                            </div>

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

    <div class="modal fade" id="modalWa" tabindex="-1" role="dialog" data-focus="false" aria-labelledby="modalWaLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title text-white font-weight-bold" id="modalWaLabel"></h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formWa">
                    @csrf
                    <input type="hidden" id="primary_id" name="primary_id">
                    <div class="modal-body">
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">No. Wa</label>
                            <div class="col-sm-4">
                                <input type="text" name="no_wa" id="no_wa" class="form-control">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Isi Pesan</label>
                            <div class="col-sm-9">
                                <textarea name="message" id="message" class="form-control" rows="5"></textarea>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary ms-1" id="submitWa">
                                <span class="spinner-border spinner-border-sm mx-1 d-none" role="status"
                                    aria-hidden="true"></span>
                                <span class="button-text">Kirim</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
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

        $(document).on('click', '[data-target="#modalWa"]', function() {
            $('#modalWaLabel').text('Form Test Pesan WhatsApp');
        });

        $('#modalWa').on('hidden.bs.modal', function() {
            $('#formWa')[0].reset();
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            let submitBtn = $('#submitWa');
            let spinner = submitBtn.find('.spinner-border');
            let btnText = submitBtn.find('.button-text');

            spinner.addClass('d-none');
            btnText.text('Simpan');
            submitBtn.prop('disabled', false);
        });

        $('#formWa').on('submit', function(e) {
            e.preventDefault();

            let submitBtn = $('#submitWa');
            let spinner = submitBtn.find('.spinner-border');
            let btnText = submitBtn.find('.button-text');

            spinner.removeClass('d-none');
            btnText.text('Proses...');
            submitBtn.prop('disabled', true);

            let url = '{{ route('kirimPesanWa') }}';
            let method = 'POST';

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
                    $('#modalWa').modal('hide');
                    audio.play();
                    let msg = "Pesan berhasil terkirim!";
                    toastr.success(msg, "BERHASIL", {
                        progressBar: true,
                        timeOut: 3500,
                        positionClass: "toast-bottom-right",
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

                        spinner.addClass('d-none');
                        btnText.text('Kirim');
                        submitBtn.prop('disabled', false);
                    }
                }
            });
        });

        $('#formData').on('submit', function(e) {
            e.preventDefault();

            let submitBtn = $('#submitBtn');
            let spinner = submitBtn.find('.spinner-border');
            let btnText = submitBtn.find('.button-text');

            spinner.removeClass('d-none');
            btnText.text('Menyimpan...');
            submitBtn.prop('disabled', true);

            let url = '{{ route('pengaturan-koneksi.store') }}';
            let method = 'POST';

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
                success: function() {
                    spinner.addClass('d-none');
                    btnText.text('Simpan Pengaturan');
                    submitBtn.prop('disabled', false);

                    audio.play();
                    let msg = "Konfigurasi Wa Berhasil diupdate!!";
                    toastr.success(msg, "BERHASIL", {
                        progressBar: true,
                        timeOut: 3500,
                        positionClass: "toast-bottom-right",
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

                        spinner.addClass('d-none');
                        btnText.text('Simpan');
                        submitBtn.prop('disabled', false);
                    }
                }
            });
        });
    </script>
@endpush
