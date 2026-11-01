<!-- resources/views/admin/bank/index.blade.php -->
@extends('admin.layout_admin')
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                    </div>
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </section>
        <div class="container-fluid">
            <div class="card">
                <div class="card-header p-3">
                    <div class="d-flex align-content-center justify-content-between">
                        <h3 class="font-weight-bold text-xl">Data Pembayaran</h3>
                        <div class="d-flex align-items-center" style="gap: 8px;">
                            <a class="btn btn-warning btn-tambah" style="height: 38px; padding: 0.375rem 0.75rem;"
                                href="{{ route('pembayaran.jatuhTempo') }}">
                                Jatuh Tempo
                            </a>
                            <a class="btn btn-info btn-tambah" style="height: 38px; padding: 0.375rem 0.75rem;"
                                href="{{ route('pembayaran.rekap') }}">
                                Rekap Pembayaran
                            </a>
                            <select class="form-control form-select select-1" style="width: 150px; height: 38px;">
                                <option value="">Semua Status</option>
                                <option value="Lunas">Lunas</option>
                                <option value="Terhutang">Terhutang</option>
                            </select>
                            <select class="form-control form-select select-2" style="width: 180px; height: 38px;">
                                <option value="">Semua Progres</option>
                                @foreach ($progreslists as $list)
                                    <option value="{{ $list->id_status_progres }}">{{ $list->status_progres }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table id="data-table" class="table table-bordered table-striped data-table">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">Nama Nasabah</th>
                                <th width="40%">Rincian Tagihan</th>
                                <th width="10%">Status</th>
                                <th width="18%">Jumlah Tagihan</th>
                                <th width="10%" class="text-center">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.select-1').select2({
                theme: "bootstrap4",
                minimumResultsForSearch: Infinity,
            });
            $('.select-2').select2({
                theme: "bootstrap4",
            });
            $('.select-jenis-pembayaran').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Jenis Pembayaran",
                minimumResultsForSearch: Infinity,
            });
        });

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            var permissions = @json($permissions);
            let table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ordering: false,
                ajax: {
                    url: "{{ route('pembayaran') }}",
                    data: function(d) {
                        d.status_bayar = $('.select-1').val();
                        d.status_progres = $('.select-2').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'nasabah',
                        name: 'nama_lengkap',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'rincian_tagihan',
                        name: 'rincian_tagihan',
                        orderable: false,
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                    },
                    {
                        data: 'jumlah_tagihan',
                        name: 'jumlah_tagihan',
                        orderable: false,
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ]
            });

            $('.select-1, .select-2').on('change', function() {
                table.ajax.reload();
            });

        });

        var audio = new Audio('{{ asset('audio/notification.ogg') }}');

        $(document).on('click', '.btn-hapus', function(e) {
            e.preventDefault();
            let url = $(this).data('url');
            let audio = document.getElementById('notif-audio');

            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: "Data akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '<span class="swal-btn-text">Ya, Hapus!</span>',
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
                            url: url,
                            type: 'DELETE',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(res) {
                                if (res.status) {
                                    audio.play();
                                    toastr.success(res.message, "Berhasil!", {
                                        closeButton: true,
                                        progressBar: true,
                                        timeOut: 3000,
                                        positionClass: 'toast-bottom-right'
                                    });

                                    $('#datatable-id').DataTable().ajax.reload();
                                    Swal.close();
                                } else {
                                    audio.play();
                                    toastr.error(res.message, "Gagal!", {
                                        closeButton: true,
                                        progressBar: true,
                                        timeOut: 3000,
                                        positionClass: 'toast-bottom-right'
                                    });

                                    btnText.innerHTML = 'Ya, Hapus!';
                                    confirmBtn.disabled = false;
                                }
                            },
                            error: function() {
                                audio.play();
                                toastr.error(
                                    "Terjadi kesalahan saat menghapus data",
                                    "Gagal!", {
                                        closeButton: true,
                                        progressBar: true,
                                        timeOut: 3000,
                                        positionClass: 'toast-bottom-right'
                                    });

                                btnText.innerHTML = 'Ya, Hapus!';
                                confirmBtn.disabled = false;
                            }
                        });
                    });
                }
            });
        });

        $('#modalPemasukan').on('hidden.bs.modal', function() {
            $('#formData')[0].reset();
            $('#lokasi_b').val('').trigger('change');
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

            let submitBtn = $('#submitBtn');
            let spinner = submitBtn.find('.spinner-border');
            let btnText = submitBtn.find('.button-text');

            spinner.removeClass('d-none');
            btnText.text('Menyimpan...');
            submitBtn.prop('disabled', true);

            let id = $('#nasabah_b').val();
            let url = '{{ route('pembayaran.tambah-pemasukan', ['id' => ':id']) }}'.replace(':id',
                id);
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
                success: function(response) {
                    $('#modalForm').modal('hide');
                    audio.play();
                    let msg = "Pemasukan berhasil ditambahkan!";
                    toastr.success(msg, "BERHASIL", {
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
                        toastr.error(
                            "Terjadi kesalahan saat menyimpan data",
                            "Gagal!", {
                                closeButton: true,
                                progressBar: true,
                                timeOut: 3000,
                                positionClass: 'toast-bottom-right'
                            });
                    }
                },
                complete: function() {
                    spinner.addClass('d-none');
                    btnText.text('Simpan');
                    submitBtn.prop('disabled', false);
                }
            });
        });
    </script>
@endpush