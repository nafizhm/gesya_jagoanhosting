@empty($data)
@else
    <form action="{{ route('template-pesan.update', $data->id_template) }}" method="POST" id="form-edit">
        @csrf
        @method('PUT')
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title" id="modalEditLabel">Form Template Pesan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label for="nama_template" class="col-sm-4 col-form-label">Nama Template</label>
                        <div class="col-sm-8">
                            <input type="text" value="{{ $data->nama_template }}" name="nama_template"
                                class="form-control nama_template">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="isi_template" class="col-sm-4 col-form-label">Isi Pesan</label>
                        <div class="col-sm-8">
                            <input type="text" value="{{ $data->isi_template }}" name="isi_template"
                                class="form-control isi_template">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="jenis_pesan" class="col-sm-4 col-form-label">Jenis Pesan</label>
                        <div class="col-sm-8">
                            <input type="text" value="{{ $data->jenis_pesan }}" name="jenis_pesan"
                                class="form-control jenis_pesan">
                        </div>
                    </div>


                </div>

                <div class="modal-footer ">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </div>
    </form>

    <script>
        var audio = new Audio('{{ asset('audio/notification.ogg') }}');

        $(document).ready(function() {
            $('#myModal').on('hidden.bs.modal', function() {
                $('#form-edit')[0].reset();
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
                        $('.error-text').text(''); // bersihkan error sebelumnya
                        $('.data-table').DataTable().ajax.reload();

                        if (response.status) {
                            $('#myModal').modal('hide');
                            $('.data-table').DataTable().ajax.reload();

                            audio.play(); // play suara
                            toastr.success(response.message, "Berhasil!", {
                                closeButton: true,
                                progressBar: true,
                                timeOut: 3000,
                                positionClass: 'toast-bottom-right'
                            });
                        } else {
                            // validasi gagal
                            $.each(response.msgField, function(prefix, val) {
                                $('#error-' + prefix).text(val[0]);
                            });

                            audio.play(); // play suara
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
