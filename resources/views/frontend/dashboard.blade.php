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
    <div class="absolute top-9 left-1/2 transform -translate-x-1/2 text-white text-center">
        <img src="https://tse2.mm.bing.net/th/id/OIP.HN5w-gh1toIzBM3qZ2c7ygHaJ4?pid=Api&h=220&P=0" class="h-20 mx-auto mb-2" alt="Pakistan Navy Logo" onerror="this.src='https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/Badge_of_the_Pakistan_Navy.png/240px-Badge_of_the_Pakistan_Navy.png'" />
        <p class="text-white font-semibold text-lg">PAKISTAN</p>
    </div>
    <!-- Filters -->
    <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 p-2 flex items-end justify-center gap-2" style="background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(3px); width: fit-content; max-width: 95%; border-radius: 0;">
        <div style="flex: 0 0 auto;">
            <label class="block text-xs font-semibold text-white mb-1">GE</label>
            <select id="filterCity" name="city_id" class="p-1.5 border filter-select" style="font-size: 0.9rem; width: 180px; border-radius: 0;">
                <option value="">Select GE</option>
                @foreach($geGroups as $ge)
                    <option value="{{ $ge->id }}" {{ $cityId == $ge->id ? 'selected' : '' }}>{{ $ge->name }}</option>
                @endforeach
            </select>
        </div>
        <div style="flex: 0 0 auto;">
            <label class="block text-xs font-semibold text-white mb-1">GE Nodes</label>
            <select id="filterSector" name="sector_id" class="p-1.5 border filter-select" style="font-size: 0.9rem; width: 180px; border-radius: 0;">
                <option value="">All GE Nodes</option>
                @foreach($geNodes as $node)
                    <option value="{{ $node->id }}" {{ $sectorId == $node->id ? 'selected' : '' }}>{{ $node->name }}</option>
                @endforeach
            </select>
        </div>
        <div style="flex: 0 0 auto;">
            <label class="block text-xs font-semibold text-white mb-1">Complaints Category</label>
            <select id="filterCategory" name="category" class="p-1.5 border filter-select" style="font-size: 0.9rem; width: 180px; border-radius: 0;">
                <option value="all">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->name }}" {{ $category == $cat->name ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div style="flex: 0 0 auto;">
            <label class="block text-xs font-semibold text-white mb-1">Complaints Status</label>
            <select id="filterStatus" name="status" class="p-1.5 border filter-select" style="font-size: 0.9rem; width: 180px; border-radius: 0;">
                <option value="all">All Status</option>
                @foreach($statuses as $key => $label)
                    <option value="{{ $key }}" {{ $status == $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div style="flex: 0 0 auto;">
            <label class="block text-xs font-semibold text-white mb-1">Date Range</label>
            <select id="filterDateRange" name="date_range" class="p-1.5 border filter-select" style="font-size: 0.9rem; width: 180px; border-radius: 0;">
                <option value="">All Time</option>
                <option value="yesterday" {{ $dateRange == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                <option value="today" {{ $dateRange == 'today' ? 'selected' : '' }}>Today</option>
                <option value="this_week" {{ $dateRange == 'this_week' ? 'selected' : '' }}>This Week</option>
                <option value="last_week" {{ $dateRange == 'last_week' ? 'selected' : '' }}>Last Week</option>
                <option value="this_month" {{ $dateRange == 'this_month' ? 'selected' : '' }}>This Month</option>
                <option value="last_month" {{ $dateRange == 'last_month' ? 'selected' : '' }}>Last Month</option>
                <option value="last_6_months" {{ $dateRange == 'last_6_months' ? 'selected' : '' }}>Last 6 Months</option>
            </select>
        </div>
        <div class="flex items-center" style="flex: 0 0 auto;">
            <label class="block text-xs font-semibold text-gray-700 mb-1" style="opacity: 0; height: 0; margin: 0;">&nbsp;</label>
            <button id="resetFilters" class="px-3 py-1.5 text-sm border bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold" style="font-size: 0.9rem; padding: 0.5rem 1.25rem; border-radius: 0;">Reset</button>
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
                <div class="h-56 w-full">
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
    <div class="col-span-1 grid grid-cols-2" style="column-gap: 0.5rem; row-gap: 0 !important;">
        <!-- Total Complaints (First) -->
        <div class="text-white p-1 rounded-md text-center text-xl font-bold flex flex-col justify-center items-center" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); height: 100px;">
            <span id="stat-total-complaints">{{ $stats['total_complaints'] ?? 0 }}</span>
            <span class="text-sm font-normal mt-0.5">Total Complaints</span>
        </div>
        <!-- In Progress -->
        <div class="text-white p-1 rounded-md text-center text-xl font-bold flex flex-col justify-center items-center" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); height: 100px;">
            <span id="stat-in-progress">{{ $stats['in_progress'] ?? 0 }}</span>
            <span class="text-sm font-normal mt-0.5">In Progress</span>
        </div>
        <!-- Addressed -->
        <div class="text-white p-1 rounded-md text-center text-xl font-bold flex flex-col justify-center items-center" style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); height: 100px; margin-top: -1px;">
            <span id="stat-addressed">{{ $stats['addressed'] ?? 0 }}</span>
            <span class="text-sm font-normal mt-0.5">Addressed</span>
        </div>
        <!-- Work Performa -->
        <div class="text-white p-1 rounded-md text-center text-xl font-bold flex flex-col justify-center items-center" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); height: 100px; margin-top: -1px;">
            <span id="stat-work-performa">{{ $stats['work_performa'] ?? 0 }}</span>
            <span class="text-sm font-normal mt-0.5">Work Performa</span>
        </div>
        <!-- Maintenance Performa -->
        <div class="text-white p-1 rounded-md text-center text-xl font-bold flex flex-col justify-center items-center" style="background: linear-gradient(135deg, #eab308 0%, #ca8a04 100%); height: 100px; margin-top: -1px;">
            <span id="stat-maint-performa">{{ $stats['maint_performa'] ?? 0 }}</span>
            <span class="text-sm font-normal mt-0.5">Maintenance Performa</span>
        </div>
        <!-- Un Authorized -->
        <div class="text-white p-1 rounded-md text-center text-xl font-bold flex flex-col justify-center items-center" style="background: linear-gradient(135deg, #ec4899 0%, #db2777 100%); height: 100px; margin-top: -1px;">
            <span id="stat-un-authorized">{{ $stats['un_authorized'] ?? 0 }}</span>
            <span class="text-sm font-normal mt-0.5">Un Authorized</span>
        </div>
        <!-- Product N/A -->
        <div class="text-white p-1 rounded-md text-center text-xl font-bold flex flex-col justify-center items-center" style="background: linear-gradient(135deg, #475569 0%, #334155 100%); height: 100px; margin-top: -1px;">
            <span id="stat-product">{{ $stats['product'] ?? 0 }}</span>
            <span class="text-sm font-normal mt-0.5">Product N/A</span>
        </div>
        <!-- Resolution Rate -->
        <div class="text-white p-1 rounded-md text-center text-xl font-bold flex flex-col justify-center items-center" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); height: 100px; margin-top: -1px;">
            <span id="stat-resolution-rate">{{ $stats['resolution_rate'] ?? 0 }}%</span>
            <span class="text-sm font-normal mt-0.5">Resolution Rate</span>
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
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<script>
// Register the datalabels plugin
Chart.register(ChartDataLabels);

