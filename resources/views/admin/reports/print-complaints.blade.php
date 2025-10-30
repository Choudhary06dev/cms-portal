<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Complaints Report</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 0; padding: 16px; background: #fff; color: #333; }
    .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 16px; }
    .header h1 { margin: 0; font-size: 20px; }
    .meta { color: #666; font-size: 12px; }
    table { width: 100%; border-collapse: collapse; font-size: 12px; }
    th, td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; }
    th { background: #f5f5f5; }
    .badge { padding: 2px 6px; border-radius: 4px; font-size: 11px; }
    .success { background: #e8f5e8; color: #2e7d32; }
    .warning { background: #fff7e6; color: #ad6800; }
    .secondary { background: #f0f0f0; color: #555; }
    @page { size: A5 portrait; margin: 10mm; }
  </style>
</head>
<body>
  <div class="header">
    <h1>Complaints Report</h1>
    <div class="meta">Generated on: {{ now()->format('M d, Y H:i') }} | Period: {{ \Carbon\Carbon::parse($dateFrom)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($dateTo)->format('M d, Y') }}</div>
  </div>

  <table>
    <thead>
      <tr>
        <th>Ticket</th>
        <th>Client</th>
        <th>Category</th>
        <th>Status</th>
        <th>Created</th>
      </tr>
    </thead>
    <tbody>
      @foreach(($data instanceof \Illuminate\Support\Collection ? $data : collect($data)) as $row)
        @if(isset($row->id))
        <tr>
          <td>{{ $row->ticket_number ?? $row->id }}</td>
          <td>{{ optional($row->client)->client_name ?? 'N/A' }}</td>
          <td>{{ ucfirst($row->category ?? '-') }}</td>
          <td>
            @php $cls = in_array(($row->status ?? ''), ['resolved','closed']) ? 'success' : (($row->status ?? '') === 'in_progress' ? 'warning' : 'secondary'); @endphp
            <span class="badge {{ $cls }}">{{ ucfirst($row->status ?? '-') }}</span>
          </td>
          <td>{{ optional($row->created_at)->format('M d, Y') }}</td>
        </tr>
        @endif
      @endforeach
    </tbody>
  </table>

  <script>
    window.onload = function(){ window.print(); };
  </script>
</body>
</html>
