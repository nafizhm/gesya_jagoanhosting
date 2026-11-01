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
                                    <h3 class="font-weight-bold text-lg">Data Kavling</h3>
                                    <div class="d-flex align-items-center">
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered table-striped data-table w-100">
                                    <thead>
                                        <tr>
                                            <th width="30px">No</th>
                                            <th>Nama Cluster</th>
                                            <th>Blok</th>
                                            <th>Panjang</th>
                                            <th>Lebar</th>
                                            <th>Luas</th>
                                            <th>Harga</th>
                                            <th>Keterangan</th>
                                            <th width="120px">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="modal fade" id="modalForm" tabindex="-1" role="dialog" data-focus="false" aria-labelledby="modalFormLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title text-white font-weight-bold" id="modalFormLabel"></h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formData" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="primary_id" name="primary_id">
                    <div class="modal-body">
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Panjang Kanan (m)</label>
                            <div class="col-sm-3">
                                <input type="number" name="panjang_kanan" id="panjang_kanan" class="form-control">
                            </div>
                            <label class="col-sm-3 col-form-label">Panjang Kiri (m)</label>
                            <div class="col-sm-3">
                                <input type="number" name="panjang_kiri" id="panjang_kiri" class="form-control">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Lebar Depan (m)</label>
                            <div class="col-sm-3">
                                <input type="number" name="lebar_depan" id="lebar_depan" class="form-control">
                            </div>
                            <label class="col-sm-3 col-form-label">Lebar Belakang (m)</label>
                            <div class="col-sm-3">
                                <input type="number" name="lebar_belakang" id="lebar_belakang" class="form-control">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Luas Tanah (m²)</label>
                            <div class="col-sm-3">
                                <input type="number" name="luas_tanah" id="luas_tanah" class="form-control">
                            </div>
                            <label class="col-sm-3 col-form-label">Luas Bangunan (m²)</label>
                            <div class="col-sm-3">
                                <input type="number" name="luas_bangunan" id="luas_bangunan" class="form-control">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Tipe Bangunan</label>
                            <div class="col-sm-3">
                                <input type="text" name="tipe_bangunan" id="tipe_bangunan" class="form-control">
                            </div>
                            <label class="col-sm-3 col-form-label">Daya Listrik</label>
                            <div class="col-sm-3">
                                <input type="text" name="daya_listrik" id="daya_listrik" class="form-control">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Harga Per Meter</label>
                            <div class="col-sm-3">
                                <input type="text" name="hrg_meter" id="hrg_meter" class="form-control format-number">
                            </div>
                            <label class="col-sm-3 col-form-label">Harga Jual</label>
                            <div class="col-sm-3">
                                <input type="text" name="hrg_jual" id="hrg_jual" class="form-control format-number">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Biaya Lain</label>
                            <div class="col-sm-3">
                                <input type="text" name="biaya_lain" id="biaya_lain" class="form-control format-number">
                            </div>
                            <label class="col-sm-3 col-form-label">Keterangan</label>
                            <div class="col-sm-3">
                                <input type="text" name="keterangan" id="keterangan" class="form-control">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">No. Sertifikat</label>
                            <div class="col-sm-3">
                                <input type="text" name="no_sertifikat" id="no_sertifikat" class="form-control">
                            </div>
                            <label class="col-sm-3 col-form-label">ID Rumah Sikumbang</label>
                            <div class="col-sm-3">
                                <input type="text" name="id_rumah_sikumbang" id="id_rumah_sikumbang" class="form-control">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Perusahaan</label>
                            <div class="col-sm-9">
                                <select name="id_perusahaan" id="id_perusahaan" class="form-control select-perusahaan">
                                    <option value="">Pilih Perusahaan</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary ms-1" id="submitBtn">
                            <span class="spinner-border spinner-border-sm mx-1 d-none" role="status"
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
        var audio = new Audio('{{ asset('audio/notification.ogg') }}');

        var permissions = @json($permissions);
        var showActionColumn = (permissions['edit'] == 1 || permissions['hapus'] == 1);

        $(function() {
            $('.select-perusahaan').select2({
                theme: 'bootstrap4',
                placeholder: 'Pilih Perusahaan',
                minimumResultsForSearch: 0,
                width: '100%',
                dropdownParent: '#modalForm',
            });
        });

        $(function() {
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ordering: false,
                ajax: "{{ route('kavling.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'nama_cluster',
                        name: 'nama_cluster',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'kode_kavling',
                        name: 'kode_kavling',
                        orderable: false,
                        searchable: true,
                        className: 'text-center',
                    },
                    {
                        data: 'panjang',
                        name: 'panjang',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'lebar',
                        name: 'lebar',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'luas',
                        name: 'luas',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'harga',
                        name: 'harga',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
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

        $(document).on('click', '[data-target="#modalForm"]', function() {
            $('#modalFormLabel').text('Tambah Kavling');
        });

        $(document).on('click', '.edit-button', function() {
            var url = $(this).data('url');

            $.get(url, function(response) {
                if (response.status === 'success') {
                    $('#modalFormLabel').text('Edit Kavling');
                    $('#primary_id').val(response.data.id_kavling);
                    $('#panjang_kanan').val(response.data.panjang_kanan);
                    $('#panjang_kiri').val(response.data.panjang_kiri);
                    $('#lebar_depan').val(response.data.lebar_depan);
                    $('#lebar_belakang').val(response.data.lebar_belakang);
                    $('#luas_tanah').val(response.data.luas_tanah);
                    $('#hrg_meter').val(response.data.hrg_meter);
                    $('#tipe_bangunan').val(response.data.tipe_bangunan);
                    $('#luas_bangunan').val(response.data.luas_bangunan);
                    $('#hrg_jual').val(response.data.hrg_jual);
                    $('#biaya_lain').val(response.data.biaya_lain);
                    $('#keterangan').val(response.data.keterangan);
                    $('#daya_listrik').val(response.data.daya_listrik);
                    $('#id_rumah_sikumbang').val(response.data.id_rumah_sikumbang);
                    $('#no_sertifikat').val(response.data.no_sertifikat);

                    var idLokasi = response.data.id_lokasi;
                    var idPerusahaan = response.data.id_perusahaan;
                    var perusahaanUrl = '{{ route('kavling.getPerusahaanByLokasi', ':id') }}'.replace(':id', idLokasi);

                    $.get(perusahaanUrl, function(res) {
                        var select = $('#id_perusahaan');
                        select.empty().append('<option value="">Pilih Perusahaan</option>');
                        if (res.data && res.data.length > 0) {
                            res.data.forEach(function(item) {
                                var selected = (item.id_perusahaan == idPerusahaan) ? 'selected' : '';
                                select.append('<option value="' + item.id_perusahaan + '" ' + selected + '>' + item.nama_perusahaan + '</option>');
                            });
                        }
                        select.trigger('change');
                    });

                    $('#modalForm').modal('show');
                }
            });
        });

        $('#modalForm').on('hidden.bs.modal', function() {
            $('#formData')[0].reset();
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();
            $('#id_perusahaan').val('').trigger('change');

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
            let url = '{{ route('kavling.update', ['kavling' => ':id']) }}'.replace(':id', id);
            let method = 'PUT';

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
                    toastr.success("Kavling berhasil diupdate!", "BERHASIL", {
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
                }
            });
        });
    </script>
@endpush
