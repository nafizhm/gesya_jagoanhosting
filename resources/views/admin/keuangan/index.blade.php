@extends('admin.layout_admin')
@section('content')
    <style>
        .select2-container .select2-selection--single {
            height: 38px;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
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
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 font-weight-bold text-xl">Transaksi Keuangan</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            @if (isset($permissions['tambah']) && $permissions['tambah'] == 1)
                                <li class="breadcrumb-item"><a href="#" class="btn btn-primary btn-sm"
                                        onclick="add()"><i class="fa fa-plus"></i> Transaksi Baru</a></li>
                            @endif
                            <li class="breadcrumb-item"><a href="#" class="btn btn-dark btn-sm" onclick="cetak()"><i
                                        class="fa fa-print"></i> Cetak Transaksi</a></li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-money-bill"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Pemasukan</span>
                                <span class="info-box-number">
                                    <b>{{ number_format($totalPemasukan, 0, ',', '.') }}</b>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="info-box mb-3">
                            <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-money-bill"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Pengeluaran</span>
                                <b>{{ number_format($totalPengeluaran, 0, ',', '.') }}</b>
                            </div>
                        </div>
                    </div>

                    <div class="clearfix hidden-md-up"></div>

                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="info-box mb-3">
                            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-money-bill"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Saldo</span>
                                <b>{{ number_format($saldo, 0, ',', '.') }}</b>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="info-box mb-3">
                            <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-cogs"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Jumlah Transaksi</span>
                                <b>{{ $jumlahTransaksi }}</b>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Main content -->
                <section class="content">
                    <div class="card">
                        <!-- /.card-header -->
                        <div class="card-body">

                            <table id="table" class="table table-striped table-hover table-bordered" cellspacing="0"
                                width="100%">
                                <thead>
                                    <tr>
                                        <th width="3%" rowspan="2">NO</th>
                                        <th width="15%" rowspan="2" class="text-center">TANGGAL</th>
                                        <th width="15%" rowspan="2" class="text-center">KATEGORI</th>
                                        <th width="25%" rowspan="2" class="text-center">KETERANGAN</th>
                                        <th colspan="2" class="text-center">JENIS</th>
                                        <th rowspan="2" width="10%" class="text-center">OPSI</th>
                                    </tr>
                                    <tr>
                                        <th width="15%" class="text-center">PEMASUKAN</th>
                                        <th width="15%" class="text-center">PENGELUARAN</th>
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


            <!-- Bootstrap modal -->
            <div class="modal fade" id="modal_form" tabindex="-1" role="dialog" aria-labelledby="modalFormLabel"
                aria-hidden="true" data-backdrop="static" data-keyboard="false" data-focus="false">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-success">
                            <h5 class="modal-title">header</h5>
                            <button type="button" class="close" data-dismiss="modal"
                                aria-label="Close"><span>&times;</span></button>
                        </div>
                        <div class="modal-body form">
                            <form id="form" class="form-horizontal" data-action="{{ route('keuanganData.store') }}">
                                <input type="hidden" value="" name="id" />
                                <div class="form-body">
                                    <div class="form-group row">
                                        <label class="control-label col-md-3">Tanggal</label>
                                        <div class="col-md-4">
                                            <input name="transaksi_tanggal" id="transaksi_tanggal" class="form-control"
                                                type="date" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="control-label col-md-3">Jenis Transaksi</label>
                                        <div class="col-md-7">
                                            <select class="form-control select-jenis" name="transaksi_jenis"
                                                id="transaksi_jenis">
                                                <option value=""></option>
                                                <option value="Pemasukan">Pemasukan</option>
                                                <option value="Pengeluaran">Pengeluaran</option>
                                            </select>
                                        </div>
                                    </div>


                                    <div class="form-group row">
                                        <label class="control-label col-md-3">Kategori</label>
                                        <div class="col-md-9">
                                            <select name="transaksi_kategori" class="form-control select-kategori"
                                                id="transaksi_kategori">
                                                <option value=""></option>
                                                @foreach ($kategoriList as $kategori)
                                                    <option value="{{ $kategori->kategori_id }}">{{ $kategori->kategori }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <span class="help-block"></span>
                                        </div>
                                    </div>


                                    <div class="form-group row">
                                        <label class="control-label col-md-3">Deskripsi</label>
                                        <div class="col-md-9">
                                            <textarea name="transaksi_keterangan" class="form-control" row="3"></textarea>
                                            <span class="help-block"></span>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="control-label col-md-3">Nominal</label>
                                        <div class="col-md-7">
                                            <input name="transaksi_nominal" class="form-control" type="text"
                                                id="transaksi_nominal">
                                            <span class="help-block"></span>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="control-label col-md-3">Rekening</label>
                                        <div class="col-md-3">
                                            <select class="form-control select-bank" name="transaksi_bank"
                                                id="transaksi_bank">
                                                <option value=""></option>
                                                <option value="1">BANK NAGARI</option>
                                                <option value="2">BANK BRI</option>
                                                <option value="3">MANDIRI</option>
                                                <option value="5">BANK NAGARI</option>
                                            </select>
                                            <span class="help-block"></span>
                                        </div>
                                    </div>


                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
                            <button type="button" class="btn btn-primary" id="submitBtn" onclick="save()">
                                <span class="spinner-border spinner-border-sm me-2 d-none" role="status"
                                    aria-hidden="true"></span>
                                <span class="button-text">Simpan</span>
                            </button>

                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->



            <div class="modal fade" id="modal_form_cetak" role="dialog">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-success">
                            <h5 class="modal-title">header</h5>
                            <button type="button" class="close" data-dismiss="modal"
                                aria-label="Close"><span>&times;</span></button>
                        </div>
                        <div class="modal-body form">
                            <form action="{{ route('cetak.transaksi') }}" id="form_cetak" class="form-horizontal"
                                method="GET" target="_blank">
                                <input type="hidden" value="" name="id" />
                                <div class="form-body">

                                    <div class="form-group row">
                                        <label class="control-label col-md-3">Periode Awal</label>
                                        <div class="col-md-4">
                                            <input name="tgl_awal"id="tgl_awal" class="form-control" type="date">
                                            <span class="help-block"></span>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="control-label col-md-3">Periode Akhir</label>
                                        <div class="col-md-4">
                                            <input name="tgl_akhir"id="tgl_akhir" class="form-control" type="date">
                                            <span class="help-block"></span>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="control-label col-md-3"></label>
                                        <div class="col-md-4">
                                            <button type="submit" id="submit" class="btn btn-primary">Cetak</button>
                                        </div>
                                    </div>

                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
    </div>
@endsection

@push('scripts')
    <script>
        var permissions = @json($permissions);
        var showActionColumn = (permissions['hapus'] == 1);

        function formatAngkaRibuan(angka) {
            return angka.replace(/\D/g, '')
                .replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        $(document).on('input', '#transaksi_nominal', function() {
            let nilai = $(this).val();
            let terformat = formatAngkaRibuan(nilai);
            $(this).val(terformat);
        });
    </script>

    <script>
        $('#transaksi_kategori').select2({
            ajax: {
                url: '{{ route('kategori.search') }}',
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: data.map(function(item) {
                            return {
                                id: item.kategori_id,
                                text: item.kategori
                            };
                        })
                    };
                },
                cache: true
            },
            minimumInputLength: 1,
            width: 'resolve'
        });

        $(document).ready(function() {
            $('.select-jenis').select2({
                theme: 'bootstrap4',
                placeholder: "Pilih Jenis Transaksi"
            });
            $('.select-bank').select2({
                theme: 'bootstrap4',
                placeholder: "Pilih Bank"
            });

        });

        $('#modal_form').on('hidden.bs.modal', function() {
            $('#form')[0].reset();

            $('.select-jenis').val('').trigger('change');
            $('.select-bank').val('').trigger('change');
            $('.select-kategori').val('').trigger('change');

            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            let submitBtn = $('#submitBtn');
            let spinner = submitBtn.find('.spinner-border');
            let btnText = submitBtn.find('.button-text');

            spinner.addClass('d-none');
            btnText.text('Simpan');
            submitBtn.prop('disabled', false);
        });

        function save() {
            const form = $('#form');
            const formData = form.serialize();
            var audio = new Audio('{{ asset('audio/notification.ogg') }}');

            const submitBtn = $('#submitBtn');
            const spinner = submitBtn.find('.spinner-border');
            const btnText = submitBtn.find('.button-text');

            spinner.removeClass('d-none');
            btnText.text('Menyimpan...');
            submitBtn.prop('disabled', true);

            $.ajax({
                url: $('#form').data('action'),
                method: 'POST',
                data: formData,
                // contentType: false, // Penting untuk FormData
                // processData: false, // Penting untuk FormData
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status) {
                        $('#modal_form').modal('hide');
                        form.trigger('reset');
                        audio.play();

                        toastr.success(response.message, "Berhasil!", {
                            closeButton: true,
                            progressBar: true,
                            timeOut: 3000,
                            positionClass: 'toast-bottom-right'
                        });

                        $('#table').DataTable().ajax.reload();
                    } else {
                        audio.play();
                        toastr.error(response.message || "Gagal menyimpan data", "Gagal!", {
                            closeButton: true,
                            progressBar: true,
                            timeOut: 3000,
                            positionClass: 'toast-bottom-right'
                        });
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
                        spinner.addClass('d-none');
                        btnText.text('Simpan');
                        submitBtn.prop('disabled', false);
                    }
                }
            });
        }

        function add() {
            $('#form')[0].reset();
            $('#modal_form').modal('show');
            $('.modal-title').text('Tambah Transaksi Baru');
        }


        function cetak() {
            $('#form_cetak')[0].reset();
            $('#modal_form_cetak').modal('show');
            $('.modal-title').text('Cetak Transaksi');
        }

        $('#table').DataTable({
            processing: true,
            serverSide: true,
            ordering: false,
            ajax: "{{ route('keuanganData') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false,
                    className: 'text-center'
                },
                {
                    data: 'tanggal',
                    name: 'transaksi_tanggal'
                },
                {
                    data: 'transaksi_kategori',
                    name: 'transaksi_kategori'
                },
                {
                    data: 'transaksi_keterangan',
                    name: 'transaksi_keterangan'
                },
                {
                    data: 'pemasukan',
                    name: 'pemasukan',
                    orderable: false,
                    searchable: false,
                    className: "text-end"
                },
                {
                    data: 'pengeluaran',
                    name: 'pengeluaran',
                    orderable: false,
                    searchable: false,
                    className: "text-end"
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    className: "text-center",
                    visible: showActionColumn
                }
            ]
        });

        $(document).on('submit', '.form-delete', function(e) {
            e.preventDefault();
            var form = this;

            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e3342f',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });


        $(function() {
            var audio = new Audio('{{ asset('audio/notification.ogg') }}');

            @if (Session::has('success'))
                audio.play();
                toastr.success("{{ Session::get('success') }}", "Berhasil!", {
                    closeButton: true,
                    progressBar: true,
                    timeOut: 3000,
                    positionClass: 'toast-bottom-right',
                });
            @endif

            @if ($errors->any())
                audio.play();
                toastr.error("{{ $errors->first() }}", "Kesalahan!", {
                    closeButton: true,
                    progressBar: true,
                    timeOut: 3000,
                    positionClass: 'toast-bottom-right'
                });
            @endif
        });
    </script>
@endpush
