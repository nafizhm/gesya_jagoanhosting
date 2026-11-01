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
                                    <h3 class="font-weight-bold text-lg">Data Lokasi Kavling</h3>
                                    <div class="d-flex align-items-center">
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered table-striped data-table w-100">
                                    <thead>
                                        <tr>
                                            <th width="30px">No</th>
                                            <th>Nama Kavling</th>
                                            <th>Alamat</th>
                                            <th>Jumlah Kavling</th>
                                            <th>Detail</th>
                                            <th width="100px">Action</th>
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
                            <label class="col-sm-3 col-form-label">Nama Lokasi</label>
                            <div class="col-sm-3">
                                <input type="text" name="nama_kavling" id="nama_kavling" class="form-control">
                            </div>
                            <label class="col-sm-3 col-form-label">Nama Singkat</label>
                            <div class="col-sm-3">
                                <input type="text" name="nama_singkat" id="nama_singkat" class="form-control">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Header</label>
                            <div class="col-sm-3">
                                <input type="text" name="header" id="header" class="form-control">
                            </div>
                            <label class="col-sm-3 col-form-label">Alamat</label>
                            <div class="col-sm-3">
                                <input type="text" name="alamat" id="alamat" class="form-control">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Nama Perusahaan</label>
                            <div class="col-sm-9">
                                <select name="id_perusahaan[]" id="id_perusahaan" class="form-control" multiple="multiple">
                                    @foreach($perusahaanList as $p)
                                        <option value="{{ $p->id_perusahaan }}">{{ $p->nama_perusahaan }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Jenis Pembelian</label>
                            <div class="col-sm-3">
                                <select name="stt_tampil" id="stt_tampil" class="form-control select-tampil">
                                    <option value=""></option>
                                    <option value="1">Penjualan</option>
                                    <option value="2">Proyek</option>
                                    <option value="3">Keduanya</option>
                                </select>
                            </div>
                            <label class="col-sm-3 col-form-label">Cluster</label>
                            <div class="col-sm-3">
                                <select name="is_cluster" id="is_cluster" class="form-control select-cluster">
                                    <option value=""></option>
                                    <option value="1">Ya (Cluster)</option>
                                    <option value="0">Tidak (Non-Cluster)</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Urutan</label>
                            <div class="col-sm-3">
                                <input type="number" name="urutan" id="urutan" class="form-control">
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
        var perusahaanOptions = $('#id_perusahaan option').clone();

        $(document).ready(function() {
            $('#id_perusahaan').select2({
                theme: 'bootstrap4',
                placeholder: 'Pilih Perusahaan',
                minimumResultsForSearch: 0,
                width: '100%',
                dropdownParent: '#modalForm',
            });

            $('.select-tampil').select2({
                theme: 'bootstrap4',
                placeholder: 'Pilih Jenis Pembelian',
                minimumResultsForSearch: 0,
                width: '100%',
                dropdownParent: '#modalForm',
            });

            $('.select-cluster').select2({
                theme: 'bootstrap4',
                placeholder: 'Pilih Cluster',
                minimumResultsForSearch: 0,
                width: '100%',
                dropdownParent: '#modalForm',
            });
        });

        var audio = new Audio('{{ asset('audio/notification.ogg') }}');

        var permissions = @json($permissions);
        var showActionColumn = (permissions['edit'] == 1 || permissions['hapus'] == 1);

        $(function() {
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ordering: false,
                ajax: "{{ route('lokasi-kavling.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'nama_kavling',
                        name: 'nama_kavling',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'alamat',
                        name: 'alamat',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'jumlah_kavling',
                        name: 'jumlah_kavling',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'detail',
                        name: 'detail',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
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

        $(document).on('click', '.edit-button', function() {
            var url = $(this).data('url');

            $.get(url, function(response) {
                if (response.status === 'success') {
                    $('#modalFormLabel').text('Edit Lokasi Kavling');
                    $('#primary_id').val(response.data.id_lokasi);
                    $('#nama_kavling').val(response.data.nama_kavling);
                    $('#nama_singkat').val(response.data.nama_singkat);
                    $('#header').val(response.data.header);
                    $('#alamat').val(response.data.alamat);
                    $('#urutan').val(response.data.urutan);
                    $('#stt_tampil').val(response.data.stt_tampil).trigger('change');
                    $('#is_cluster').val(response.data.is_cluster).trigger('change');

                    $('#id_perusahaan').empty().append(perusahaanOptions.clone());
                    if (response.data.perusahaan && response.data.perusahaan.length > 0) {
                        var selectedIds = response.data.perusahaan.map(item => String(item.id_perusahaan));
                        $('#id_perusahaan').val(selectedIds).trigger('change');
                    }

                    $('#modalForm').modal('show');
                }
            });
        });

        $('#modalForm').on('hidden.bs.modal', function() {
            $('#formData')[0].reset();
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();
            $('#id_perusahaan').empty().append(perusahaanOptions.clone()).val('').trigger('change');
            $('#stt_tampil').val('').trigger('change');
            $('#is_cluster').val('').trigger('change');

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
            let url = '{{ route('lokasi-kavling.update', ['lokasi_kavling' => ':id']) }}'.replace(':id', id);
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
                    toastr.success("Lokasi kavling berhasil diupdate!", "BERHASIL", {
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
