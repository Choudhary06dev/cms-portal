<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Spare Parts Report</title>
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
    <h1>Spare Parts Report</h1>
    <div class="meta">Generated on: {{ now()->format('M d, Y H:i') }} | Period: {{ \Carbon\Carbon::parse($dateFrom)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($dateTo)->format('M d, Y') }}</div>
  </div>

  <table>
    <thead>
      <tr>
        <th>Item Name</th>
        <th>Category</th>
        <th>Total Used</th>
        <th>Usage Count</th>
        <th>Current Stock</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      @foreach($spares as $row)
      <tr>
        <td>{{ $row['spare']->item_name }}</td>
        <td>{{ ucfirst($row['spare']->category) }}</td>
        <td>{{ $row['total_used'] }}</td>
        <td>{{ $row['usage_count'] }}</td>
        <td>{{ $row['current_stock'] }}</td>
        <td>{{ ucfirst(str_replace('_',' ',$row['stock_status'])) }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>

  <script>
    window.onload = function(){ window.print(); };
  </script>
</body>
</html>

