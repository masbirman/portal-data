<table>
    <thead>
        <tr>
            <th colspan="15" style="text-align: center; font-weight: bold;">Data Detail Sekolah Tahun {{ $year }}</th>
        </tr>
        <tr></tr>
        <tr>
            <th style="border: 1px solid #000; font-weight: bold; text-align: center;">No</th>
            <th style="border: 1px solid #000; font-weight: bold; text-align: center;">NPSN</th>
            <th style="border: 1px solid #000; font-weight: bold; text-align: center;">Nama Sekolah</th>
            <th style="border: 1px solid #000; font-weight: bold; text-align: center;">Kota/Kabupaten</th>
            <th style="border: 1px solid #000; font-weight: bold; text-align: center;">Jenjang</th>
            <th style="border: 1px solid #000; font-weight: bold; text-align: center;">Status Sekolah</th>
            <th style="border: 1px solid #000; font-weight: bold; text-align: center;">Peserta</th>
            <th style="border: 1px solid #000; font-weight: bold; text-align: center;">Status Pelaksanaan</th>
            <th style="border: 1px solid #000; font-weight: bold; text-align: center;">Moda Pelaksanaan</th>
            <th style="border: 1px solid #000; font-weight: bold; text-align: center;">Partisipasi Literasi (%)</th>
            <th style="border: 1px solid #000; font-weight: bold; text-align: center;">Partisipasi Numerasi (%)</th>
            <th style="border: 1px solid #000; font-weight: bold; text-align: center;">Tempat Pelaksanaan</th>
            <th style="border: 1px solid #000; font-weight: bold; text-align: center;">Penanggung Jawab</th>
            <th style="border: 1px solid #000; font-weight: bold; text-align: center;">Proktor</th>
            <th style="border: 1px solid #000; font-weight: bold; text-align: center;">Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $index => $row)
        @php
            $asesmen = $row->pelaksanaanAsesmen->first();
        @endphp
        <tr>
            <td style="border: 1px solid #000; text-align: center;">{{ $index + 1 }}</td>
            <td style="border: 1px solid #000;">{{ $row->npsn ?? '-' }}</td>
            <td style="border: 1px solid #000;">{{ $row->nama ?? '-' }}</td>
            <td style="border: 1px solid #000;">{{ $row->wilayah->nama ?? '-' }}</td>
            <td style="border: 1px solid #000; text-align: center;">{{ $row->jenjangPendidikan->nama ?? '-' }}</td>
            <td style="border: 1px solid #000; text-align: center;">{{ $row->status_sekolah ?? '-' }}</td>
            <td style="border: 1px solid #000; text-align: center;">{{ $asesmen?->jumlah_peserta ?? '-' }}</td>
            <td style="border: 1px solid #000; text-align: center;">{{ $asesmen?->status_pelaksanaan ?? '-' }}</td>
            <td style="border: 1px solid #000; text-align: center;">{{ $asesmen?->moda_pelaksanaan ?? '-' }}</td>
            <td style="border: 1px solid #000; text-align: center;">{{ $asesmen?->partisipasi_literasi ?? '-' }}</td>
            <td style="border: 1px solid #000; text-align: center;">{{ $asesmen?->partisipasi_numerasi ?? '-' }}</td>
            <td style="border: 1px solid #000;">{{ $asesmen?->tempat_pelaksanaan ?? '-' }}</td>
            <td style="border: 1px solid #000;">{{ $asesmen?->nama_penanggung_jawab ?? '-' }}</td>
            <td style="border: 1px solid #000;">{{ $asesmen?->nama_proktor ?? '-' }}</td>
            <td style="border: 1px solid #000;">{{ $asesmen?->keterangan ?? '-' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
