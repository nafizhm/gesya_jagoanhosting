@empty($data)
@else
    <form action="{{ route('list-pembangunan.update', $data->id_list_pembangunan) }}" method="POST" id="form-edit-bank">
        @csrf
        @method('PUT')
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title">Form List Progres Pembangunan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Keterangan</label>
                        <input value="{{ $data->keterangan }}" type="text" name="keterangan" id="keterangan"
                            class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Warna</label>
                        <input value="{{ $data->warna }}" type="color" name="warna" id="warna"
                            class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-danger">Batal</button>
                    <button type="submit" id="submitBtn" class="btn btn-primary">
                        <span class="spinner-border spinner-border-sm mx-1 d-none" role="status" aria-hidden="true"></span>
                        <span class="button-text">Simpan</span></button>
                </div>
            </div>
        </div>
    </form>

    <script>
        var audio = new Audio('{{ asset('audio/notification.ogg') }}');

        $(document).ready(function() {
            $('#form-edit-bank').on('submit', function(e) {
                e.preventDefault();

                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();

                let submitBtn = $('#submitBtn');
                let spinner = submitBtn.find('.spinner-border');
                let btnText = submitBtn.find('.button-text');

                spinner.removeClass('d-none');
                btnText.text('Menyimpan...');
                submitBtn.prop('disabled', true);

                let form = this;
                let formData = new FormData(form);

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
    </script>
@endempty
