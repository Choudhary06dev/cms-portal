<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Employees Report</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 0; padding: 16px; background: #fff; color: #333; }
    .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 16px; }
    .header h1 { margin: 0; font-size: 20px; }
    .meta { color: #666; font-size: 12px; }
    table { width: 100%; border-collapse: collapse; font-size: 12px; }
    th, td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; }
    th { background: #f5f5f5; }
    @page { size: A5 portrait; margin: 10mm; }
  </style>
</head>
<body>
  <div class="header">
    <h1>Employees Report</h1>
    <div class="meta">Generated on: {{ now()->format('M d, Y H:i') }} | Period: {{ \Carbon\Carbon::parse($dateFrom)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($dateTo)->format('M d, Y') }}</div>
  </div>

  <table>
    <thead>
      <tr>
        <th>Employee</th>
        <th>Department</th>
        <th>Total</th>
        <th>Resolved</th>
        <th>Resolution %</th>
        <th>Avg Hours</th>
      </tr>
    </thead>
    <tbody>
      @foreach($employees as $row)
      <tr>
        <td>{{ $row['employee']->user->username ?? 'N/A' }}</td>
        <td>{{ ucfirst($row['employee']->department ?? '-') }}</td>
        <td>{{ $row['total_complaints'] }}</td>
        <td>{{ $row['resolved_complaints'] }}</td>
        <td>{{ $row['resolution_rate'] }}%</td>
        <td>{{ round($row['avg_resolution_time'], 1) }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>

  <script>
    window.onload = function(){ window.print(); };
  </script>
</body>
</html>
