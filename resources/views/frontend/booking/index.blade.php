@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container">
        @php
            $logo = \App\Models\PengaturanMedia::where('jenis_data', 'logo website')->first();
        @endphp

            <center>
                <img class="mt-5 mb-5" src="{{ asset('config_media/' . ($logo->nama_file ?? 'default.png')) }}" style="max-width: 15%">
            </center>

        <section class="content">
            <div class="card">
                <div class="card-header bg-success">
                    <h4 class="fw-bold">Form Data Customer (Booking)</h4>
                </div>

                <!-- /.card-header -->
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" id="myForm" action="{{ route('store.booking') }}">
                        @csrf
                        <div class="form-body">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label col-md-4">Tanggal</label>
                                        <div class="col-md-8">
                                            <input name="tgl_registrasi" id="tgl_registrasi" class="form-control" type="date"
                                                readonly value="{{ date('Y-m-d') }}">
                                            <span class="help-block"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label col-md-4">Nama Lengkap <span
                                                style="color: red;">*</span></label>
                                         <div class="col-md-8">
                                            <input name="nama_lengkap" id="nama_lengkap"
                                                class="form-control" type="text"
                                                value="{{ old('nama_lengkap') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label col-md-4">No. KTP <span style="color: red;">*</span></label>
                                        <div class="col-md-8">
                                            <input name="nik" id="nik"
                                                class="form-control" type="text"
                                                value="{{ old('nik') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label col-md-4">Tempat Lahir <span
                                                style="color: red;">*</span></label>
                                        <div class="col-md-8">
                                            <input name="tempat_lahir" id="tempat_lahir"
                                                class="form-control"
                                                value="{{ old('tempat_lahir') }}" type="text">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label col-md-4">Tanggal Lahir <span
                                                style="color: red;">*</span></label>
                                        <div class="col-md-8">
                                            <input name="tgl_lahir" id="tgl_lahir"
                                                class="form-control"
                                                value="{{ old('tgl_lahir') }}" type="date">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label col-md-4">Jenis Kelamin<span
                                                style="color: red;">*</span></label>
                                        <div class="col-md-8">
                                            <select class="form-control select-jk"
                                                name="jenis_kelamin" id="jenis_kelamin">
                                                <option value=""></option>
                                                <option value="Laki-laki">Laki-laki</option>
                                                <option value="Perempuan">Perempuan</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label col-md-4">No. Telp / WA <span
                                                style="color: red;">*</span></label>
                                        <div class="col-md-8">
                                            <input name="no_telp_customer" id="no_telp_customer"
                                                class="form-control"
                                                value="{{ old('no_telp_customer') }}" type="text">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label col-md-4">Email</label>
                                        <div class="col-md-8">
                                            <input name="email" id="email"
                                                class="form-control"
                                                value="{{ old('email') }}" type="text">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label col-md-4">NPWP</label>
                                        <div class="col-md-8">
                                            <input name="npwp" id="npwp"
                                                class="form-control"
                                                value="{{ old('npwp') }}" type="text">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label col-md-4">No. BPJS Kes</label>
                                        <div class="col-md-8">
                                            <input name="no_bpjs_kes" id="no_bpjs_kes" class="form-control" type="text">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label col-md-4">Pekerjaan</label>
                                        <div class="col-md-8">
                                            <input name="pekerjaan" id="pekerjaan"
                                                class="form-control"
                                                value="{{ old('pekerjaan') }}" type="text">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        <label class="control-label col-md-2">Alamat</label>
                                        <div class="col-md-10">
                                            <input name="alamat" id="alamat"
                                                class="form-control"
                                                value="{{ old('alamat') }}" type="text">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label col-md-4">Status Pernikahan</label>
                                        <div class="col-md-8">
                                            <select class="form-control select-status"
                                                name="status" id="status">
                                                <option value=""></option>
                                                <option value="Belum Menikah">Belum Menikah</option>
                                                <option value="Menikah">Menikah</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div id="pasangan">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="control-label col-md-4">Nama Pasangan</label>
                                            <div class="col-md-8">
                                                <input name="nama_p" id="nama_p" class="form-control" type="text">
                                                <span class="help-block"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="control-label col-md-4">No. KTP Pasangan</label>
                                            <div class="col-md-8">
                                                <input name="nik_p" id="nik_p" class="form-control" type="text">
                                                <span class="help-block"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <hr>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label col-md-4">Nama Saudara</label>
                                        <div class="col-md-8">
                                            <input name="nama_saudara" id="nama_saudara"
                                                class="form-control"
                                                value="{{ old('nama_saudara') }}" type="text">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label col-md-4">No. Telp Saudara</label>
                                        <div class="col-md-8">
                                            <input name="no_telp_saudara" id="no_telp_saudara" class="form-control"
                                                type="text">
                                            <span class="help-block"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label col-md-4">Lokasi Perumahan<span
                                                style="color: red;">*</span></label>
                                        <div class="col-md-8">
                                            <select class="form-control select-lokasi"
                                                name="id_lokasi" id="id_lokasi">
                                                <option value=""></option>
                                                @foreach ($lokasi as $l)
                                                    <option value="{{ $l->id_lokasi }}">{{ $l->nama_kavling }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label col-md-4">Lokasi Blok/Kav<span
                                                style="color: red;">*</span></label>
                                        <div class="col-md-8">
                                            <select name="id_kavling" id="id_kavling"
                                                class="form-control select-kavling"></select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label col-md-4">Harga Rumah</label>
                                        <div class="col-md-8">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Rp.</span>
                                                </div>
                                                <input type="text" name="hrg_jual" id="hrg_jual_display" class="form-control">
                                            </div>
                                            <span class="help-block"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label col-md-4">Marketing<span style="color: red;">*</span></label>
                                        <div class="col-md-8">
                                            <select
                                                class="form-control select-marketing"
                                                name="id_marketing" id="id_marketing">
                                                <option value=""></option>
                                                @foreach ($marketing as $m)
                                                    <option value="{{ $m->id_marketing }}">{{ $m->nama_marketing }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label col-md-4">Marketing Freelance</label>
                                        <div class="col-md-8">
                                            <select
                                                class="form-control select-freelance"
                                                name="id_freelance" id="id_freelance">
                                                <option value=""></option>
                                                @foreach ($freelance as $f)
                                                    <option value="{{ $f->id_freelance }}">{{ $f->nama_freelance }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label col-md-4">Jenis Perumahan</label>
                                        <div class="col-md-8">
                                            <select class="form-control select-jp"
                                                name="jenis_perumahan" id="jenis_perumahan">
                                                <option value="">-- pilih --</option>
                                                <option value="Subsidi">Subsidi</option>
                                                <option value="Komersil">Komersil</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label col-md-4">Jenis Pembayaran</label>
                                        <div class="col-md-8">
                                            <select
                                                class="form-control select-pembayaran"
                                                name="jenis_pembelian" id="jenis_pembelian">
                                                <option value="">-- pilih --</option>
                                                <option value="Cash Keras">Cash Keras</option>
                                                <option value="Cash Bertahap">Cash Bertahap</option>
                                                <option value="KPR">KPR</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label col-md-4">Booking Fee</label>
                                        <div class="col-md-8">
                                            <input name="booking_fee" id="booking_fee" class="form-control" type="text">
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <hr>

                        <div class="form-group row">
                            <label class="control-label col-md-3">Foto Pemohon</label>
                            <div class="col-md-4">
                                <input name="foto_pemohon" id="foto_pemohon" type="file" accept=".jpg,.jpeg,.png"
                                    class=""
                                    onchange="handleFileChange(this, 'preview_foto_pemohon')">
                                <div id="preview_foto_pemohon" class="mt-2 d-none">
                                    <button type="button" class="btn btn-sm btn-primary"
                                        onclick="showPreview(this)">View</button>
                                    <button type="button" class="btn btn-sm btn-danger"
                                        onclick="clearFile('foto_pemohon', 'preview_foto_pemohon')">Hapus</button>

                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-md-3">Foto KTP</label>
                            <div class="col-md-4">
                                <input name="foto_ktp" id="foto_ktp" type="file" accept=".jpg,.jpeg,.png"
                                    class=""
                                    onchange="handleFileChange(this, 'preview_foto_ktp')">
                                <div id="preview_foto_ktp" class="mt-2 d-none">
                                    <button type="button" class="btn btn-sm btn-primary"
                                        onclick="showPreview(this)">View</button>
                                    <button type="button" class="btn btn-sm btn-danger"
                                        onclick="clearFile('foto_ktp', 'preview_foto_ktp')">Hapus</button>

                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-md-3">Foto NPWP</label>
                            <div class="col-md-4">
                                <input name="foto_npwp" id="foto_npwp" type="file" accept=".jpg,.jpeg,.png"
                                    class=""
                                    onchange="handleFileChange(this, 'preview_foto_npwp')">
                                <div id="preview_foto_npwp" class="mt-2 d-none">
                                    <button type="button" class="btn btn-sm btn-primary"
                                        onclick="showPreview(this)">View</button>
                                    <button type="button" class="btn btn-sm btn-danger"
                                        onclick="clearFile('foto_npwp', 'preview_foto_npwp')">Hapus</button>

                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-md-3">Foto KK</label>
                            <div class="col-md-4">
                                <input name="foto_kk" id="foto_kk" type="file" accept=".jpg,.jpeg,.png"
                                    class=""
                                    onchange="handleFileChange(this, 'preview_foto_kk')">
                                <div id="preview_foto_kk" class="mt-2 d-none">
                                    <button type="button" class="btn btn-sm btn-primary"
                                        onclick="showPreview(this)">View</button>
                                    <button type="button" class="btn btn-sm btn-danger"
                                        onclick="clearFile('foto_kk', 'preview_foto_kk')">Hapus</button>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-md-3">Foto BPJS</label>
                            <div class="col-md-4">
                                <input name="foto_bpjs" id="foto_bpjs" type="file" accept=".jpg,.jpeg,.png"
                                    class=""
                                    onchange="handleFileChange(this, 'preview_foto_bpjs')">
                                <div id="preview_foto_bpjs" class="mt-2 d-none">
                                    <button type="button" class="btn btn-sm btn-primary"
                                        onclick="showPreview(this)">View</button>
                                    <button type="button" class="btn btn-sm btn-danger"
                                        onclick="clearFile('foto_bpjs', 'preview_foto_bpjs')">Hapus</button>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-md-3">Foto KTP Pasangan</label>
                            <div class="col-md-4">
                                <input name="foto_ktp_p" id="foto_ktp_p" type="file" accept=".jpg,.jpeg,.png"
                                    class="" onchange="handleFileChange(this, 'preview_foto_ktp_p')">
                                <div id="preview_foto_ktp_p" class="mt-2 d-none">
                                    <button type="button" class="btn btn-sm btn-primary"
                                        onclick="showPreview(this)">View</button>
                                    <button type="button" class="btn btn-sm btn-danger"
                                        onclick="clearFile('foto_ktp_p', 'preview_foto_ktp_p')">Hapus</button>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-md-3">Bukti Transfer</label>
                            <div class="col-md-4">
                                <input name="file_bukti" id="file_bukti" type="file"
                                    class=""
                                    onchange="handleFileChange(this, 'preview_file_bukti')">
                                <div id="preview_file_bukti" class="mt-2 d-none">
                                    <button type="button" class="btn btn-sm btn-primary"
                                        onclick="showPreview(this)">View</button>
                                    <button type="button" class="btn btn-sm btn-danger"
                                        onclick="clearFile('file_bukti', 'preview_file_bukti')">Hapus</button>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-sm-12 text-right">
                                <button type="submit" id="submit-btn" class="btn btn-primary">
                                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                    <span class="btn-text">Kirim Data</span>
                                </button>
                            </div>
                        </div>

                    </form>

                    <div class="modal fade" id="previewModal" tabindex="-1" role="dialog"
                        aria-labelledby="previewModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Preview File</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span>&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body text-center">
                                    <img id="modalPreviewImage" class="img-fluid" alt="Preview">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            @endsection

            @push('scripts')
                <script type="text/javascript">
                    $(document).ready(function() {
                        $('#id_lokasi').on('change', function() {
                            const id = $(this).val();

                            $('#id_kavling').empty().append('<option value="">Loading...</option>');

                            $.get('/get-kavling/' + id, function(data) {
                                $('#id_kavling').empty().append('<option value=""></option>');
                                if (data.length > 0) {
                                    data.forEach(function(item) {
                                        $('#id_kavling').append(
                                            `<option value="${item.id_kavling}">${item.kode_kavling}</option>`
                                        );
                                    });
                                } else {
                                    $('#id_kavling').append(
                                        '<option value="">Tidak ada kavling tersedia</option>');
                                }
                            });
                        });
                    });

                    function formatAngkaRibuan(angka) {
                        return angka.replace(/\D/g, '')
                            .replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                    }

                    $(document).on('input', '#booking_fee', function() {
                        let nilai = $(this).val();
                        let terformat = formatAngkaRibuan(nilai);
                        $(this).val(terformat);
                    });

                    $(document).on('input', '#hrg_jual_display', function() {
                        let nilai = $(this).val();
                        let terformat = formatAngkaRibuan(nilai);
                        $(this).val(terformat);
                    });

                    function handleFileChange(input, previewId) {
                        const file = input.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                const previewDiv = document.getElementById(previewId);
                                const viewBtn = previewDiv.querySelector('button.btn-primary');
                                viewBtn.setAttribute('data-src', e.target.result);

                                input.style.display = 'none';
                                previewDiv.classList.remove('d-none');
                            };
                            reader.readAsDataURL(file);
                        }
                    }

                    function showPreview(modalIdOrEvent) {
                        let src = '';
                        if (typeof modalIdOrEvent === 'string') {
                            src = document.querySelector(`[onclick="showPreview('${modalIdOrEvent}')"]`).getAttribute('data-src');
                        } else {
                            src = modalIdOrEvent.getAttribute('data-src');
                        }

                        document.getElementById('modalPreviewImage').src = src;
                        const myModal = new bootstrap.Modal(document.getElementById('previewModal'));
                        myModal.show();
                    }


                    function clearFile(inputId, previewId) {
                        const input = document.getElementById(inputId);
                        input.value = '';
                        input.style.display = 'block';
                        document.getElementById(previewId).classList.add('d-none');
                        document.getElementById('preview_image').src = '';
                    }


                    $(document).ready(function() {
                        var audio = new Audio('{{ asset('audio/notification.ogg') }}');

                        $('#myForm').on('submit', function(e) {
                        e.preventDefault();

                        const submitBtn = $('#submit-btn');
                        const spinner = submitBtn.find('.spinner-border');
                        const btnText = submitBtn.find('.btn-text');

                        spinner.removeClass('d-none');
                        btnText.text('Mengirim...');
                        submitBtn.prop('disabled', true);

                        $('.is-invalid').removeClass('is-invalid');
                        $('.invalid-feedback').remove();

                        let formData = new FormData(this);

                        $.ajax({
                            url: "{{ route('store.booking') }}",
                            method: 'POST',
                            data: formData,
                            contentType: false,
                            processData: false,
                            success: function(response) {
                                window.location.href = response.redirect; 
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
                                        input.parent().append(
                                            '<span class="invalid-feedback" role="alert"><strong>' +
                                            val[0] + '</strong></span>'
                                        );
                                    });

                                    spinner.addClass('d-none');
                                    btnText.text('Kirim Data');
                                    submitBtn.prop('disabled', false);
                                }
                            }
                        });
                    });

                        let today = new Date();
                        let tanggal = today.toISOString().split('T')[0];
                        $('#tgl_registrasi').val(tanggal);

                        $('.select-lokasi').select2({
                            theme: "bootstrap4",
                            minimumResultsForSearch: Infinity,
                            placeholder: "Pilih Lokasi",
                        });
                        $('.select-jk').select2({
                            theme: "bootstrap4",
                            minimumResultsForSearch: Infinity,
                            placeholder: "Pilih Jenis Kelamin",
                        });
                        $('.select-kavling').select2({
                            theme: "bootstrap4",
                        });
                        $('.select-marketing').select2({
                            theme: "bootstrap4",
                            placeholder: "Pilih Marketing",
                        });

                        $('.select-freelance').select2({
                            theme: "bootstrap4",
                            placeholder: "Pilih Freelance",
                        });

                        $('.select-jp').select2({
                            theme: "bootstrap4",
                            minimumResultsForSearch: Infinity,
                            placeholder: "Pilih Jenis Perumahan",
                        });

                        $('.select-pembayaran').select2({
                            theme: "bootstrap4",
                            minimumResultsForSearch: Infinity,
                            placeholder: "Pilih Pembayaran",
                        });

                        $('.select-status').select2({
                            theme: "bootstrap4",
                            placeholder: "Pilih Status",
                            minimumResultsForSearch: Infinity,
                        });
                    });

                    $(document).ready(function() {
                        $('.select-lokasi').select2({
                            theme: "bootstrap4",
                            placeholder: "Pilih Lokasi",
                        });

                        $('.select-kavling').select2({
                            theme: "bootstrap4",
                            placeholder: "Pilih Kavling",
                        });

                        const routeGetKavling = "{{ route('booking.getKavling', ':id') }}";
                        const routeGetHarga = "{{ route('booking.getHargaKavling', ':id') }}";

                        $('#id_lokasi').on('change', function() {
                            let idLokasi = $(this).val();
                            $('#id_kavling').html('<option value="">Loading...</option>').trigger('change');
                            $('#hrg_jual').val('');
                            $('#hrg_jual_display').val('');

                            if (idLokasi) {
                                const urlKavling = routeGetKavling.replace(':id', idLokasi);
                                $.get(urlKavling, function(data) {
                                    let options = '<option value=""></option>';
                                    data.forEach(function(item) {
                                        options +=
                                            `<option value="${item.id_kavling}">${item.kode_kavling}</option>`;
                                    });
                                    $('#id_kavling').html(options).trigger('change');
                                });
                            }
                        });

                        $('#id_kavling').on('change', function() {
                            let idKavling = $(this).val();

                            if (idKavling) {
                                const urlHarga = routeGetHarga.replace(':id', idKavling);
                                $.get(urlHarga, function(data) {
                                    console.log(data);
                                    $('#hrg_jual_display').val(formatRupiah(data.hrg_jual));
                                });
                            } else {
                                $('#hrg_jual_display').val('');
                            }
                        });
                    });


                    function formatRupiah(angka) {
                        if (!angka) return '';
                        return angka.toString().replace(/\D/g, '')
                            .replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                    }

                    $('#status').on('change', function() {
                        if ($(this).val() === 'Menikah') {
                            $('#pasangan').show();
                            $('#nama_p, #nik_p').prop('required', true);
                        } else {
                            $('#pasangan').hide();
                            $('#nama_p, #nik_p').prop('required', false);
                        }
                    }).trigger('change');
                </script>
            @endpush
