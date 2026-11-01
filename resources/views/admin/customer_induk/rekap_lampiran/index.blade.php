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
                                    <h3 class="font-weight-bold text-xl">Data Rekap Lampiran</h3>
                                </div>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered table-striped data-table">
                                    <thead>
                                        <tr>
                                            <th width="50">No</th>
                                            <th>Nama Customer</th>
                                            <th>Marketing</th>
                                            <th width="7%">Foto KTP</th>
                                            <th width="7%">Foto NPWP</th>
                                            <th width="7%">Foto KK</th>
                                            <th width="7%">FotoPemohon</th>
                                            <th width="7%">Foto KTP Pasangan</th>
                                            <th width="7%">Foto BPJS</th>
                                            <th class="text-center">Action</th>
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
        var permissions = @json($permissions);
        var showActionColumn = (permissions['tambah'] == 1);

        $(function() {
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ordering: false,
                ajax: "{{ route('rekap-lampiran.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'nama_customer',
                        name: 'nama_customer',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'marketing',
                        name: 'marketing',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'KTP',
                        name: 'KTP',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'NPWP',
                        name: 'NPWP',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'KK',
                        name: 'KK',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'Pemohon',
                        name: 'Pemohon',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'KTP_Pasangan',
                        name: 'KTP_Pasangan',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'BPJS',
                        name: 'BPJS',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
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
                }, ]
            });
        });
    </script>
@endpush
