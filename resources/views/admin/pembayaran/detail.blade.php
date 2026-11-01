<!-- resources/views/admin/bank/index.blade.php -->
@extends('admin.layout_admin')
@section('content')
    <style>
        .table tr td,
        .table tr th {
            padding: 6px 10px;
            font-size: 14px;
            line-height: 1.2;
        }
    </style>

    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">

            </div><!-- /.container-fluid -->
        </section>

        <div class="container-fluid">
            <div class="card">
                <div class="card-header p-3">
                    <h3 class="font-weight-bold text-lg">Detail Pembayaran</h3>
                </div>
                <div class="card-body">

                    <div class="row">
                        <div class="col col-7">
                            <div class="form-group row">
                                <label class="control-label col-md-4">Nama Nasabah</label>
                                <div class="col-md-7">
                                    <input name="nama_lengkap" value="{{ $customer->nama_lengkap }}" class="form-control"
                                        type="text" readonly>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-4">Lokasi Perumahan</label>
                                <div class="col-md-7">
                                    <input name="lokasi_perumanah" class="form-control" type="text"
                                        value="{{ $lokasi }} # {{ $kavling }}" readonly>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-4">Jenis Pembayaran</label>
                                <div class="col-md-7">
                                    <input name="ket_cashback" id="ket_cashback" class="form-control" type="text"
                                        value="{{ $customer->jenis_pembelian }}" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Awal Kanan -->
                        <div class="col col-5">
                            <div class="form-group row">
                                <label class="control-label col-md-4">Total Tagihan</label>
                                <div class="col-md-4">
                                    <input class="form-control text-right" type="text" id="total_tagihan_all"
                                        value="Rp. {{ number_format($totalTagihan, 0, ',', '.') }}" readonly>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-4">Jumlah Bayar</label>
                                <div class="col-md-4">
                                    <input class="form-control text-right" type="text" id="jumlah_bayar_all"
                                        value="Rp. {{ number_format($jumlahBayar, 0, ',', '.') }}" readonly>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-4">Sisa Bayar</label>
                                <div class="col-md-4">
                                    <input class="form-control text-right" type="text" id="sisa_bayar_all"
                                        value="Rp. {{ number_format($sisaBayar, 0, ',', '.') }}" readonly>
                                </div>
                            </div>
                            <a href="{{ route('customer.cetak_rekap', $customer->id_customer) }}" target="_blank"
                                class="btn btn-sm btn-primary">

                                <i class="fa fa-print"></i> Cetak Rekap
                            </a>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col col-1"></div>
                        <div class="col col-10">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center">
                                    <h5>- Tagihan - </h5>
                                </div>
                                @if (isset($permissions['tambah']) && $permissions['tambah'] == 1)
                                    <button class="btn btn-primary btn-sm" data-toggle="modal"
                                        data-target="#modalTagihan"><i class="fa fa-home"></i>
                                        Tambah Tagihan</button>
                                @endif
                            </div>

                            <table class="table table-bordered table-tagihan">
                                <thead>
                                    <tr class="table-primary">
                                        <th width="5%">No</th>
                                        <th width="55%">Jenis Tagihan</th>
                                        <th width="12%">Harga</th>
                                        <th width="20%">Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2" align="right"><b>Total Tagihan</b></td>
                                        <td align="right" id="total-tagihan"><b>Rp. 0</b></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>

                            <hr>

                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center">
                                    <h5>- Pemasukan -</h5>
                                </div>
                                @if (isset($permissions['tambah']) && $permissions['tambah'] == 1)
                                    <button class="btn btn-success btn-sm" data-toggle="modal"
                                        data-target="#modalPemasukan"><i class="fa fa-money-bill"></i>
                                        Tambah Pemasukan</button>
                                @endif
                            </div>

                            <table class="table table-bordered table-pemasukan">
                                <thead>
                                    <tr class="table-success">
                                        <th width="5%">No</th>
                                        <th width="13%">Tanggal</th>
                                        <th width="35%">Jenis Pembayaran</th>
                                        <th width="15%">Jenis</th>
                                        <th width="12%">Jumlah</th>
                                        <th width="22%">Action</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" align="right"><b>Total Pemasukan</b></td>
                                        <td align="right"><b id="total-pemasukan">Rp. 0</b></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>


                            <!-- awal Pengeluaran -->
                            {{-- <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center">
                                    <h5>- Pengeluaran -</h5>
                                </div>
                                <button class="btn btn-danger btn-sm" data-toggle="modal"
                                    data-target="#modalPengeluaran"><i class="fa fa-money-bill"></i>
                                    Tambah Pengeluaran</button>
                            </div>


                            <table class="table table-bordered">
                                <tr class="table-danger">
                                    <th width="5%">No</th>
                                    <th width="13%">Tanggal</th>
                                    <th width="35%">Jenis Pembayaran</th>
                                    <th width="15%">Jenis</th>
                                    <th width="12%">Jumlah</th>
                                    <th width="22%">Action</th>
                                </tr>

                                @php $no = 1; @endphp
                                @foreach ($pengeluaranList as $pengeluaran)
                                    <tr>
                                        <td>{{ $no++ }}</td>
                                        <td>{{ formatTanggalIndo($pengeluaran->tanggal) }}
                                        </td>
                                        <td>{{ $pengeluaran->keterangan }}</td>
                                        <td>{{ $pengeluaran->nama_jenis }}</td>
                                        <td align="right">Rp. {{ number_format($pengeluaran->jumlah, 0, ',', '.') }}</td>
                                        <td align="center">
                                            <a href="{{ route('pengeluaran.Cetak', $pengeluaran->id_pembayaran_keluar) }}" target="_blank" class="btn btn-xs btn-primary">
                                                Cetak
                                            </a>
                                                                                        <form
                                                action="{{ route('pembayaran.DeletePengeluaran', $pengeluaran->id_pembayaran_keluar) }}"
                                                method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="delete-button btn btn-xs btn-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach

                                <tr>
                                    <td colspan="4" align="right"><b>Total Pengeluaran</b></td>
                                    <td align="right"><b>Rp. {{ number_format($totalPengeluaran, 0, ',', '.') }}</b></td>
                                    <td></td>
                                </tr>
                            </table> --}}

                            <!-- Awal tabel tagihan -->
                        </div>
                    </div>
                    {{-- <style>
                        .red-hr {
                            border: 1px solid red;
                            /* Mengubah warna garis menjadi merah */
                            border: 2px solid red;
                            /* Menambah ketebalan garis */
                        }
                    </style>

                    <br> --}}
                    {{-- <div class="row">
                        <div class="col col-1"></div>
                        <div class="col col-12">
                            <hr class="red-hr">
                        </div>
                    </div> --}}

                    {{-- <div class="row">
                        <div class="col col-1"></div>
                        <div class="col col-10">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center">
                                    <h5>- INVOICE -</h5>
                                </div>
                                <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modalInvoice"><i
                                        class="fa fa-file"></i>
                                    Buat Invoice</button>
                            </div>

                            <table class="table table-bordered">
                                <tr class="table-warning">
                                    <th width="5%">No</th>
                                    <th width="17%">No Invoice</th>
                                    <th width="17%">Tanggal</th>
                                    <th width="25%">Deskripsi</th>
                                    <th width="15%">Jumlah</th>
                                    <th width="15%">Action</th>
                                </tr>

                            </table>
                        </div>

                        <br>
                        <br>
                        <br>
                        <br>



                    </div> --}}
                </div>
            </div>
        </div>
    </div>

    {{-- modal tagihan --}}
    <div class="modal fade" id="modalTagihan" tabindex="-1" role="dialog" data-focus="false"
        aria-labelledby="modalTagihanLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title text-white font-weight-bold" id="modalTagihanLabel">Tambah Tagihan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formTagihan">
                    @csrf
                    <input type="hidden" id="primary_id" name="primary_id" value="{{ $customer->id_customer }}">
                    <div class="modal-body">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Jenis Tagihan</label>
                            <div class="col-sm-8">
                                <input type="text" name="jenis_tagihan" id="jenis_tagihan" class="form-control">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Jumlah Tagihan</label>
                            <div class="col-sm-8">
                                <input type="text" name="jumlah_tagihan" id="jumlah_tagihan"
                                    class="form-control format-number">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Catatan</label>
                            <div class="col-sm-8">
                                <textarea name="catatan" id="catatan" rows="2" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary ms-1" id="submitBtnTagihan">
                            <span class="spinner-border spinner-border-sm mx-1 d-none" role="status"
                                aria-hidden="true"></span>
                            <span class="button-text">Simpan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- modal pemasukan --}}
    <div class="modal fade" id="modalPemasukan" tabindex="-1" role="dialog" data-focus="false"
        aria-labelledby="modalPemasukanLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title text-white font-weight-bold" id="modalPemasukanLabel">Tambah Pemasukan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formPemasukan">
                    @csrf
                    <input type="hidden" id="primary_id" name="primary_id" value="{{ $customer->id_customer }}">
                    <div class="modal-body">
                        <div class="form-group row">
                            <label class="control-label col-md-3">Tanggal Pembayaran</label>
                            <div class="col-md-4">
                                <input name="tanggal_pembayaran" id="tanggal_pembayaran" class="form-control"
                                    type="date" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-md-3">Nama Nasabah</label>
                            <div class="col-md-6">
                                <input name="nasabah_b" value="{{ $customer->nama_lengkap }}" class="form-control"
                                    type="text" readonly>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-md-3">Lokasi Perumahan</label>
                            <div class="col-md-4">
                                <input name="lokasi_b" value="{{ $lokasi }}" class="form-control" type="text"
                                    readonly>
                            </div>
                            <div class="col-md-2">
                                <input name="kode_kavling" value="{{ $kavling }}" class="form-control"
                                    type="text" readonly>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-md-3">Jenis Pembayaran</label>
                            <div class="col-md-4">
                                <select name="jenis_pembayaran" id="jenis_pembayaran"
                                    class="form-control select-jenis-pembayaran">
                                    <option value=""></option>
                                    @foreach ($jenisPembayaran as $bayar)
                                        <option value="{{ $bayar->id_jenis_pembayaran }}">{{ $bayar->nama_jenis }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-md-3">Jumlah Pembayaran</label>
                            <div class="col-md-3">
                                <input name="jumlah_bayar" id="jumlah_bayar" class="form-control format-number"
                                    type="text">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-md-3">Keterangan</label>
                            <div class="col-md-9">
                                <input name="keterangan_pembayaran" id="keterangan_pembayaran" class="form-control"
                                    type="text">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-md-3">Cara Bayar</label>
                            <div class="col-md-4">
                                <input name="metode_pembayaran" id="metode_pembayaran" class="form-control"
                                    type="text">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-md-3">Bukti Pembayaran</label>
                            <div class="col-md-4">
                                <input name="lampiran" id="lampiran" type="file" accept="image/*,application/pdf">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary ms-1" id="submitBtnPemasukan">
                            <span class="spinner-border spinner-border-sm mx-1 d-none" role="status"
                                aria-hidden="true"></span>
                            <span class="button-text">Simpan</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    {{-- modal edit pemasukan --}}
    <div class="modal fade" id="modalEditPemasukan" tabindex="-1" role="dialog" data-focus="false"
        aria-labelledby="modalEditPemasukanLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title text-white font-weight-bold" id="modalEditPemasukanLabel">Edit Pemasukan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formEditPemasukan">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_id_pembayaran" name="id_pembayaran">
                    <div class="modal-body">
                        <div class="form-group row">
                            <label class="control-label col-md-3">Tanggal Pembayaran</label>
                            <div class="col-md-4">
                                <input name="tanggal_pembayaran" id="edit_tanggal_pembayaran" class="form-control"
                                    type="date">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-md-3">Jenis Pembayaran</label>
                            <div class="col-md-4">
                                <select name="jenis_pembayaran" id="edit_jenis_pembayaran"
                                    class="form-control select-jenis-pembayaran">
                                    <option value=""></option>
                                    @foreach ($jenisPembayaran as $bayar)
                                        <option value="{{ $bayar->id_jenis_pembayaran }}">{{ $bayar->nama_jenis }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-md-3">Jumlah Pembayaran</label>
                            <div class="col-md-3">
                                <input name="jumlah_bayar" id="edit_jumlah_bayar" class="form-control format-number"
                                    type="text">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-md-3">Keterangan</label>
                            <div class="col-md-9">
                                <input name="keterangan_pembayaran" id="edit_keterangan_pembayaran" class="form-control"
                                    type="text">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-md-3">Cara Bayar</label>
                            <div class="col-md-4">
                                <input name="metode_pembayaran" id="edit_metode_pembayaran" class="form-control"
                                    type="text">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-md-3">Bukti Pembayaran</label>
                            <div class="col-md-4">
                                <input name="lampiran" id="edit_lampiran" type="file"
                                    accept="image/*,application/pdf">
                                <small class="text-muted">Biarkan kosong jika tidak ingin mengubah lampiran</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary ms-1" id="submitBtnEditPemasukan">
                            <span class="spinner-border spinner-border-sm mx-1 d-none" role="status"
                                aria-hidden="true"></span>
                            <span class="button-text">Update</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- modal pengeluaran --}}
    {{-- <div class="modal fade" id="modalPengeluaran" tabindex="-1" role="dialog" aria-labelledby="modalPengeluaranLabel"
        data-backdrop="static" aria-hidden="true" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title" id="modalPengeluaranLabel">Tambah Pengeluaran</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-tambah-pengeluaran"
                    action="{{ route('pembayaran.tambah-pengeluaran', $customer->id_customer) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group row">
                            <label class="control-label col-md-3">Tanggal Transaksi</label>
                            <div class="col-md-4">
                                <input name="tanggal_pengeluaran"
                                    class="form-control" type="date"
                                    value="{{ old('tanggal_pengeluaran', $tanggalSekarang) }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-md-3">Nama Nasabah</label>
                            <div class="col-md-6">
                                <input name="nasabah_b" value="{{ $customer->nama_lengkap }}" class="form-control"
                                    type="text" readonly>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-md-3">Lokasi Perumahan</label>
                            <div class="col-md-5">
                                <input name="lokasi_b" value="{{ $lokasi }}" class="form-control" type="text"
                                    readonly required>
                            </div>
                            <div class="col-md-2">
                                <input name="kode_kavling" value="{{ $kavling }}" class="form-control"
                                    type="text" readonly required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-md-3">Jenis Pengeluaran</label>
                            <div class="col-md-4">
                                <select name="jenis_pengeluaran"
                                    class="form-control select-jenis-pengeluaran">
                                    <option value=""></option>
                                    @foreach ($jenisPengeluaran as $bayar)
                                        <option value="{{ $bayar->id_jenis_pembayaran }}"
                                            {{ old('jenis_pengeluaran') == $bayar->id_jenis_pembayaran ? 'selected' : '' }}>
                                            {{ $bayar->nama_jenis }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div id="kavling">
                            <div class="form-group row">
                                <label class="control-label col-md-3">Jumlah Pembayaran</label>
                                <div class="col-md-3">
                                    <input name="jumlah_bayar_keluar" id="jumlah_bayar_keluar"
                                        class="form-control"
                                        type="text" value="{{ old('jumlah_bayar_keluar') }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-md-3">Keterangan</label>
                            <div class="col-md-9">
                                <input name="keterangan_pengeluaran" id="deskripsi"
                                    class="form-control"
                                    type="text" value="{{ old('keterangan_pengeluaran') }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-md-3">Cara Bayar</label>
                            <div class="col-md-4">
                                <select name="metode_pembayaran_pengeluaran" id="metode_pembayaran"
                                    class="form-control select-cara-bayar">
                                    <option value=""></option>
                                    @foreach ($metodeBayar as $bayar)
                                        <option value="{{ $bayar->jenis_bayar }}"
                                            {{ old('metode_pembayaran_pengeluaran') == $bayar->jenis_bayar ? 'selected' : '' }}>
                                            {{ $bayar->jenis_bayar }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-md-3">Bukti Pembayaran</label>
                            <div class="col-md-4">
                                <input name="lampiran_pengeluaran" id="lampiran" type="file"
                                    class="">
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="submitBtnPengeluaran">
                            <span class="spinner-border spinner-border-sm me-2 d-none" role="status"
                                aria-hidden="true"></span>
                            <span class="button-text">Simpan</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div> --}}

    {{-- modal invoice --}}
    {{-- <div class="modal fade" id="modalInvoice" tabindex="-1" role="dialog" aria-labelledby="modalInvoiceLabel"
        data-backdrop="static" aria-hidden="true" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalInvoiceLabel">Buat Invoice</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label class="control-label col-md-2">Tanggal Invoice</label>
                        <div class="col-md-5">
                            <input name="nasabah" value="2" class="form-control" type="hidden">
                            <input name="id_kavling" value="28" class="form-control" type="hidden">
                            <input name="tanggal" id="tanggal" class="form-control" type="date"
                                value="2025-04-25">
                            <span class="help-block"></span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="control-label col-md-2">No. Invoice</label>
                        <div class="col-md-5">
                            <input name="no_invoice" id="no_invoice" value="001/DAX-SV/INV/IV/2025" class="form-control"
                                type="text" readonly>
                            <span class="help-block"></span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="control-label col-md-2">Nama Customer</label>
                        <div class="col-md-5">
                            <input name="nama_customer" id="nama_customer" value="Riangga" class="form-control"
                                type="text" readonly>
                            <span class="help-block"></span>
                        </div>
                        <label class="control-label col-md-1">Telp</label>
                        <div class="col-md-3">
                            <input name="no_telp" id="no_telp" value="0812502747666" class="form-control"
                                type="text" readonly>
                            <span class="help-block"></span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="control-label col-md-2">Alamat</label>
                        <div class="col-md-8">
                            <input name="alamat" id="alamat" value="Jl. Kupang Dleima No 123" class="form-control"
                                type="text" readonly>
                            <span class="help-block"></span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="control-label col-md-2">Perumahan</label>
                        <div class="col-md-4">
                            <input name="perumahan" id="perumahan" value="NGAGLIK RESIDENCE" class="form-control"
                                type="text" readonly>
                            <span class="help-block"></span>
                        </div>

                        <label class="control-label col-md-1">Lokasi</label>
                        <div class="col-md-2">
                            <input name="kode_kavling_x" id="kode_kavling_x" value="A-43" class="form-control"
                                type="text" readonly>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <hr>

                    <div class="form-group row">
                        <label class="control-label col-md-2">Deskripsi</label>
                        <div class="col-md-8">
                            <input name="deskripsi" id="deskripsi" class="form-control" type="text">
                            <span class="help-block"></span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="control-label col-md-2">Nominal</label>
                        <div class="col-md-3">
                            <input name="nominal" id="nominal" class="form-control" type="text"
                                oninput="calculatePpnAndTotal()">
                            <span class="help-block"></span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="control-label col-md-2">Ppn (%)</label>
                        <div class="col-md-3">
                            <input name="ppn" id="ppn" class="form-control" type="text"
                                oninput="calculatePpn()" value="11">
                            <span class="help-block"></span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="control-label col-md-2">Total</label>
                        <div class="col-md-3">
                            <input name="total" id="total" class="form-control" type="text" readonly>
                            <span class="help-block"></span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="control-label col-md-2">Keterangan</label>
                        <div class="col-md-8">
                            <input name="keteangan" id="keteangan" class="form-control" type="text">
                            <span class="help-block"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </div>
    </div> --}}
    {{-- modal update harga rumah --}}
    <div class="modal fade" id="modalUpdateHarga" tabindex="-1" role="dialog" aria-labelledby="modalUpdateHargaLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title font-weight-bold" id="modalUpdateHargaLabel">Update Harga Rumah</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formUpdateHarga">
                    @csrf
                    <input type="hidden" id="cust_id_update" name="cust_id_update"
                        value="{{ $customer->id_customer }}">
                    <div class="modal-body">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Nama Nasabah</label>
                            <div class="col-sm-8">
                                <input type="text" id="nama_lengkap_update" class="form-control" readonly>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Lokasi Perumahan</label>
                            <div class="col-sm-8">
                                <input type="text" id="lokasi_perumahan_update" class="form-control" readonly>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Harga Jual</label>
                            <div class="col-sm-8">
                                <input type="text" name="hrg_jual" id="hrg_jual_update"
                                    class="form-control format-number">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Biaya Surat-surat , Biaya Notaris (AJB, SHGB), BPHTB,
                                promosi/iklan, pemasaran dan biaya lain-lain</label>
                            <div class="col-sm-8">
                                <input type="text" name="biaya_lain" id="biaya_lain_update"
                                    class="form-control format-number">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary ms-1" id="submitBtnUpdateHarga">
                            <span class="spinner-border spinner-border-sm mx-1 d-none" role="status"
                                aria-hidden="true"></span>
                            <span class="button-text">Simpan Perubahan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        var audio = new Audio('{{ asset('audio/notification.ogg') }}');

        $(function() {
            var customerId = '{{ $customer->id_customer }}';
            var url = "{{ route('pembayaran.detail-tagihan', ['id' => '__id__']) }}".replace('__id__', customerId);
            var permissions = @json($permissions);
            var showActionColumn = (permissions['edit'] == 1);

            $('.table-tagihan').DataTable({
                processing: false,
                serverSide: true,
                paging: false,
                searching: false,
                ordering: false,
                info: false,
                ajax: url,
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        className: 'text-center'
                    },
                    {
                        data: 'jenis_tagihan',
                        name: 'jenis_tagihan'
                    },
                    {
                        data: 'jumlah_tagihan',
                        name: 'jumlah_tagihan',
                        className: 'text-end'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        className: 'text-center',
                        orderable: false,
                        searchable: false,
                        visible: showActionColumn
                    }
                ],
                drawCallback: function(settings) {
                    $('#total-tagihan').html('<b>Rp. ' + settings.json.total_tagihan_formatted +
                        '</b>');

                    $('.btn-update-harga').off('click').on('click', function(e) {
                        e.preventDefault();

                        let customerId = $(this).closest('form').data('id');
                        let url = '{{ route('pembayaran.getDataKavling', ':id') }}'.replace(
                            ':id', customerId);

                        $.ajax({
                            url: url,
                            type: 'GET',
                            success: function(response) {
                                $('#nama_lengkap_update').val(response
                                    .nama_lengkap);
                                $('#lokasi_perumahan_update').val(response
                                    .lokasi_perumahan);
                                $('#hrg_jual_update').val(response.hrg_jual);
                                $('#biaya_lain_update').val(response.biaya_lain);

                                $('#modalUpdateHarga').modal('show');
                            },
                            error: function(xhr) {
                                toastr.error("Gagal mengambil data kavling",
                                    "ERROR");
                            }
                        });
                    });
                }
            });

            $('#formUpdateHarga').on('submit', function(e) {
                e.preventDefault();

                let submitBtn = $('#submitBtnUpdateHarga');
                let spinner = submitBtn.find('.spinner-border');
                let btnText = submitBtn.find('.button-text');

                spinner.removeClass('d-none');
                btnText.text('Menyimpan...');
                submitBtn.prop('disabled', true);

                let id = $('#cust_id_update').val();
                let url = '{{ route('pembayaran.update-harga-rumah', ':id') }}'.replace(':id', id);
                let formData = new FormData(this);
                formData.append('_method', 'PUT');

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $('#modalUpdateHarga').modal('hide');
                        audio.play();
                        toastr.success(response.message, "BERHASIL", {
                            progressBar: true,
                            timeOut: 3500,
                            positionClass: "toast-bottom-right",
                        });

                        $('#total_tagihan_all').val('Rp. ' + response.total_tagihan_formatted);
                        $('#sisa_bayar_all').val('Rp. ' + response.sisa_bayar_formatted);

                        $('.table-tagihan').DataTable().ajax.reload();
                    },
                    error: function(xhr) {
                        audio.play();
                        toastr.error("Gagal memperbarui harga", "GAGAL");
                    },
                    complete: function() {
                        spinner.addClass('d-none');
                        btnText.text('Simpan Perubahan');
                        submitBtn.prop('disabled', false);
                    }
                });
            });
        });

        $('#modalTagihan').on('hidden.bs.modal', function() {
            $('#formTagihan')[0].reset();
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            let submitBtn = $('#submitBtnTagihan');
            let spinner = submitBtn.find('.spinner-border');
            let btnText = submitBtn.find('.button-text');

            spinner.addClass('d-none');
            btnText.text('Simpan');
            submitBtn.prop('disabled', false);
        });

        $('#formTagihan').on('submit', function(e) {
            e.preventDefault();

            let submitBtn = $('#submitBtnTagihan');
            let spinner = submitBtn.find('.spinner-border');
            let btnText = submitBtn.find('.button-text');

            spinner.removeClass('d-none');
            btnText.text('Menyimpan...');
            submitBtn.prop('disabled', true);

            let id = $('#primary_id').val();
            let url = '{{ route('pembayaran.tambah-tagihan', ['id' => ':id']) }}'.replace(':id',
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
                    $('#modalTagihan').modal('hide');
                    audio.play();
                    toastr.success("Tagihan berhasil ditambahkan!", "BERHASIL", {
                        progressBar: true,
                        timeOut: 3500,
                        positionClass: "toast-bottom-right",
                    });

                    $('#total_tagihan_all').val('Rp. ' + response.total_tagihan_formatted);
                    $('#sisa_bayar_all').val('Rp. ' + response.sisa_bayar_formatted);

                    $('.table-tagihan').DataTable().ajax.reload();
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

                        spinner.addClass('d-none');
                        btnText.text('Simpan');
                        submitBtn.prop('disabled', false);
                    }
                }
            });
        });

        $(document).on('submit', '.formHargaRumah', function(e) {
            e.preventDefault();

            let form = $(this);
            let id = form.data('id');
            let url = '{{ route('pembayaran.update-harga-rumah', ['id' => ':id']) }}'.replace(':id', id);
            let formData = new FormData(this);
            formData.append('_method', 'PUT');

            Swal.fire({
                title: 'Yakin update harga rumah?',
                text: "Harga akan disesuaikan dengan harga jual kavling!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '<span class="swal-btn-text">Ya, Update</span>',
                cancelButtonText: 'Batal',
                showLoaderOnConfirm: false,
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'btn btn-primary mx-2',
                    cancelButton: 'btn btn-secondary'
                },
                preConfirm: () => {
                    return new Promise((resolve) => {
                        const confirmBtn = Swal.getConfirmButton();
                        const btnText = confirmBtn.querySelector('.swal-btn-text');

                        btnText.innerHTML =
                            `
                            <span class="spinner-border spinner-border-sm mx-2" role="status" aria-hidden="true"></span> Mengupdate...`;
                        confirmBtn.disabled = true;

                        $.ajax({
                            url: url,
                            method: 'POST',
                            data: formData,
                            contentType: false,
                            processData: false,
                            success: function(response) {
                                audio.play();
                                toastr.success("Harga Rumah berhasil di update!",
                                    "BERHASIL", {
                                        progressBar: true,
                                        timeOut: 3500,
                                        positionClass: "toast-bottom-right",
                                    });

                                $('#total_tagihan_all').val('Rp. ' + response
                                    .total_tagihan_formatted);
                                $('#sisa_bayar_all').val('Rp. ' + response
                                    .sisa_bayar_formatted);
                                $('.table-tagihan').DataTable().ajax.reload(null,
                                    false);

                                Swal.close();
                            },
                            error: function(xhr) {
                                if (xhr.status === 422) {
                                    audio.play();
                                    toastr.error("Ada inputan yang salah!",
                                        "GAGAL!", {
                                            progressBar: true,
                                            timeOut: 3500,
                                            positionClass: "toast-bottom-right",
                                        });

                                    let errors = xhr.responseJSON.errors;
                                    $.each(errors, function(key, val) {
                                        let input = $('#' + key);
                                        input.addClass('is-invalid');
                                        input.parent().find(
                                                '.invalid-feedback')
                                            .remove();
                                        input.parent().append(
                                            '<span class="invalid-feedback" role="alert"><strong>' +
                                            val[0] + '</strong></span>'
                                        );
                                    });

                                    btnText.innerHTML = 'Ya, Update';
                                    confirmBtn.disabled = false;
                                }
                            }
                        });
                    });
                }
            });
        });


        $(document).on('click', '.btn-edit-pemasukan', function() {
            let id = $(this).data('id');
            let url = "{{ route('pembayaran.getDataPemasukan', ['id' => ':id']) }}".replace(':id', id);

            $.ajax({
                url: url,
                method: 'GET',
                success: function(response) {
                    $('#edit_id_pembayaran').val(response.id_pembayaran);
                    $('#edit_tanggal_pembayaran').val(response.tanggal);
                    $('#edit_jenis_pembayaran').val(response.id_jenis_pembayaran);
                    $('#edit_jumlah_bayar').val(response.jumlah);
                    $('#edit_keterangan_pembayaran').val(response.keterangan);
                    $('#edit_metode_pembayaran').val(response.metode_bayar);
                    $('#modalEditPemasukan').modal('show');
                },
                error: function(xhr) {
                    toastr.error("Gagal mengambil data pemasukan");
                }
            });
        });

        $('#formEditPemasukan').on('submit', function(e) {
            e.preventDefault();

            let submitBtn = $('#submitBtnEditPemasukan');
            let spinner = submitBtn.find('.spinner-border');
            let btnText = submitBtn.find('.button-text');

            spinner.removeClass('d-none');
            btnText.text('Mengupdate...');
            submitBtn.prop('disabled', true);

            let id = $('#edit_id_pembayaran').val();
            let url = "{{ route('pembayaran.updatePemasukan', ['id' => ':id']) }}".replace(':id', id);
            let formData = new FormData(this);

            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    $('#modalEditPemasukan').modal('hide');
                    audio.play();
                    toastr.success(response.message, "BERHASIL", {
                        progressBar: true,
                        timeOut: 3500,
                        positionClass: "toast-bottom-right",
                    });

                    $('#jumlah_bayar_all').val(response.jumlah_bayar);
                    $('#total_tagihan_all').val(response.total_tagihan);
                    $('#sisa_bayar_all').val(response.sisa_bayar);

                    $('.table-pemasukan').DataTable().ajax.reload();
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        toastr.error("Ada inputan yang salah!", "GAGAL!");
                    } else {
                        toastr.error("Terjadi kesalahan saat mengupdate data", "Gagal!");
                    }
                },
                complete: function() {
                    spinner.addClass('d-none');
                    btnText.text('Update');
                    submitBtn.prop('disabled', false);
                }
            });
        });

        $('#modalEditPemasukan').on('hidden.bs.modal', function() {
            $('#formEditPemasukan')[0].reset();
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            let submitBtn = $('#submitBtnEditPemasukan');
            let spinner = submitBtn.find('.spinner-border');
            let btnText = submitBtn.find('.button-text');

            spinner.addClass('d-none');
            btnText.text('Update');
            submitBtn.prop('disabled', false);
        });

        $(function() {
            var customerId = '{{ $customer->id_customer }}';
            var url = "{{ route('pembayaran.detail-pemasukan', ['id' => '__id__']) }}".replace('__id__',
                customerId);

            $('.table-pemasukan').DataTable({
                processing: false,
                serverSide: true,
                paging: false,
                searching: false,
                ordering: false,
                info: false,
                ajax: url,
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        className: 'text-center'
                    },
                    {
                        data: 'tanggal',
                        name: 'tanggal'
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan'
                    },
                    {
                        data: 'nama_jenis',
                        name: 'nama_jenis'
                    },
                    {
                        data: 'jumlah',
                        name: 'jumlah',
                        className: 'text-end'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        className: 'text-center'
                    }
                ],
                drawCallback: function(settings) {
                    $('#total-pemasukan').html('<b>Rp. ' + settings.json.total_pemasukan_formatted +
                        '</b>');
                }
            });
        });

        $('#modalPemasukan').on('hidden.bs.modal', function() {
            $('#formPemasukan')[0].reset();
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            let submitBtn = $('#submitBtnPemasukan');
            let spinner = submitBtn.find('.spinner-border');
            let btnText = submitBtn.find('.button-text');

            spinner.addClass('d-none');
            btnText.text('Simpan');
            submitBtn.prop('disabled', false);
        });

        $('#formPemasukan').on('submit', function(e) {
            e.preventDefault();

            let submitBtn = $('#submitBtnPemasukan');
            let spinner = submitBtn.find('.spinner-border');
            let btnText = submitBtn.find('.button-text');

            spinner.removeClass('d-none');
            btnText.text('Menyimpan...');
            submitBtn.prop('disabled', true);

            let id = $('#primary_id').val();
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
                    $('#modalPemasukan').modal('hide');
                    audio.play();
                    let msg = "Pemasukan berhasil ditambahkan!";
                    toastr.success(msg, "BERHASIL", {
                        progressBar: true,
                        timeOut: 3500,
                        positionClass: "toast-bottom-right",
                    });

                    $('#jumlah_bayar_all').val(response.jumlah_bayar);
                    $('#total_tagihan_all').val(response.total_tagihan);
                    $('#sisa_bayar_all').val(response.sisa_bayar);

                    $('.table-pemasukan').DataTable().ajax.reload();
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

        $(document).on('click', '.delete-pemasukan', function(e) {
            e.preventDefault();

            const form = $(this).closest('form');

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Data ini akan dihapus secara permanen!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '<span class="swal-btn-text">Ya, Hapus</span>',
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

                        btnText.innerHTML =
                            `<span class="spinner-border spinner-border-sm mx-2" role="status" aria-hidden="true"></span> Menghapus...`;
                        confirmBtn.disabled = true;

                        $.ajax({
                            url: form.attr('action'),
                            method: 'POST',
                            data: form.serialize(),
                            success: function(response) {
                                audio.play();
                                toastr.success("Pemasukan telah dihapus!",
                                    "BERHASIL", {
                                        progressBar: true,
                                        timeOut: 3500,
                                        positionClass: "toast-bottom-right"
                                    });

                                $('.table-pemasukan').DataTable().ajax.reload(null,
                                    false);
                                $('#jumlah_bayar_all').val(response.jumlah_bayar);
                                $('#total_tagihan_all').val(response.total_tagihan);
                                $('#sisa_bayar_all').val(response.sisa_bayar);

                                Swal.close();
                            },
                            error: function() {
                                audio.play();
                                toastr.error("Gagal menghapus data.", "GAGAL!", {
                                    progressBar: true,
                                    timeOut: 3500,
                                    positionClass: "toast-bottom-right"
                                });

                                btnText.innerHTML = `Ya, Hapus`;
                                confirmBtn.disabled = false;
                            }
                        });
                    });
                }
            });
        });

        $(document).ready(function() {
            $('.select-jenis-pembayaran').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Jenis Pembayaran",
            });
            $('.select-cara-bayar').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Cara Pembayaran",
            });
            $('.select-jenis-pengeluaran').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Jenis Pengeluaran",
            });
        });
    </script>
@endpush
