@extends('admin.layout_admin')
@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="container mt-4">

                <form id="formData">
                    @csrf

                    <div class="card mb-4">
                        <div class="card-header">
                            <h3 class="font-weight-bold text-xl" style="color: #343a40;">Verifikasi Data Permohonan</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="hidden" id="primary_id" name="primary_id" value="{{ $data->id_reg }}">
                                    <div class="form-group row">
                                        <label for="tgl_registrasi" class="col-sm-4 col-form-label">Tanggal</label>
                                        <div class="col-sm-8">
                                            <input type="text" id="tgl_registrasi" value="{{ $data->tgl_registrasi }}"
                                                class="form-control" disabled>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="tgl_registrasi" class="col-sm-4 col-form-label">Nama Lengkap</label>
                                        <div class="col-sm-8">
                                            <input type="text" id="tgl_registrasi" value="{{ $data->nama_lengkap }}"
                                                class="form-control" disabled>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="tgl_registrasi" class="col-sm-4 col-form-label">NIK</label>
                                        <div class="col-sm-8">
                                            <input type="text" id="tgl_registrasi" value="{{ $data->nik }}"
                                                class="form-control" disabled>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="tgl_registrasi" class="col-sm-4 col-form-label">Jenis Kelamin</label>
                                        <div class="col-sm-8">
                                            <input type="text" id="tgl_registrasi" value="{{ $data->jenis_kelamin }}"
                                                class="form-control" disabled>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="tgl_registrasi" class="col-sm-4 col-form-label">Tempat Lahir</label>
                                        <div class="col-sm-8">
                                            <input type="text" id="tgl_registrasi" value="{{ $data->tempat_lahir }}"
                                                class="form-control" disabled>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="tgl_registrasi" class="col-sm-4 col-form-label">Tgl Lahir</label>
                                        <div class="col-sm-8">
                                            <input type="text" id="tgl_registrasi" value="{{ $data->tgl_lahir }}"
                                                class="form-control" disabled>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="tgl_registrasi" class="col-sm-4 col-form-label">Alamat</label>
                                        <div class="col-sm-8">
                                            <input type="text" id="tgl_registrasi" value="{{ $data->alamat }}"
                                                class="form-control" disabled>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="tgl_registrasi" class="col-sm-4 col-form-label">NPWP</label>
                                        <div class="col-sm-8">
                                            <input type="text" id="tgl_registrasi" value="{{ $data->npwp }}"
                                                class="form-control" disabled>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="no_bpjs_kes" class="col-sm-4 col-form-label">No. BPJS Kes</label>
                                        <div class="col-sm-8">
                                            <input type="text" id="no_bpjs_kes" value="{{ $data->no_bpjs_kes }}"
                                                class="form-control" disabled>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="tgl_registrasi" class="col-sm-4 col-form-label">Email</label>
                                        <div class="col-sm-8">
                                            <input type="text" id="tgl_registrasi" value="{{ $data->email }}"
                                                class="form-control" disabled>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="tgl_registrasi" class="col-sm-4 col-form-label">Nomor Telepon</label>
                                        <div class="col-sm-8">
                                            <input type="text" id="tgl_registrasi"
                                                value="{{ $data->no_telp_customer }}" class="form-control" disabled>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="tgl_registrasi" class="col-sm-4 col-form-label">Nama Saudara</label>
                                        <div class="col-sm-8">
                                            <input type="text" id="tgl_registrasi" value="{{ $data->nama_saudara }}"
                                                class="form-control" disabled>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="tgl_registrasi" class="col-sm-4 col-form-label">No. Telp
                                            Saudara</label>
                                        <div class="col-sm-8">
                                            <input type="text" id="tgl_registrasi"
                                                value="{{ $data->no_telp_saudara }}" class="form-control" disabled>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Nama Marketing</label>
                                        <div class="col-sm-8">
                                            <input type="text" value="{{ $data->marketing->nama_marketing ?? '-' }}"
                                                class="form-control" disabled>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Lokasi Perumahan</label>
                                        <div class="col-sm-8">
                                            <input type="text" value="{{ $data->lokasi->nama_kavling ?? '-' }}"
                                                class="form-control" disabled>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Blok Kavling</label>
                                        <div class="col-sm-8">
                                            <input type="text" value="{{ $data->kavling->kode_kavling ?? '-' }}"
                                                class="form-control" disabled>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="hrg_jual" class="col-sm-4 col-form-label">Harga Jual</label>
                                        <div class="col-sm-8">
                                            <input type="text" id="hrg_jual"
                                                value="Rp {{ number_format($data->hrg_jual, 0, ',', '.') }}"
                                                class="form-control" disabled>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="tgl_registrasi" class="col-sm-4 col-form-label">Jenis
                                            Perumahan</label>
                                        <div class="col-sm-8">
                                            <input type="text" id="tgl_registrasi"
                                                value="{{ $data->jenis_perumahan }}" class="form-control" disabled>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="tgl_registrasi" class="col-sm-4 col-form-label">Booking Fee</label>
                                        <div class="col-sm-8">
                                            <input type="text" id="tgl_registrasi"
                                                value="Rp {{ number_format((float) $data->booking_fee, 0, ',', '.') }}"
                                                class="form-control" disabled>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="row">
                                        @php
                                            $fields = [
                                                'foto_pemohon' => 'Foto Pemohon',
                                                'foto_ktp_p' => 'Foto KTP Pasangan',
                                                'file_bukti' => 'File Bukti Bayar',
                                                'foto_ktp' => 'Foto KTP',
                                                'foto_npwp' => 'Foto NPWP',
                                                'foto_kk' => 'Foto KK',
                                                'foto_bpjs' => 'Foto BPJS',
                                            ];
                                        @endphp

                                        @foreach ($fields as $field => $label)
                                            <div class="col-md-6 mb-3 text-center">
                                                <strong>{{ $label }}</strong>
                                                @if ($data->$field)
                                                    <img src="{{ route('file.preview', $data->$field) }}"
                                                        alt="{{ $label }}" class="img-fluid mb-2"
                                                        style="max-height: 180px;">
                                                @else
                                                    <p><em>Tidak ada file</em></p>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="stt_reg" class="col-sm-2 col-form-label">Status Verifikasi</label>
                                <div class="col-sm-4">
                                    <select name="stt_reg" id="stt_reg" class="form-control select-status" required>
                                        <option value="1" {{ $data->stt_reg == 1 ? 'selected' : '' }}>Pending
                                        </option>
                                        <option value="2" {{ $data->stt_reg == 2 ? 'selected' : '' }}>Disetujui
                                        </option>
                                        <option value="3" {{ $data->stt_reg == 3 ? 'selected' : '' }}>Ditolak
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row mt-3">
                                <label for="jenis_pembelian" class="col-sm-2 col-form-label">Jenis Pembelian</label>
                                <div class="col-sm-4">
                                    <select name="jenis_pembelian" id="jenis_pembelian"
                                        class="form-control select-jenis">
                                        <option value=""></option>
                                        <option value="Cash Keras"
                                            {{ $data->jenis_pembelian == 'Cash Keras' ? 'selected' : '' }}>Cash Keras
                                        </option>
                                        <option value="Cash Bertahap"
                                            {{ $data->jenis_pembelian == 'Cash Bertahap' ? 'selected' : '' }}>Cash Bertahap
                                        </option>
                                        <option value="Kredit Inhouse"
                                            {{ $data->jenis_pembelian == 'Kredit Inhouse' ? 'selected' : '' }}>Kredit
                                            Inhouse</option>
                                        <option value="KPR" {{ $data->jenis_pembelian == 'KPR' ? 'selected' : '' }}>KPR
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="jenis-input Cash_Keras" style="display:none;">
                                <div class="form-group row mt-2">
                                    <label class="col-sm-2 col-form-label">Jumlah Pembayaran</label>
                                    <div class="col-sm-4">
                                        <input type="text" name="jumlah_pembayaran" id="jumlah_pembayaran"
                                            class="form-control format-number" value="{{ $data->jumlah_pembayaran }}">
                                    </div>
                                </div>
                            </div>


                            <div class="jenis-input Cash_Bertahap" style="display:none;">
                                <div class="form-group row mt-2">
                                    <label class="col-sm-2 col-form-label">Jumlah Bulan X</label>
                                    <div class="col-sm-4">
                                        <input type="number" name="jumlah_bulan_x" class="form-control"
                                            value="{{ $data->jumlah_bulan_x }}">
                                    </div>
                                </div>
                            </div>


                            <div class="jenis-input Kredit_Inhouse" style="display:none;">
                                <div class="form-group row mt-2">
                                    <label class="col-sm-2 col-form-label">Cicilan Per Bulan</label>
                                    <div class="col-sm-4">
                                        <input type="text" name="cicil_perbulan" id="cicil_perbulan"
                                            class="form-control format-number" value="{{ $data->cicil_perbulan }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Tenor Per Bulan</label>
                                    <div class="col-sm-4">
                                        <input type="number" name="tenor_cicilan" class="form-control"
                                            value="{{ $data->tenor_cicilan }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Jatuh Tempo</label>
                                    <div class="col-sm-4">
                                        <input type="date" name="jatuh_tempo" class="form-control"
                                            value="{{ $data->jatuh_tempo }}">
                                    </div>
                                </div>
                            </div>

                            {{-- <div class="jenis-input kpr" style="display:none;">
                    <div class="form-group row mt-2">
                        <label class="col-sm-2 col-form-label">Bank KPR</label>
                        <div class="col-sm-4">
                            <select name="id_bank" class="form-control select-bank">
                                <option value="">-- Pilih Bank --</option>
                                @foreach ($bankList as $bank)
                                    <option value="{{ $bank->id }}" {{ $data->id_bank == $bank->id ? 'selected' : '' }}>{{ $bank->bank }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div> --}}

                            <div class="text-end justify-content-between">
                                <a href="{{ route('pengajuan-hold.index') }}"
                                    class="btn btn-sm btn-secondary">Kembali</a>
                                <button type="submit" class="btn btn-sm btn-primary" id="submitBtn">
                                    <span class="spinner-border spinner-border-sm mx-1 d-none" role="status"
                                        aria-hidden="true"></span>
                                    <span class="button-text">Proses Verifikasi</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection
@push('scripts')
    <script>
        var audio = new Audio('{{ asset('audio/notification.ogg') }}');

        $(document).ready(function() {
            $('.select-status').select2({
                theme: "bootstrap4",
                minimumResultsForSearch: Infinity,
            });

            $('.select-jenis').select2({
                theme: "bootstrap4",
                placeholder: 'Pilih Jenis Pembelian',
                minimumResultsForSearch: Infinity,
            });

            $('.select-bank').select2({
                theme: "bootstrap4",
                placeholder: 'Pilih Bank',
                minimumResultsForSearch: Infinity,
            });

            function toggleJenisPembelian() {
                const stt = $('#stt_reg').val();

                if (stt == 2) {
                    $('#jenis-pembelian').attr('required', true);
                } else {
                    $('#jenis-pembelian').removeAttr('required');
                }
            }

            toggleJenisInput();

            $('#stt_reg').on('change', function() {
                toggleJenisPembelian();
            });

            $('#formData').on('submit', function(e) {
                e.preventDefault();
                let submitBtn = $('#submitBtn');
                let spinner = submitBtn.find('.spinner-border');
                let btnText = submitBtn.find('.button-text');

                spinner.removeClass('d-none');
                btnText.text('Menyimpan...');
                submitBtn.prop('disabled', true);

                let id = $('#primary_id').val();

                let cicilanValue = $('#cicil_perbulan').val();
                if (cicilanValue) {
                    cicilanValue = cicilanValue.replace(/\./g, '').replace(/[^\d]/g, '');
                    $('#cicil_perbulan').val(cicilanValue);
                }

                let jumlahPembayaranValue = $('#jumlah_pembayaran').val();
                if (jumlahPembayaranValue) {
                    jumlahPembayaranValue = jumlahPembayaranValue.replace(/\./g, '').replace(/[^\d]/g, '');
                    $('#jumlah_pembayaran').val(jumlahPembayaranValue);
                }

                let formData = new FormData(this);

                let url = `{{ route('pengajuan-hold.verifikasi.simpan', ':id') }}`.replace(':id', id);
                let method = 'POST';

                $.ajax({
                    url: url,
                    method: method,
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(res) {
                        if (res.status == 'success') {
                            sessionStorage.setItem('success',
                                'Data berhasil diproses verifikasi!');
                            window.location.href = "{{ route('pengajuan-hold.index') }}";
                        } else {
                            audio.play();
                            toastr.error("Terjadi kesalahan!", "GAGAL!", {
                                progressBar: true,
                                timeOut: 3500,
                                positionClass: "toast-bottom-right",
                            });
                        }
                        spinner.addClass('d-none');
                        btnText.text('Simpan');
                        submitBtn.prop('disabled', false);
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
                            toastr.error("Terjadi kesalahan!", "GAGAL!", {
                                progressBar: true,
                                timeOut: 3500,
                                positionClass: "toast-bottom-right",
                            });
                        }

                        spinner.addClass('d-none');
                        btnText.text('Proses Verifikasi');
                        submitBtn.prop('disabled', false);
                    }
                });
            });

        });

        function toggleJenisInput() {
            let jenis = $('#jenis_pembelian').val();
            $('.jenis-input').hide();
            if (jenis) {
                let className = jenis.replace(/\s+/g, '_');
                $('.' + className).show();
            }
        }

        $(document).ready(function() {
            toggleJenisInput();
            $('#jenis_pembelian').on('change', function() {
                toggleJenisInput();
            });
        });
    </script>
@endpush
