@extends('admin.layout_admin')

@section('content')
<div class="content-wrapper">
        <div class="container">
          <section class="content-header">
            <div class="container-fluid">
                <h5 class="text-xl font-weight-bold">Data Foto Unit Rumah</h5>
            </div>
          </section>
            <div class="card">
                <div class="card-body">

                  <div class="form-group row">
                       <label class="control-label col-md-2">Lokasi Perumahan</label>
                       <div class="col-md-3">
                        <input type="text" class="form-control" value="{{ $kavling->lokasi->nama_kavling ?? '-' }}" disabled>
                        </div>
                       <label class="control-label col-md-2">Lokasi Kav/Blok</label>
                         <div class="col-md-2">
                        <input type="text" class="form-control" value="{{ $kavling->kode_kavling }}" disabled>
                         </div>
                    </div>

                      <div class="form-group row">
                            <label class="control-label col-md-2"></label>
                            <div class="col-md-3">
                              <a href="#" class="btn btn-info btn-sm" onclick="add()"><i class="fa fa-upload"></i> Upload File</a>&nbsp;                            </div>
                        </div>

        <div class="modal fade" id="modal_form" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-success">
                        <h5 class="modal-title">header</h5>
                    </div>
                    <div class="modal-body">
                <form id="formData" class="form-horizontal" enctype="multipart/form-data"
                    method="POST" action="{{ route('kavling.lampiran.upload', $kavling->id_kavling) }}">
                        @csrf
                        <div class="form-body">
                         <input type="hidden" name="id_kavling" id="id_kavling" value="{{ $kavling->id_kavling }}">

                            <div class="form-group row">
                                <label class="control-label col-md-3">Tanggal Upload</label>
                                <div class="col-md-3">
                                    <input name="tanggal" id="tanggal" class="form-control" type="date" value="{{ date('Y-m-d') }}">
                                    <span class="help-block"></span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3">Nama File</label>
                                <div class="col-md-5">
                                    <input name="nama_file" id="nama_file" class="form-control" type="text">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3">File</label>
                                <div class="col-md-3">
                                    <input name="lampiran" id="lampiran" type="file">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3">Keterangan File</label>
                                <div class="col-md-8">
                                    <input name="keterangan" id="keterangan" class="form-control" type="text">
                                </div>
                            </div>


                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
                    <button type="submit"  class="btn btn-primary">Simpan</button>
                </div>
            </form>
            </div>
        </div>
        </div>

                    <hr>

                    <h4>Daftar Foto</h4>
                <div class="row">
                    @foreach($fotos as $foto)
                        <div class="col-md-3 mb-3 text-center">
                            <img src="{{ asset($foto->lampiran) }}" class="img-fluid mb-2" alt="Foto" style="max-height:200px; object-fit:cover; border:1px solid #ddd; padding:5px;">
                            <form action="{{ route('kavling.destroy', $foto->id_foto) }}" method="POST" class="form-delete">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm btn-delete">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>

                        </div>
                    @endforeach
                </div>

                </div>
            </div>
    </div>
</div>
@endsection
@push('scripts')

<script>
    var audio = new Audio('{{ asset('audio/notification.ogg') }}');

         @if(session('success'))
            audio.play();
            toastr.success('{{ session('success') }}', 'Berhasil!', {
                closeButton: true,
                progressBar: true,
                timeOut: 3000,
                positionClass: 'toast-bottom-right'
            });
        @endif

         $(document).ready(function() {
            @if ($errors->any())
                $('#modal_form').modal('show');
            @endif
        });

           $(document).ready(function() {
        @if ($errors->any())
            @foreach ($errors->all() as $error)
                audio.play();
                toastr.error("{{ $error }}", "Error!", {
                    closeButton: true,
                    progressBar: true,
                    timeOut: 5000,
                    positionClass: 'toast-bottom-right'
                });
            @endforeach
        @endif
    });

      document.addEventListener('DOMContentLoaded', function () {
        const deleteForms = document.querySelectorAll('.form-delete');

        deleteForms.forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    text: "Data yang dihapus tidak bisa dikembalikan.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });

      $('#modalForm').on('hidden.bs.modal', function() {
            $('#formData')[0].reset();
            $('#primary_id').val('');
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();
        });

    function add()
   {
        var id_kavling = document.getElementById('id_kavling').value;
        if(id_kavling == ''){
          alert('Terjadi kesalahan hubungi programmer anda');
          exit;
        }
       $('#formData')[0].reset();
       $('.form-group').removeClass('has-error');
       $('.help-block').empty();

       $('#modal_form').modal('show');
       $('.modal-title').text('Upload File Nasabah');
   }
</script>
@endpush
