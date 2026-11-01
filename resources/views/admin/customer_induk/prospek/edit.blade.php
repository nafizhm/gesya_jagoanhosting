@empty($data)
@else
<form action="{{ route('prospek.update', $data->id_prospek) }}" method="POST" id="form-edit">
    @csrf
    @method('PUT')
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
  <div class="modal-header">
    <h5 class="modal-title" id="modalEditLabel">Edit Data User Prospek
    </h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  <div class="modal-body">
    <div class="form-group row">
      <label for="tgl_terima" class="col-sm-4 col-form-label">Tanggal</label>
      <div class="col-sm-8">
        <input type="text" value="{{ $data->tgl_terima }}" name="tgl_terima"  class="form-control" required>
      </div>
    </div>

    <div class="form-group row">
      <label for="nama_lengkap" class="col-sm-4 col-form-label">Nama Lengkap</label>
      <div class="col-sm-8">
        <input type="text" value="{{ $data->nama_lengkap }}" name="nama_lengkap" class="form-control" >
      </div>
    </div>

    <div class="form-group row">
      <label for="no_wa" class="col-sm-4 col-form-label">No. Telp</label>
      <div class="col-sm-8">
        <input type="text" value="{{ $data->no_wa }}" name="no_wa" class="form-control" >
      </div>
    </div>

    <div class="form-group row">
        <label for="usia" class="col-sm-4 col-form-label">Usia</label>
        <div class="col-sm-8">
        <input type="number" value="{{ $data->usia }}" name="usia"  class="form-control" >
        </div>
    </div>

    <div class="form-group row">
        <label for="pekerjaan" class="col-sm-4 col-form-label">Pekerjaan</label>
        <div class="col-sm-8">
        <input type="text" value="{{ $data->pekerjaan }}" name="pekerjaan" class="form-control">
        </div>
    </div>

    <div class="form-group row">
        <label for="penghasilan" class="col-sm-4 col-form-label">Penghasilan</label>
        <div class="col-sm-8">
        <input type="number" value="{{ $data->penghasilan }}" name="penghasilan" class="form-control">
        </div>
    </div>

    <div class="form-group row">
        <label for="sumber_informasi" class="col-sm-4 col-form-label">Sumber Informasi</label>
        <div class="col-sm-8">
        <input type="text" value="{{ $data->sumber_informasi }}" name="sumber_informasi" class="form-control">
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-4 col-form-label">Rangking</label>
        <div class="col-sm-8">
            <select name="rangking" class="form-control" required>
                @foreach ($rangking as $item)
                    <option value="{{ $item }}" {{ $data->rangking == $item ? 'selected' : '' }}>{{ $item }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-4 col-form-label">Marketing</label>
        <div class="col-sm-8">
            <select name="id_marketing" class="form-control" required>
                <option value="">-- Pilih Marketing --</option>
                @foreach($marketing as $m)
                <option value="{{ $m->id_marketing }}" {{ $data->id_marketing == $m->id_marketing ? 'selected' : '' }}>
                    {{ $m->nama_marketing }}
                </option>
                @endforeach
            </select>
        </div>
    </div>


    <div class="form-group row">
        <label class="col-sm-4 col-form-label">Freelance</label>
        <div class="col-sm-8">
            <select name="id_freelance" class="form-control" required>
                <option value="">-- Pilih Freelance --</option>
                @foreach($freelance as $f)
                <option value="{{ $f->id_freelance }}" {{ $data->id_freelance == $f->id_freelance ? 'selected' : '' }}>
                    {{ $f->nama_freelance }}
                </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="form-group row">
        <label for="keterangan_belum" class="col-sm-4 col-form-label">Keterangan</label>
        <div class="col-sm-8">
        <input type="text" value="{{ $data->keterangan_belum }}" name="keterangan_belum" class="form-control">
        </div>
    </div>


  </div>

  <div class="modal-footer">
      <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
      <button type="submit" class="btn btn-primary">Simpan</button>
  </div>
  </div>
  </div>
</form>


@endempty
