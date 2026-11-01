@empty($data)
@else
    <form action="{{ route('kavling.update', $data->id_kavling) }}" method="POST" id="form-edit">
        @csrf
        @method('PUT')
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title" id="modalEditLabel">Form Kavling</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label for="id_lokasi" class="col-sm-3 col-form-label">Lokasi / Cluster</label>
                        <div class="col-sm-6">
                            <input type="text" value="{{ $data->lokasi->nama_kavling ?? '-' }}" class="form-control"
                                disabled>
                        </div>
                    </div>


                    <div class="form-group row">
                        <label for="kode_kavling" class="col-sm-3 col-form-label">Kode Kavling</label>
                        <div class="col-sm-6">
                            <input type="text" value="{{ $data->kode_kavling }}" name="kode_kavling" class="form-control"
                                required disabled>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="panjang_kanan" class="col-sm-3 col-form-label">Panjang Kanan</label>
                        <div class="col-sm-2">
                            <input type="number" value="{{ $data->panjang_kanan }}" name="panjang_kanan" id="panjang_kanan"
                                class="form-control">
                        </div>

                        <label for="panjang_kiri" class="col-sm-2 col-form-label">Panjang Kiri</label>
                        <div class="col-sm-2">
                            <input type="number" value="{{ $data->panjang_kiri }}" name="panjang_kiri" id="panjang_kiri"
                                class="form-control">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="lebar_depan" class="col-sm-3 col-form-label">Lebar Depan</label>
                        <div class="col-sm-2">
                            <input type="number" value="{{ $data->lebar_depan }}" name="lebar_depan" id="lebar_depan"
                                class="form-control">
                        </div>

                        <label for="lebar_belakang" class="col-sm-2 col-form-label">Lebar Belakang</label>
                        <div class="col-sm-2">
                            <input type="number" value="{{ $data->lebar_belakang }}" name="lebar_belakang"
                                id="lebar_belakang" class="form-control">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="luas_tanah" class="col-sm-3 col-form-label">Luas Tanah</label>
                        <div class="col-sm-2">
                            <input type="number" value="{{ $data->luas_tanah }}" name="luas_tanah" id="luas_tanah"
                                class="form-control">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="hrg_meter" class="col-sm-3 col-form-label">Harga Per Meter</label>
                        <div class="col-sm-4">
                            <input type="number" value="{{ $data->hrg_meter }}" name="hrg_meter" id="hrg_meter"
                                class="form-control">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="tipe_bangunan" class="col-sm-3 col-form-label">Type Rumah</label>
                        <div class="col-sm-4">
                            <input type="number" value="{{ $data->tipe_bangunan }}" name="tipe_bangunan" id="tipe_bangunan"
                                class="form-control">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="luas_bangunan" class="col-sm-3 col-form-label">Luas Bangunan</label>
                        <div class="col-sm-4">
                            <input type="number" value="{{ $data->luas_bangunan }}" name="luas_bangunan"
                                id="luas_bangunan" class="form-control">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="hrg_jual" class="col-sm-3 col-form-label">Harga Jual</label>
                        <div class="col-sm-4">
                            <input type="text" value="{{ number_format($data->hrg_jual, 0, ',', '.') }}"
                                name="hrg_jual" id="hrg_jual" class="form-control">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="biaya_lain" class="col-sm-3 col-form-label">Biaya Surat-surat , Biaya Notaris (AJB,
                            SHGB), BPHTB, promosi/iklan, pemasaran dan biaya lain-lain</label>
                        <div class="col-sm-4">
                            <input type="text" value="{{ number_format($data->biaya_lain, 0, ',', '.') }}"
                                name="biaya_lain" id="biaya_lain" class="form-control">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="daya_listrik" class="col-sm-3 col-form-label">Daya Listrik</label>
                        <div class="col-sm-4">
                            <input type="number" value="{{ $data->daya_listrik }}" name="daya_listrik"
                                id="daya_listrik" class="form-control">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="keterangan" class="col-sm-3 col-form-label">Keterangan</label>
                        <div class="col-sm-4">
                            <input type="text" value="{{ $data->keterangan }}" name="keterangan" id="keterangan"
                                class="form-control">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="id_rumah_sikumbang" class="col-sm-3 col-form-label">ID Rumah</label>
                        <div class="col-sm-4">
                            <input type="text" value="{{ $data->id_rumah_sikumbang }}" name="id_rumah_sikumbang"
                                id="id_rumah_sikumbang" class="form-control">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="no_sertifikat" class="col-sm-3 col-form-label">NO. Sertifikat</label>
                        <div class="col-sm-4">
                            <input type="text" value="{{ $data->no_sertifikat }}" name="no_sertifikat"
                                id="no_sertifikat" class="form-control">
                        </div>
                    </div>

                    {{-- <div class="form-group row">
        <label for="" class="col-sm-3 col-form-label">Foto</label>
        <div class="col-sm-4">
        <input type="number"  class="form-control">
        </div>
    </div> --}}
                </div>

                <div class="modal-footer ">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary ms-1" id="submitBtn">
                        <span class="spinner-border spinner-border-sm mx-1 d-none" role="status"
                            aria-hidden="true"></span>
                        <span class="button-text">Simpan</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
    <script>
        function formatAngkaRibuan(angka) {
            return angka.replace(/\D/g, '')
                .replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        $(document).on('input', '#hrg_jual', function() {
            let nilai = $(this).val();
            let terformat = formatAngkaRibuan(nilai);
            $(this).val(terformat);
        });

        $(document).on('input', '#biaya_lain', function() {
            let nilai = $(this).val();
            let terformat = formatAngkaRibuan(nilai);
            $(this).val(terformat);
        });
    </script>

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

                // Reset error states
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();
                $('.error-text').text('');

                let form = this;
                let formData = new FormData(form);
                let submitBtn = $('#submitBtn');
                let spinner = submitBtn.find('.spinner-border');
                let btnText = submitBtn.find('.button-text');

                // Set loading state
                spinner.removeClass('d-none');
                btnText.text('Menyimpan...');
                submitBtn.prop('disabled', true);

                $.ajax({
                    url: $(form).attr('action'),
                    type: $(form).attr('method'),
                    data: formData,
                    processData: false,
                    contentType: false,
                    timeout: 30000, // 30 detik timeout
                    success: function(response) {
                        if (response.status) {
                            $('#myModal').modal('hide');
                            if (typeof audio !== 'undefined') audio.play();

                            toastr.success(response.message, "Berhasil!", {
                                closeButton: true,
                                progressBar: true,
                                timeOut: 3000,
                                positionClass: 'toast-bottom-right'
                            });

                            // Reload DataTable jika ada
                            if ($.fn.DataTable.isDataTable('#table')) {
                                $('#table').DataTable().ajax.reload();
                            }
                        } else {
                            // Handle server-side validation errors
                            if (response.msgField) {
                                $.each(response.msgField, function(prefix, val) {
                                    $('#error-' + prefix).text(val[0]);
                                });
                            }

                            if (typeof audio !== 'undefined') audio.play();
                            toastr.error(response.message ||
                                'Terjadi kesalahan saat menyimpan data', "Gagal!", {
                                    closeButton: true,
                                    progressBar: true,
                                    timeOut: 3000,
                                    positionClass: 'toast-bottom-right'
                                });

                            // Reset button state for failed submission
                            resetButtonState();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', {
                            status: xhr.status,
                            statusText: xhr.statusText,
                            responseText: xhr.responseText,
                            error: error
                        });

                        let errorMessage = 'Terjadi kesalahan yang tidak diketahui';
                        let errorTitle = 'Error!';

                        // Handle different error types
                        if (status === 'timeout') {
                            errorMessage = 'Waktu tunggu habis. Silakan coba lagi.';
                            errorTitle = 'Timeout!';
                        } else if (status === 'abort') {
                            errorMessage = 'Permintaan dibatalkan.';
                            errorTitle = 'Dibatalkan!';
                        } else if (xhr.status === 0) {
                            errorMessage =
                                'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.';
                            errorTitle = 'Koneksi Gagal!';
                        } else if (xhr.status === 422) {
                            // Validation errors
                            errorMessage = 'Ada inputan yang tidak valid!';
                            errorTitle = 'Validasi Gagal!';

                            try {
                                let errors = xhr.responseJSON?.errors;
                                if (errors) {
                                    $.each(errors, function(key, val) {
                                        let input = $('#' + key);
                                        if (input.length) {
                                            input.addClass('is-invalid');
                                            // Remove existing feedback
                                            input.siblings('.invalid-feedback')
                                                .remove();
                                            // Add new feedback
                                            input.after(
                                                '<div class="invalid-feedback d-block">' +
                                                val[0] + '</div>'
                                            );
                                        }
                                    });
                                }
                            } catch (parseError) {
                                console.error('Error parsing validation response:', parseError);
                            }
                        } else if (xhr.status === 500) {
                            errorMessage =
                                'Terjadi kesalahan server internal. Silakan hubungi administrator.';
                            errorTitle = 'Server Error!';
                        } else if (xhr.status === 403) {
                            errorMessage = 'Anda tidak memiliki izin untuk melakukan aksi ini.';
                            errorTitle = 'Akses Ditolak!';
                        } else if (xhr.status === 404) {
                            errorMessage =
                                'Endpoint tidak ditemukan. Silakan hubungi administrator.';
                            errorTitle = 'Not Found!';
                        } else if (xhr.status >= 400 && xhr.status < 500) {
                            errorMessage =
                                'Permintaan tidak valid. Silakan periksa data yang dikirim.';
                            errorTitle = 'Bad Request!';
                        } else if (xhr.status >= 500) {
                            errorMessage = 'Terjadi kesalahan server. Silakan coba lagi nanti.';
                            errorTitle = 'Server Error!';
                        }

                        // Show error notification
                        if (typeof audio !== 'undefined') audio.play();
                        toastr.error(errorMessage, errorTitle, {
                            closeButton: true,
                            progressBar: true,
                            timeOut: 5000,
                            positionClass: 'toast-bottom-right'
                        });

                        // Always reset button state on error
                        resetButtonState();
                    }
                });

                // Function to reset button state
                function resetButtonState() {
                    spinner.addClass('d-none');
                    btnText.text('Simpan');
                    submitBtn.prop('disabled', false);
                }
            });
        });
    </script>
@endempty
