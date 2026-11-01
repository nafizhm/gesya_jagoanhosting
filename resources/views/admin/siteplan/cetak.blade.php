<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cetak Siteplan</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 24px; background: #f4f6f9; color: #243447; }
        button, a { display: inline-block; margin: 8px 8px 8px 0; padding: 10px 16px; }
        iframe { display: block; width: 100%; height: 80vh; border: 0; background: white; }
    </style>
</head>
<body>
    <h1>Siteplan {{ $data_cetak['nama_lokasi'] }}</h1>
    <p id="status-cetak" role="status">Menyiapkan berkas cetak…</p>
    <button id="ulang-cetak" type="button" hidden>Coba lagi</button>
    <a id="unduh-cetak" hidden>Unduh berkas</a>
    <iframe id="pratinjau-cetak" title="Pratinjau PDF siteplan" hidden></iframe>
    <script>window.dataCetakSiteplan = {{ Illuminate\Support\Js::from($data_cetak) }};</script>
    <script src="{{ asset('assets/js/cetak-siteplan.js') }}" defer></script>
</body>
</html>
