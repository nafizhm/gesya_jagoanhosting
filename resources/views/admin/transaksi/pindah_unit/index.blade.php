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
                                    <h3 class="font-weight-bold text-xl">Data Pindah Unit Rumah</h3>
                                    @if ($permissions['tambah'])
                                        <div class="d-flex align-items-center">
                                            <button class="btn btn-primary btn-sm" data-toggle="modal"
                                                data-target="#modalForm"><i class="fas fa-plus"></i>
                                                Tambah Pindah Unit</button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered small table-striped data-table">
                                    <thead>
                                        <tr>
                                            <th width="5%">No</th>
                                            <th>Tanggal Pindah</th>
                                            <th>Nama Customer</th>
                                            <th>Lokasi Lama</th>
                                            <th>Lokasi Baru</th>
                                            <th>Keterangan</th>
                                            <th>Biaya Admin</th>
                                            <th>Action</th>
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
    <div class="modal fade" id="modalForm" tabindex="-1" role="dialog" data-focus="false" aria-labelledby="modalFormLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title text-white font-weight-bold" id="modalFormLabel">Form Pindah Unit</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formData">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" id="primary_id" name="primary_id">
                        <div class="row mb-3">
                            <label for="tanggal" class="col-md-3 col-form-label">Tanggal Pindah</label>
                            <div class="col-md-4">
                                <input name="tanggal" value="{{ date('Y-m-d') }}" class="form-control" type="date">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Customer</label>
                            <div class="col-sm-5">
                                <select class="form-select select-customer" name="id_customer" id="id_customer">
                                    <option value=""></option>
                                    @foreach ($customers as $cust)
                                        <option value="{{ $cust->id_customer }}">{{ $cust->nama_lengkap }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="no_ktp" class="col-md-3 col-form-label">NIK</label>
                            <div class="col-md-5">
                                <input name="no_ktp" id="no_ktp" class="form-control" type="text" readonly>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="alamat" class="col-md-3 col-form-label">Alamat</label>
                            <div class="col-md-7">
                                <input name="alamat" id="alamat" class="form-control" type="text" readonly>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="kavling" class="col-md-3 col-form-label">Lokasi Kav Lama</label>
                            <div class="col-md-3">
                                <input name="id_kavling_lama" id="id_kavling_lama" class="form-control" type="text"
                                    readonly>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="control-label col-md-3">Biaya Administrasi</label>
                            <div class="col-md-6">
                                <input name="biaya_admin" id="biaya_admin" class="form-control format-number" type="text"
                                    id="biaya_admin">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Lokasi Kav Baru</label>
                            <div class="col-sm-3">
                                <select class="form-select select-kavling" name="id_kavling_baru" id="id_kavling_baru">
                                    @foreach ($kavBaru as $kav)
                                        <option value=""></option>
                                        <option value="{{ $kav->id_kavling }}">{{ $kav->kode_kavling }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="keterangan" class="col-md-3 col-form-label">Keterangan</label>
                            <div class="col-md-9">
                                <textarea name="keterangan" id="keterangan" class="form-control" rows="3"></textarea>
                            </div>
                        </div>

                        <hr>

                        <div class="row mb-3">
                            <label class="control-label col-md-3">Cara Bayar</label>
                            <div class="col-md-4">
                                <select name="metode_pembayaran" id="metode_pembayaran"
                                    class="form-control select-cara-bayar">
                                    <option value=""></option>
                                    @foreach ($metodeBayar as $bayar)
                                        <option value="{{ $bayar->jenis_bayar }}">{{ $bayar->jenis_bayar }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="control-label col-md-3">Bukti Pembayaran</label>
                            <div class="col-md-4">
                                <input name="lampiran_bukti" id="lampiran_bukti" type="file" class="form-control">
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
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $('.select-customer').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Customer",
            });
            $('.select-kavling').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Kavling Baru",
            });
            $('.select-cara-bayar').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Cara Pembayaran",
            });
        });

        $(document).ready(function() {

            $('#id_customer').on('change', function() {

                let idCustomer = $(this).val();

                if (idCustomer) {

                    let url = "{{ route('pindah-unit.getCustomerData', ':id_customer') }}";
                    url = url.replace(':id_customer', idCustomer);

                    $.ajax({
                        url: url,
                        type: 'GET',
                        dataType: 'json',

                        success: function(response) {

                            $('#no_ktp').val(response.no_ktp);
                            $('#alamat').val(response.alamat);
                            $('#id_kavling_lama').val(response.blok);

                        },

                        error: function() {

                            $('#no_ktp').val('');
                            $('#alamat').val('');
                            $('#id_kavling_lama').val('');

                        }
                    });

                } else {

                    $('#no_ktp').val('');
                    $('#alamat').val('');
                    $('#id_kavling_lama').val('');

                }

            });

        });

        const permissions = @json($permissions);
        const showActionColumn = permissions['edit'] == 1 || permissions['hapus'] == 1;

        $(function() {
            var table = $('.data-table').DataTable({
                processing: false,
                serverSide: true,
                ordering: false,
                responsive: true,
                ajax: "{{ route('pindah-unit.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: true,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'tgl_pindah',
                        name: 'tgl_pindah',
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
                        data: 'kode_kavling_lama',
                        name: 'kode_kavling_lama',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'kode_kavling_baru',
                        name: 'kode_kavling_baru',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'keterangan_pindah',
                        name: 'keterangan_pindah',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'biaya_admin',
                        name: 'biaya_admin',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        visible: showActionColumn,
                        className: 'text-center'
                    }
                ],
                columnDefs: [{
                    targets: 0,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                }]
            })
        })

        $('#modalForm').on('hidden.bs.modal', function() {
            $('#formData')[0].reset();
            $('#primary_id').val('');
            $('.select-customer').val('').trigger('change');
            $('.select-kavling').val('').trigger('change');
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

        $('#formData').on('submit', function(e) {
            e.preventDefault();

            let submitBtn = $('#submitBtn');
            let spinner = submitBtn.find('.spinner-border');
            let btnText = submitBtn.find('.button-text');

            spinner.removeClass('d-none');
            btnText.text('Menyimpan...');
            submitBtn.prop('disabled', true);

            let id = $('#primary_id').val();
            let url = id ? '{{ route('pindah-unit.update', ['pindah_unit' => ':id']) }}'.replace(':id', id) :
                '{{ route('pindah-unit.store') }}';
            let method = id ? 'PUT' : 'POST';

            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            let formData = new FormData(this);
            formData.append('_method', method);

            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    $('#modalForm').modal('hide');
                    audio.play();
                    let msg = id ? "Data Pindah Unit berhasil diupdate!" :
                        "Data Pindah Unit berhasil ditambahkan!";
                    toastr.success(msg, "BERHASIL", {
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

                        spinner.addClass('d-none');
                        btnText.text('Simpan');
                        submitBtn.prop('disabled', false);
                    }
                },
                complete: function() {
                    spinner.addClass('d-none');
                    btnText.text('Simpan');
                    submitBtn.prop('disabled', false);
                }
            });
        });

        $(document).on('click', '.delete-button', function(e) {
            e.preventDefault();

            const form = $(this).closest('form');

            if (!form.has('button.delete-button').length) return;

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Data Pindah Unit ini akan dihapus secara permanen!',
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

                        btnText.innerHTML = `
                    <span class="spinner-border spinner-border-sm mx-2" role="status" aria-hidden="true"></span>
                    Menghapus...`;
                        confirmBtn.disabled = true;

                        $.ajax({
                            url: form.attr('action'),
                            method: 'POST',
                            data: form.serialize(),
                            success: function() {
                                audio.play();
                                toastr.success("Data Pindah Unit telah dihapus!", "BERHASIL", {
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
