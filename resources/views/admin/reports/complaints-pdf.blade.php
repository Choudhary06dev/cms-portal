<!DOCTYPE html>
<html>
<head>
    <title>SUB-DIVISION WISE PERFORMANCE Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 5px 0;
        }
        .header p {
            margin: 2px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #333;
            padding: 8px;
            text-align: center;
            font-size: 11px;
        }
        th {
            background-color: #333;
            color: #fff;
            font-weight: bold;
        }
        td {
            background-color: #fff;
            color: #000;
        }
        .fw-bold {
            font-weight: bold;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>CMS COMPLAINT MANAGEMENT SYSTEM</h2>
        <p><strong>SUB-DIVISION WISE PERFORMANCE</strong></p>
        <p>From: {{ $dateFrom }} To: {{ $dateTo }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" class="text-center" style="width: 200px;">Description</th>
                @foreach($categories as $catKey => $catName)
                  <th colspan="2" class="text-center">{{ $catName }}</th>
                @endforeach
                <th colspan="2" class="text-center">Total</th>
            </tr>
            <tr>
                @foreach($categories as $catKey => $catName)
                  <th class="text-center">Qty (No's)</th>
                  <th class="text-center">%age</th>
                @endforeach
                <th class="text-center">Qty (No's)</th>
                <th class="text-center">%age</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData as $rowKey => $row)
            <tr>
                <td class="fw-bold">{{ $row['name'] }}</td>
                @foreach($categories as $catKey => $catName)
                    @php
                        $cellData = $row['categories'][$catKey] ?? ['count' => 0, 'percentage' => 0];
                    @endphp
                    <td class="text-center">{{ number_format($cellData['count']) }}</td>
                    <td class="text-center">{{ number_format($cellData['percentage'], 1) }}%</td>
                @endforeach
                @php
                    $rowGrandTotal = array_sum(array_column($row['categories'], 'count'));
                    $rowGrandPercent = $grandTotal > 0 ? ($rowGrandTotal / $grandTotal * 100) : 0;
                @endphp
                <td class="text-center fw-bold">{{ number_format($rowGrandTotal) }}</td>
                <td class="text-center fw-bold">{{ number_format($rowGrandPercent, 1) }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

