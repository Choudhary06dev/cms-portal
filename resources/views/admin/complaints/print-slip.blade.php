<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complaint Slip - {{ $complaint->title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: white;
            color: #333;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .header h1 {
            margin: 0;
            color: #333;
            font-size: 24px;
        }

        .header p {
            margin: 5px 0;
            color: #666;
        }

        .complaint-info {
            margin-bottom: 30px;
        }

        .info-row {
            display: flex;
            margin-bottom: 10px;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }

        .info-label {
            font-weight: bold;
            width: 150px;
            color: #333;
        }

        .info-value {
            flex: 1;
            color: #666;
        }

        .description {
            margin: 20px 0;
            padding: 15px;
            background: #f8f9fa;
            border-left: 4px solid #007bff;
        }

        .description h3 {
            margin-top: 0;
            color: #333;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-new {
            background: #e3f2fd;
            color: #1976d2;
        }

        .status-assigned {
            background: #fff3e0;
            color: #f57c00;
        }

        .status-in_progress {
            background: #e8f5e8;
            color: #388e3c;
        }

        .status-resolved {
            background: #e8f5e8;
            color: #388e3c;
        }

        .status-closed {
            background: #f3e5f5;
            color: #7b1fa2;
        }

        .priority-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .priority-low {
            background: #e8f5e8;
            color: #388e3c;
        }

        .priority-medium {
            background: #fff3e0;
            color: #f57c00;
        }

        .priority-high {
            background: #ffebee;
            color: #d32f2f;
        }

        .priority-urgent {
            background: #fce4ec;
            color: #c2185b;
        }

        @media print {
            body {
                margin: 0;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Complaint Management System</h1>
        <p>Complaint Slip</p>
        <p>Generated on: {{ now()->format('M d, Y H:i') }}</p>
    </div>

    <!-- Complainant Information -->
    <div class="complaint-info" style="margin-bottom: 30px;">
        <h3
            style="color: #333; font-size: 18px; margin-bottom: 15px; border-bottom: 2px solid #007bff; padding-bottom: 8px;">
            Complainant Information</h3>

        <div class="info-row">
            <div class="info-label">Name:</div>
            <div class="info-value">{{ $complaint->client->client_name ?? 'N/A' }}</div>
        </div>

        @if($complaint->client && $complaint->client->phone)
            <div class="info-row">
                <div class="info-label">Phone:</div>
                <div class="info-value">{{ $complaint->client->phone }}</div>
            </div>
        @endif

        @if($complaint->client && $complaint->client->address)
            <div class="info-row">
                <div class="info-label">Address:</div>
                <div class="info-value">{{ $complaint->client->address }}</div>
            </div>
        @endif

        @if($complaint->city_id && $complaint->city)
            <div class="info-row">
                <div class="info-label">GE Groups:</div>
                <div class="info-value">{{ $complaint->city->name }}</div>
            </div>
        @endif

        @if($complaint->sector_id && $complaint->sector)
            <div class="info-row">
                <div class="info-label">GE Nodes:</div>
                <div class="info-value">{{ $complaint->sector->name }}</div>
            </div>
        @endif
    </div>

    <!-- Complaint Information -->
    <div class="complaint-info">
        <h3
            style="color: #333; font-size: 18px; margin-bottom: 15px; border-bottom: 2px solid #007bff; padding-bottom: 8px;">
            Complaint Information</h3>

        <div class="info-row">
            <div class="info-label">Complaint ID:</div>
            <div class="info-value">#{{ $complaint->id }}</div>
        </div>

        <div class="info-row">
            <div class="info-label">Title:</div>
            <div class="info-value">{{ $complaint->title }}</div>
        </div>

        <div class="info-row">
            <div class="info-label">Category:</div>
            <div class="info-value">{{ ucfirst($complaint->category) }}</div>
        </div>

        <div class="info-row">
            <div class="info-label">Priority:</div>
            <div class="info-value">
                <span class="priority-badge priority-{{ strtolower($complaint->priority) }}">
                    {{ ucfirst($complaint->priority) }}
                </span>
            </div>
        </div>

        <div class="info-row">
            <div class="info-label">Status:</div>
            <div class="info-value">
                <span class="status-badge status-{{ strtolower($complaint->status) }}">
                    {{ ucfirst($complaint->status) }}
                </span>
            </div>
        </div>

        <div class="info-row">
            <div class="info-label">Assigned To:</div>
            <div class="info-value">
                {{ $complaint->assignedEmployee->name ?? 'Unassigned' }}@if($complaint->assignedEmployee && $complaint->assignedEmployee->designation)
                ({{ $complaint->assignedEmployee->designation }})@endif
            </div>
        </div>

        <div class="info-row">
            <div class="info-label">Created:</div>
            <div class="info-value">{{ $complaint->created_at->format('M d, Y H:i') }}</div>
        </div>

        @if($complaint->closed_at)
            <div class="info-row">
                <div class="info-label">Closed:</div>
                <div class="info-value">{{ $complaint->closed_at->format('M d, Y H:i') }}</div>
            </div>
        @endif
    </div>

    <div class="description">
        <h3>Description</h3>
        <p>{{ $complaint->description }}</p>
    </div>

    @if($complaint->attachments->count() > 0)
        <div class="info-row">
            <div class="info-label">Attachments:</div>
            <div class="info-value">{{ $complaint->attachments->count() }} file(s)</div>
        </div>
    @endif

    <!-- Manual Feedback Section -->
    <div class="feedback-section" style="margin-top: 40px; border: 1px solid #ccc; padding: 15px;">
        <h3 style="margin-top: 0; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 15px;">
            Feedback</h3>
        <div style="display: flex; justify-content: space-around; align-items: center;">
            <div style="text-align: center;">
                <div
                    style="width: 20px; height: 20px; border: 2px solid #333; display: inline-block; vertical-align: middle; margin-right: 5px;">
                </div>
                <span style="font-weight: bold;">Excellent</span>
            </div>
            <div style="text-align: center;">
                <div
                    style="width: 20px; height: 20px; border: 2px solid #333; display: inline-block; vertical-align: middle; margin-right: 5px;">
                </div>
                <span style="font-weight: bold;">Good</span>
            </div>
            <div style="text-align: center;">
                <div
                    style="width: 20px; height: 20px; border: 2px solid #333; display: inline-block; vertical-align: middle; margin-right: 5px;">
                </div>
                <span style="font-weight: bold;">Poor</span>
            </div>
        </div>
        <div style="margin-top: 40px; border-top: 1px dashed #ccc; padding-top: 20px;">
            <p style="margin: 0; color: #666; margin-top: 10px;">Customer Signature: ___________________________</p>
        </div>
    </div>

    <div class="footer">
        <p>This is a computer-generated document. No signature required.</p>
        <p>Complaint Management System - {{ config('app.name') }}</p>
    </div>

    <script>
        // Auto-print when page loads
        window.onload = function () {
            window.print();
        }
    </script>
</body>

</html>