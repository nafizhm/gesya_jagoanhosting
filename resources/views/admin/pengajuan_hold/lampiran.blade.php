@extends('admin.layout_admin')
@section('content')

<div class="content-wrapper">
<section class="content">
    <div class="container-fluid">
        <div class="card mt-4">
            <div class="card-header p-3">
                <h3 class="font-weight-bold text-xl" >Lampiran Dokumen</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    @php
                        $fields = [
                            'foto_pemohon' => 'Foto Pemohon',
                            'foto_ktp_p' => 'Foto KTP Pasangan',
                            'file_bukti' => 'File Bukti Bayar',
                            'foto_ktp' => 'Foto KTP',
                            'foto_npwp' => 'Foto NPWP',
                            'foto_kk' => 'Foto KK',
                            'foto_bpjs' => 'Foto BPJS',
                        ];
                    @endphp

                    @foreach($fields as $field => $label)
                        <div class="col-md-4 mb-4">
                            <div class="card">
                                <div class="card-header text-center">
                                    <strong>{{ $label }}</strong>
                                </div>
                                <div class="card-body text-center">
                                    @if($data->$field)
                                      <img src="{{ route('file.preview', ['filename' => $data->$field]) }}"
                                        alt="{{ $label }}" class="img-fluid mb-2" style="max-height: 180px;">

                                        <div class="text-center">
                                            <button class="btn btn-danger btn-sm" onclick="hapusFile('{{ $field }}')">Hapus</button>
                                        </div>
                                    @else
                                        <input type="file"  onchange="uploadFile(this, '{{ $field }}')">
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
</div>

<script>
    function uploadFile(input, field) {
        let file = input.files[0];
        if (!file) return;

        let formData = new FormData();
        formData.append('file', file);
        formData.append('field', field);
        formData.append('_token', '{{ csrf_token() }}');

        fetch("{{ route('pengajuan-hold.upload', $data->id_reg) }}", {
            method: 'POST',
            body: formData,
        })
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                location.reload();
            } else {
                alert('Upload gagal');
            }
        })
        .catch(err => {
            alert('Terjadi kesalahan');
            console.error(err);
        });
    }

  function hapusFile(field) {
    Swal.fire({
        title: 'Yakin ingin menghapus file ini?',
        text: "File akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch("{{ route('pengajuan-hold.delete-file', $data->id_reg) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ field })
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    Swal.fire(
                        'Terhapus!',
                        'File berhasil dihapus.',
                        'success'
                    ).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Gagal', 'File gagal dihapus.', 'error');
                }
            })
            .catch(err => {
                Swal.fire('Error', 'Terjadi kesalahan saat menghapus.', 'error');
                console.error(err);
            });
        }
    });
}

</script>
@endsection
