/* Konversi siteplan mandiri menggunakan SVG dan Canvas browser. */
(() => {
    'use strict';
    const data = window.dataCetakSiteplan;
    const status = document.getElementById('status-cetak');
    const ulang = document.getElementById('ulang-cetak');
    const unduh = document.getElementById('unduh-cetak');
    const pratinjau = document.getElementById('pratinjau-cetak');
    let urlHasil;

    function buatPdf(jpeg, lebar, tinggi) {
        const enkoder = new TextEncoder();
        const bagian = [];
        const posisi = [0];
        let panjang = 0;
        const tambah = (isi) => {
            const bita = typeof isi === 'string' ? enkoder.encode(isi) : isi;
            bagian.push(bita);
            panjang += bita.length;
        };
        const objek = (nomor, isi) => {
            posisi[nomor] = panjang;
            tambah(`${nomor} 0 obj\n${isi}\nendobj\n`);
        };
        tambah('%PDF-1.4\n');
        objek(1, '<< /Type /Catalog /Pages 2 0 R >>');
        objek(2, '<< /Type /Pages /Kids [3 0 R] /Count 1 >>');
        objek(3, '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595.28 841.89] /Resources << /XObject << /Gambar 4 0 R >> >> /Contents 5 0 R >>');
        posisi[4] = panjang;
        tambah(`4 0 obj\n<< /Type /XObject /Subtype /Image /Width ${lebar} /Height ${tinggi} /ColorSpace /DeviceRGB /BitsPerComponent 8 /Filter /DCTDecode /Length ${jpeg.length} >>\nstream\n`);
        tambah(jpeg);
        tambah('\nendstream\nendobj\n');
        const skala = Math.min(555.28 / lebar, 801.89 / tinggi);
        const w = lebar * skala;
        const h = tinggi * skala;
        const perintah = `q ${w.toFixed(3)} 0 0 ${h.toFixed(3)} ${((595.28 - w) / 2).toFixed(3)} ${(821.89 - h).toFixed(3)} cm /Gambar Do Q\n`;
        objek(5, `<< /Length ${enkoder.encode(perintah).length} >>\nstream\n${perintah}endstream`);
        const awal = panjang;
        tambah('xref\n0 6\n0000000000 65535 f \n');
        for (let i = 1; i <= 5; i++) tambah(`${String(posisi[i]).padStart(10, '0')} 00000 n \n`);
        tambah(`trailer\n<< /Size 6 /Root 1 0 R >>\nstartxref\n${awal}\n%%EOF`);
        return new Blob(bagian, { type: 'application/pdf' });
    }

    async function cetak() {
        ulang.hidden = true;
        unduh.hidden = true;
        pratinjau.hidden = true;
        status.textContent = 'Menyiapkan berkas cetak…';
        try {
            const dokumen = new DOMParser().parseFromString(data.svg, 'image/svg+xml');
            if (dokumen.querySelector('parsererror')) throw new Error('Data SVG siteplan tidak valid.');
            const svg = dokumen.documentElement;
            if (svg.localName !== 'svg') throw new Error('Data denah bukan SVG.');
            svg.querySelectorAll('script, foreignObject').forEach((elemen) => elemen.remove());
            svg.querySelectorAll('*').forEach((elemen) => {
                [...elemen.attributes].forEach((atribut) => {
                    if (/^on/i.test(atribut.name)) elemen.removeAttribute(atribut.name);
                });
            });
            const kotak = svg.viewBox.baseVal;
            const rasio = kotak.width > 0 && kotak.height > 0
                ? kotak.width / kotak.height : svg.width.baseVal.value / svg.height.baseVal.value;
            if (!Number.isFinite(rasio) || rasio <= 0) throw new Error('Ukuran denah tidak valid.');
            const lebar = Math.max(1, Math.round(rasio >= 1 ? 3000 : 3000 * rasio));
            const tinggi = Math.max(1, Math.round(rasio >= 1 ? 3000 / rasio : 3000));
            svg.setAttribute('width', lebar);
            svg.setAttribute('height', tinggi);
            const urlSvg = URL.createObjectURL(new Blob([new XMLSerializer().serializeToString(svg)], { type: 'image/svg+xml;charset=utf-8' }));
            const gambar = new Image();
            try {
                await new Promise((selesai, gagal) => {
                    gambar.onload = selesai;
                    gambar.onerror = () => gagal(new Error('Denah gagal dirender. Periksa gambar dan isi SVG.'));
                    gambar.src = urlSvg;
                });
            } finally {
                URL.revokeObjectURL(urlSvg);
            }
            const kanvas = document.createElement('canvas');
            const kepala = data.format === 'pdf' ? 200 : 0;
            kanvas.width = lebar;
            kanvas.height = tinggi + kepala;
            const konteks = kanvas.getContext('2d');
            if (!konteks) throw new Error('Browser tidak mendukung pembuatan gambar.');
            konteks.fillStyle = '#ffffff';
            konteks.fillRect(0, 0, kanvas.width, kanvas.height);
            if (kepala) {
                konteks.fillStyle = '#000000';
                konteks.textAlign = 'center';
                konteks.font = 'bold 38px Arial';
                konteks.fillText(data.nama_perusahaan, lebar / 2, 48, lebar - 40);
                konteks.font = '32px Arial';
                konteks.fillText(`SITE PLAN ${data.nama_lokasi}`, lebar / 2, 100, lebar - 40);
                konteks.font = '26px Arial';
                konteks.fillText(`Periode Cetak: ${data.periode}`, lebar / 2, 146, lebar - 40);
            }
            konteks.drawImage(gambar, 0, kepala, lebar, tinggi);
            const jpg = await new Promise((selesai) => kanvas.toBlob(selesai, 'image/jpeg', 0.95));
            if (!jpg) throw new Error('Gagal membuat gambar JPG.');
            const hasil = data.format === 'pdf'
                ? buatPdf(new Uint8Array(await jpg.arrayBuffer()), kanvas.width, kanvas.height) : jpg;
            if (urlHasil) URL.revokeObjectURL(urlHasil);
            urlHasil = URL.createObjectURL(hasil);
            unduh.href = urlHasil;
            unduh.download = `${data.nama_file}.${data.format}`;
            unduh.hidden = false;
            status.textContent = 'Berkas siap. Klik Unduh berkas jika unduhan tidak dimulai otomatis.';
            if (data.format === 'pdf') {
                pratinjau.src = urlHasil;
                pratinjau.hidden = false;
            } else {
                unduh.click();
            }
        } catch (galat) {
            console.error('Gagal mencetak siteplan:', galat);
            status.textContent = `Gagal membuat berkas: ${galat.message}`;
            ulang.hidden = false;
        }
    }
    ulang.addEventListener('click', cetak);
    window.addEventListener('pagehide', () => { if (urlHasil) URL.revokeObjectURL(urlHasil); });
    cetak();
})();
