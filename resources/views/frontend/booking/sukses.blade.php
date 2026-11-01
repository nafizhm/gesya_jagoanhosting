@extends('layouts.app')

@php
        $konfigurasi = \App\Models\PengaturanProfil::first();
 @endphp
        <title>{{ $konfigurasi->nama_perusahaan ?? 'Template Aplikasi' }}</title>

@section('content')
 <div class="container">
@php
    $logo = \App\Models\PengaturanMedia::where('jenis_data', 'logo website')->first();
@endphp

<center>
    <img class="mt-5 mb-5" src="{{ asset('config_media/' . ($logo->nama_file ?? 'default.png')) }}" max-width="40%">
</center>

<section class="content">
  <div class="card">
    <div class="card-body">
      <center>
        <b>Terima Kasih</b>
        <br><br>
        Booking atas nama:<br>
        <b>{{ $nama }}</b><br>
        Lokasi Perumahan: <b>{{ $lokasi }}</b><br>
        Blok Unit Rumah: <b>{{ $blok }}</b><br>
        Telah kami terima, kami akan segera memproses permohonan anda.
        <br><br><br>
        <b>{{ $konfigurasi->nama_perusahaan ?? 'Template Aplikasi' }}</b>
      </center>
    </div>
  </div>

<a href="{{ route('booking') }}" class="btn btn-sm btn-secondary">Kembali</a>

</section>
        </div>

@endsection
