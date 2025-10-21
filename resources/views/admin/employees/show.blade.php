@extends('layouts.sidebar')

@section('title', 'Employee Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Employee Details</h5>
                    <div>
                        <a href="{{ route('admin.employees.edit', $employee) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(!isset($employee))
                        <div class="alert alert-danger">
                            <strong>Error:</strong> Employee data not found. Please check the URL or try again.
                        </div>
                    @else
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Personal Information</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Username:</strong></td>
                                    <td>{{ $employee->user->username ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{{ $employee->user->email ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Phone:</strong></td>
                                    <td>{{ $employee->user->phone ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Role:</strong></td>
                                    <td>{{ $employee->user->role->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $employee->user->status === 'active' ? 'success' : 'danger' }}">
                                            {{ ucfirst($employee->user->status ?? 'inactive') }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Theme:</strong></td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ ucfirst($employee->user->theme ?? 'light') }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h6 class="text-muted">Employee Information</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Department:</strong></td>
                                    <td>{{ $employee->department }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Designation:</strong></td>
                                    <td>{{ $employee->designation }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Biometric ID:</strong></td>
                                    <td>{{ $employee->biometric_id ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Leave Quota:</strong></td>
                                    <td>{{ $employee->leave_quota }} days</td>
                                </tr>
                                <tr>
                                    <td><strong>Created:</strong></td>
                                    <td>{{ $employee->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="text-muted">Performance Metrics</h6>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body text-center">
                                            <h5 class="card-title">
                                                @try
                                                    {{ $employee->assignedComplaints()->count() }}
                                                @catch
                                                    0
                                                @endtry
                                            </h5>
                                            <p class="card-text">Total Complaints</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-success text-white">
                                        <div class="card-body text-center">
                                            <h5 class="card-title">
                                                @try
                                                    {{ $employee->assignedComplaints()->where('status', 'resolved')->count() }}
                                                @catch
                                                    0
                                                @endtry
                                            </h5>
                                            <p class="card-text">Resolved</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-info text-white">
                                        <div class="card-body text-center">
                                            <h5 class="card-title">
                                                @try
                                                    {{ $employee->leaves()->count() }}
                                                @catch
                                                    0
                                                @endtry
                                            </h5>
                                            <p class="card-text">Leave Requests</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-warning text-white">
                                        <div class="card-body text-center">
                                            <h5 class="card-title">
                                                @try
                                                    {{ $employee->getRemainingLeaves() }}
                                                @catch
                                                    {{ $employee->leave_quota }}
                                                @endtry
                                            </h5>
                                            <p class="card-text">Remaining Leaves</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
