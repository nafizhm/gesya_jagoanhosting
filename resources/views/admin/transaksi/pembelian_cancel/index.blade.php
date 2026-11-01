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
                                    <h3 class="font-weight-bold text-xl">Data Pembatalan Pesanan</h3>
                                    @if ($permissions['tambah'])
                                        <div class="d-flex align-items-center">
                                            <button class="btn btn-info btn-sm" data-toggle="modal"
                                                data-target="#modalCreate">+
                                                Tambah Data</button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered table-striped data-table">
                                    <thead>
                                        <tr>
                                            <th width="5%">No</th>
                                            <th width="15%">Tanggal Pembatalan</th>
                                            <th width="25%">Nama Customer</th>
                                            <th width="12%">No. Telp</th>
                                            <th width="12%">Keterangan</th>
                                            <th width="20%">Surat Pernyataan</th>
                                            <th width="15%">Action</th>
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

    <!-- Modal Create -->
    <div class="modal fade" id="modalCreate" tabindex="-1" role="dialog" aria-labelledby="modalCreateLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title" id="modalCreateLabel">Form Pembatalan Pesanan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-tambah-cancel" action="{{ route('pembelian-cancel.store') }}" method="POST" id="createForm"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group row mb-3">
                            <label for="tanggal" class="col-md-3 col-form-label">Tanggal Pindah</label>
                            <div class="col-md-4">
                                <input name="tanggal" value="{{ old('tanggal', $tanggalSekarang) }}"
                                    class="form-control" type="date" id="tanggal">
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label">Nama Customer</label>
                            <div class="col-sm-5">
                                <select class="form-control select-customer"
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

                        <div class="form-group row">
                            <label class="control-label col-md-3">NIK</label>
                            <div class="col-md-5">
                                <input name="no_ktp" id="no_ktp_c" class="form-control" type="text" readonly>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-md-3">Alamat</label>
                            <div class="col-md-7">
                                <input name="alamat" id="alamat_c" class="form-control" type="text" readonly>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-md-3">Lokasi Kav Lama</label>
                            <div class="col-md-3">
                                <input name="kavling" id="kavling_c" class="form-control" type="text" readonly>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-md-3">Nominal UTJ</label>
                            <div class="col-md-3">
                                <input name="nominal_utj" class="form-control" type="text" readonly>
                                <input name="tgl_utj" class="form-control" type="hidden" readonly>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="keterangan" class="col-md-3 col-form-label">Keterangan</label>
                            <div class="col-md-9">
                                <input name="keterangan" value="{{ old('keterangan') }}"
                                    class="form-control" type="text" id="keterangan">
                            </div>
                        </div>

                        <hr>

                        <div class="form-group row">
                            <label class="control-label col-md-3">Jumlah Pembayaran</label>
                            <div class="col-md-3">
                                <input name="jumlah_bayar"
                                    class="form-control" type="text"
                                    id="jumlah_bayar" value="{{ old('jumlah_bayar') }}">
                            </div>
                        </div>


                        <div class="form-group row">
                            <label class="control-label col-md-3">Cara Bayar</label>
                            <div class="col-md-4">
                                <select name="metode_pembayaran"
                                    class="form-control select-cara-bayar" id="metode_pembayaran">
                                    <option value=""></option>
                                    @foreach ($metodeBayar as $bayar)
                                        <option value="{{ $bayar->jenis_bayar }}"
                                            {{ old('metode_pembayaran') == $bayar->jenis_bayar ? 'selected' : '' }}>
                                            {{ $bayar->jenis_bayar }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        <div class="form-group row mb-3">
                            <label for="nama_bank" class="col-md-3 col-form-label">Nama Bank</label>
                            <div class="col-md-4">
                                <input name="nama_bank" value="{{ old('nama_bank') }}"
                                    class="form-control" type="text" id="nama_bank">
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="no_rekening" class="col-md-3 col-form-label">No. Rekening</label>
                            <div class="col-md-3">
                                <input name="no_rekening" value="{{ old('no_rekening') }}"
                                    class="form-control" type="text" id="no_rekening">
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="atas_nama" class="col-md-3 col-form-label">Atas Nama</label>
                            <div class="col-md-6">
                                <input name="atas_nama" value="{{ old('atas_nama') }}"
                                    class="form-control" type="text" id="atas_nama">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-md-3">Bukti Pembayaran</label>
                            <div class="col-md-4">
                                <input name="lampiran_bukti"
                                    id="lampiran" type="file">
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <span class="spinner-border spinner-border-sm me-2 d-none" role="status"
                                aria-hidden="true"></span>
                            <span class="button-text">Simpan</span>
                        </button>

                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal Detail -->
    <div class="modal fade" id="modalDetail" tabindex="-1" role="dialog" aria-labelledby="modalDetailLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title" id="modalDetailLabel">Form Pembatalan Pesanan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label class="control-label col-md-3">Tanggal Pindah</label>
                        <div class="col-md-5">
                            <input name="tanggal" id="tanggal_pindah" class="form-control" type="text" readonly>

                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="control-label col-md-3">Nama Customer</label>
                        <div class="col-md-5">
                            <input name="nama_customer" id="nama_customer" class="form-control" type="text" readonly>

                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="control-label col-md-3">NIK</label>
                        <div class="col-md-5">
                            <input name="no_ktp" id="no_ktp" class="form-control" type="text" readonly>

                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="control-label col-md-3">Alamat</label>
                        <div class="col-md-7">
                            <input name="alamat" id="alamat" class="form-control" type="text" readonly>

                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="control-label col-md-3">Lokasi Kav Lama</label>
                        <div class="col-md-3">
                            <input name="kavling" id="kavling" class="form-control" type="text" readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="control-label col-md-3">Nominal UTJ</label>
                        <div class="col-md-3">
                            <input name="nominal_utj" id="nominal_utj" class="form-control" type="text" readonly>

                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="control-label col-md-3">Nama Bank</label>
                        <div class="col-md-4">
                            <input name="nama_bank" id="nama_bank" class="form-control" type="text" readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="control-label col-md-3">No. Rekening</label>
                        <div class="col-md-3">
                            <input name="no_rekening" id="no_rekening" class="form-control" type="text" readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="control-label col-md-3">Atas Nama</label>
                        <div class="col-md-6">
                            <input name="atas_nama" id="atas_nama" class="form-control" type="text" readonly>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Keluar</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        function formatAngkaRibuan(angka) {
            return angka.replace(/\D/g, '')
                .replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        $(document).on('input', '#biaya_admin, #jumlah_bayar', function() {
            let nilai = $(this).val();

            if (nilai === '' || nilai === null) {
                nilai = '0';
            }

            let terformat = formatAngkaRibuan(nilai);
            $(this).val(terformat);
        });
    </script>

    <script>
        $(document).ready(function() {
            function initializeSelect2() {
                $('.select-customer').select2({
                    theme: "bootstrap4",
                    placeholder: "Pilih Customer",
                    width: '100%',
                    minimumInputLength: 0,
                    dropdownParent: $('#modalCreate')
                });

                $('.select-cara-bayar').select2({
                    theme: "bootstrap4",
                    placeholder: "Pilih Cara Pembayaran",
                    width: '100%',
                    minimumInputLength: 0,
                    dropdownParent: $('#modalCreate')
                });

                $('.select-kavling').select2({
                    theme: "bootstrap4",
                    placeholder: "Pilih Kavling Baru",
                    width: '100%',
                    minimumInputLength: 0,
                    dropdownParent: $('#modalCreate')
                });
            }

            initializeSelect2();

            $('#modalCreate').on('shown.bs.modal', function() {
                initializeSelect2();
            });
        });

        $(document).ready(function() {
            const customerSelect = $('select[name="nama_customer"]');

            customerSelect.on('change', function() {
                let customerId = $(this).val();

                console.log('ID Customer (via Select2):', customerId);

                if (customerId) {
                    fetch(`/admin/transaksi/pembatalan/get-customer-data/${customerId}`)
                        .then(response => response.json())
                        .then(data => {
                            console.log('Data dari server:', data);

                            $('#no_ktp_c').val(data.no_ktp ?? '');

                            $('#alamat_c').val(data.alamat_ktp ?? '');

                            $('#kavling_c').val(data.blok ?? '');


                        })
                        .catch(error => {
                            console.error('Terjadi error:', error);
                        });
                } else {
                    $('#no_ktp').val('');
                    $('#alamat_ktp').val('');
                    $('#blok').val('');
                }
            });
        });

        var audio = new Audio('{{ asset('audio/notification.ogg') }}');

        @if (session('success'))
            toastr.success("{{ session('success') }}", "BERHASIL", {
                progressBar: true,
                timeOut: 3500,
                positionClass: "toast-bottom-right",
            });
            audio.play();
        @elseif (session('error'))
            toastr.error("{{ session('error') }}", "GAGAL!", {
                progressBar: true,
                timeOut: 3500,
                positionClass: "toast-bottom-right",
            });
            audio.play();
        @endif

        const permissions = @json($permissions);
        const showActionColumn = permissions['edit'] == 1 || permissions['hapus'] == 1;

        $(function() {
            var table = $('.data-table').DataTable({
                processing: false,
                serverSide: true,
                ordering: false,
                responsive: true,
                ajax: "{{ route('pembelian-cancel.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'tgl_batal',
                        name: 'tgl_batal',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nama_customer',
                        name: 'nama_customer',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'no_telp',
                        name: 'no_telp',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'keterangan_batal',
                        name: 'keterangan_batal',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'surat_pernyataan',
                        name: 'surat_pernyataan',
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
                    },
                    {
                        targets: 1,
                        render: function(data) {
                            if (data) {
                                const date = new Date(data);
                                const options = {
                                    day: '2-digit',
                                    month: 'long',
                                    year: 'numeric'
                                };
                                return date.toLocaleDateString('id-ID', options);
                            }
                            return '';
                        }
                    }
                ]

            });
        });

        $('#modalCreate').on('hidden.bs.modal', function() {
            $('#form-tambah-cancel')[0].reset();
            $('#primary_id').val('');
            $('.select-customer').val('').trigger('change');
            $('.select-cara-bayar').val('').trigger('change');
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            let submitBtn = $('#submitBtn');
            let spinner = submitBtn.find('.spinner-border');
            let btnText = submitBtn.find('.button-text');

            spinner.addClass('d-none');
            btnText.text('Simpan');
            submitBtn.prop('disabled', false);
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

        $(document).on('click', '#detail-button', function() {
            var url = $(this).data('url');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'GET',
                success: function(response) {
                    if (response.status === 'success') {
                        let data = response.data;

                        $('#tanggal_pindah').val(data.tgl_batal);
                        $('#nama_customer').val(data.nama_customer);
                        $('#no_ktp').val(data.no_ktp);
                        $('#alamat').val(data.alamat);
                        $('#kavling').val(data.kode_kavling);
                        $('#nominal_utj').val(data.nominal_utj);
                        $('#nama_bank').val(data.nama_bank);
                        $('#no_rekening').val(data.no_rekening);
                        $('#atas_nama').val(data.atas_nama);
                    } else {
                        alert('Gagal mendapatkan data pembatalan.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan. Silakan coba lagi.');
                },
            });
        });

        $(document).ready(function() {
            $('#form-tambah-cancel').on('submit', function(e) {
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
                        $('#modalCreate').modal('hide');
                        audio.play();
                        toastr.success(response.message || "Data berhasil disimpan!", "BERHASIL", {
                            progressBar: true,
                            timeOut: 3500,
                            positionClass: "toast-bottom-right"
                        });
                        $('.data-table').DataTable().ajax.reload();
                        form.reset();
                        spinner.addClass('d-none');
                        btnText.text('Simpan');
                        submitBtn.prop('disabled', false);
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
                                positionClass: "toast-bottom-right"
                            });
                        } else {
                            toastr.error(xhr.responseJSON.message || "Gagal menyimpan data.", "GAGAL!", {
                                progressBar: true,
                                timeOut: 3500,
                                positionClass: "toast-bottom-right"
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
@endpush
