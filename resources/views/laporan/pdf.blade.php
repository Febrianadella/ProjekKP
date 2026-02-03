<!DOCTYPE html>
<html>

<head>
    <title>Laporan Surat - SIFORA</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px double #333;
        }

        .header h1 {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .header h2 {
            font-size: 16px;
            margin: 5px 0;
        }

        .header p {
            font-size: 12px;
            margin: 3px 0;
        }

        .filter {
            background: #f8f9fa;
            padding: 12px;
            margin-bottom: 20px;
            border-left: 4px solid #007bff;
            font-size: 11px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 10px;
        }

        th {
            background-color: #343a40 !important;
            color: white !important;
            font-weight: bold;
            padding: 10px 8px;
            text-align: center;
            border: 1px solid #333 !important;
            font-size: 11px;
        }

        td {
            padding: 8px 6px;
            border: 1px solid #333 !important;
            vertical-align: middle;
        }

        tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        tr:nth-child(odd) {
            background-color: #ffffff;
        }

        .sudah {
            background-color: #d4edda !important;
            color: #155724 !important;
            font-weight: bold;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 9px;
        }

        .belum {
            background-color: #f8d7da !important;
            color: #721c24 !important;
            font-weight: bold;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 9px;
        }

        .no-data {
            text-align: center;
            padding: 50px;
            color: #666;
            font-style: italic;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 9px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>

<body>
    {{-- HEADER --}}
    <div class="header">
        <h1>Balai Besar Perikanan Budidaya Laut Lampung</h1>
        <h2>LAPORAN SURAT MASUK</h2>
        <p><strong>Lampung</strong></p>
        <p>Tanggal Cetak: {{ now()->translatedFormat('d F Y H:i') }}</p>
    </div>

    {{-- FILTER --}}
    @if ($tanggalMulai || $tanggalAkhir || $perihal || $status !== 'semua')
        <div class="filter">
            <strong>FILTER TERAPKAN:</strong><br>
            Tanggal: {{ $tanggalMulai ?? 'Semua' }} s/d {{ $tanggalAkhir ?? 'Semua' }} |
            Perihal: "{{ $perihal ?? 'Semua' }}" |
            Status: {{ ucfirst($status) ?? 'Semua' }}
        </div>
    @endif

    {{-- TABEL --}}
    <table>
        <thead>
            <tr>
                <th style="width: 40px;">No</th>
                <th>Tanggal Masuk</th>
                <th>Asal Surat</th>
                <th>Perihal</th>
                <th>Tanggal Keluar</th>
                <th style="width: 80px;">Status</th>
                <th style="width: 180px;">File</th>
            </tr>
        </thead>
        <tbody>
            @forelse($surat as $index => $row)
                @php
                    $isDibalas = !empty($row->file_balasan);
                    $statusClass = $isDibalas ? 'sudah' : 'belum';
                    $tanggalKeluar = $row->tanggal_balasan
                        ? \Carbon\Carbon::parse($row->tanggal_balasan)->translatedFormat('d F Y')
                        : '-';
                @endphp
                <tr>
                    <td style="text-align: center; font-weight: bold;">{{ $index + 1 }}</td>
                    <td style="font-weight: 500;">
                        {{ \Carbon\Carbon::parse($row->tanggal_surat)->translatedFormat('d F Y') }}
                    </td>
                    <td>{{ $row->asal_surat ?? '-' }}</td>
                    <td style="font-size: 9.5px;">{{ Str::limit($row->perihal ?? '-', 60, '...') }}</td>
                    <td>{{ $tanggalKeluar }}</td>
                    <td style="text-align: center;">
                        <span class="{{ $statusClass }}">
                            {{ $isDibalas ? 'Sudah Dibalas' : 'Belum Dibalas' }}
                        </span>
                    </td>
                    <td style="font-size: 9px;">
                        <div style="margin-bottom: 2px;">
                            <strong>Masuk:</strong> {{ basename($row->file_surat ?? '-') }}
                        </div>
                        <div>
                            <strong>Keluar:</strong> {{ basename($row->file_balasan ?? '-') }}
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="no-data">
                        Tidak ada data surat untuk filter yang dipilih
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- FOOTER --}}
    <div class="footer">
        <p>Dicetak melalui SIFORA - Sistem Informasi Persuratan</p>
        <p>{{ now()->translatedFormat('d F Y H:i:s') }}</p>
    </div>
</body>

</html>
