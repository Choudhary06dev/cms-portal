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
    </div>
    <!-- Filters -->
    <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 rounded-xl p-4 flex items-end justify-center gap-3" style="background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(10px); width: fit-content; max-width: 95%;">
        <div style="flex: 0 0 auto;">
            <label class="block text-xs font-semibold text-gray-700 mb-1">GE</label>
            <select id="filterCity" name="city_id" class="p-2 rounded-lg border filter-select" style="font-size: 0.9rem; width: 180px;">
                <option value="">Select GE</option>
                @foreach($geGroups as $ge)
                    <option value="{{ $ge->id }}" {{ $cityId == $ge->id ? 'selected' : '' }}>{{ $ge->name }}</option>
                @endforeach
            </select>
        </div>
        <div style="flex: 0 0 auto;">
            <label class="block text-xs font-semibold text-gray-700 mb-1">GE Nodes</label>
            <select id="filterSector" name="sector_id" class="p-2 rounded-lg border filter-select" style="font-size: 0.9rem; width: 180px;">
                <option value="">All GE Nodes</option>
                @foreach($geNodes as $node)
                    <option value="{{ $node->id }}" {{ $sectorId == $node->id ? 'selected' : '' }}>{{ $node->name }}</option>
                @endforeach
            </select>
        </div>
        <div style="flex: 0 0 auto;">
            <label class="block text-xs font-semibold text-gray-700 mb-1">Complaints Category</label>
            <select id="filterCategory" name="category" class="p-2 rounded-lg border filter-select" style="font-size: 0.9rem; width: 180px;">
                <option value="all">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->name }}" {{ $category == $cat->name ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div style="flex: 0 0 auto;">
            <label class="block text-xs font-semibold text-gray-700 mb-1">Complaints Status</label>
            <select id="filterStatus" name="status" class="p-2 rounded-lg border filter-select" style="font-size: 0.9rem; width: 180px;">
                <option value="all">All Status</option>
                @foreach($statuses as $key => $label)
                    <option value="{{ $key }}" {{ $status == $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div style="flex: 0 0 auto;">
            <label class="block text-xs font-semibold text-gray-700 mb-1">Date Range</label>
            <select id="filterDateRange" name="date_range" class="p-2 rounded-lg border filter-select" style="font-size: 0.9rem; width: 180px;">
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
            <button id="resetFilters" class="px-3 py-1.5 text-sm rounded-lg border bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold" style="font-size: 0.9rem; padding: 0.5rem 1.25rem;">Reset</button>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="mx-auto mt-10 mb-8 grid grid-cols-4 gap-6" style="background: white; padding: 2rem 3rem; max-width: 95%; border-radius: 12px;">
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
        <!-- Complaint Resolution Trend -->
        <div class="bg-white p-6 rounded-xl shadow">
            <h2 class="text-xl font-semibold mb-4">Complaint Resolution Trend (2025)</h2>
            <div class="h-64">
                <canvas id="resolutionTrendChart"></canvas>
            </div>
        </div>
    </div>
    <!-- Right Stats Boxes -->
    <div class="col-span-1 grid grid-cols-2 gap-3" style="align-self: start;">
        <!-- Total Complaints (First) -->
        <div class="text-white rounded-xl text-center font-bold flex flex-col items-center justify-start" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); min-height: 90px; padding: 0.75rem 0.5rem;">
            <span id="stat-total-complaints" class="text-xl mb-1" style="line-height: 1.2;">{{ $stats['total_complaints'] ?? 0 }}</span>
            <span class="text-xs font-normal" style="line-height: 1.2;">Total Complaints</span>
        </div>
        <!-- In Progress -->
        <div class="text-white rounded-xl text-center font-bold flex flex-col items-center justify-start" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); min-height: 90px; padding: 0.75rem 0.5rem;">
            <span id="stat-in-progress" class="text-xl mb-1" style="line-height: 1.2;">{{ $stats['in_progress'] ?? 0 }}</span>
            <span class="text-xs font-normal" style="line-height: 1.2;">In Progress</span>
        </div>
        <!-- Addressed -->
        <div class="text-white rounded-xl text-center font-bold flex flex-col items-center justify-start" style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); min-height: 90px; padding: 0.75rem 0.5rem;">
            <span id="stat-addressed" class="text-xl mb-1" style="line-height: 1.2;">{{ $stats['addressed'] ?? 0 }}</span>
            <span class="text-xs font-normal" style="line-height: 1.2;">Addressed</span>
        </div>
        <!-- Work Performa -->
        <div class="text-white rounded-xl text-center font-bold flex flex-col items-center justify-start" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); min-height: 90px; padding: 0.75rem 0.5rem;">
            <span id="stat-work-performa" class="text-xl mb-1" style="line-height: 1.2;">{{ $stats['work_performa'] ?? 0 }}</span>
            <span class="text-xs font-normal" style="line-height: 1.2;">Work Performa</span>
        </div>
        <!-- Maintenance Performa -->
        <div class="text-white rounded-xl text-center font-bold flex flex-col items-center justify-start" style="background: linear-gradient(135deg, #eab308 0%, #ca8a04 100%); min-height: 90px; padding: 0.75rem 0.5rem;">
            <span id="stat-maint-performa" class="text-xl mb-1" style="line-height: 1.2;">{{ $stats['maint_performa'] ?? 0 }}</span>
            <span class="text-xs font-normal" style="line-height: 1.2;">Maintenance Performa</span>
        </div>
        <!-- Un Authorized -->
        <div class="text-white rounded-xl text-center font-bold flex flex-col items-center justify-start" style="background: linear-gradient(135deg, #ec4899 0%, #db2777 100%); min-height: 90px; padding: 0.75rem 0.5rem;">
            <span id="stat-un-authorized" class="text-xl mb-1" style="line-height: 1.2;">{{ $stats['un_authorized'] ?? 0 }}</span>
            <span class="text-xs font-normal" style="line-height: 1.2;">Un Authorized</span>
        </div>
        <!-- Product N/A -->
        <div class="text-white rounded-xl text-center font-bold flex flex-col items-center justify-start" style="background: linear-gradient(135deg, #475569 0%, #334155 100%); min-height: 90px; padding: 0.75rem 0.5rem;">
            <span id="stat-product" class="text-xl mb-1" style="line-height: 1.2;">{{ $stats['product'] ?? 0 }}</span>
            <span class="text-xs font-normal" style="line-height: 1.2;">Product N/A</span>
        </div>
        <!-- Resolution Rate -->
        <div class="text-white rounded-xl text-center font-bold flex flex-col items-center justify-start" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); min-height: 90px; padding: 0.75rem 0.5rem;">
            <span id="stat-resolution-rate" class="text-xl mb-1" style="line-height: 1.2;">{{ $stats['resolution_rate'] ?? 0 }}%</span>
            <span class="text-xs font-normal" style="line-height: 1.2;">Resolution Rate</span>
        </div>
        <!-- Pertains to GE/Const/Isld -->
        <div class="text-white rounded-xl text-center font-bold flex flex-col items-center justify-start" style="background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); min-height: 90px; padding: 0.75rem 0.5rem;">
            <span id="stat-pertains-ge" class="text-xl mb-1" style="line-height: 1.2;">{{ $stats['pertains_to_ge_const_isld'] ?? 0 }}</span>
            <span class="text-xs font-normal" style="line-height: 1.2;">Pertains to GE/Const/Isld</span>
        </div>
        <!-- Closed Complaints -->
        <div class="text-white rounded-xl text-center font-bold flex flex-col items-center justify-start" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); min-height: 90px; padding: 0.75rem 0.5rem;">
            <span id="stat-closed" class="text-xl mb-1" style="line-height: 1.2;">{{ $stats['closed'] ?? 0 }}</span>
            <span class="text-xs font-normal" style="line-height: 1.2;">Closed</span>
        </div>
    </div>
