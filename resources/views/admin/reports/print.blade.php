<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>{{ $reportTitle }}</title>
    <style>
        body { color: #1f2937; font: 12px Arial, sans-serif; margin: 24px; }
        h1 { color: #075985; font-size: 20px; margin-bottom: 6px; }
        .period { color: #4b5563; margin-bottom: 18px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #cbd5e1; padding: 7px; text-align: left; vertical-align: top; }
        th { background: #e0f2fe; }
        tr { break-inside: avoid; }
        @page { size: landscape; margin: 12mm; }
        @media screen {
            body { margin: 32px auto; max-width: 1200px; }
            .print-button { margin-bottom: 16px; }
        }
        @media print {
            .print-button { display: none; }
        }
    </style>
</head>
<body>
    <button class="print-button" type="button" onclick="window.print()">In / Lưu PDF</button>
    <h1>{{ $reportTitle }} - Bệnh viện Tâm Anh</h1>
    <div class="period">
        Thời gian: {{ $dateFrom->format('d/m/Y') }} - {{ $dateTo->format('d/m/Y') }}
        <br>{{ $summary }}
    </div>

    <table>
        <thead>
            <tr>
                @foreach($headers as $header)
                    <th>{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
                <tr>
                    @foreach($row as $value)
                        <td>{{ $value }}</td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($headers) }}">Không có dữ liệu trong khoảng thời gian đã chọn.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <script>
        window.addEventListener('load', () => window.print());
    </script>
</body>
</html>
