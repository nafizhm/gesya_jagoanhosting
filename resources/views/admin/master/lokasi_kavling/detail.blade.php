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
                                    <h3 class="font-weight-bold text-lg">Data Kavling - {{ $nama_kavling }}</h3>
                                    <div class="d-flex align-items-center">
                                        <a href="{{ route('lokasi-kavling.index') }}"
                                            class="btn btn-secondary btn-sm">Kembali</a>
                                        <a href="{{ route('lokasi-kavling.export', $id) }}"
                                            class="btn btn-primary btn-sm ml-1">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered table-striped data-table w-100">
                                    <thead>
                                        <tr>
                                            <th width="30px">No</th>
                                            <th>Lokasi</th>
                                            @if ($is_cluster)
                                                <th>Cluster</th>
                                            @endif
                                            <th>Panjang</th>
                                            <th>Lebar</th>
                                            <th>Luas</th>
                                            <th>Harga</th>
                                            <th>Status</th>
                                            <th>Keterangan</th>
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
        var audio = new Audio('{{ asset('audio/notification.ogg') }}');
        var id = @json($id);
        var isCluster = @json($is_cluster);

        $(function() {
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ordering: false,
                ajax: '{{ route('lokasi-kavling.detail', ':id') }}'.replace(':id', id),
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'kode_kavling',
                        name: 'kode_kavling'
                    },
                    ...(isCluster ? [{
                        data: 'cluster',
                        name: 'cluster',
                    }] : []),
                    {
                        data: null,
                        name: 'panjang_kanan',
                        render: function(data, type, row) {
                            return 'pjg kanan: <b>' + parseFloat(row.panjang_kanan).toFixed(1) +
                                '</b> m<br>pjg kiri: <b>' + parseFloat(row.panjang_kiri).toFixed(
                                1) +
                                '</b> m';
                        }
                    },
                    {
                        data: null,
                        name: 'lebar_depan',
                        render: function(data, type, row) {
                            return 'lbr depan: <b>' + parseFloat(row.lebar_depan).toFixed(1) +
                                '</b> m<br>lbr belakang: <b>' + parseFloat(row.lebar_belakang)
                                .toFixed(
                                    1) + '</b> m';
                        }
                    },
                    {
                        data: 'luas_tanah',
                        name: 'luas_tanah',
                        render: function(data) {
                            return data + ' m²';
                        }
                    },
                    {
                        data: 'hrg_jual',
                        name: 'hrg_jual',
                    },
                    {
                        data: 'status',
                        name: 'status',
                        className: 'text-center'
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan',
                        orderable: false,
                        searchable: false
                    },
                ],
            });
        });
    </script>
@endpush
