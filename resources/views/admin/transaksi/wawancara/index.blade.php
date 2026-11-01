@extends('admin.layout_admin')
@section('content')
    <style>
        .select2-container .select2-selection--single {
            height: 38px;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            padding: 6px 2px;
            font-size: 1rem;
            background-color: #fff;
        }

        .select2-container .select2-selection__arrow {
            display: none;
        }

        .select2-container .select2-selection--single:focus {
            outline: none;
        }

        .select2-container .select2-selection__rendered {
            line-height: 30px;
        }
    </style>



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
                                    <h3 class="font-weight-bold text-xl">Data Wawancara</h3>
                                    <div class="d-flex align-items-center" style="gap: 3px">
                                        @if (isset($permissions['tambah']) && $permissions['tambah'] == 1)
                                            <button class="btn btn-info btn-sm" data-toggle="modal"
                                                data-target="#modalForm"><i class="fas fa-plus"></i>
                                                Tambah Data Wawancara</button>
                                        @endif

                                        <button type="button" class="btn border px-3 py-1 btn-xs" onclick="reloadTable()">
                                            Reload
                                        </button>

                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered table-striped data-table">
                                    <thead>
                                        <tr>
                                            <th width="5%">No</th>
                                            <th>Nama Customer</th>
                                            <th>Lokasi Rumah</th>
                                            <th>Tgl Wawancara</th>
                                            <th>Nama Bank</th>
                                            <th>Catatan</th>
                                            <th>Status</th>
                                            <th class="text-center" width="15%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
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

    <!-- Modal -->
    <div class="modal fade" id="modalForm" tabindex="-1" role="dialog" data-focus="false" aria-labelledby="modalFormLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title" id="modalFormLabel">Form Tambah Wawancara</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formData">
                    @csrf
                    <input type="hidden" id="primary_id" name="primary_id">
                    <div class="modal-body">

                        <div class="form-group row">
                            <label for="hari_wawancara" class="col-sm-2 col-form-label">Hari</label>
                            <div class="col-sm-3">
                                <input type="text" name="hari_wawancara" id="hari_wawancara" class="form-control"
                                    readonly>
                            </div>
                            <label for="tgl_wawancara" class="col-sm-2 col-form-label">Tanggal</label>
                            <div class="col-sm-3">
                                <input type="date" name="tgl_wawancara" id="tgl_wawancara" class="form-control">
                            </div>
                        </div>


                        <div class="form-group row mb-3">
                            <label for="id_customer" class="col-sm-2 col-form-label">Nama Customer</label>
                            <div class="col-sm-8">
                                <select class="form-control select2" id="nama_nasabah" name="id_customer">
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="no_ktp" class="col-sm-2 col-form-label">No Ktp</label>
                            <div class="col-sm-3">
                                <input type="text" name="no_ktp" id="no_ktp" class="form-control" disabled>
                            </div>
                            <label for="alamat" class="col-sm-2 col-form-label">Alamat Ktp</label>
                            <div class="col-sm-3">
                                <input type="text" name="alamat" id="alamat" class="form-control" disabled>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="kode_kavling" class="col-sm-2 col-form-label">Klaster/Blok</label>
                            <div class="col-sm-3">
                                <input type="text" name="kode_kavling" id="kode_kavling" class="form-control" disabled>
                                <input type="hidden" name="id_kavling" id="id_kavling">
                            </div>
                            <label for="tipe_bangunan" class="col-sm-2 col-form-label">Tipe Rumah</label>
                            <div class="col-sm-3">
                                <input type="text" name="tipe_bangunan" id="tipe_bangunan" class="form-control"
                                    disabled>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="luas_tanah" class="col-sm-2 col-form-label">Luas Tanah</label>
                            <div class="col-sm-3">
                                <input type="text" name="luas_tanah" id="luas_tanah" class="form-control" disabled>
                            </div>
                            <label for="luas_bangunan" class="col-sm-2 col-form-label">LB</label>
                            <div class="col-sm-3">
                                <input type="text" name="luas_bangunan" id="luas_bangunan" class="form-control"
                                    disabled>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="id_marketing" class="col-sm-2 col-form-label">Marketing</label>
                            <div class="col-sm-8">
                                <input type="text" id="nama_marketing" name="nama_marketing" class="form-control"
                                    disabled>
                                <input type="hidden" id="id_marketing" name="id_marketing">
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="id_bank" class="col-sm-2 col-form-label">Nama Bank</label>
                            <div class="col-sm-4">
                                <select class="form-control select2" id="id_bank" name="id_bank">
                                    <option value="">-- Pilih Bank --</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="catatan_kekurangan" class="col-sm-2 col-form-label">Catatan Wawancara</label>
                            <div class="col-sm-8">
                                <textarea id="catatan_kekurangan" name="catatan_kekurangan" class="form-control summernote"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary " id="submitBtn">
                            <span class="spinner-border spinner-border-sm me-2 d-none" role="status"
                                aria-hidden="true"></span>
                            <span class="button-text">Simpan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" aria-labelledby="modalFormLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false" data-focus="false">
    @endsection
    @push('scripts')
        <script>
            var permissions = @json($permissions);
            var showActionColumn = (permissions['edit'] == 1 || permissions['hapus'] == 1);

            $(document).ready(function() {
                $('.summernote').summernote({
                    height: 200,
                    placeholder: 'Tulis catatan wawancara di sini...',
                    toolbar: [
                        ['style', ['bold', 'italic', 'underline', 'clear']],
                        ['font', ['strikethrough']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['insert', ['link']],
                        ['view', ['codeview']]
                    ]
                });
            });

            function modalAction(url = '') {
                $('#myModal').load(url, function() {
                    $('#myModal').modal('show');

                    var audio = new Audio('{{ asset('audio/notification.ogg') }}');

                    $('#form-sp3k').on('submit', function(e) {
                        e.preventDefault();

                        let submitBtn = $('#submitBtnEdit');
                        let spinner = submitBtn.find('.spinner-border');
                        let btnText = submitBtn.find('.button-text');

                        spinner.removeClass('d-none');
                        btnText.text('Menyimpan...');
                        submitBtn.prop('disabled', true);

                        let idWawancara = $('input[name="id_wawancara"]').val();
                        let url = '{{ route('wawancara.sp3k', ':id') }}';
                        url = url.replace(':id', idWawancara);

                        let formData = new FormData(this);

                        $('.is-invalid').removeClass('is-invalid');
                        $('.invalid-feedback').remove();

                        $.ajax({
                            url: url,
                            method: 'POST',
                            data: formData,
                            contentType: false,
                            processData: false,
                            success: function() {
                                $('#myModal').modal('hide');
                                audio.play();
                                toastr.success("Data berhasil di perbarui!", "BERHASIL", {
                                    progressBar: true,
                                    timeOut: 3500,
                                    positionClass: "toast-bottom-right",
                                });
                                $('.data-table').DataTable().ajax.reload();
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
                                    toastr.error(
                                        'Terjadi Kesalahan Pada Server! Coba beberapa saat lagi.',
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

                    $(document).ready(function() {
                        $('.select-notaris').select2({
                            placeholder: "Pilih Notaris",
                        });
                    });
                });
            }

            function getHariIndonesia(date) {
                const hari = [
                    'Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'
                ];
                const tanggal = new Date(date);
                return hari[tanggal.getDay()];
            }

            const today = new Date().toISOString().split('T')[0];
            document.getElementById('tgl_wawancara').value = today;
            document.getElementById('hari_wawancara').value = getHariIndonesia(today);

            document.getElementById('tgl_wawancara').addEventListener('change', function() {
                const selectedDate = this.value;
                document.getElementById('hari_wawancara').value = getHariIndonesia(selectedDate);
            });

            let nasabahTerpilih = [];

            $(document).ready(function() {
                $('#nama_nasabah').select2({
                    ajax: {
                        url: '{{ route('cari.nasabah') }}',
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                q: params.term,
                                exclude: nasabahTerpilih
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data.map(function(item) {
                                    return {
                                        id: item.id_customer,
                                        text: item.nama_lengkap + ' (' + item.kode_customer + ')',
                                    };
                                })
                            };
                        },
                        cache: true
                    },
                    minimumInputLength: 0,
                });

                $('#id_bank').select2({
                    ajax: {
                        url: '{{ route('bank.list') }}',
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                q: params.term
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data.map(function(item) {
                                    return {
                                        id: item.id_bank,
                                        text: item.bank,
                                    };
                                })
                            };
                        },
                        cache: true
                    },
                    minimumInputLength: 0,
                    placeholder: 'Pilih Bank',
                    allowClear: true,
                    templateResult: function(data) {
                        if (data.loading) return data.text;
                        if (data.id === '') return data.text;
                        return $('<span>' + data.text + '</span>');
                    },
                    templateSelection: function(data) {
                        if (data.id === '') return data.text;
                        return data.text;
                    }
                });

                $('#nama_nasabah').on('change', function() {
                    var nasabahId = $(this).val();

                    if (nasabahId) {
                        $.ajax({
                            url: '/admin/transaksi/wawancara/' + nasabahId + '/details',
                            method: 'GET',
                            success: function(data) {
                                $('input[name="no_ktp"]').val(data.no_ktp);
                                $('input[name="alamat"]').val(data.alamat);
                                $('input[name="tipe_bangunan"]').val(data.tipe_bangunan);
                                $('input[name="luas_tanah"]').val(data.luas_tanah);
                                $('input[name="luas_bangunan"]').val(data.luas_bangunan);
                                $('input[name="nama_marketing"]').val(data.nama_marketing);

                                if (!$('#primary_id').val()) {
                                    if (data.id_bank && data.bank) {
                                        let newOption = new Option(data.bank, data.id_bank, true,
                                            true);
                                        $('#id_bank').empty().append(newOption).trigger('change');
                                    } else {
                                        $('#id_bank').val('').trigger('change');
                                    }
                                } else {

                                }

                                $('input[name="kode_kavling"]').val(data.lokasi_kav_blok);
                                $('input[name="id_kavling"]').val(data.id_kavling);
                                $('input[name="id_marketing"]').val(data.id_marketing);
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data nasabah tidak ditemukan!',
                                });
                            }
                        });
                    } else {
                        $('input[name="no_ktp"]').val('');
                        $('input[name="alamat"]').val('');
                        $('input[name="tipe_bangunan"]').val('');
                        $('input[name="luas_tanah"]').val('');
                        $('input[name="luas_bangunan"]').val('');
                        $('input[name="nama_marketing"]').val('');
                        $('input[name="kode_kavling"]').val('');
                        $('input[name="id_kavling"]').val('');
                        $('input[name="id_marketing"]').val('');
                        $('#id_bank').val('').trigger('change');
                    }
                });
            });


            function reloadTable() {
                $('#table').DataTable().ajax.reload();
            }

            var audio = new Audio('{{ asset('audio/notification.ogg') }}');

            $(function() {
                var table = $('.data-table').DataTable({
                    processing: false,
                    serverSide: true,
                    ordering: false,
                    responsive: true,
                    ajax: "{{ route('wawancara.index') }}",
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false,
                            className: 'text-center'
                        },
                        {
                            data: 'nasabah.nama_lengkap',
                            name: 'nasabah.nama_lengkap',
                            orderable: false,
                            searchable: true
                        },
                        {
                            data: 'id_kavling',
                            name: 'id_kavling',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'tgl_wawancara',
                            name: 'tgl_wawancara',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'id_bank',
                            name: 'id_bank',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'catatan_kekurangan',
                            name: 'catatan_kekurangan',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'stt_wawancara',
                            name: 'stt_wawancara',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            visible: showActionColumn
                        }
                    ],
                    columnDefs: [{
                        targets: 0,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    }, ]
                });
            });

            $('#modalForm').on('show.bs.modal', function() {
                if (!$('#primary_id').val()) {
                    $('#formData')[0].reset();
                    $('#primary_id').val('');
                    $('#nama_nasabah').val('').trigger('change').prop('disabled', false);
                    $('#tgl_wawancara').prop('disabled', false);
                    $('#id_bank').val('').trigger('change');
                    $('#catatan_kekurangan').summernote('code', '');
                    $('#modalFormLabel').text('Form Tambah Wawancara');
                }
            });

            $('#modalForm').on('hidden.bs.modal', function() {
                let hari = $('#hari_wawancara').val();
                let tanggal = $('#tgl_wawancara').val();

                $('#formData')[0].reset();
                $('#primary_id').val('');

                $('#tgl_wawancara').val(tanggal).prop('disabled', false);
                $('#hari_wawancara').val(hari);
                $('#nama_nasabah').val('').trigger('change').prop('disabled', false);

                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();

                $('#kode_kavling').val('');
                $('#id_kavling').val('');
                $('#tipe_bangunan').val('');
                $('#luas_tanah').val('');
                $('#luas_bangunan').val('');
                $('#nama_marketing').val('');
                $('#id_marketing').val('');
                $('#id_bank').val('').trigger('change');
                $('#catatan_kekurangan').summernote('code', '');

                let submitBtn = $('#submitBtn');
                let spinner = submitBtn.find('.spinner-border');
                let btnText = submitBtn.find('.button-text');

                spinner.addClass('d-none');
                btnText.text('Simpan');
                submitBtn.prop('disabled', false);
            });

            $('#formData').on('submit', function(e) {
                e.preventDefault();

                let formData = new FormData(this);
                let id = $('#primary_id').val();

                let url = id ?
                    `/admin/transaksi/wawancara/${id}` :
                    `{{ route('wawancara.store') }}`;

                let method = id ? 'POST' : 'POST';

                let submitBtn = $('#submitBtn');
                let spinner = submitBtn.find('.spinner-border');
                let btnText = submitBtn.find('.button-text');

                spinner.removeClass('d-none');
                btnText.text('Menyimpan...');
                submitBtn.prop('disabled', true);

                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();

                $.ajax({
                    url: url,
                    method: method,
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: id ? {
                        'X-HTTP-Method-Override': 'PUT'
                    } : {},
                    success: function() {
                        $('#modalForm').modal('hide');
                        audio.play();
                        toastr.success("Data telah disimpan!", "BERHASIL", {
                            progressBar: true,
                            timeOut: 3500,
                            positionClass: "toast-bottom-right",
                        });

                        let idNasabahBaru = $('#nama_nasabah').val();
                        if (idNasabahBaru && !nasabahTerpilih.includes(idNasabahBaru)) {
                            nasabahTerpilih.push(idNasabahBaru);
                        }

                        $('.data-table').DataTable().ajax.reload();

                        $('#formData')[0].reset();
                        $('#nama_nasabah').val(null).trigger('change');
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
                            toastr.error('Periksa koneksi atau input Anda.', "Terjadi Kesalahan!", {
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


            $(document).on('click', '.edit', function() {
                let url = $(this).data('url');

                $.get(url, function(response) {
                    if (response.status === 'success') {
                        $('#modalFormLabel').text('Edit Data Wawancara');
                        $('#modalForm').modal('show');

                        let data = response.data;

                        $('#primary_id').val(data.id_wawancara);
                        $('#tgl_wawancara').val(data.tgl_wawancara).prop('disabled', true);
                        $('#hari_wawancara').val(getHariIndonesia(data.tgl_wawancara));

                        let newOption = new Option(data.nama_nasabah ?? '-', data.id_customer, true, true);
                        $('#nama_nasabah').append(newOption).trigger('change').prop('disabled', true);

                        $('#no_ktp').val(data.no_ktp ?? '');
                        $('#alamat').val(data.alamat ?? '');
                        $('#kode_kavling').val(data.kode_kavling ?? '');
                        $('#id_kavling').val(data.id_kavling ?? '');
                        $('#tipe_bangunan').val(data.tipe_bangunan ?? '');
                        $('#luas_tanah').val(data.luas_tanah ?? '');
                        $('#luas_bangunan').val(data.luas_bangunan ?? '');
                        $('#nama_marketing').val(data.nama_marketing ?? '');
                        $('#id_marketing').val(data.id_marketing ?? '');


                        if (data.id_bank && data.bank) {
                            let bankOption = new Option(data.bank, data.id_bank, true, true);
                            $('#id_bank').empty().append(bankOption).trigger('change');
                        } else {
                            $('#id_bank').val('').trigger('change');
                        }

                        $('#catatan_kekurangan').summernote('code', data.catatan_kekurangan ?? '');
                    }
                }).fail(function(xhr) {
                    alert('Gagal memuat data. Coba lagi.');
                });
            });




            $(document).on('submit', 'form', function(e) {
                if ($(this).has('button.delete-button').length) {
                    e.preventDefault();

                    $.ajax({
                        url: $(this).attr('action'),
                        method: 'POST',
                        data: $(this).serialize(),
                        success: function() {
                            audio.play();
                            toastr.success("Data telah dihapus!", "BERHASIL", {
                                progressBar: true,
                                timeOut: 3500,
                                positionClass: "toast-bottom-right",
                            });
                            $('.data-table').DataTable().ajax.reload();
                        },
                        error: function() {
                            audio.play();
                            toastr.error("Gagal menghapus data.", "GAGAL!", {
                                progressBar: true,
                                timeOut: 3500,
                                positionClass: "toast-bottom-right",
                            });
                        }
                    });

                }
            });

            $(document).on('click', '.delete-button', function(e) {
                e.preventDefault();

                const form = $(this).closest('form');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: 'Data ini akan dihapus secara permanen!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '<span class="swal-btn-text">Ya, Hapus</span>',
                    cancelButtonText: 'Batal',
                    showLoaderOnConfirm: false,
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'btn btn-danger mx-2',
                        cancelButton: 'btn btn-secondary'
                    },
                    preConfirm: () => {
                        return new Promise((resolve) => {
                            const confirmBtn = Swal.getConfirmButton();
                            const btnText = confirmBtn.querySelector('.swal-btn-text');

                            btnText.innerHTML =
                                `<span class="spinner-border spinner-border-sm mx-2" role="status" aria-hidden="true"></span> Menghapus...`;
                            confirmBtn.disabled = true;

                            $.ajax({
                                url: form.attr('action'),
                                method: 'POST',
                                data: form.serialize(),
                                success: function() {
                                    audio.play();
                                    toastr.success("Data telah dihapus!", "BERHASIL", {
                                        progressBar: true,
                                        timeOut: 3500,
                                        positionClass: "toast-bottom-right"
                                    });

                                    $('.data-table').DataTable().ajax.reload(null,
                                        false);
                                    Swal.close();
                                },
                                error: function() {
                                    audio.play();
                                    toastr.error("Gagal menghapus data.", "GAGAL!", {
                                        progressBar: true,
                                        timeOut: 3500,
                                        positionClass: "toast-bottom-right"
                                    });

                                    btnText.innerHTML = `Ya, Hapus`;
                                    confirmBtn.disabled = false;
                                }
                            });
                        });
                    }
                });
            });
        </script>
    @endpush
