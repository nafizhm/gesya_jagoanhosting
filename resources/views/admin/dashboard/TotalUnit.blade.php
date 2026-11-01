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
                                    <h3 class="font-weight-bold text-xl">Total Unit</h3>
                                    <div class="d-flex align-items-center" style="gap: 3px">
                                        <a href="{{ route('dashboard') }}" class="btn btn-secondary align-items-center d-flex btn-sm" ><i
                                                class="fas fa-arrow-left mr-2"></i>
                                            Kembali</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <table id="data-table" class="table table-bordered table-striped table-hover table-sm data-table">
                                    <thead>
                                        <tr>
                                            <th width="5%">No</th>
                                            <th>Nama Cluster</th>
                                            <th>Lokasi</th>
                                            <th>Panjang</th>
                                            <th>Lebar</th>
                                            <th>Luas</th>
                                            <th>Harga</th>
                                        </tr>
                                    </thead>
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
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            let table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('total.unit') }}",
                columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                { data: 'nama_cluster', name: 'nama_cluster', orderable: false, searchable: true },
                { data: 'lokasi', name: 'lokasi', orderable: true, searchable: true },
                { data: 'panjang', name: 'panjang', orderable: false, searchable: false },
                { data: 'lebar', name: 'lebar', orderable: false, searchable: false },
                { data: 'luas', name: 'luas', orderable: false, searchable: false },
                { data: 'harga', name: 'harga', orderable: true, searchable: false },
             ]
            });
        });
    </script>
@endpush