document.addEventListener('DOMContentLoaded', function() {
    // Chart data from backend
    const monthlyData = @json($monthlyComplaints ?? []);
    @php
        $defaultMonthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    @endphp
    const monthLabels = @json($monthLabels ?? $defaultMonthLabels);
    const complaintsByStatus = @json($complaintsByStatus ?? []);
    const resolvedVsEdData = @json($resolvedVsEdData ?? []);
    const recentEdData = @json($recentEdData ?? []);
    const yearTdData = @json($yearTdData ?? []);
    
    // Monthly Complaints Chart
    const ctx = document.getElementById('monthlyComplaintsChart').getContext('2d');
    const monthlyComplaintsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: monthLabels,
            datasets: [{
                label: 'Complaints',
                data: monthlyData,
                backgroundColor: [
                    '#FF6B35', // Orange
                    '#3B82F6', // Blue
                    '#10B981', // Green
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
                },
                datalabels: {
                    color: '#ffffff',
                    font: {
                        weight: 'bold',
                        size: 12
                    },
                    anchor: 'center',
                    align: 'center',
                    formatter: function(value) {
                        return value;
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
            labels: monthLabels,
            datasets: [
                {
                    label: 'Recent Ed',
                    data: recentEdData,
                    backgroundColor: '#FF6B35', // Orange
                    borderRadius: 4,
                },
                {
                    label: 'Resolved',
                    data: resolvedVsEdData,
                    backgroundColor: '#EC4899', // Pink
                    borderRadius: 4,
                },
                {
                    label: 'Year TD',
                    data: yearTdData,
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

    // Complaints by Status Chart (Donut Chart) - Using same colors as admin side
    const statusMap = {
        'assigned': { label: 'Assigned', color: '#3b82f6' }, // Blue
        'in_progress': { label: 'In Progress', color: '#dc2626' }, // Red
        'resolved': { label: 'Addressed', color: '#16a34a' }, // Green
        'work_performa': { label: 'Work Performa', color: '#60a5fa' }, // Light Blue
        'maint_performa': { label: 'Maintenance Performa', color: '#eab308' }, // Yellow
        'work_priced_performa': { label: 'Work Performa Priced', color: '#9333ea' }, // Purple
        'maint_priced_performa': { label: 'Maintenance Performa Priced', color: '#ea580c' }, // Orange Red
        'product_na': { label: 'Product N/A', color: '#000000' }, // Black
        'un_authorized': { label: 'Un-Authorized', color: '#ec4899' }, // Pink
        'pertains_to_ge_const_isld': { label: 'Pertains to GE(N) Const Isld', color: '#06b6d4' }, // Aqua/Cyan
        'closed': { label: 'Closed', color: '#06b6d4' }, // Aqua/Cyan (same as pertains_to_ge_const_isld)
        'new': { label: 'New', color: '#3b82f6' } // Blue (same as assigned)
    };
    
    const statusKeys = Object.keys(complaintsByStatus);
    const statusLabels = statusKeys.map(key => {
        if (statusMap[key] && statusMap[key].label) {
            return statusMap[key].label;
        }
        // Fallback: format the key nicely
        return key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
    });
    const statusData = Object.values(complaintsByStatus);
    const statusColors = statusKeys.map(key => {
        if (statusMap[key] && statusMap[key].color) {
            return statusMap[key].color;
        }
        // Default color from admin side if status not found
        return '#64748b';
    });
    
    const ctx3 = document.getElementById('complaintsByStatusChart').getContext('2d');
    
    // Calculate total for percentage
    const totalComplaints = statusData.reduce((a, b) => a + b, 0);
    
    // Center text plugin for Chart.js
    const centerTextPlugin = {
        id: 'centerText',
        beforeDraw: function(chart) {
            const ctx = chart.ctx;
            const centerX = chart.chartArea.left + (chart.chartArea.right - chart.chartArea.left) / 2;
            const centerY = chart.chartArea.top + (chart.chartArea.bottom - chart.chartArea.top) / 2;
            
            ctx.save();
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            
            // Get hovered segment
            const activeElements = chart.getActiveElements();
            if (activeElements.length > 0) {
                const activeIndex = activeElements[0].index;
                const value = statusData[activeIndex];
                const percentage = totalComplaints > 0 ? ((value / totalComplaints) * 100).toFixed(1) : 0;
                const label = statusLabels[activeIndex];
                
                // Show status name
                ctx.font = 'bold 14px Arial';
                ctx.fillStyle = '#1f2937';
                ctx.fillText(label, centerX, centerY - 10);
                
                // Show percentage
                ctx.font = 'bold 20px Arial';
                ctx.fillStyle = statusColors[activeIndex];
                ctx.fillText(percentage + '%', centerX, centerY + 15);
                
                // Show count
                ctx.font = '12px Arial';
                ctx.fillStyle = '#6b7280';
                ctx.fillText(value + ' complaints', centerX, centerY + 35);
            } else {
                // Show total when not hovering
                ctx.font = 'bold 14px Arial';
                ctx.fillStyle = '#1f2937';
                ctx.fillText('Total', centerX, centerY - 10);
                
                ctx.font = 'bold 20px Arial';
                ctx.fillStyle = '#3b82f6';
                ctx.fillText(totalComplaints, centerX, centerY + 15);
                
                ctx.font = '12px Arial';
                ctx.fillStyle = '#6b7280';
                ctx.fillText('Complaints', centerX, centerY + 35);
            }
            ctx.restore();
        }
    };
    
    const complaintsByStatusChart = new Chart(ctx3, {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                label: 'Complaints',
                data: statusData,
                backgroundColor: statusColors,
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '60%',
            interaction: {
                intersect: false,
                mode: 'index'
            },
            animation: {
                animateRotate: true,
                animateScale: false
            },
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
                            const value = context.parsed;
                            const percentage = totalComplaints > 0 ? ((value / totalComplaints) * 100).toFixed(1) : 0;
                            if (label) {
                                label += ': ';
                            }
                            label += value + ' (' + percentage + '%)';
                            return label;
                        }
                    }
                },
                datalabels: {
                    color: '#ffffff',
                    font: {
                        weight: 'bold',
                        size: 12
                    },
                    formatter: function(value, context) {
                        const percentage = totalComplaints > 0 ? ((value / totalComplaints) * 100).toFixed(1) : 0;
                        return percentage + '%';
                    },
                    textAlign: 'center',
                    textStrokeColor: 'rgba(0, 0, 0, 0.5)',
                    textStrokeWidth: 2
                }
            }
        },
        plugins: [centerTextPlugin]
    });
    
    // Add event listener for hover to update chart center text
    const chartCanvas = document.getElementById('complaintsByStatusChart');
    chartCanvas.addEventListener('mousemove', function() {
        complaintsByStatusChart.update('none');
    });
    chartCanvas.addEventListener('mouseleave', function() {
        complaintsByStatusChart.update('none');
    });
    
    // Filter functionality
    const filterSelects = document.querySelectorAll('.filter-select');
    const resetBtn = document.getElementById('resetFilters');
    
    // Handle filter changes
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            applyFilters();
        });
    });
    
    // Handle GE change to update GE Nodes
    document.getElementById('filterCity').addEventListener('change', function() {
        const cityId = this.value;
        // Reload page with new city filter to get updated GE Nodes
        applyFilters();
    });
    
    // Reset filters
    resetBtn.addEventListener('click', function() {
        window.location.href = '{{ route("frontend.dashboard") }}';
    });
    
    function applyFilters() {
        const cityId = document.getElementById('filterCity').value;
        const sectorId = document.getElementById('filterSector').value;
        const category = document.getElementById('filterCategory').value;
        const status = document.getElementById('filterStatus').value;
        const dateRange = document.getElementById('filterDateRange').value;
        
        const params = new URLSearchParams();
        if (cityId) params.append('city_id', cityId);
        if (sectorId) params.append('sector_id', sectorId);
        if (category && category !== 'all') params.append('category', category);
        if (status && status !== 'all') params.append('status', status);
        if (dateRange) params.append('date_range', dateRange);
        
        window.location.href = '{{ route("frontend.dashboard") }}?' + params.toString();
    }
});
</script>
@endpush
