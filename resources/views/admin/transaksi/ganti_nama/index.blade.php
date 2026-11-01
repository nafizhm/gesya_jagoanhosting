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
                                    <h3 class="font-weight-bold text-xl">Data Ganti Nama</h3>
                                    @if ($permissions['tambah'])
                                        <div class="d-flex align-items-center">
                                            <button class="btn btn-info btn-sm open-modal-btn" data-toggle="modal"
                                                data-target="#modalCreate" data-mode="tambah">+
                                                Tambah Data</button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered small table-striped data-table">
                                    <thead>
                                        <th width="50px">No</th>
                                        <th>Tanggal Pindah</th>
                                        <th>Nama Customer Lama</th>
                                        <th>Nama Customer Baru</th>
                                        <th>Kode Kavling</th>
                                        <th>Biaya Ganti Nama</th>
                                        <th>Keterangan</th>
                                        <th width="100px">Action</th>
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
    <div class="modal fade" id="modalCreate" tabindex="-1" role="dialog" data-focus="false"
        aria-labelledby="modalFormLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title" id="modalCreateLabel">Form Ganti Nama</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="createForm">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group row mb-3">
                            <label for="tgl_pindah" class="col-md-3 col-form-label">Tanggal Pindah</label>
                            <div class="col-md-4">
                                <input name="tgl_pindah" id="tgl_pindah" class="form-control" type="date">

                            </div>
                        </div>

                        <div class="form-group row mb-3" id="formSelectCustomerLama">
                            <label class="col-sm-3 col-form-label">Nama Customer Lama</label>
                            <div class="col-sm-5">
                                <select class="form-control select-customer-lama" name="id_customer_lama"
                                    id="id_customer_lama">
                                    <option value=""></option>
                                    @foreach ($dataCustomer as $ctm)
                                        <option value="{{ $ctm->id_customer }}">
                                            {{ $ctm->nama_lengkap }}

                                        </option>
                                    @endforeach
                                </select>

                            </div>
                        </div>
                        <div class="form-group row mb-3 d-none" id="formCust">
                            <label for="cust_hide" class="col-sm-3 col-form-label">Nama Customer Lama</label>
                            <div class="col-sm-5">
                                <input type="text" name="cust_hide" id="cust_hide" class="form-control" disabled>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="nik" class="col-sm-3 col-form-label">NIK</label>
                            <div class="col-sm-5">
                                <input type="text" name="nik_lama" id="nik_lama" class="form-control" disabled>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="alamat" class="col-sm-3 col-form-label">Alamat</label>
                            <div class="col-sm-5">
                                <input type="text" name="alamat_customer_lama" id="alamat_customer_lama"
                                    class="form-control" disabled>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="alamat" class="col-sm-3 col-form-label">Lokasi Unit</label>
                            <div class="col-sm-5">
                                <input type="text" name="lokasi_unit" id="lokasi_unit" class="form-control" disabled>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="biaya_ganti_nama" class="col-sm-3 col-form-label">Biaya Ganti Nama</label>
                            <div class="col-sm-3">
                                <input type="text" name="biaya_ganti_nama" id="biaya_ganti_nama" class="form-control">
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <div class="col-sm-12">
                                <hr style="border-top: 1px solid #000;">
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="nama_lengkap" class="col-sm-3 col-form-label">Nama Lengkap</label>
                            <div class="col-sm-5">
                                <input type="text" name="nama_lengkap" id="nama_lengkap" class="form-control">
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="no_ktp" class="col-sm-3 col-form-label">No. KTP</label>
                            <div class="col-sm-3">
                                <input type="number" name="no_ktp" id="no_ktp" class="form-control">
                            </div>
                            <label for="no_ktp_p" class="col-sm-2 col-form-label">No. KTP Pasangan</label>
                            <div class="col-sm-3">
                                <input type="number" name="no_ktp_p" id="no_ktp_p" class="form-control">
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="tempat_lahir" class="col-sm-3 col-form-label">Tempat Lahir</label>
                            <div class="col-sm-3">
                                <input type="text" name="tempat_lahir" id="tempat_lahir" class="form-control">
                            </div>

                            <label for="tgl_lahir" class="col-sm-2 col-form-label">Tanggal Lahir</label>
                            <div class="col-sm-3">
                                <input type="date" name="tgl_lahir" id="tgl_lahir" class="form-control">
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="jenis_kelamin" class="col-sm-3 col-form-label">Jenis Kelamin</label>
                            <div class="col-sm-3">
                                <select name="jenis_kelamin" id="jenis_kelamin" class="form-control select-jk">
                                    <option value=""></option>
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="no_telp" class="col-sm-3 col-form-label">No. Telp / WA</label>
                            <div class="col-sm-3">
                                <input type="number" name="no_telp" id="no_telp" class="form-control">
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="email" class="col-sm-3 col-form-label">Email</label>
                            <div class="col-sm-3">
                                <input type="email" name="email" id="email" class="form-control">
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="npwp" class="col-sm-3 col-form-label">NPWP</label>
                            <div class="col-sm-3">
                                <input type="text" name="npwp" id="npwp" class="form-control">
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="alamat" class="col-sm-3 col-form-label">Alamat KTP</label>
                            <div class="col-sm-5">
                                <input type="text" name="alamat" id="alamat" class="form-control">
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="alamat_domisili" class="col-sm-3 col-form-label">Alamat Domisili</label>
                            <div class="col-sm-5">
                                <input type="text" name="alamat_domisili" id="alamat_domisili" class="form-control">
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="pekerjaan" class="col-sm-3 col-form-label">Pekerjaan</label>
                            <div class="col-sm-3">
                                <input type="text" name="pekerjaan" id="pekerjaan" class="form-control">
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="warga_negara" class="col-sm-3 col-form-label">Warga Negara</label>
                            <div class="col-sm-3">
                                <input type="text" name="warga_negara" id="warga_negara" class="form-control">
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="id_lokasi" class="col-sm-3 col-form-label">Lokasi Perumahan</label>
                            <div class="col-sm-3">
                                <select name="id_lokasi" id="id_lokasi" class="form-control select-lokasi">
                                    <option value=""></option>
                                    @foreach ($lokasi as $l)
                                        <option value="{{ $l->id_lokasi }}">{{ $l->nama_kavling }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="id_kavling" class="col-sm-3 col-form-label">Blok / Kode Kavling</label>
                            <div class="col-sm-3">
                                <select name="id_kavling" id="id_kavling" class="form-control select-kavling"></select>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="hrg_jual" class="col-sm-3 col-form-label">Harga Jual</label>
                            <div class="col-sm-3">
                                <input type="text" id="hrg_jual" name="hrg_jual_display" class="form-control"
                                    disabled>
                                <input type="text" name="hrg_jual" id="hrg_jual_hidden" hidden>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="id_marketing" class="col-sm-3 col-form-label">Marketing</label>
                            <div class="col-sm-3">
                                <select name="id_marketing" id="id_marketing" class="form-control select-marketing">
                                    <option value=""></option>
                                    @foreach ($marketing as $m)
                                        <option value="{{ $m->id_marketing }}">{{ $m->nama_marketing }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="id_freelance" class="col-sm-3 col-form-label">Freelance</label>
                            <div class="col-sm-3">
                                <select name="id_freelance" id="id_freelance" class="form-control select-freelance">
                                    <option value=""></option>
                                    @foreach ($freelance as $f)
                                        <option value="{{ $f->id_freelance }}">{{ $f->nama_freelance }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="id_status_progres" class="col-sm-3 col-form-label">Status Progres</label>
                            <div class="col-sm-3">
                                <select name="id_status_progres" id="id_status_progres"
                                    class="form-control select-status-progres">
                                    <option value=""></option>
                                    @foreach ($progres as $p)
                                        <option value="{{ $p->id_status_progres }}">{{ $p->status_progres }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="ket_cashback" class="col-sm-3 col-form-label">Keterangan Cashback</label>
                            <div class="col-sm-5">
                                <input type="text" name="ket_cashback" id="ket_cashback" class="form-control">
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="jenis_pembelian" class="col-sm-3 col-form-label">Jenis Pembelian</label>
                            <div class="col-sm-3">
                                <select name="jenis_pembelian" id="jenis_pembelian"
                                    class="form-control select-jenis-pembelian">
                                    <option value=""></option>
                                    <option value="Booking">Booking</option>
                                    <option value="Pembelian Cash">Pembelian Cash</option>
                                    <option value="Cash Bertahap">Cash Bertahap</option>
                                    <option value="Kredit">Kredit</option>
                                </select>
                            </div>
                        </div>

                        <!-- BOOKING ==================================> -->
                        <hr class="hr-transaksi" style="display: none;">
                        <div id="trx_booking" style="display: none;">

                            <div class="form-group row mb-3">
                                <label class="col-sm-3 col-form-label">Pembayaran Booking</label>
                                <div class="col-sm-3">
                                    <input name="pembayaran_booking" id="pembayaran_booking" class="form-control"
                                        type="text">
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label class="col-sm-3 col-form-label">Tanggal Batas Booking</label>
                                <div class="col-sm-3">
                                    <input name="tgl_batas_booking" id="tgl_batas_booking" class="form-control"
                                        type="date">
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label class="col-sm-3 col-form-label">Keterangan</label>
                                <div class="col-sm-3">
                                    <input name="ket_booking" id="ket_booking" class="form-control" type="text">
                                </div>
                            </div>
                        </div>

                        <!-- CASH ==================================> -->
                        <hr class="hr-transaksi" style="display: none;">
                        <div id="trx_cash" style="display: none;">
                            <div class="form-group row mb-3">
                                <label class="col-sm-3 col-form-label">Diskon</label>
                                <div class="col-sm-3">
                                    <input name="diskon_cash" id="diskon_cash" class="form-control" type="text"
                                        value="0">
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label class="col-sm-3 col-form-label">Pembayaran Cash</label>
                                <div class="col-sm-3">
                                    <input name="pembayaran_cash" id="pembayaran_cash" class="form-control"
                                        type="text" value="0">
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label class="col-sm-3 col-form-label">Sisa Pembayaran (AJB)</label>
                                <div class="col-sm-3">
                                    <input name="sisa_bayar_ajb" id="sisa_bayar_ajb" class="form-control" type="text"
                                        value="0">
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label class="col-sm-3 col-form-label">Atas Nama Surat</label>
                                <div class="col-sm-3">
                                    <input name="an_surat_cash" id="an_surat_cash" class="form-control" type="text">
                                </div>
                            </div>
                        </div>

                        <!-- CASH BERTAHAP ==================================> -->
                        <hr class="hr-transaksi" style="display: none;">
                        <div id="trx_cash_bertahap" style="display: none;">

                            <div class="form-group row mb-3">
                                <label class="col-sm-3 col-form-label">DP</label>
                                <div class="col-sm-3">
                                    <input name="dp_cash_b" id="dp_cash_b" class="form-control" type="text"
                                        value="0">
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label class="col-sm-3 col-form-label">Termin (x)</label>
                                <div class="col-sm-3">
                                    <input name="termin_x_cash_b" id="termin_x_cash_b" class="form-control"
                                        type="number" value="0">
                                </div>
                            </div>
                        </div>

                        <!-- KREDIT ==================================> -->
                        <hr class="hr-transaksi" style="display: none;">
                        <div id="trx_kredit" style="display: none;">

                            <div class="form-group row mb-3">
                                <label class="col-sm-3 col-form-label">DP / Uang Muka</label>
                                <div class="col-sm-3">
                                    <input name="dp_kredit" id="dp_kredit" class="form-control" type="text">
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label class="col-sm-3 col-form-label">Lama Cicilan</label>
                                <div class="col-sm-3">
                                    <input name="lama_cicilan_kredit" id="lama_cicilan_kredit" class="form-control"
                                        type="text">
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label class="col-sm-3 col-form-label">Cicilan Per Bulan</label>
                                <div class="col-sm-3">
                                    <input name="cicilan_kredit" id="cicilan_kredit" class="form-control"
                                        type="text">
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label class="col-sm-3 col-form-label">Tanggal Tempo Cicilan Pertama</label>
                                <div class="col-sm-3">
                                    <input name="tgl_tempo_cicilan_1" id="tgl_tempo_cicilan_1" class="form-control"
                                        type="date">
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label class="col-sm-3 col-form-label">Atas Nama Surat</label>
                                <div class="col-sm-3">
                                    <input name="an_surat_kredit" id="an_surat_kredit" class="form-control"
                                        type="text">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(function() {
            if (!$.fn.DataTable.isDataTable('.data-table')) {
                var table = $('.data-table').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ordering: false,
                    ajax: "{{ route('gantiNama.index') }}",

                    columns: [{
                            data: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'tanggal',
                            name: 'tgl_ganti',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'nama_customer_lama',
                            name: 'nama_customer_lama'
                        },
                        {
                            data: 'nama_customer_baru',
                            name: 'nama_customer_baru'
                        },
                        {
                            data: 'perumahan',
                            name: 'perumahan',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'biaya_formatted',
                            name: 'biaya_ganti_nama',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'status',
                            name: 'stt_approval',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'action',
                            orderable: false,
                            searchable: false
                        },
                    ]
                });
            }
        });

        $(document).on('click', '.open-modal-btn', function() {
            const mode = $(this).data('mode');
            openModal(mode);
        });

        function openModal(mode, response = null) {

            $('#createForm')[0].reset();
            $('#createForm :input').prop('disabled', false);
            $('.modal-footer button[type="submit"]').show();


            $('.select-jk, .select-lokasi, .select-kavling, .select-marketing, .select-freelance, .select-status-progres, .select-jenis-pembelian, .select-bank, .select-customer-lama')
                .val('').trigger('change');
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();


            if (mode === 'tambah') {
                document.getElementById('formCust').classList.add('d-none');
                document.getElementById('formSelectCustomerLama').classList.remove('d-none');


                $('#nik_lama, #alamat_customer_lama, #lokasi_unit, #cust_hide').prop('disabled', true);

                $('#id_lokasi, #id_kavling, #hrg_jual, #id_marketing, #id_freelance, #id_status_progres, #ket_cashback, #jenis_pembelian, \
                                                                             #pembayaran_booking, #tgl_batas_booking, #ket_booking, \
                                                                             #diskon_cash, #pembayaran_cash, #sisa_bayar_ajb, #an_surat_cash, \
                                                                             #dp_cash_b, #termin_x_cash_b, \
                                                                             #dp_kredit, #lama_cicilan_kredit, #cicilan_kredit, #tgl_tempo_cicilan_1, #an_surat_kredit')
                    .prop('disabled', true);
            }

            // else if (mode === 'detail') {
            //     document.getElementById('formCust').classList.remove('d-none');
            //     document.getElementById('formSelectCustomerLama').classList.add('d-none');


            //     $('#createForm :input').prop('disabled', true);
            //     $('.modal-footer button[type="submit"]').hide();

            //     // Isi data
            //     if (response) fillFormData(response);
            // }

            // Tampilkan modal
            $('#modalCreate').modal('show');
        }

        var audio = new Audio('{{ asset('audio/notification.ogg') }}');

        function refreshCustomerDropdown() {
            $.get('/get-customers', function(data) {
                let options = '<option value=""></option>';
                data.forEach(customer => {
                    options += `<option value="${customer.id_customer}">${customer.nama_lengkap}</option>`;
                });
                $('.select-customer-lama').html(options);
            });
        }

        $('#createForm').on('submit', function(e) {
            e.preventDefault();

            // Ambil token dari meta tag
            const token = $('meta[name="csrf-token"]').attr('content');
            const submitBtn = $(this).find('[type="submit"]');
            submitBtn.prop('disabled', true).html(
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...'
            );
            $.ajax({
                url: 'gantiNama',
                type: 'POST',
                data: {
                    _token: token, // Kirim token
                    ...$('#createForm').serializeArray().reduce((obj, item) => {
                        obj[item.name] = item.value;
                        return obj;
                    }, {})
                },
                success: function(response) {
                    // Handle success
                    if (response.success) {
                        submitBtn.prop('disabled', false).html('Simpan');
                        refreshCustomerDropdown();
                        $('#modalCreate').modal('hide');
                        audio.play();
                        toastr.success("Data telah disimpan!", "BERHASIL", {
                            progressBar: true,
                            timeOut: 3500,
                            positionClass: "toast-bottom-right",
                        });

                        $('.data-table').DataTable().ajax.reload();



                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        submitBtn.prop('disabled', false).html('Simpan');
                        audio.play();
                        toastr.error("Ada inputan yang salah!", "GAGAL!", {
                            progressBar: true,
                            timeOut: 3500,
                            positionClass: "toast-bottom-right",
                        });

                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, val) {
                            console.log("Error di field:", key, "-", val[
                                0]); // <-- Tambahkan baris ini

                            let input = $('#' + key);
                            input.addClass('is-invalid');

                            // Hapus pesan error sebelumnya agar tidak dobel
                            input.parent().find('.invalid-feedback').remove();

                            input.parent().append(
                                '<span class="invalid-feedback" role="alert"><strong>' +
                                val[0] + '</strong></span>'
                            );
                        });
                    } else {
                        submitBtn.prop('disabled', false).html('Simpan');
                    }
                }
            });
        });

        $(document).ready(function() {
            function hideAllTransactionForms() {
                $('#trx_booking').hide();
                $('#trx_cash').hide();
                $('#trx_cash_bertahap').hide();
                $('#trx_kredit').hide();
                $('.hr-transaksi').hide();
            }

            // $('#modalForm').on('hidden.bs.modal', function() {
            //     hideAllTransactionForms();
            //     $('#createForm')[0].reset();
            //     $('#jenis_pembelian').val('').trigger('change');
            // });

            // Ketika modal dibuka
            $('#modalCreate').on('shown.bs.modal', function() {
                hideAllTransactionForms();
            });

            $('#jenis_pembelian').on('change', function() {
                let selected = $(this).val();
                hideAllTransactionForms();

                switch (selected) {
                    case 'Booking':
                        $('#trx_booking').prev('.hr-transaksi').show();
                        $('#trx_booking').show();
                        break;
                    case 'Pembelian Cash':
                        $('#trx_cash').prev('.hr-transaksi').show();
                        $('#trx_cash').show();
                        break;
                    case 'Cash Bertahap':
                        $('#trx_cash_bertahap').prev('.hr-transaksi').show();
                        $('#trx_cash_bertahap').show();
                        break;
                    case 'Kredit':
                        $('#trx_kredit').prev('.hr-transaksi').show();
                        $('#trx_kredit').show();
                        break;
                }
            });

        });


        // $(document).ready(function() {
        //     // Inisialisasi Select2
        //     $('.select-lokasi').select2({
        //         theme: "bootstrap4",
        //         placeholder: "Pilih Lokasi",
        //     });

        //     $('.select-kavling').select2({
        //         theme: "bootstrap4",
        //         placeholder: "Pilih Kavling",
        //     });

        //     const routeGetKavling = "{{ route('customer.getKavling', ':id') }}";
        //     const routeGetHarga = "{{ route('customer.getHargaKavling', ':id') }}";

        //     $('#id_lokasi').on('change', function() {
        //         let idLokasi = $(this).val();
        //         $('#id_kavling').html('<option value="">Loading...</option>').trigger('change');
        //         $('#hrg_jual').val('');
        //         $('#hrg_jual_hidden').val('');

        //         if (idLokasi) {
        //             const urlKavling = routeGetKavling.replace(':id', idLokasi);
        //             $.get(urlKavling, function(data) {
        //                 let options = '<option value=""></option>';
        //                 data.forEach(function(item) {
        //                     options +=
        //                         `<option value="${item.id_kavling}">${item.kode_kavling}</option>`;
        //                 });
        //                 $('#id_kavling').html(options).trigger('change');
        //             });
        //         }
        //     });

        //     // Event saat kavling berubah

        // });

        function formatRupiahInput(angka) {
            if (!angka) return '';
            return angka.toString().replace(/\D/g, '') // hanya angka
                .replace(/\B(?=(\d{3})+(?!\d))/g, '.'); // ribuan
        }

        const uangFields = [
            '#pembayaran_booking',
            '#diskon_cash',
            '#pembayaran_cash',
            '#sisa_bayar_ajb',
            '#dp_cash_b',
            '#dp_kredit',
            '#cicilan_kredit',
            '#biaya_ganti_nama'
        ];

        uangFields.forEach(function(selector) {
            $(document).on('input', selector, function() {
                let val = $(this).val().replace(/\./g, '');
                $(this).val(formatRupiahInput(val));
            });
        });

        $(document).on('click', '.detail-btn', function() {
            var url = $(this).data('url');
            var $btn = $(this);

            $btn.prop('disabled', true).html('Loading...');
            $('#modalCreate').modal('show');
            $('#createForm input, #createForm select, #createForm textarea').prop('disabled', true);
            $('.modal-footer button[type="submit"]').hide();
            $.get(url, function(response) {
                if (response.data) {
                    const data = response.data;
                    const custLama = response.customer_lama;
                    const custBaru = response.customer_baru;

                    $(document).one('ajaxStop', function() {
                        $('#tgl_pindah').val(data.tgl_ganti);

                        $('#id_customer_lama').append(new Option(data.nama_customer_lama, data
                            .id_customer_lama, true, true));
                        $('#biaya_ganti_nama').val(formatRupiah(data.biaya_ganti_nama));
                        $('#nik_lama').val(custLama.nik);

                        $('#alamat_customer_lama').val(custLama.alamat);
                        $('#lokasi_unit').val(custLama.lokasi_unit);
                        $('#cust_hide').val(custLama.nama);

                        $('#nama_lengkap').val(custBaru.nama);
                        $('#no_ktp').val(custBaru.no_ktp);
                        $('#no_ktp_p').val(custBaru.no_ktp_p);
                        $('#tempat_lahir').val(custBaru.tempat_lahir);
                        $('#tgl_lahir').val(custBaru.tgl_lahir);
                        $('#jenis_kelamin').val(custBaru.jenis_kelamin).trigger('change');
                        $('#no_telp').val(custBaru.no_telp);
                        $('#email').val(custBaru.email);
                        $('#npwp').val(custBaru.npwp);
                        $('#alamat').val(custBaru.alamat);
                        $('#alamat_domisili').val(custBaru.alamat_domisili);
                        $('#pekerjaan').val(custBaru.pekerjaan);
                        $('#warga_negara').val(custBaru.warga_negara);

                        $('#hrg_jual').val(formatRupiah(custBaru.hrg_jual));
                        $('#hrg_jual_hidden').val(custBaru.hrg_jual);
                        $('#id_marketing').append(
                            new Option(
                                data.customer_baru?.marketing?.nama_marketing ?? '',
                                data.customer_baru?.marketing?.id_marketing ?? '',
                                true, true
                            )
                        );

                        $('#id_freelance').append(
                            new Option(
                                data.customer_baru?.freelance?.nama_freelance ?? '',
                                data.customer_baru?.freelance?.id_freelance ?? '',
                                true, true
                            )
                        );

                        $('#id_status_progres').append(
                            new Option(
                                data.customer_baru?.progres?.status_progres ?? '',
                                data.customer_baru?.progres?.id_status_progres ?? '',
                                true, true
                            )
                        );

                        $('#id_lokasi').append(
                            new Option(
                                data.customer_baru?.lokasi_kavling?.nama_kavling ?? '',
                                data.customer_baru?.id_lokasi ?? '',
                                true, true
                            )
                        );

                        $('#id_kavling').append(
                            new Option(
                                data.customer_baru?.kavling_peta?.kode_kavling ?? '',
                                data.customer_baru?.id_kavling ?? '',
                                true, true
                            )
                        );


                        $('#ket_cashback').val(custBaru.ket_cashback);
                        $('#jenis_pembelian').val(data.jenis_pembelian).trigger('change');
                        $('#jenis_pembelian').append(
                            new Option(
                                data.customer_baru?.jenis_pembelian ?? '',
                                data.customer_baru?.jenis_pembelian ?? '',
                                true, true
                            )
                        );

                        if (custBaru.jenis_pembelian === 'Booking') {
                            $('#pembayaran_booking').val(formatRupiah(data.pembayaran_booking ||
                                0));
                            $('#tgl_batas_booking').val(data.tgl_batas_booking || '');
                            $('#ket_booking').val(data.keterangan_booking || '');
                        }
                    });
                }
            }).always(function() {
                $btn.prop('disabled', false).html('Detail');
            });
        });

        function formatRupiah(angka) {
            if (!angka) return 'Rp 0';

            var number_string = angka.toString(),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return 'Rp ' + rupiah;
        }

        $('#modalCreate').on('hidden.bs.modal', function() {
            $('#createForm')[0].reset();
            $('#primary_id').val('');
            $('.select-jk').val('').trigger('change');

            $('.select-lokasi').val('').trigger('change');
            $('.select-kavling').val('').trigger('change');
            $('.select-marketing').val('').trigger('change');
            $('.select-freelance').val('').trigger('change');
            $('.select-status-progres').val('').trigger('change');
            $('.select-jenis-pembelian').val('').trigger('change');
            $('.select-bank').val('').trigger('change');
            $('.select-customer-lama').val('').trigger('change');
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();
            $('#createForm :input').prop('disabled', false);
            $('.modal-footer button[type="submit"]').show();
            document.getElementById('formCust').classList.add('d-none');
            document.getElementById('formSelectCustomerLama').classList.remove('d-none');
        });

        $(document).ready(function() {
            $('.select-lokasi').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Lokasi",
            });
            $('.select-customer-lama').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Customer",
                dropdownParent: $('#modalCreate')
            });
            $('.select-jk').select2({
                theme: "bootstrap4",
                minimumResultsForSearch: Infinity,
                placeholder: "Pilih Jenis Kelamin",
            });
            $('.select-kavling').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Kavling",
            });
            $('.select-marketing').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Marketing",
            });

            $('.select-freelance').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Freelance",
            });

            $('.select-status-progres').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Status Progres",
            });

            $('.select-bank').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Bank KPR",
            });

            $('.select-jenis-pembelian').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Jenis Pembelian",
                minimumResultsForSearch: Infinity,
            });
        });

        $(document).ready(function() {
            $('.select-customer-lama').change(function() {
                var customerId = $(this).val();

                if (customerId) {
                    $.ajax({
                        url: '/admin/transaksi/get-customer/' + customerId,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {

                            $('#nik_lama').val(data.data.no_ktp);
                            $('#alamat_customer_lama').val(data.data.alamat);


                            $('#id_lokasi')
                                .append(new Option(data.kavling.nama_lokasi, data.kavling
                                    .id_lokasi, true, true))
                                .trigger('change');


                            $('#id_kavling')
                                .append(new Option(data.kavling.kode_kavling, data.kavling
                                    .id_kavling, true, true))
                                .trigger('change');


                            $('#lokasi_unit').val(data.kavling.lokasi_unit || data.data
                                .kavling_peta.kode_kavling || '');

                            $('#hrg_jual').val(formatRupiah(data.kavling.hrg_jual));
                            $('#hrg_jual_hidden').val(data.kavling.hrg_jual);

                            $('#id_marketing')
                                .append(new Option(data.marketing.nama, data.marketing.id, true,
                                    true))
                                .trigger('change');

                            $('#id_freelance')
                                .append(new Option(data.freelance.nama, data.freelance.id, true,
                                    true))
                                .trigger('change');

                            $('#id_status_progres')
                                .append(new Option(data.progres.status, data.progres.id, true,
                                    true))
                                .trigger('change');

                            $('#ket_cashback').val(data.transaksi.ket_cashback);
                            $('#jenis_pembelian').append(new Option(data.transaksi
                                    .jenis_pembelian, data.transaksi.jenis_pembelian, true,
                                    true))
                                .trigger('change');
                        },
                        error: function() {
                            alert('Gagal mengambil data customer');
                            resetForm();
                        }
                    });
                }
                // } else {
                //     resetForm();
                // }
            });

            // function resetForm() {
            //     // Kosongkan semua field
            //     $('input').val('');
            //     $('select').val('').trigger('change');
            //     $('#hrg_jual').val('');
            //     $('#hrg_jual_hidden').val('');
            // }

            function formatRupiah(angka) {
                return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }
        });

        $(document).on('click', '.delete-button', function(e) {
            e.preventDefault();

            const form = $(this).closest('form');

            if (!form.has('button.delete-button').length) return;

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Data Ganti Nama ini akan dihapus secara permanen!',
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
                                toastr.success("Data Ganti Nama telah dihapus!",
                                    "BERHASIL", {
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
