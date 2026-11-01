
@empty($data)
@else
    <form id="formUtj">
        @csrf
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditLabel">Edit Data UTJ</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="form-group row">
                        <label for="tgl_terima" class="col-sm-3 col-form-label">Tanggal</label>
                        <div class="col-sm-3">
                            <input type="date" value="{{ $data->tgl_terima }}" name="tgl_terima" id="tgl_terima" class="form-control">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="nama_lengkap" class="col-sm-3 col-form-label">Nama Lengkap</label>
                        <div class="col-sm-5">
                            <input type="text" value="{{ $data->nama_lengkap }}" name="nama_lengkap" id="nama_lengkap" class="form-control"
                                required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="no_ktp" class="col-sm-3 col-form-label">No. KTP</label>
                        <div class="col-sm-3">
                            <input type="number" value="{{ $data->no_ktp }}" name="no_ktp" id="no_ktp" class="form-control"
                                >
                        </div>

                        <label for="no_ktp_p" class="col-sm-2 col-form-label">No. KTP Pasangan</label>
                        <div class="col-sm-3">
                            <input type="number" name="no_ktp_p" id="no_ktp_p" class="form-control"
                                >
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="tempat_lahir" class="col-sm-3 col-form-label">Tempat Lahir</label>
                        <div class="col-sm-3">
                            <input type="text"  name="tempat_lahir" id="tempat_lahir"
                                class="form-control">
                        </div>

                        <label for="tgl_lahir" class="col-sm-2 col-form-label">Tanggal Lahir</label>
                        <div class="col-sm-3">
                            <input type="date"  name="tgl_lahir" id="tgl_lahir" class="form-control">
                        </div>
                    </div>


                    <div class="form-group row">
                        <label for="jenis_kelamin" class="col-sm-3 col-form-label ">Jenis Kelamin</label>
                        <div class="col-sm-3">
                            <select name="jenis_kelamin" id="jenis_kelamin" class="form-control select-jk" required>
                                <option value="Laki laki">Laki-Laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="no_wa" class="col-sm-3 col-form-label">No. Telp / WA</label>
                        <div class="col-sm-3">
                            <input type="number" value="{{ $data->no_wa }}" name="no_wa" id="no_wa" class="form-control">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="email" class="col-sm-3 col-form-label">Email</label>
                        <div class="col-sm-3">
                            <input type="email" value="{{ $data->email }}" name="email" id="email" class="form-control">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="npwp" class="col-sm-3 col-form-label">NPWP</label>
                        <div class="col-sm-3">
                            <input type="text" name="npwp" id="npwp" class="form-control">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="alamat" class="col-sm-3 col-form-label">Alamat KTP</label>
                        <div class="col-sm-3">
                            <input type="text" name="alamat" id="alamat" class="form-control">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="alamat_domisili" class="col-sm-3 col-form-label">Alamat Domisili</label>
                        <div class="col-sm-3">
                            <input type="text" name="alamat_domisili" id="alamat_domisili"
                                class="form-control">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="pekerjaan" class="col-sm-3 col-form-label">Pekerjaan</label>
                        <div class="col-sm-3">
                            <input type="text" value="{{ $data->pekerjaan }}" name="pekerjaan" id="pekerjaan" class="form-control">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="penghasilan" class="col-sm-3 col-form-label">Penghasilan</label>
                        <div class="col-sm-3">
                            <input type="text" value="{{ $data->penghasilan }}" name="penghasilan" id="penghasilan" class="form-control"
                                required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="warga_negara" class="col-sm-3 col-form-label">Warga Negara</label>
                        <div class="col-sm-3">
                            <input type="text"  name="warga_negara" id="warga_negara"
                                class="form-control">
                        </div>
                    </div>

                     <div class="form-group row">
                                <label for="id_lokasi" class="col-sm-3 col-form-label">Lokasi Perumahan</label>
                                <div class="col-sm-3">
                                    <select name="id_lokasi" id="id_lokasi" class="form-control select-lokasi">
                                        <option value=""></option>
                                        @foreach ($lokasi as $l)
                                            <option value="{{ $l->id_lokasi }}">{{ $l->nama_kavling }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="id_kavling" class="col-sm-3 col-form-label">Blok / Kode Kavling</label>
                                <div class="col-sm-3">
                                    <select name="id_kavling" id="id_kavling"
                                        class="form-control select-kavling"></select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="hrg_jual" class="col-sm-3 col-form-label">Harga Jual</label>
                                <div class="col-sm-3">
                                    <input type="text" id="hrg_jual" name="hrg_jual_display" class="form-control"
                                        disabled>
                                    <input type="text" name="hrg_jual" id="hrg_jual_hidden" hidden>
                                </div>
                            </div>



                    <div class="form-group row">
                        <label for="id_marketing" class="col-sm-3 col-form-label">Marketing</label>
                        <div class="col-sm-3">
                            <select name="id_marketing" id="id_marketing" class="form-control select-marketing" required>
                                @foreach ($marketing as $m)
                                    <option value="{{ $m->id_marketing }}"
                                        {{ $data->id_marketing == $m->id_marketing ? 'selected' : '' }}>
                                        {{ $m->nama_marketing }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="id_freelance" class="col-sm-3 col-form-label">Marketing Freelance</label>
                        <div class="col-sm-3">
                            <select name="id_freelance" id="id_freelance" class="form-control select-freelance" required>
                                @foreach ($freelance as $f)
                                    <option value="{{ $f->id_freelance }}"
                                        {{ $data->id_freelance == $f->id_freelance ? 'selected' : '' }}>
                                        {{ $f->nama_freelance }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>


                    <div class="form-group row">
                        <label for="referal_fee" class="col-sm-3 col-form-label">Kode Referal</label>
                        <div class="col-sm-3">
                            <input type="number" name="referal_fee" id="referal_fee"
                                class="form-control">
                        </div>
                    </div>


                    <div class="form-group row ">
                        <label for="id_bank" class="col-sm-3 col-form-label">Bank KPR</label>
                        <div class="col-sm-3">
                            <select name="id_bank" id="id_bank" class="form-control select-bank">
                                <option value="">- Pilih -</option>
                                @foreach ($bank as $b)
                                    <option value="{{ $b->id_bank }}" {{ $data->id_bank == $b->id_bank ? 'selected' : '' }}>{{ $b->bank }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="id_status_progres" class="col-sm-3 col-form-label">Status Progres</label>
                        <div class="col-sm-3">
                            <select name="id_status_progres" id="id_status_progres" class="form-control select-progres" required>
                                @foreach ($progres as $p)
                                    <option value="{{ $p->id_status_progres }}"
                                        {{ $data->id_status_progres == $p->id_status_progres ? 'selected' : '' }}>
                                        {{ $p->status_progres }}
                                    </option>
                                @endforeach
                            </select>
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
