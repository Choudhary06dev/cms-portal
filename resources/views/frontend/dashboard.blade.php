@extends('frontend.layouts.app')

@section('title', 'Dashboard UI')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
@endpush

@section('content')
<!-- Header Background -->
<div class="relative h-64 bg-cover bg-center" style="background-image: url('https://wallpaperaccess.com/full/7431952.jpg');">
    <div class="absolute inset-0 bg-blue-900 bg-opacity-40"></div>
    <!-- Logo -->
    <div class="absolute top-6 left-1/2 transform -translate-x-1/2 text-white text-center">
        <img src="https://share.google/images/oNtI0nbVxtLiSmH4B" class="h-20 mx-auto mb-2" alt="Pakistan Navy Logo" onerror="this.src='https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/Badge_of_the_Pakistan_Navy.png/240px-Badge_of_the_Pakistan_Navy.png'" />
        <p class="text-white font-semibold text-lg">PAKISTAN</p>
    </div>
    <!-- Filters -->
    <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 w-11/12 rounded-xl p-4 flex justify-between space-x-4" style="background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(10px);">
        <div class="w-1/5">
            <label class="block text-xs font-semibold text-gray-700 mb-1">GE</label>
            <select class="w-full p-2 rounded-lg border">
                <option>Select GE</option>
            </select>
        </div>
        <div class="w-1/5">
            <label class="block text-xs font-semibold text-gray-700 mb-1">GE Nodes</label>
            <select class="w-full p-2 rounded-lg border">
                <option>All GE Nodes</option>
            </select>
        </div>
        <div class="w-1/5">
            <label class="block text-xs font-semibold text-gray-700 mb-1">Complaints Category</label>
            <select class="w-full p-2 rounded-lg border">
                <option>All Categories</option>
            </select>
        </div>
        <div class="w-1/5">
            <label class="block text-xs font-semibold text-gray-700 mb-1">Complaints Status</label>
            <select class="w-full p-2 rounded-lg border">
                <option>All Status</option>
            </select>
        </div>
        <div class="w-1/5">
            <label class="block text-xs font-semibold text-gray-700 mb-1">&nbsp;</label>
            <select class="w-full p-2 rounded-lg border">
                <option>Select</option>
            </select>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="px-6 mt-10 mb-8 grid grid-cols-4 gap-6">
    <!-- Left Graphs -->
    <div class="col-span-3 space-y-6">
        <!-- Monthly Complaints and TVRR Complaints Row -->
        <div class="grid grid-cols-2 gap-6">
            <!-- Monthly Complaints -->
            <div class="bg-white p-6 rounded-xl shadow">
                <h2 class="text-xl font-semibold mb-4">Monthly Complaints (2025)</h2>
                <div class="h-48">
                    <canvas id="monthlyComplaintsChart"></canvas>
                </div>
            </div>
            <!-- Complaints by Status -->
            <div class="bg-white p-6 rounded-xl shadow">
                <h2 class="text-xl font-semibold mb-4">Complaints by Status</h2>
                <div class="h-48">
                    <canvas id="complaintsByStatusChart"></canvas>
                </div>
            </div>
        </div>
        <!-- Yearly Comparison -->
        <div class="bg-white p-6 rounded-xl shadow">
            <h2 class="text-xl font-semibold mb-4">Recen Ed VS Resolved Year TD Date (2025)</h2>
            <div class="h-64">
                <canvas id="resolvedVsEdChart"></canvas>
            </div>
        </div>
    </div>
    <!-- Right Stats Boxes -->
    <div class="col-span-1 grid grid-cols-2 gap-3">
        <div class="bg-green-500 text-white p-4 rounded-xl text-center font-bold flex flex-col justify-center items-center" style="min-height: 90px;">
            <div class="text-lg">98%</div>
            <div class="text-xs mt-1">Resolution Rate</div>
        </div>
        <div class="text-white p-4 rounded-xl text-center font-bold flex flex-col justify-center items-center" style="background-color: #FF6B35; min-height: 90px;">
            <div class="text-lg">932</div>
            <div class="text-xs mt-1">Total Number Of Complaints</div>
        </div>
        <div class="bg-purple-500 text-white p-4 rounded-xl text-center font-bold flex flex-col justify-center items-center" style="min-height: 90px;">
            <div class="text-lg">3</div>
            <div class="text-xs mt-1">Average Resolution</div>
        </div>
        <div class="bg-blue-500 text-white p-4 rounded-xl text-center font-bold flex flex-col justify-center items-center" style="min-height: 90px;">
            <div class="text-lg">1</div>
            <div class="text-xs mt-1">Work Performa</div>
        </div>
        <div class="bg-pink-600 text-white p-4 rounded-xl text-center font-bold flex flex-col justify-center items-center" style="min-height: 90px;">
            <div class="text-lg">88</div>
            <div class="text-xs mt-1">In Progress</div>
        </div>
        <div class="text-white p-4 rounded-xl text-center font-bold flex flex-col justify-center items-center" style="background-color: #06b6d4; min-height: 90px;">
            <div class="text-lg">20</div>
            <div class="text-xs mt-1">Complaints Closed</div>
        </div>
        <div class="bg-blue-700 text-white p-4 rounded-xl text-center font-bold flex flex-col justify-center items-center" style="min-height: 90px;">
            <div class="text-lg">1</div>
            <div class="text-xs mt-1">Product</div>
        </div>
        <div class="bg-green-700 text-white p-4 rounded-xl text-center font-bold flex flex-col justify-center items-center" style="min-height: 90px;">
            <div class="text-lg">8</div>
            <div class="text-xs mt-1">Addressed</div>
        </div>
    </div>
