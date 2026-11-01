@extends('admin.layout_admin')
<x-toast />
@section('content')
    <!-- Content Wrapper. Contains page content -->
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
                                    <h3 class="font-weight-bold text-xl">Hak Akses</h3>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="form-group row mb-4">
                                    <label for="" class="col-md-3 col-form-label">Pilih Pengguna</label>
                                    <div class="col-sm-9" style="max-width: 350px;">
                                        <select id="pilih-pengguna" class="form-select select-pengguna"
                                            style="width: 100%;">
                                            <option value=""></option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->username }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <table class="table table-bordered table-striped data-table">
                                    <thead>
                                        <tr>
                                            <th width="50px">No</th>
                                            <th>ID</th>
                                            <th width="200px">Induk Menu</th>
                                            <th>Judul Menu</th>
                                            <th>Route</th>
                                            <th width="70px">Lihat</th>
                                            <th width="70px">Beranda</th>
                                            <th width="70px">Tambah</th>
                                            <th width="70px">Edit</th>
                                            <th width="70px">Hapus</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                                <!-- Tombol Simpan -->
                                @if ($permissions['edit'] == 1)
                                    <div class="text-left mt-4">
                                        <button type="submit" id="submitBtn" class="btn btn-primary">
                                            <span class="spinner-border spinner-border-sm mx-1 d-none" role="status"
                                                aria-hidden="true"></span>
                                            <span class="button-text">SIMPAN HAK AKSES</span>
                                        </button>
                                    </div>
                                @endif
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
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.select-pengguna').select2({
                theme: 'bootstrap4',
                placeholder: 'Pilih Pengguna',
            });
        });

        $(document).ready(function() {
            const selectedUserId = localStorage.getItem('selectedUserId');
            if (selectedUserId) {
                $('#pilih-pengguna').val(selectedUserId).trigger('change');
                localStorage.removeItem('selectedUserId');
            }

            const successMsg = localStorage.getItem('hakAksesSuccess');
            if (successMsg) {
                audio.play();
                toastr.success(successMsg, "BERHASIL", {
                    progressBar: true,
                    timeOut: 3500,
                    positionClass: "toast-bottom-right",
                });
                localStorage.removeItem('hakAksesSuccess');
            }

        });

        var btnSimpan = $('#submitBtn');

        btnSimpan.prop('disabled', true);

        $('#pilih-pengguna').change(function() {
            if ($(this).val()) {
                btnSimpan.prop('disabled', false);
            } else {
                btnSimpan.prop('disabled', true);
            }
        });

        var audio = new Audio('{{ asset('audio/notification.ogg') }}');

        @if (session('success'))
            audio.play();
            toastr.success("{{ session('success') }}", "BERHASIL", {
                progressBar: true,
                timeOut: 3500,
                positionClass: "toast-bottom-right",
            });
        @elseif (session('error'))
            audio.play();
            toastr.error("{{ session('error') }}", "GAGAL!", {
                progressBar: true,
                timeOut: 3500,
                positionClass: "toast-bottom-right",
            });

            let submitBtn = $('#submitBtn');
            let spinner = submitBtn.find('.spinner-border');
            let btnText = submitBtn.find('.button-text');

            spinner.addClass('d-none');
            btnText.text('Simpan');
            submitBtn.prop('disabled', false);
        @endif

        var permissions = @json($permissions);
        var table = $('.data-table').DataTable({
            processing: false,
            serverSide: true,
            ordering: false,
            responsive: true,
            ajax: {
                url: "{{ route('admin.getHakAkses') }}",
                data: function(d) {
                    d.id_user = $('#pilih-pengguna').val();
                    d.permissions = permissions;
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'id',
                    name: 'id',
                    orderable: false,
                    searchable: false,
                    visible: false
                },
                {
                    data: 'induk_menu',
                    name: 'induk_menu',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'title',
                    name: 'title',
                    orderable: false,
                    searchable: true
                },
                {
                    data: 'route_name',
                    name: 'route_name',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'lihat',
                    name: 'lihat',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'beranda',
                    name: 'beranda',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'tambah',
                    name: 'tambah',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'edit',
                    name: 'edit',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'hapus',
                    name: 'hapus',
                    orderable: false,
                    searchable: false
                }
            ],
            columnDefs: [{
                targets: 0,
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            }]
        });

        $('.data-table').on('draw.dt', function() {
            $(document).off('change', 'input[name^="lihat"]').on('change', 'input[name^="lihat"]', function() {
                let rowId = $(this).attr('name').match(/\[(.*?)\]/)[1];
                let checked = $(this).is(':checked');

                $(`input[name="tambah[${rowId}]"], input[name="edit[${rowId}]"], input[name="hapus[${rowId}]"]`)
                    .prop('checked', checked);
            });

            $(document).off('change', 'input[name^="tambah"], input[name^="edit"], input[name^="hapus"]').on(
                'change', 'input[name^="tambah"], input[name^="edit"], input[name^="hapus"]',
                function() {
                    let rowId = $(this).attr('name').match(/\[(.*?)\]/)[1];
                    let lihatCheckbox = $(`input[name="lihat[${rowId}]"]`);

                    if ($(this).is(':checked') && !lihatCheckbox.is(':checked')) {
                        lihatCheckbox.prop('checked', true);
                    }
                });
        });

        $('#pilih-pengguna').change(function() {
            table.draw();
        });

        btnSimpan.click(function() {

            var id_user = $('#pilih-pengguna').val();
            var hak_akses_data = {
                id_user: id_user,
                lihat: {},
                beranda: {},
                tambah: {},
                edit: {},
                hapus: {}
            };

            $('.data-table tbody tr').each(function() {
                var row = $(this);
                var rowData = table.row(row).data();
                var id = rowData.id;

                var lihat = row.find('input[name="lihat[' + id + ']"]').prop('checked') ? 1 : 0;
                var beranda = row.find('input[name="beranda[' + id + ']"]').prop('checked') ? 1 : 0;
                var tambah = row.find('input[name="tambah[' + id + ']"]').prop('checked') ? 1 : 0;
                var edit = row.find('input[name="edit[' + id + ']"]').prop('checked') ? 1 : 0;
                var hapus = row.find('input[name="hapus[' + id + ']"]').prop('checked') ? 1 : 0;

                hak_akses_data.lihat[id] = lihat;
                hak_akses_data.beranda[id] = beranda;
                hak_akses_data.tambah[id] = tambah;
                hak_akses_data.edit[id] = edit;
                hak_akses_data.hapus[id] = hapus;
            });

            let submitBtn = $('#submitBtn');
            let spinner = submitBtn.find('.spinner-border');
            let btnText = submitBtn.find('.button-text');

            spinner.removeClass('d-none');
            btnText.text('Menyimpan...');
            submitBtn.prop('disabled', true);

            $.ajax({
                url: '{{ route('admin.updateHakAkses') }}',
                method: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}',
                    hak_akses_data: hak_akses_data
                },
                success: function(response) {
                    if (response.success) {
                        localStorage.setItem('hakAksesSuccess', response.message);
                        localStorage.setItem('selectedUserId', id_user);
                        location.reload();
                    } else {
                        audio.play();
                        toastr.error("Terjadi kesalahan saat memperbarui Hak Akses.",
                            "GAGAL!", {
                                progressBar: true,
                                timeOut: 3500,
                                positionClass: "toast-bottom-right",
                            });

                        spinner.addClass('d-none');
                        btnText.text('Simpan');
                        submitBtn.prop('disabled', false);
                    }
                },
                error: function() {
                    toastr.error("Terjadi kesalahan saat menghubungi server.", "GAGAL!", {
                        progressBar: true,
                        timeOut: 3500,
                        positionClass: "toast-bottom-right",
                    });

                    spinner.addClass('d-none');
                    btnText.text('Simpan');
                    submitBtn.prop('disabled', false);
                }
            });
        });
    </script>
@endpush
