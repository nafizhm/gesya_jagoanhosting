@empty($data)
@else
    <form action="{{ route('list-penjualan.update', $data->id_status_progres) }}" method="POST" id="form-edit">
        @csrf
        @method('PUT')
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title" id="modalEditLabel">Form List Penjualan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label for="status_progres" class="col-sm-4 col-form-label">Progres Penjualan</label>
                        <div class="col-sm-8">
                            <input type="text" value="{{ $data->status_progres }}" name="status_progres"
                                class="form-control status_progres" placeholder="Progres Penjualan">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="short_name" class="col-sm-4 col-form-label">Nama Singkat</label>
                        <div class="col-sm-8">
                            <input type="text" value="{{ $data->short_name }}" name="short_name"
                                class="form-control short_name" placeholder="Nama Singkat">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="keterangan" class="col-sm-4 col-form-label">Keterangan</label>
                        <div class="col-sm-8">
                            <input type="text" value="{{ $data->keterangan }}" name="keterangan"
                                class="form-control keterangan" placeholder="Keterangan">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="urutan" class="col-sm-4 col-form-label">Urutan</label>
                        <div class="col-sm-8">
                            <input type="number" value="{{ $data->urutan }}" name="urutan" class="form-control urutan"
                                placeholder="Urutan">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="warna" class="col-sm-4 col-form-label">Warna</label>
                        <div class="col-sm-8">
                            <input type="color" value="{{ $data->warna }}" name="warna" class="form-control warna">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="stt_tampil" class="col-sm-4 col-form-label">Status Tampil</label>
                        <div class="col-sm-8">
                            <select name="stt_tampil" id="stt_tampil" class="form-control stt_tampil">
                                <option value="1" {{ isset($data) && $data->stt_tampil == 1 ? 'selected' : '' }}>
                                    Aktif</option>
                                <option value="0" {{ isset($data) && $data->stt_tampil == 0 ? 'selected' : '' }}>
                                    Tidak Aktif</option>
                            </select>
                        </div>
                    </div>


                </div>

                <div class="modal-footer ">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
                    <button type="submit" id="submitBtn" class="btn btn-primary">
                        <span class="spinner-border spinner-border-sm mx-1 d-none" role="status" aria-hidden="true"></span>
                        <span class="button-text">Simpan</span></button>
                </div>
            </div>
        </div>
    </form>

    <script>
        $(document).ready(function() {
            $('.stt_tampil').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Status",
                minimumResultsForSearch: Infinity,
            });
        });

        var audio = new Audio('{{ asset('audio/notification.ogg') }}');

        $(document).ready(function() {
            $('#myModal').on('hidden.bs.modal', function() {
                $('#form-edit')[0].reset();
                $('#primary_id').val('');

                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();

                let submitBtn = $('#submitBtn');
                let spinner = submitBtn.find('.spinner-border');
                let btnText = submitBtn.find('.button-text');

                spinner.addClass('d-none');
                btnText.text('Simpan');
                submitBtn.prop('disabled', false);
            });

            $('#form-edit').on('submit', function(e) {
                e.preventDefault();
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();

                let form = this;
                let formData = new FormData(form);

                let submitBtn = $('#submitBtn');
                let spinner = submitBtn.find('.spinner-border');
                let btnText = submitBtn.find('.button-text');

                spinner.removeClass('d-none');
                btnText.text('Menyimpan...');
                submitBtn.prop('disabled', true);

                $.ajax({
                    url: $(form).attr('action'),
                    type: $(form).attr('method'),
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('.error-text').text('');

                        if (response.status) {
                            $('#myModal').modal('hide');

                            $('#table').DataTable().ajax.reload(null, false);

                            audio.play();
                            toastr.success(response.message, "Berhasil!", {
                                closeButton: true,
                                progressBar: true,
                                timeOut: 3000,
                                positionClass: 'toast-bottom-right'
                            });
                        } else {
                            $.each(response.msgField, function(prefix, val) {
                                $('#error-' + prefix).text(val[0]);
                            });

                            audio.play();
                            toastr.error(response.message, "Gagal!", {
                                closeButton: true,
                                progressBar: true,
                                timeOut: 3000,
                                positionClass: 'toast-bottom-right'
                            });

                            spinner.addClass('d-none');
                            btnText.text('Simpan');
                            submitBtn.prop('disabled', false);
                        }
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
                                let input = $('.' + key);
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
    </script>
@endempty
