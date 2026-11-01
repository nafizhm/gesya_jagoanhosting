@extends('admin.layout_admin')
@section('content')
    <x-toast />

    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
            </div><!-- /.container-fluid -->
        </section>
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header p-3">
                        <div class="d-flex align-content-center justify-content-between">
                            <h3 class="font-weight-bold text-xl">Data Prospek</h3>
                            <div class="d-flex align-items-center" style="gap: 8px;">
                                @if ($permissions['tambah'])
                                    <button type="button" class="btn btn-sm btn-info" data-toggle="modal"
                                        data-target="#modalForm">
                                        <i class="fas fa-plus"></i> Tambah Prospek
                                    </button>
                                @endif
                                <button type="button" class="btn border px-3 py-1 btn-xs" onclick="reloadTable()">
                                    Reload
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <table id="table" class="table table-bordered table-striped data-table">
                            <thead>
                                <tr>
                                    <th width="40px">No</th>
                                    <th>Nama User</th>
                                    <th>No.Tlp</th>
                                    <th>Pekerjaan</th>
                                    <th>Rangking</th>
                                    <th>Keterangan</th>
                                    <th width="15%" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </section>

        <!-- /.content -->
    </div>

    <div class="modal fade" id="modalForm" tabindex="-1" role="dialog" aria-labelledby="modalFormLabel" aria-hidden="true"
        data-backdrop="static" data-keyboard="false" data-focus="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title" id="modalFormLabel">Form Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formData">
                    @csrf
                    <input type="hidden" id="primary_id" name="primary_id">
                    <div class="modal-body">
                        <div class="form-group row">
                            <label for="tgl_terima" class="col-sm-4 col-form-label">Tanggal</label>
                            <div class="col-sm-8">
                                <input type="date" name="tgl_terima" id="tgl_terima" class="form-control"
                                    value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="nama_lengkap" class="col-sm-4 col-form-label">Nama Lengkap</label>
                            <div class="col-sm-8">
                                <input type="text" name="nama_lengkap" id="nama_lengkap" class="form-control">
                            </div>
                        </div>


                        <div class="form-group row">
                            <label for="no_wa" class="col-sm-4 col-form-label">No. Telp</label>
                            <div class="col-sm-8">
                                <input type="number" name="no_wa" id="no_wa" class="form-control">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="usia" class="col-sm-4 col-form-label">Usia</label>
                            <div class="col-sm-8">
                                <select name="usia" id="usia" class="form-control select2bs4" style="width: 100%;">
                                    <option value="">-- Pilih Usia --</option>
                                    @for ($i = 17; $i <= 60; $i++)
                                        <option value="{{ $i }}">{{ $i }} tahun</option>
                                    @endfor
                                </select>
                            </div>
                        </div>


                        <div class="form-group row">
                            <label for="pekerjaan" class="col-sm-4 col-form-label">Pekerjaan</label>
                            <div class="col-sm-8">
                                <input type="text" name="pekerjaan" id="pekerjaan" class="form-control">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="penghasilan" class="col-sm-4 col-form-label">Penghasilan</label>
                            <div class="col-sm-8">
                                <input type="text" name="penghasilan" id="penghasilan" class="form-control">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="sumber_informasi" class="col-sm-4 col-form-label">Sumber Informasi</label>
                            <div class="col-sm-8">
                                <input type="text" name="sumber_informasi" id="sumber_informasi"
                                    class="form-control">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="rangking" class="col-sm-4 col-form-label">Rangking</label>
                            <div class="col-sm-8">
                                <select name="rangking" id="rangking" class="form-control select-rank">
                                    <option value="">-- Pilih Rangking --</option>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="C">C</option>
                                    <option value="X">X</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="id_marketing" class="col-sm-4 col-form-label">Marketing</label>
                            <div class="col-sm-8">
                                <select name="id_marketing" id="id_marketing" class="form-control select-marketing">
                                    <option value="">-- Pilih Marketing --</option>
                                    @foreach ($marketing as $m)
                                        <option value="{{ $m->id_marketing }}">{{ $m->nama_marketing }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="id_freelance" class="col-sm-4 col-form-label">Freelance</label>
                            <div class="col-sm-8">
                                <select name="id_freelance" id="id_freelance" class="form-control select-freelance">
                                    <option value="">-- Pilih Freelance --</option>
                                    @foreach ($freelance as $f)
                                        <option value="{{ $f->id_freelance }}">{{ $f->nama_freelance }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        <div class="form-group row">
                            <label for="keterangan_belum" class="col-sm-4 col-form-label">Keterangan</label>
                            <div class="col-sm-8">
                                <input type="text" name="keterangan_belum" id="keterangan_belum"
                                    class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer ">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <span class="spinner-border spinner-border-sm mx-1 d-none" role="status"
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
    </div>
@endsection

@push('scripts')
    <script>
        function formatAngkaRibuan(angka) {
            return angka.replace(/\D/g, '')
                .replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        $(document).on('input', '#penghasilan', function() {
            let nilai = $(this).val();
            let terformat = formatAngkaRibuan(nilai);
            $(this).val(terformat);
        });
    </script>


    <script>
        var audio = new Audio('{{ asset('audio/notification.ogg') }}');
        $(document).ready(function() {
            $('.select2bs4').select2({
                theme: 'bootstrap4',
                placeholder: "Pilih Usia"
            });
            $('.select-rank').select2({
                theme: 'bootstrap4',
                placeholder: "Pilih Ranking"
            });
            $('.select-marketing').select2({
                theme: 'bootstrap4',
                placeholder: "Pilih Marketing"
            });
            $('.select-freelance').select2({
                theme: 'bootstrap4',
                placeholder: "Pilih Freelance"
            });

        });

        function reloadTable() {
            $('#table').DataTable().ajax.reload();
        }

        // function proses utj
        function modalAction(url = '') {
            $('#myModal').load(url, function() {
                $('#myModal').modal('show');


                var audio = new Audio('{{ asset('audio/notification.ogg') }}');

                $('#formUtj').on('submit', function(e) {
                    e.preventDefault();

                    let url = '{{ route('prospekData.createUtj') }}';
                    let method = 'POST';

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
                        success: function() {
                            $('#myModal').modal('hide');
                            audio.play();
                            toastr.success("Data UTJ telah disimpan!", "BERHASIL", {
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
                                toastr.error('Periksa koneksi atau input Anda.',
                                    "Terjadi Kesalahan!", {
                                        closeButton: true,
                                        progressBar: true,
                                        timeOut: 3000,
                                        positionClass: 'toast-bottom-right'
                                    });
                            }
                        }
                    });
                });



                $(document).ready(function() {
                    $('.select-jk').select2({
                        theme: "bootstrap4",
                        minimumResultsForSearch: Infinity,
                        placeholder: "Pilih Jenis Kelamin",
                    });
                    $('.select-lokasi').select2({
                        theme: "bootstrap4",
                        placeholder: "Pilih Lokasi",
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
                    $('.select-bank').select2({
                        theme: "bootstrap4",
                        minimumResultsForSearch: Infinity,
                        placeholder: "Pilih Bank",
                    });
                    $('.select-progres').select2({
                        theme: "bootstrap4",
                        placeholder: "Pilih Progres",
                    });
                });

                $(document).ready(function() {
                    function formatRupiah(angka) {
                        if (!angka) return '';
                        return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                    }

                    $('#id_lokasi').on('change', function() {
                        const idLokasi = $(this).val();

                        $('#id_kavling').empty().append(
                            '<option value="">-- Pilih Kavling --</option>');
                        $('#hrg_jual').val('');
                        $('#hrg_jual_hidden').val('');

                        if (idLokasi) {
                            $.ajax({
                                url: '/admin/prospekData/' + idLokasi + '/get-kavling',
                                type: 'GET',
                                dataType: 'json',
                                success: function(data) {
                                    $.each(data, function(index, item) {
                                        $('#id_kavling').append(
                                            $('<option>', {
                                                value: item.id_kavling,
                                                text: item.kode_kavling,
                                                'data-harga': item
                                                    .hrg_jual
                                            })
                                        );
                                    });
                                }
                            });
                        }
                    });

                    $('#id_kavling').on('change', function() {
                        const harga = $('option:selected', this).data('harga');
                        if (harga) {
                            $('#hrg_jual').val(formatRupiah(harga));
                            $('#hrg_jual_hidden').val(harga);
                        } else {
                            $('#hrg_jual').val('');
                            $('#hrg_jual_hidden').val('');
                        }
                    });
                });
            });
        }

        $(function() {
            var permissions = @json($permissions);
            var showActionColumn = (permissions['edit'] == 1 || permissions['hapus'] == 1);

            var table = $('.data-table').DataTable({
                processing: false,
                serverSide: true,
                responsive: true,
                ordering: false,
                ajax: "{{ route('prospek.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'nama_lengkap',
                        name: 'nama_lengkap'
                    },
                    {
                        data: 'no_wa',
                        name: 'no_wa'
                    },
                    {
                        data: 'pekerjaan',
                        name: 'pekerjaan'
                    },
                    {
                        data: 'rangking',
                        name: 'rangking'
                    },
                    {
                        data: 'keterangan_belum',
                        name: 'keterangan_belum'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        visible: showActionColumn
                    }
                ],
                pageLength: 10,
            });
        });

        $(document).on('click', '[data-target="#modalForm"]', function() {
            $('#modalFormLabel').text('Tambah Data');
        });

        $(document).on('click', '#edit-button', function() {
            var url = $(this).data('url');
            $.get(url, function(response) {
                if (response.status === 'success') {
                    $('#modalFormLabel').text('Edit Data');
                    $('#primary_id').val(response.data.id_prospek);
                    $('#tgl_terima').val(response.data.tgl_terima);
                    $('#nama_lengkap').val(response.data.nama_lengkap);
                    $('#no_wa').val(response.data.no_wa);
                    $('#usia').val(response.data.usia).trigger('change'); // untuk select2
                    $('#pekerjaan').val(response.data.pekerjaan);
                    $('#penghasilan').val(formatAngkaRibuan(response.data.penghasilan.toString()));
                    $('#sumber_informasi').val(response.data.sumber_informasi);
                    $('#rangking').val(response.data.rangking).trigger('change'); // untuk select-rank
                    $('#id_marketing').val(response.data.id_marketing).trigger(
                        'change'); // untuk select-marketing
                    $('#id_freelance').val(response.data.id_freelance).trigger(
                        'change'); // untuk select-freelance
                    $('#keterangan_belum').val(response.data.keterangan_belum);
                }
            });


        });

        $('#modalForm').on('hidden.bs.modal', function() {
            $('#formData')[0].reset();
            $('#primary_id').val('');

            // Reset semua select2
            $('.select2bs4').val('').trigger('change');
            $('.select-rank').val('').trigger('change');
            $('.select-marketing').val('').trigger('change');
            $('.select-freelance').val('').trigger('change');
            $('.select-jk').val('').trigger('change');
            $('.select-lokasi').val('').trigger('change');
            $('.select-kavling').val('').trigger('change');
            $('.select-bank').val('').trigger('change');
            $('.select-progres').val('').trigger('change');

            // Hapus validasi error
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

            let id = $('#primary_id').val();
            let url = id ? '{{ route('prospek.update', ['prospek' => ':id']) }}'.replace(':id', id) :
                '{{ route('prospek.store') }}';
            let method = id ? 'PUT' : 'POST';

            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            let submitBtn = $('#submitBtn');
            let spinner = submitBtn.find('.spinner-border');
            let btnText = submitBtn.find('.button-text');

            spinner.removeClass('d-none');
            btnText.text('Menyimpan...');
            submitBtn.prop('disabled', true);

            let formData = new FormData(this); // Ambil semua data input dalam form
            formData.append('_method', method); // Laravel butuh method override via _method

            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                contentType: false, // Penting untuk FormData
                processData: false, // Penting untuk FormData
                success: function() {
                    $('#modalForm').modal('hide');
                    audio.play();
                    toastr.success("Data telah disimpan!", "BERHASIL", {
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

        // Hapus data
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
