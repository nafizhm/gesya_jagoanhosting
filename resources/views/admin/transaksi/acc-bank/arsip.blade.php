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
                                    <h3 class="font-weight-bold text-xl">Data Arsip SP3K</h3>
                                    <div class="d-flex align-items-center" style="gap: 3px">
                                        <a href="{{ route('acc-bank.index') }}" class="btn btn-dark btn-sm">Acc Bank</a>

                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered w-100 small table-striped data-table">
                                    <thead>
                                        <tr>
                                            <th width="3%">No</th>
                                            <th>Nama Customer</th>
                                            <th>Lokasi Rumah</th>
                                            <th width="10%">Bank</th>
                                            <th>Harga Jual</th>
                                            <th>ACC Plafon</th>
                                            <th>Tgl Wawancara</th>
                                            <th>Tgl Expired</th>
                                            <th>Status</th>
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
@endsection
@push('scripts')
    <script>
        function reloadTable() {
            $('#table').DataTable().ajax.reload();
        }

        var audio = new Audio('{{ asset('audio/notification.ogg') }}');

        $(function() {
            var table = $('.data-table').DataTable({
                processing: false,
                serverSide: false,
                ordering: false,
                responsive: true,
                ajax: "{{ route('acc-bank.arsip') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'wawancara.nasabah.nama_lengkap',
                        name: 'wawancara.nasabah.nama_lengkap',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'kode_kavling',
                        name: 'kode_kavling',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'bank',
                        name: 'bank',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'harga_jual',
                        name: 'harga_jual',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'acc_plafon',
                        name: 'acc_plafon',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'tgl_terbit_sp3k',
                        name: 'tgl_terbit_sp3k',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'tgl_expired',
                        name: 'tgl_expired',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'sisa_hari',
                        name: 'sisa_hari',
                        orderable: false,
                        searchable: false,
                    },
                ],
                columnDefs: [{
                    targets: 0,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                }, ]
            });
        });
    </script>
@endpush
