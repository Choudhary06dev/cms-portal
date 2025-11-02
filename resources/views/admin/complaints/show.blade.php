@extends('layouts.sidebar')

@section('title', 'Complaint Details — CMS Admin')

@section('content')
<!-- PAGE HEADER -->
<div class="mb-4">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h2 class="text-white mb-2">Complaint Details</h2>
      <p class="text-light">View and manage complaint information</p>
    </div>
   
  </div>
</div>

<!-- COMPLAINT INFORMATION -->
<div class="card-glass">
  <div class="card-header">
    <h5 class="card-title mb-0 text-white">
      <i data-feather="alert-triangle" class="me-2"></i>Complaint Details: {{ $complaint->complaint_id ?? $complaint->id }} - {{ $complaint->title ?? 'N/A' }}
    </h5>
  </div>
  <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <div class="mb-4">
                <h6 class="text-white fw-bold">Complaint Information</h6>
                <table class="table table-borderless">
                  <tr>
                    <td class="text-white"><strong>Complaint ID:</strong></td>
                    <td class="text-white">{{ $complaint->complaint_id ?? $complaint->id }}</td>
                  </tr>
                  <tr>
                    <td class="text-white"><strong>Complaint Title:</strong></td>
                    <td class="text-white">{{ $complaint->title ?? 'N/A' }}</td>
                  </tr>
                  <tr>
                    <td class="text-white"><strong>Apply Date/Time:</strong></td>
                    <td class="text-white">{{ $complaint->created_at ? $complaint->created_at->format('d-m-Y H:i:s') : 'N/A' }}</td>
                  </tr>
                  <tr>
                    <td class="text-white"><strong>Completion Time:</strong></td>
                    <td class="text-white">
                      @if($complaint->closed_at)
                        {{ $complaint->closed_at->format('Y-m-d H:i:s') }}
                      @elseif($complaint->status == 'resolved' || $complaint->status == 'closed')
                        {{ $complaint->updated_at->format('Y-m-d H:i:s') }}
                      @else
                        -
                      @endif
                    </td>
                  </tr>
                  <tr>
                    <td class="text-white"><strong>Complaint Nature & Type:</strong></td>
                    <td>
                      @php
                        $category = $complaint->category ?? 'N/A';
                        $department = $complaint->department ?? '';
                        
                        // Get product name from spare parts
                        $productName = '';
                        if ($complaint->spareParts && $complaint->spareParts->count() > 0) {
                          $firstSpare = $complaint->spareParts->first();
                          if ($firstSpare && $firstSpare->spare) {
                            $productName = $firstSpare->spare->item_name ?? '';
                            if ($complaint->spareParts->count() > 1) {
                              $productName .= ' (+' . ($complaint->spareParts->count() - 1) . ')';
                            }
                          }
                        }
                        
                        // Category to REQ type mapping
                        $reqTypeMap = [
                          'electric' => 'ELECTRECION REQ',
                          'technical' => 'TECHNICAL REQ',
                          'service' => 'SERVICE REQ',
                          'billing' => 'BILLING REQ',
                          'water' => 'PIPE FITTER REQ',
                          'sanitary' => 'SANITARY REQ',
                          'plumbing' => 'PLUMBING REQ',
                          'kitchen' => 'KITCHEN REQ',
                          'other' => 'OTHER REQ',
                        ];
                        
                        $reqType = $reqTypeMap[strtolower($category)] ?? strtoupper($category) . ' REQ';
                        
                        // Format display text with category and product name
                        if ($department) {
                          if (strpos(strtoupper($department), 'B&R') !== false) {
                            if ($productName) {
                              $displayText = $department . ' - ' . $productName . ' - MASSON REQ';
                            } else {
                              $displayText = $department . ' - MASSON REQ';
                            }
                          } else {
                            if ($productName) {
                              $displayText = $department . ' - ' . $productName . ' - ' . $reqType;
                            } else {
                              $displayText = $department . ' - ' . $reqType;
                            }
                          }
                        } else {
                          $categoryDisplay = [
                            'electric' => 'Electric',
                            'technical' => 'Technical',
                            'service' => 'Service',
                            'billing' => 'Billing',
                            'water' => 'Water Supply',
                            'sanitary' => 'Sanitary',
                            'plumbing' => 'Plumbing',
                            'kitchen' => 'Kitchen',
                            'other' => 'Other',
                          ];
                          $catDisplay = $categoryDisplay[strtolower($category)] ?? ucfirst($category);
                          
                          if ($productName) {
                            $displayText = $catDisplay . ' - ' . $productName . ' - ' . $reqType;
                          } else {
                            $displayText = $catDisplay . ' - ' . $reqType;
                          }
                        }
                      @endphp
                      <span class="text-white fw-bold">{{ $displayText }}</span>
                    </td>
                  </tr>
                  <tr>
                    <td class="text-white"><strong>Status:</strong></td>
                    <td>
                      @php
                        $statusDisplay = $complaint->status == 'in_progress' ? 'In-Process' : 
                                        ($complaint->status == 'resolved' || $complaint->status == 'closed' ? 'Addressed' : 
                                        ucfirst(str_replace('_', ' ', $complaint->status)));
                        $statusColor = ($complaint->status == 'in_progress' || $complaint->status == 'new' || $complaint->status == 'assigned') ? 
                                      'rgba(239, 68, 68, 0.25)' : 'rgba(34, 197, 94, 0.25)';
                        $textColor = ($complaint->status == 'in_progress' || $complaint->status == 'new' || $complaint->status == 'assigned') ? 
                                    '#ef4444' : '#22c55e';
                        $borderColor = ($complaint->status == 'in_progress' || $complaint->status == 'new' || $complaint->status == 'assigned') ? 
                                      '#ef4444' : '#22c55e';
                      @endphp
                      <span class="badge" style="background-color: {{ $statusColor }}; color: {{ $textColor }}; border: 1px solid {{ $borderColor }}; padding: 4px 8px; border-radius: 4px; font-weight: 600;">
                        {{ $statusDisplay }}
                      </span>
                    </td>
                  </tr>
                  <tr>
                    <td class="text-white"><strong>Priority:</strong></td>
                    <td>
                      <span class="badge bg-{{ $complaint->priority === 'high' ? 'danger' : ($complaint->priority === 'medium' ? 'warning' : 'success') }}">
                        {{ ucfirst($complaint->priority) }}
                      </span>
                    </td>
                  </tr>
                  <tr>
                    <td class="text-white"><strong>Description:</strong></td>
                    <td class="text-white fw-bold">{{ $complaint->description }}</td>
                  </tr>
                </table>
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="mb-4">
                <h6 class="text-white fw-bold">Complainant Information</h6>
                <table class="table table-borderless">
                  <tr>
                    <td class="text-white"><strong>Complainant Name:</strong></td>
                    <td class="text-white">{{ $complaint->client->client_name ?? 'N/A' }}</td>
                  </tr>
                  <tr>
                    <td class="text-white"><strong>Address:</strong></td>
                    <td class="text-white">{{ $complaint->client->address ?? 'N/A' }}</td>
                  </tr>
                  <tr>
                    <td class="text-white"><strong>Mobile No.:</strong></td>
                    <td class="text-white">{{ $complaint->client->phone ?? 'N/A' }}</td>
                  </tr>
                  <tr>
                    <td class="text-white"><strong>City:</strong></td>
                    <td class="text-white">{{ $complaint->city ?? 'N/A' }}</td>
                  </tr>
                  <tr>
                    <td class="text-white"><strong>Sector:</strong></td>
                    <td class="text-white">{{ $complaint->sector ?? 'N/A' }}</td>
                  </tr>
                  <tr>
                    <td class="text-white"><strong>Assigned To:</strong></td>
                    <td class="text-white">{{ $complaint->assignedEmployee->name ?? 'Unassigned' }}</td>
                  </tr>
                  <tr>
                    <td class="text-white"><strong>Department:</strong></td>
                    <td class="text-white">{{ $complaint->department ?? 'N/A' }}</td>
                  </tr>
                  <tr>
                    <td class="text-white"><strong>Created:</strong></td>
                    <td class="text-white">{{ $complaint->created_at->format('d-m-Y H:i:s') }}</td>
                  </tr>
                  @if($complaint->closed_at)
                  <tr>
                    <td class="text-white"><strong>Closed:</strong></td>
                    <td class="text-white">{{ $complaint->closed_at->format('Y-m-d H:i:s') }}</td>
                  </tr>
                  @endif
                </table>
              </div>
            </div>
          </div>

          @if($complaint->attachments->count() > 0)
          <div class="row mt-4">
            <div class="col-12">
              <h6 class="text-muted">Attachments</h6>
              <div class="row">
                @foreach($complaint->attachments as $attachment)
                <div class="col-md-3 mb-3">
                  <div class="card">
                    <div class="card-body text-center">
                      <i data-feather="file" class="mb-2"></i>
                      <p class="card-text small">{{ $attachment->original_name }}</p>
                      <a href="{{ Storage::url($attachment->file_path) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                        View
                      </a>
                    </div>
                  </div>
                </div>
                @endforeach
              </div>
            </div>
          </div>
          @endif


          @if($complaint->logs->count() > 0)
          <div class="row mt-4">
            <div class="col-12">
              <h6 class="text-muted">Activity Log</h6>
              <div class="table-responsive">
                <table class="table table-sm table-striped">
                  <thead>
                    <tr>
                      <th>Date</th>
                      <th>Action</th>
                      <th>User</th>
                      <th>Notes</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($complaint->logs as $log)
                    <tr>
                      <td>{{ $log->created_at->format('M d, Y H:i') }}</td>
                      <td>{{ ucfirst($log->action) }}</td>
                      <td>{{ $log->user->username ?? 'System' }}</td>
                      <td>{{ $log->notes ?? '-' }}</td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          @endif

          <div class="row mt-4">
            <div class="col-12">
              <div class="d-flex justify-content-between">
                <a href="{{ route('admin.complaints.index') }}" class="btn btn-secondary">
                  <i data-feather="arrow-left"></i> Back to Complaints
                </a>
               
                 
                 
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


@endsection

