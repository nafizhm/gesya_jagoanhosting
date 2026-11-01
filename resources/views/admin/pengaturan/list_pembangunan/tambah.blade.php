<form action="{{ Route('list-pembangunan.store') }}" method="POST" id="form-tambah" enctype="multipart/form-data">
    @csrf
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h5 class="modal-title" id="exampleModalLabel">Form List Progres Pembangunan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group d-flex align-items-center mb-3">
                    <label for="keterangan" class="me-2" style="width: 100px;">Keterangan</label>
                    <div class="col-sm-10">
                        <input type="text" name="keterangan" id="keterangan" class="form-control">
                    </div>
                </div>
                <small id="error-keterangan" class="error-text form-text text-danger ms-2"></small>

                <div class="form-group d-flex align-items-center mb-3">
                    <label for="warna" class="me-2" style="width: 100px;">Warna</label>
                    <div class="col-sm-2">
                        <input type="color" name="warna" id="warna" class="form-control form-control-color">
                    </div>
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
    $(document).ready(function() {
        $('#myModal').on('hidden.bs.modal', function() {
            $('#form-tambah')[0].reset();

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
    });

    $(document).ready(function() {
        $('#form-tambah').on('submit', function(e) {
            e.preventDefault();

            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();
            let form = this;
            let formData = new FormData(form);
            var audio = new Audio('{{ asset('audio/notification.ogg') }}');

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
                        $('.data-table').DataTable().ajax.reload();

                        audio.play();
                        toastr.success(response.message, "Berhasil!", {
                            closeButton: true,
                            progressBar: true,
                            timeOut: 3000,
                            positionClass: 'toast-bottom-right',
                        });
                    } else {
                        // Tampilkan error validasi
                        $.each(response.msgField, function(prefix, val) {
                            $('#error-' + prefix).text(val[0]);
                        });

                        audio.play();
                        toastr.error(response.message, "Gagal!", {
                            closeButton: true,
                            progressBar: true,
                            timeOut: 3000,
                            positionClass: 'toast-bottom-right',
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
