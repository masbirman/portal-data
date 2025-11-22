<table>
    <thead>
        <tr>
            <th colspan="8" style="text-align: center; font-weight: bold;">Rekap Data Asesmen Nasional Tahun {{ $year }}</th>
        </tr>
        <tr></tr>
        <tr>
            <th rowspan="2" style="border: 1px solid #000; text-align: center; vertical-align: middle; font-weight: bold;">No</th>
            <th rowspan="2" style="border: 1px solid #000; text-align: center; vertical-align: middle; font-weight: bold;">Kota / Kabupaten</th>
            <th colspan="3" style="border: 1px solid #000; text-align: center; font-weight: bold;">Status Pelaksanaan</th>
            <th colspan="3" style="border: 1px solid #000; text-align: center; font-weight: bold;">Moda Pelaksanaan</th>
        </tr>
        <tr>
            <th style="border: 1px solid #000; text-align: center; font-weight: bold;">Mandiri</th>
            <th style="border: 1px solid #000; text-align: center; font-weight: bold;">Menumpang</th>
            <th style="border: 1px solid #000; text-align: center; font-weight: bold;">Belum</th>
            <th style="border: 1px solid #000; text-align: center; font-weight: bold;">Online</th>
            <th style="border: 1px solid #000; text-align: center; font-weight: bold;">Semi Online</th>
            <th style="border: 1px solid #000; text-align: center; font-weight: bold;">Belum</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $index => $row)
        <tr>
            <td style="border: 1px solid #000; text-align: center;">{{ $index + 1 }}</td>
            <td style="border: 1px solid #000;">{{ $row['wilayah'] }}</td>
            <td style="border: 1px solid #000; text-align: right;">{{ $row['status']['mandiri'] }}</td>
            <td style="border: 1px solid #000; text-align: right;">{{ $row['status']['menumpang'] }}</td>
            <td style="border: 1px solid #000; text-align: right;">{{ $row['status']['belum'] }}</td>
            <td style="border: 1px solid #000; text-align: right;">{{ $row['moda']['online'] }}</td>
            <td style="border: 1px solid #000; text-align: right;">{{ $row['moda']['semi'] }}</td>
            <td style="border: 1px solid #000; text-align: right;">{{ $row['moda']['belum'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
