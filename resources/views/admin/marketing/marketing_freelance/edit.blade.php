@empty($data)
@else
    <form action="{{ route('marketing-freelance.update', $data->id_freelance) }}" method="POST" id="form-edit" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="modal-dialog modal-lg" id="modal" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title" id="modalEditLabel">Form Freelance</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="form-group row">
                        <label for="nama_freelance" class="col-sm-3 col-form-label">Nama Freelance</label>
                        <div class="col-sm-5">
                            <input type="text" value="{{ $data->nama_freelance }}" name="nama_freelance"  class="form-control nama_freelance">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="alamat" class="col-sm-3 col-form-label">Alamat</label>
                        <div class="col-sm-5">
                            <input type="text" value="{{ $data->alamat }}" name="alamat"  class="form-control alamat"
                                required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="jenis_kelamin" class="col-sm-3 col-form-label ">Jenis Kelamin</label>
                        <div class="col-sm-3">
                            <select name="jenis_kelamin"  class="form-control select-jk jenis_kelamin" required>
                                <option value="Laki-laki" {{ $data->status == 'Laki-laki' ? 'selected' : '' }}>Laki - laki</option>
                                <option value="Perempuan" {{ $data->status == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="pekerjaan" class="col-sm-3 col-form-label">Pekerjaan</label>
                        <div class="col-sm-5">
                            <input type="text" value="{{ $data->pekerjaan }}" name="pekerjaan" class="form-control pekerjaan"
                                >
                        </div>
                        </div>

                        <div class="form-group row">
                        <label for="no_telp_freelance" class="col-sm-3 col-form-label">No. Telp</label>
                        <div class="col-sm-5">
                            <input type="number" value="{{ $data->no_telp_freelance }}" name="no_telp_freelance"  class="form-control no_telp_freelance"
                                >
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="foto" class="col-sm-3 col-form-label">Foto</label>
                        <div class="col-sm-3">
                            <input type="file" name="foto"  class="foto">
                        </div>
                        <div class="col-sm-3">
                            @if($data->foto)
                                <img src="{{ asset('photo/' . $data->foto) }}" alt="Foto Freelance" width="80" height="80" style="border:1px solid #ccc; object-fit: cover;">
                            @else
                                <p class="text-muted">Tidak ada foto</p>
                            @endif
                        </div>
                    </div>


                    <div class="form-group row">
                        <label for="status" class="col-sm-3 col-form-label ">Status</label>
                        <div class="col-sm-3">
                            <select name="status"  class="form-control select-status status" required>
                                <option value="1" {{ $data->status == '1' ? 'selected' : '' }}>Aktif</option>
                                <option value="0" {{ $data->status == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                            </select>
                        </div>
                    </div>


                    <div class="form-group row">
                        <label for="nama_bank" class="col-sm-3 col-form-label">Nama Bank</label>
                        <div class="col-sm-5">
                            <input type="text" value="{{ $data->nama_bank }}" name="nama_bank" class="form-control nama_bank">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="no_rekening" class="col-sm-3 col-form-label">No. Rekening</label>
                        <div class="col-sm-5">
                            <input type="number" value="{{ $data->no_rekening }}" name="no_rekening"  class="form-control no_rekening">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="atas_nama" class="col-sm-3 col-form-label">Atas Nama</label>
                        <div class="col-sm-5">
                            <input type="text" value="{{ $data->atas_nama }}" name="atas_nama" class="form-control atas_nama">
                        </div>
                    </div>


                </div>

                <div class="modal-footer ">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </div>
    </form>

@endempty