</div>

<!-- Footer -->
{{-- <footer class="text-center py-6 text-gray-600 mt-10 bg-blue-900 text-white">
    © 2021 - 2025 All rights reserved.
</footer> --}}

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Monthly Complaints Chart
    const ctx = document.getElementById('monthlyComplaintsChart').getContext('2d');
    const monthlyComplaintsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep'],
            datasets: [{
                label: 'Complaints',
                data: [45, 52, 38, 65, 58, 72, 68, 55, 62],
                backgroundColor: [
                    '#FF6B35', // Orange
                    '#3B82F6', // Blue
                    '#10B981', // Green
                    '#FF6B35', // Orange
                    '#3B82F6', // Blue
                    '#10B981', // Green
                    '#FF6B35', // Orange
                    '#3B82F6', // Blue
                    '#10B981'  // Green
                ],
                borderRadius: 4,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 10,
                    titleFont: {
                        size: 14
                    },
                    bodyFont: {
                        size: 12
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        font: {
                            size: 11
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 11
                        }
                    }
                }
            }
        }
    });

    // Recen Ed VS Resolved Year TD Date Chart (Stacked Bar Chart)
    const ctx2 = document.getElementById('resolvedVsEdChart').getContext('2d');
    const resolvedVsEdChart = new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep'],
            datasets: [
                {
                    label: 'Recent Ed',
                    data: [25, 30, 22, 35, 28, 40, 38, 32, 35],
                    backgroundColor: '#FF6B35', // Orange
                    borderRadius: 4,
                },
                {
                    label: 'Resolved',
                    data: [20, 22, 16, 30, 30, 32, 30, 23, 27],
                    backgroundColor: '#EC4899', // Pink
                    borderRadius: 4,
                },
                {
                    label: 'Year TD',
                    data: [15, 18, 12, 20, 18, 22, 20, 15, 18],
                    backgroundColor: '#9333EA', // Purple
                    borderRadius: 4,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        font: {
                            size: 11
                        },
                        padding: 8,
                        usePointStyle: true
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 10,
                    titleFont: {
                        size: 14
                    },
                    bodyFont: {
                        size: 12
                    }
                }
            },
            scales: {
                x: {
                    stacked: true,
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 11
                        }
                    }
                },
                y: {
                    stacked: true,
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        font: {
                            size: 11
                        }
                    }
                }
            }
        }
    });

    // Complaints by Status Chart (Donut Chart)
    const ctx3 = document.getElementById('complaintsByStatusChart').getContext('2d');
    const complaintsByStatusChart = new Chart(ctx3, {
        type: 'doughnut',
        data: {
            labels: ['Assigned', 'In Progress', 'Addressed', 'Work Performa', 'Maintenance Performa'],
            datasets: [{
                label: 'Complaints',
                data: [120, 88, 350, 45, 32],
                backgroundColor: [
                    '#3b82f6', // Blue - Assigned
                    '#dc2626', // Red - In Progress
                    '#16a34a', // Green - Addressed
                    '#60a5fa', // Light Blue - Work Performa
                    '#eab308'  // Yellow - Maintenance Performa
                ],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '60%',
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        font: {
                            size: 10
                        },
                        padding: 8,
                        usePointStyle: true
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 10,
                    titleFont: {
                        size: 12
                    },
                    bodyFont: {
                        size: 11
                    },
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += context.parsed;
                            return label;
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
