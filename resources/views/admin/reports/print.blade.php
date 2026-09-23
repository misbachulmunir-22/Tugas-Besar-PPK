<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Rekapitulasi Fasilitas Kampus - SIMFAS 2026</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            background-color: #fff;
            padding: 20px;
        }

        .kop-surat {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 12px;
            margin-bottom: 25px;
        }

        .kop-surat h4 {
            font-weight: bold;
            margin-bottom: 2px;
            text-transform: uppercase;
        }

        .kop-surat h5 {
            font-size: 1.1rem;
            margin-bottom: 4px;
        }

        .kop-surat p {
            margin-bottom: 0;
            font-size: 0.9rem;
            color: #333;
        }

        .table-print {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 0.9rem;
        }

        .table-print th, .table-print td {
            border: 1px solid #000;
            padding: 6px 8px;
        }

        .table-print th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }

        .ttd-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Tombol Aksi Print di Layar -->
    <div class="no-print mb-4 d-flex gap-2">
        <button onclick="window.print()" class="btn btn-primary btn-sm px-4">
            <i class="bi bi-printer"></i> Cetak Dokumen / Simpan PDF
        </button>
        <button onclick="window.close()" class="btn btn-secondary btn-sm px-3">
            Tutup
        </button>
    </div>

    <!-- Kop Surat Kampus -->
    <div class="kop-surat">
        <h4>KEMENTERIAN PENDIDIKAN TINGGI, SAINS, DAN TEKNOLOGI</h4>
        <h5>UNIVERSITAS TEKNOLOGI KAMPUS TERPADU</h5>
        <p>BAGIAN PENGELOLAAN SARANA, PRASARANA, DAN FASILITAS KAMPUS (SIMFAS 2026)</p>
        <p class="small">Jl. Kampus Terpadu No. 1, Yogyakarta 55281 | Telp: (0274) 555-0199 | Email: sarpras@kampus.ac.id</p>
    </div>

    <!-- Judul Laporan -->
    <div class="text-center mb-3">
        <h5 class="fw-bold text-uppercase mb-1" style="text-decoration: underline;">
            LAPORAN REKAPITULASI OKUPANSI & KERUSAKAN FASILITAS
        </h5>
        <div class="small">
            Periode: <strong>{{ date('d F Y', strtotime($startDate)) }}</strong> s/d <strong>{{ date('d F Y', strtotime($endDate)) }}</strong>
            @if($locationFilter)
                | Lokasi: <strong>{{ $locationFilter }}</strong>
            @endif
        </div>
    </div>

    <!-- Data Rekapitulasi Ringkas -->
    <table class="table-print mb-4">
        <thead>
            <tr>
                <th style="width: 40px;">No</th>
                <th>Kode</th>
                <th>Nama Fasilitas</th>
                <th>Tipe</th>
                <th>Lokasi</th>
                <th>Kapasitas</th>
                <th>Reservasi Disetujui</th>
                <th>Total Jam</th>
                <th>Total Kerusakan</th>
                <th>Selesai</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rekapData['facilities'] as $index => $row)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center font-monospace">{{ $row['code'] }}</td>
                    <td>{{ $row['name'] }}</td>
                    <td>{{ $row['type_label'] }}</td>
                    <td>{{ $row['location'] }}</td>
                    <td class="text-center">{{ $row['capacity'] }} Org</td>
                    <td class="text-center">{{ $row['total_reservations'] }}</td>
                    <td class="text-center">{{ $row['total_hours'] }} Jam</td>
                    <td class="text-center">{{ $row['total_reports'] }}</td>
                    <td class="text-center">{{ $row['resolved_reports'] }}</td>
                    <td class="text-center text-capitalize">{{ str_replace('_', ' ', $row['status']) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center py-3">Tidak ada data tercatat pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background-color: #f9f9f9;">
                <td colspan="6" class="text-center text-uppercase">TOTAL KESELURUHAN</td>
                <td class="text-center">{{ $rekapData['totals']['total_reservations'] }}</td>
                <td class="text-center">{{ $rekapData['totals']['total_hours'] }} Jam</td>
                <td class="text-center">{{ $rekapData['totals']['total_reports'] }}</td>
                <td class="text-center">{{ $rekapData['totals']['resolved_reports'] }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <!-- Tanda Tangan Pengesahan -->
    <div class="ttd-section">
        <div style="width: 250px; text-align: center;">
            <p class="mb-1">Mengetahui,</p>
            <p class="fw-bold mb-5">Kepala Biro Sarana & Prasarana</p>
            <br><br>
            <p class="fw-bold mb-0" style="text-decoration: underline;">Dr. Ir. Bambang Hermanto, M.Eng.</p>
            <p class="small text-muted mb-0">NIP. 197805122003121001</p>
        </div>

        <div style="width: 250px; text-align: center;">
            <p class="mb-1">Dicetak pada: {{ date('d F Y') }}</p>
            <p class="fw-bold mb-5">Administrator Sistem Fasilitas</p>
            <br><br>
            <p class="fw-bold mb-0" style="text-decoration: underline;">{{ auth()->user()->name ?? 'Administrator Kampus' }}</p>
            <p class="small text-muted mb-0">ID: {{ auth()->user()->identity_number ?? 'ADM-2026-001' }}</p>
        </div>
    </div>

    <script>
        window.addEventListener('load', () => {
            // Optional auto print trigger
        });
    </script>
</body>
</html>
