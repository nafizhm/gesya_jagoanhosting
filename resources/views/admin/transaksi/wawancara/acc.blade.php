@empty($data)
@else
    <form id="form-sp3k" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title" id="modalEditLabel">Form Update Wawancara
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_wawancara" value="{{ $data->id_wawancara }}">

                    <div class="form-group row mb-3">
                        <label for="id_bank" class="col-sm-2 col-form-label">Nama Bank</label>
                        <div class="col-sm-5">
                            <select class="form-control select-bank" id="id_bank" name="id_bank">
                                <option value=""></option>
                                @foreach ($bankList as $bank)
                                    <option value="{{ $bank->id_bank }}"
                                        {{ $data->id_bank == $bank->id_bank ? 'selected' : '' }}>
                                        {{ $bank->bank }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>


                    <div class="form-group row">
                        <label for="acc_plafon" class="col-sm-2 col-form-label">Acc Plafon</label>
                        <div class="col-sm-3">
                            <input type="text" name="acc_plafon" id="acc_plafon" class="form-control format-number">
                        </div>
                        <label for="tenor" class="col-sm-2 col-form-label">Tenor</label>
                        <div class="col-sm-3">
                            <input type="number" name="tenor" id="tenor" class="form-control">
                        </div>
                    </div>

                    <div class="form-group row mb-3">
                        <label for="tgl_terbit_sp3k" class="col-sm-2 col-form-label">Tanggal SP3K</label>
                        <div class="col-sm-3">
                            <input type="date" name="tgl_terbit_sp3k" id="tgl_terbit_sp3k" class="form-control" value="{{ date('Y-m-d')}}">
                        </div>
                    </div>

                    <div class="form-group row mb-3">
                        <label for="id_notaris" class="col-sm-2 col-form-label">Nama Notaris</label>
                        <div class="col-sm-8">
                            <select class="form-control select-notaris" id="id_notaris" name="id_notaris">
                                <option value=""></option>
                                @foreach ($notarisList as $notaris)
                                    <option value="{{ $notaris->id_notaris }}"
                                        {{ $data->id_notaris == $notaris->id_notaris ? 'selected' : '' }}>
                                        {{ $notaris->nama_notaris }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row mb-3">
                        <label for="catatan_acc" class="col-sm-2 col-form-label">Catatan ACC</label>
                        <div class="col-sm-8">
                            <textarea name="catatan_acc" id="catatan_acc" class="form-control"></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary " id="submitBtnEdit">
                        <span class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true"></span>
                        <span class="button-text">Simpan</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
@endempty
