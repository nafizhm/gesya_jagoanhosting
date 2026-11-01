@extends('admin.layout_admin')
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header p-3">
                                <div class="d-flex align-content-center justify-content-between">
                                    <h3 class="font-weight-bold text-xl">Tambah BAST</h3>
                                </div>
                            </div>
                            <div class="card-body">

                                <form id="form-tambah-bast" action="{{ route('bast.store') }}" method="POST">
                                    @csrf
                                    <div class="form-group row mb-3">
                                        <label for="hari" class="col-sm-2 col-form-label">Hari</label>
                                        <div class="col-sm-2">
                                            <input type="text" class="form-control"
                                                id="hari" name="hari" value="{{ old('hari') }}" readonly>
                                        </div>

                                        <label for="tanggal_bast" class="col-sm-1 col-form-label">Tanggal</label>
                                        <div class="col-sm-2">
                                            <input type="date"
                                                class="form-control"
                                                id="tanggal_bast" name="tanggal_bast"
                                                value="{{ old('tanggal_bast', $tanggalSekarang) }}">
                                        </div>
                                    </div>

                                    <div class="form-group row mb-3">
                                        <label for="no_bast" class="col-sm-2 col-form-label">No. BAST</label>
                                        <div class="col-sm-4">
                                            <input type="text"
                                                class="form-control" id="no_bast"
                                                name="no_bast" readonly>
                                        </div>
                                    </div>

                                    <hr>

                                    <div class="form-group row mb-3">
                                        <label class="col-sm-2 col-form-label">Nama Customer</label>
                                        <div class="col-sm-4">
                                            <select
                                                class="form-control select-customer"
                                                name="nama_customer" id="nama_customer">
                                                <option value=""></option>
                                                @foreach ($customers as $ctm)
                                                    <option value="{{ $ctm->id_customer }}"
                                                        {{ old('nama_customer') == $ctm->id_customer ? 'selected' : '' }}>
                                                        {{ $ctm->nama_lengkap }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row mb-3">
                                        <label for="no_ktp" class="col-sm-2 col-form-label">No. KTP</label>
                                        <div class="col-sm-3">
                                            <input type="text" class="form-control"
                                                id="no_ktp" name="no_ktp" value="{{ old('no_ktp') }}" readonly>
                                        </div>
                                    </div>

                                    <div class="form-group row mb-3">
                                        <label for="jenis_kelamin" class="col-sm-2 col-form-label">Jenis Kelamin</label>
                                        <div class="col-sm-2">
                                            <input type="text"
                                                class="form-control"
                                                id="jenis_kelamin" name="jenis_kelamin" value="{{ old('jenis_kelamin') }}"
                                                readonly>
                                        </div>
                                    </div>

                                    <div class="form-group row mb-3">
                                        <label for="tempat_lahir" class="col-sm-2 col-form-label">Tempat Lahir</label>
                                        <div class="col-sm-3">
                                            <input type="text"
                                                class="form-control"
                                                id="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir') }}"
                                                readonly>
                                        </div>

                                        <label for="tanggal_lahir" class="col-sm-2 col-form-label">Tanggal Lahir</label>
                                        <div class="col-sm-2">
                                            <input type="date"
                                                class="form-control"
                                                id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}"
                                                readonly>
                                        </div>
                                    </div>

                                    <div class="form-group row mb-3">
                                        <label for="domisili" class="col-sm-2 col-form-label">Domisili</label>
                                        <div class="col-sm-6">
                                            <input type="text"
                                                class="form-control"
                                                id="domisili" name="domisili" value="{{ old('domisili') }}" readonly>
                                        </div>
                                    </div>

                                    <div class="form-group row mb-3">
                                        <label for="alamat_ktp" class="col-sm-2 col-form-label">Alamat KTP</label>
                                        <div class="col-sm-6">
                                            <input type="text"
                                                class="form-control"
                                                id="alamat_ktp" name="alamat_ktp" value="{{ old('alamat_ktp') }}"
                                                readonly>
                                        </div>
                                    </div>

                                    <div class="form-group row mb-3">
                                        <label for="pekerjaan" class="col-sm-2 col-form-label">Pekerjaan</label>
                                        <div class="col-sm-6">
                                            <input type="text"
                                                class="form-control"
                                                id="pekerjaan" name="pekerjaan" value="{{ old('pekerjaan') }}" readonly>
                                        </div>
                                    </div>

                                    <div class="form-group row mb-3">
                                        <label for="warga_negara" class="col-sm-2 col-form-label">Warga Negara</label>
                                        <div class="col-sm-3">
                                            <input type="text"
                                                class="form-control"
                                                id="warga_negara" name="warga_negara" value="{{ old('warga_negara') }}"
                                                readonly>
                                        </div>
                                    </div>

                                    <div class="form-group row mb-3">
                                        <label for="no_telp" class="col-sm-2 col-form-label">No. Telp</label>
                                        <div class="col-sm-2">
                                            <input type="text"
                                                class="form-control"
                                                id="no_telp" name="no_telp" value="{{ old('no_telp') }}" readonly>
                                        </div>

                                        <label for="email" class="col-sm-1 col-form-label">Email</label>
                                        <div class="col-sm-4">
                                            <input type="text"
                                                class="form-control" id="email"
                                                name="email" value="{{ old('email') }}" readonly>
                                        </div>
                                    </div>

                                    <div class="form-group row mb-3">
                                        <label for="no_npwp" class="col-sm-2 col-form-label">No. NPWP</label>
                                        <div class="col-sm-6">
                                            <input type="text"
                                                class="form-control"
                                                id="no_npwp" name="no_npwp" value="{{ old('no_npwp') }}" readonly>
                                        </div>
                                    </div>

                                    <hr>

                                    <div class="form-group row mb-3">
                                        <label for="lokasi_rumah" class="col-sm-2 col-form-label">Lokasi Rumah</label>
                                        <div class="col-sm-4">
                                            <input type="text"
                                                class="form-control"
                                                id="lokasi_rumah" name="lokasi_rumah" value="{{ old('lokasi_rumah') }}"
                                                readonly>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="control-label col-md-2">Klaster/Blok</label>
                                        <div class="col-md-2">
                                            <input name="blok" id="blok" class="form-control" type="text"
                                                readonly>
                                        </div>

                                        <label class="control-label col-md-1">Tipe Rumah</label>
                                        <div class="col-md-2">
                                            <input name="tipe_rumah" id="tipe_rumah" class="form-control" type="text"
                                                readonly>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="control-label col-md-2">Luas Tanah</label>
                                        <div class="col-md-2">
                                            <input name="luas_tanah" id="luas_tanah" class="form-control" type="text"
                                                readonly>
                                        </div>

                                        <label class="control-label col-md-1">Luas Bangunan</label>
                                        <div class="col-md-2">
                                            <input name="luas_bangunan" id="luas_bangunan" class="form-control"
                                                type="text" readonly>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="control-label col-md-2"></label>
                                        <div class="col-md-3">
                                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                                <span class="spinner-border spinner-border-sm me-2 d-none" role="status"
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
        var audio = new Audio('{{ asset('audio/notification.ogg') }}');

        document.addEventListener("DOMContentLoaded", function() {
            const tanggalInput = document.getElementById("tanggal_bast");
            const hariInput = document.getElementById("hari");

            function updateHari() {
                const tanggalValue = tanggalInput.value;
                if (tanggalValue) {
                    const tanggal = new Date(tanggalValue);
                    const options = {
                        weekday: 'long'
                    };
                    const hari = new Intl.DateTimeFormat('id-ID', options).format(tanggal);
                    hariInput.value = hari.charAt(0).toUpperCase() + hari.slice(1);
                } else {
                    hariInput.value = "";
                }
            }

            tanggalInput.addEventListener("change", updateHari);
            updateHari();
        });

        $(document).ready(function() {
            const customerSelect = $('select[name="nama_customer"]');

            customerSelect.select2({
                theme: "bootstrap4",
                placeholder: "Pilih Customer",
            });

            customerSelect.on('change', function() {
                let customerId = $(this).val();

                if (customerId) {
                    fetch(`/admin/transaksi/get-customer-data/${customerId}`)
                        .then(response => response.json())
                        .then(data => {
                            $('#no_ktp').val(data.no_ktp ?? '');
                            $('#jenis_kelamin').val(data.jenis_kelamin ?? '');
                            $('#tempat_lahir').val(data.tempat_lahir ?? '');
                            $('#tanggal_lahir').val(data.tanggal_lahir ?? '');
                            $('#domisili').val(data.domisili ?? '');
                            $('#alamat_ktp').val(data.alamat_ktp ?? '');
                            $('#pekerjaan').val(data.pekerjaan ?? '');
                            $('#no_npwp').val(data.no_npwp ?? '');
                            $('#warga_negara').val(data.warga_negara ?? '');
                            $('#no_telp').val(data.no_telp ?? '');
                            $('#email').val(data.email ?? '');
                            $('#lokasi_rumah').val(data.lokasi_rumah ?? '');

                            $.ajax({
                                url: '{{ route('generateNoBAST') }}',
                                type: 'GET',
                                data: {
                                    lokasi_rumah: data.lokasi_rumah ?? ''
                                },
                                success: function(res) {
                                    $('#no_bast').val(res.no_bast);
                                },
                                error: function(err) {
                                    console.log(err);
                                    alert('Gagal generate nomor BAST');
                                }
                            });

                            $('#blok').val(data.blok ?? '');
                            $('#tipe_rumah').val(data.tipe_rumah ?? '');
                            $('#luas_tanah').val(data.luas_tanah ?? '');
                            $('#luas_bangunan').val(data.luas_bangunan ?? '');
                            $('#daya_listrik').val(data.daya_listrik ?? '');
                            $('#harga_jual').val(data.harga_jual ?? '');

                        })
                        .catch(error => {
                            console.error('Terjadi error:', error);
                        });
                } else {
                    $('#no_ktp').val('');
                    $('#alamat_ktp').val('');
                    $('#blok').val('');
                    $('#tipe_rumah').val('');
                    $('#luas_tanah').val('');
                    $('#luas_bangunan').val('');
                }
            });
        });

        $('#form-tambah-bast').on('submit', function(e) {
            e.preventDefault();

            let submitBtn = $('#submitBtn');
            let spinner = submitBtn.find('.spinner-border');
            let btnText = submitBtn.find('.button-text');

            spinner.removeClass('d-none');
            btnText.text('Menyimpan...');
            submitBtn.prop('disabled', true);

            let form = this;
            let formData = new FormData(form);

            $.ajax({
                url: form.action,
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    audio.play();
                    toastr.success(response.message || "BAST berhasil ditambahkan!", "BERHASIL", {
                        progressBar: true,
                        timeOut: 3500,
                        positionClass: "toast-bottom-right",
                    });
                    setTimeout(() => window.location.href = '{{ route("bast.index") }}', 1000);
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
                        toastr.error(xhr.responseJSON.message || "Gagal menyimpan data.", "GAGAL!", {
                            progressBar: true,
                            timeOut: 3500,
                            positionClass: "toast-bottom-right",
                        });
                    }
                    spinner.addClass('d-none');
                    btnText.text('Simpan');
                    submitBtn.prop('disabled', false);
                }
            });
        });
    </script>
@endpush
