@extends('layouts.sidebar')

@section('title', 'Approval Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Approval Details</h5>
                    <div>
                        <a href="{{ route('admin.approvals.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Approvals
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Approval Information</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Approval ID:</strong></td>
                                    <td>#{{ $approval->id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="badge 
                                            @if($approval->status === 'pending') bg-warning
                                            @elseif($approval->status === 'approved') bg-success
                                            @elseif($approval->status === 'rejected') bg-danger
                                            @else bg-secondary @endif">
                                            {{ ucfirst($approval->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Created:</strong></td>
                                    <td>{{ $approval->created_at ? $approval->created_at->format('M d, Y H:i') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Approved:</strong></td>
                                    <td>{{ $approval->approved_at ? $approval->approved_at->format('M d, Y H:i') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Requested By:</strong></td>
                                    <td>{{ $approval->requestedBy->user->username ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Approved By:</strong></td>
                                    <td>{{ $approval->approvedBy->user->username ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Complaint Information</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Complaint ID:</strong></td>
                                    <td>{{ $approval->complaint->getTicketNumberAttribute() }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Client:</strong></td>
                                    <td>{{ $approval->complaint->client ? $approval->complaint->client->client_name : 'Deleted Client' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Title:</strong></td>
                                    <td>{{ $approval->complaint->title ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Category:</strong></td>
                                    <td>
                                        <span class="category-badge category-{{ strtolower($approval->complaint->category) }}">
                                            {{ ucfirst($approval->complaint->category) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Priority:</strong></td>
                                    <td>
                                        <span class="badge 
                                            @if($approval->complaint->priority === 'urgent' || $approval->complaint->priority === 'emergency') bg-danger
                                            @elseif($approval->complaint->priority === 'high') bg-warning
                                            @elseif($approval->complaint->priority === 'medium') bg-info
                                            @else bg-secondary @endif">
                                            {{ ucfirst($approval->complaint->priority) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="badge 
                                            @if($approval->complaint->status === 'resolved' || $approval->complaint->status === 'closed') bg-success
                                            @elseif($approval->complaint->status === 'in_progress') bg-primary
                                            @elseif($approval->complaint->status === 'assigned') bg-info
                                            @else bg-secondary @endif">
                                            {{ ucfirst($approval->complaint->status) }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($approval->remarks)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="text-muted">Remarks</h6>
                            <div class="alert alert-info">
                                {{ $approval->remarks }}
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="text-muted">Requested Items</h6>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Item Name</th>
                                            <th>Quantity Requested</th>
                                            <th>Unit Price</th>
                                            <th>Total Cost</th>
                                            <th>Reason</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($approval->items as $item)
                                        <tr>
                                            <td>{{ $item->spare->item_name ?? 'N/A' }}</td>
                                            <td>{{ $item->quantity_requested }}</td>
                                            <td>PKR {{ number_format($item->spare->unit_price ?? 0, 2) }}</td>
                                            <td>PKR {{ number_format(($item->spare->unit_price ?? 0) * $item->quantity_requested, 2) }}</td>
                                            <td>{{ $item->reason ?? 'N/A' }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">No items found</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-primary">
                                            <th colspan="3">Total Estimated Cost:</th>
                                            <th>PKR {{ number_format($approval->getTotalEstimatedCostAttribute(), 2) }}</th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>


@endsection