</div>

<!-- Footer -->


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
        // Ensure we have 12 months of data
        $monthlyComplaintsData = $monthlyComplaints ?? [];
        $monthlyResolvedData = $resolvedVsEdData ?? [];
        
        // Pad arrays to ensure 12 months
        while(count($monthlyComplaintsData) < 12) {
            $monthlyComplaintsData[] = 0;
        }
        while(count($monthlyResolvedData) < 12) {
            $monthlyResolvedData[] = 0;
        }
        
        // Take only first 12 months
        $monthlyComplaintsData = array_slice($monthlyComplaintsData, 0, 12);
        $monthlyResolvedData = array_slice($monthlyResolvedData, 0, 12);
    @endphp
    const monthLabels = @json($monthLabels ?? $defaultMonthLabels);
    const monthlyComplaintsReceived = @json($monthlyComplaintsData);
    const monthlyComplaintsResolved = @json($monthlyResolvedData);
    const complaintsByStatus = @json($complaintsByStatus ?? []);
    const resolvedVsEdData = @json($resolvedVsEdData ?? []);
    const recentEdData = @json($recentEdData ?? []);
    const yearTdData = @json($yearTdData ?? []);
    
    // Monthly Complaints Chart (Grouped Bar Chart)
    const ctx = document.getElementById('monthlyComplaintsChart').getContext('2d');
    const monthlyComplaintsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: monthLabels,
            datasets: [{
                label: 'Total Complaints',
                data: monthlyComplaintsReceived,
                backgroundColor: '#3B82F6', // Blue
                borderRadius: 4,
                borderSkipped: false,
            }, {
                label: 'Resolved Complaints',
                data: resolvedVsEdData,
                backgroundColor: '#22c55e', // Green
                borderRadius: 4,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 15,
                        font: {
                            size: 12
                        }
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
                },
                datalabels: {
                    color: '#ffffff',
                    font: {
                        weight: 'bold',
                        size: 11
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

    // Complaint Resolution Trend Chart (Line Chart)
    const ctx2 = document.getElementById('resolutionTrendChart').getContext('2d');
    const resolutionTrendChart = new Chart(ctx2, {
        type: 'line',
        data: {
            labels: monthLabels,
            datasets: [
                {
                    label: 'Complaints Received',
                    data: monthlyComplaintsReceived,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#3b82f6',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                },
                {
                    label: 'Complaints Resolved',
                    data: monthlyComplaintsResolved,
                    borderColor: '#22c55e',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#22c55e',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
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
                    },
                    mode: 'index',
                    intersect: false
                }
            },
            scales: {
                x: {
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
            },
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
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
