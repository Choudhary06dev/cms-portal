<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Employee;
use App\Models\Spare;
use App\Models\ComplaintCategory;
use App\Models\City;
use App\Models\Sector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\LocationFilterTrait;

class HomeController extends Controller
{
    use LocationFilterTrait;

    /**
     * Apply location-based filtering to complaints query for frontend users
     * using the GE Groups/Nodes assigned via frontend_user_locations records.
     */
    protected function filterComplaintsByLocationForFrontend($query, $user, ?array $locationScope = null)
    {
        $scope = $locationScope ?? $this->getFrontendUserLocationScope($user);

        return $this->applyFrontendLocationScope($query, $scope);
    }

    /**
     * Build location scope (cities and sectors) assigned to frontend user
     */
    protected function getFrontendUserLocationScope($user): array
    {
        $scope = [
            'restricted' => false,
            'city_ids' => [],
            'sector_ids' => [],
            'city_sector_map' => [],
            'sector_city_map' => [],
        ];

        if (!$user) {
            return $scope;
        }

        // Get privileges from JSON columns
        $cityIds = $user->group_ids ?? [];
        $sectorIds = $user->node_ids ?? [];

        if (empty($cityIds) && empty($sectorIds)) {
            // Check if user is Admin (role_id 1 or has admin role)
            // If Admin, return unrestricted scope (restricted = false)
            if ($user->role_id === 1 || (method_exists($user, 'isAdmin') && $user->isAdmin())) {
                return $scope;
            }

            // Regular user with no privileges -> Restricted access (sees nothing)
            $scope['restricted'] = true;
            return $scope;
        }

        $scope['restricted'] = true;
        $scope['city_ids'] = $cityIds;
        $scope['sector_ids'] = $sectorIds;

        // Build city_sector_map and sector_city_map for sector-based filtering
        if (!empty($sectorIds)) {
            $sectors = \App\Models\Sector::whereIn('id', $sectorIds)->get();
            foreach ($sectors as $sector) {
                $scope['sector_city_map'][$sector->id] = $sector->city_id;
                if (!isset($scope['city_sector_map'][$sector->city_id])) {
                    $scope['city_sector_map'][$sector->city_id] = [];
                }
                $scope['city_sector_map'][$sector->city_id][] = $sector->id;
            }
        }

        return $scope;
    }

    /**
     * Apply location scope (cities/sectors) to a query builder
     */
    protected function applyFrontendLocationScope($query, array $scope, string $cityColumn = 'city_id', string $sectorColumn = 'sector_id')
    {
        if (empty($scope['restricted'])) {
            return $query;
        }

        $cityIds = $scope['city_ids'] ?? [];
        $sectorIds = $scope['sector_ids'] ?? [];

        if (empty($cityIds) && empty($sectorIds)) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where(function ($q) use ($cityIds, $sectorIds, $cityColumn, $sectorColumn) {
            $applied = false;
            if (!empty($sectorIds)) {
                $q->whereIn($sectorColumn, $sectorIds);
                $applied = true;
            }
            if (!empty($cityIds)) {
                $method = $applied ? 'orWhereIn' : 'whereIn';
                $q->{$method}($cityColumn, $cityIds);
            }
        });
    }

    /**
     * Determine if selected city is accessible for frontend user
     */
    protected function canAccessCity(?int $cityId, array $scope): bool
    {
        if (!$cityId) {
            return true;
        }

        if (empty($scope['restricted'])) {
            return true;
        }

        if (!empty($scope['city_ids']) && in_array($cityId, $scope['city_ids'])) {
            return true;
        }

        // City accessible through sector-level assignments (used for dropdown visibility)
        if (!empty($scope['city_sector_map']) && array_key_exists($cityId, $scope['city_sector_map'])) {
            return true;
        }

        return false;
    }

    /**
     * Determine if selected sector is accessible for frontend user
     */
    protected function canAccessSector(?int $sectorId, array $scope): bool
    {
        if (!$sectorId) {
            return true;
        }

        if (empty($scope['restricted'])) {
            return true;
        }

        if (!empty($scope['sector_ids']) && in_array($sectorId, $scope['sector_ids'])) {
            return true;
        }

        if (!empty($scope['city_ids'])) {
            $sectorCityId = $this->resolveSectorCity($sectorId, $scope);
            if ($sectorCityId && in_array($sectorCityId, $scope['city_ids'])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get sectors permitted within a specific city for the current scope
     */
    protected function getPermittedSectorsForCity(int $cityId, array $scope): array
    {
        if (!empty($scope['city_ids']) && in_array($cityId, $scope['city_ids'])) {
            // Entire city is accessible
            return [];
        }

        return $scope['city_sector_map'][$cityId] ?? [];
    }

    /**
     * Resolve sector's city via cached data or database lookup
     */
    protected function resolveSectorCity(int $sectorId, array $scope): ?int
    {
        if (!empty($scope['sector_city_map']) && array_key_exists($sectorId, $scope['sector_city_map'])) {
            return $scope['sector_city_map'][$sectorId];
        }

        static $sectorCityCache = [];
        if (array_key_exists($sectorId, $sectorCityCache)) {
            return $sectorCityCache[$sectorId];
        }

        $sector = Sector::find($sectorId);
        $sectorCityCache[$sectorId] = $sector ? $sector->city_id : null;

        return $sectorCityCache[$sectorId];
    }

    /**
     * Return list of city IDs that should appear in GE Group dropdown
     */
    protected function getAccessibleCityIdsForDropdown(array $scope): ?array
    {
        if (empty($scope['restricted'])) {
            return null;
        }

        $cityIds = $scope['city_ids'] ?? [];
        $derivedCityIds = array_keys($scope['city_sector_map'] ?? []);

        $combined = array_unique(array_merge($cityIds, $derivedCityIds));

        return empty($combined) ? null : $combined;
    }

    public function index()
    {
        return view('frontend.home');
    }

    public function features()
    {
        return view('frontend.features');
    }

    public function dashboard(Request $request)
    {
        // Get logged-in user
        $user = Auth::user();
        $locationScope = $this->getFrontendUserLocationScope($user);

        // Get filter parameters (including CMES)
        $cmesId = $request->get('cmes_id');
        $cityId = $request->get('city_id');
        $sectorId = $request->get('sector_id');
        $category = $request->get('category');
        $status = $request->get('status');
        $dateRange = $request->get('date_range');

        // Build base query with filters
        $complaintsQuery = Complaint::query();

        // Apply location filtering based on GE Group (city_id) and GE Node (sector_id) selections
        $hasRestrictions = !empty($locationScope['restricted']);

        if ($cityId) {
            if ($this->canAccessCity((int) $cityId, $locationScope)) {
                // If sector is also selected, prioritize sector filter
                if ($sectorId) {
                    if ($this->canAccessSector((int) $sectorId, $locationScope)) {
                        $complaintsQuery->where('sector_id', $sectorId);
                    } else {
                        $complaintsQuery->whereRaw('1 = 0');
                    }
                } else {
                    // Only city selected, no sector
                    if (!empty($locationScope['city_ids']) && in_array((int) $cityId, $locationScope['city_ids'])) {
                        $complaintsQuery->where('city_id', $cityId);
                    } else {
                        $allowedSectors = $this->getPermittedSectorsForCity((int) $cityId, $locationScope);
                        if (!empty($allowedSectors)) {
                            $complaintsQuery->whereIn('sector_id', $allowedSectors);
                        } else {
                            $complaintsQuery->whereRaw('1 = 0');
                        }
                    }
                }
            } else {
                $complaintsQuery->whereRaw('1 = 0');
            }
        } elseif ($sectorId) {
            if ($this->canAccessSector((int) $sectorId, $locationScope)) {
                $complaintsQuery->where('sector_id', $sectorId);
            } else {
                $complaintsQuery->whereRaw('1 = 0');
            }
        } elseif ($hasRestrictions) {
            $this->filterComplaintsByLocationForFrontend($complaintsQuery, $user, $locationScope);
        }

        if ($category && $category !== 'all') {
            $complaintsQuery->where('category', $category);
        }

        if ($status && $status !== 'all') {
            $complaintsQuery->where('status', $status);
        }

        // Filter by date range
        if ($dateRange) {
            $now = now();
            switch ($dateRange) {
                case 'yesterday':
                    $complaintsQuery->whereDate('created_at', $now->copy()->subDay()->toDateString());
                    break;
                case 'today':
                    $complaintsQuery->whereDate('created_at', $now->toDateString());
                    break;
                case 'this_week':
                    $complaintsQuery->whereBetween('created_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()]);
                    break;
                case 'last_week':
                    $complaintsQuery->whereBetween('created_at', [$now->copy()->subWeek()->startOfWeek(), $now->copy()->subWeek()->endOfWeek()]);
                    break;
                case 'this_month':
                    $complaintsQuery->whereMonth('created_at', $now->month)
                        ->whereYear('created_at', $now->year);
                    break;
                case 'last_month':
                    $complaintsQuery->whereMonth('created_at', $now->copy()->subMonth()->month)
                        ->whereYear('created_at', $now->copy()->subMonth()->year);
                    break;
                case 'last_6_months':
                    $complaintsQuery->where('created_at', '>=', $now->copy()->subMonths(6)->startOfDay());
                    break;
            }
        }

        // Get filter options - filter based on user's location access
        $geGroupsQuery = City::where(function ($q) {
            $q->where('name', 'LIKE', '%GE%')
                ->orWhere('name', 'LIKE', '%AGE%')
                ->orWhere('name', 'LIKE', '%ge%')
                ->orWhere('name', 'LIKE', '%age%');
        })
            ->where('status', 'active');

        // If CMES selected, restrict GE groups to that CMES
        if ($cmesId) {
            $geGroupsQuery->where('cme_id', $cmesId);
        } else {
            $accessibleCityIds = $this->getAccessibleCityIdsForDropdown($locationScope);
            if (!empty($accessibleCityIds)) {
                $geGroupsQuery->whereIn('id', $accessibleCityIds);
            } elseif (!empty($locationScope['restricted'])) {
                // Restricted user but no accessible cities -> show nothing
                $geGroupsQuery->whereRaw('1 = 0');
            }
        }

        $geGroups = $geGroupsQuery->orderBy('name')->get();

        $geNodesQuery = Sector::where('status', 'active');

        // If CMES selected, show only nodes belonging to cities of that CMES
        if ($cmesId) {
            $cityIdsForCmes = City::where('cme_id', $cmesId)->pluck('id')->toArray();
            $geNodesQuery->where(function ($q) use ($cityIdsForCmes, $cmesId) {
                if (!empty($cityIdsForCmes)) {
                    $q->whereIn('city_id', $cityIdsForCmes);
                }
                // Also include sectors that have cme_id set directly
                $q->orWhere('cme_id', $cmesId);
            });

            // If a specific GE (city) was selected after choosing CMES, narrow nodes to that city
            if (!empty($cityId)) {
                $geNodesQuery->where('city_id', $cityId);
            }
        } else {
            // Apply location filter to GE Nodes dropdown based on user's location
            if (!empty($locationScope['restricted'])) {
                if (!empty($locationScope['sector_ids'])) {
                    $geNodesQuery->whereIn('id', $locationScope['sector_ids']);
                } elseif (!empty($locationScope['city_ids'])) {
                    $geNodesQuery->whereIn('city_id', $locationScope['city_ids']);
                } elseif (!empty($locationScope['city_sector_map'])) {
                    $geNodesQuery->whereIn('city_id', array_keys($locationScope['city_sector_map']));
                } else {
                    // Restricted user but no assigned sectors/cities -> show nothing
                    $geNodesQuery->whereRaw('1 = 0');
                }
            }

            // Apply manual city filter if provided (for when user selects a GE Group)
            if ($cityId) {
                $geNodesQuery->where('city_id', $cityId);
            }
        }

        $geNodes = $geNodesQuery->orderBy('name')->get();

        // If CMES selected, also filter the main complaints query to CMES scope
        if ($cmesId) {
            $cityIdsForCmes = City::where('cme_id', $cmesId)->pluck('id')->toArray();

            $sectorIdsForCmes = Sector::where(function ($q) use ($cmesId, $cityIdsForCmes) {
                $q->where('cme_id', $cmesId);
                if (!empty($cityIdsForCmes)) {
                    $q->orWhereIn('city_id', $cityIdsForCmes);
                }
            })->pluck('id')->toArray();

            if (empty($cityIdsForCmes) && empty($sectorIdsForCmes)) {
                // No matching CMES scope — return no complaints
                $complaintsQuery->whereRaw('1 = 0');
            } else {
                $complaintsQuery->where(function ($q) use ($cityIdsForCmes, $sectorIdsForCmes) {
                    if (!empty($cityIdsForCmes)) {
                        $q->whereIn('city_id', $cityIdsForCmes);
                    }
                    if (!empty($sectorIdsForCmes)) {
                        // If city filter already applied above, this will OR with sector filter
                        $method = !empty($cityIdsForCmes) ? 'orWhereIn' : 'whereIn';
                        $q->{$method}('sector_id', $sectorIdsForCmes);
                    }
                });
            }
        }

        $categories = ComplaintCategory::all();

        // Get all statuses from database (same as admin side)
        $statuses = [
            'assigned' => 'Assigned',
            'in_progress' => 'In Progress',
            'resolved' => 'Addressed',
            'work_performa' => 'Work Performa',
            'maint_performa' => 'Maintenance Performa',
            'work_priced_performa' => 'Work Performa Priced',
            'maint_priced_performa' => 'Maintenance Performa Priced',
            'product_na' => 'Product N/A',
            'un_authorized' => 'Un-Authorized',
            'pertains_to_ge_const_isld' => 'Pertains to GE(N) Const Isld',
        ];

        // Calculate stats with filters
        $stats = [
            'total_complaints' => (clone $complaintsQuery)->count(),
            'new_complaints' => (clone $complaintsQuery)->where('status', 'new')->count(),
            'pending_complaints' => (clone $complaintsQuery)->whereIn('status', ['new', 'assigned', 'in_progress'])->count(),
            'resolved_complaints' => (clone $complaintsQuery)->whereIn('status', ['resolved', 'closed'])->count(),
            'overdue_complaints' => (clone $complaintsQuery)->whereIn('status', ['new', 'assigned', 'in_progress'])->count(),
            'complaints_today' => (clone $complaintsQuery)->whereDate('created_at', now()->startOfDay())->count(),
            'complaints_this_month' => (clone $complaintsQuery)->where('created_at', '>=', now()->startOfMonth())->count(),
            'complaints_last_month' => (clone $complaintsQuery)->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->startOfMonth()])->count(),
        ];

        // Calculate resolution rate
        $totalComplaints = $stats['total_complaints'];
        $resolvedComplaints = $stats['resolved_complaints'];
        $resolutionRate = $totalComplaints > 0 ? round(($resolvedComplaints / $totalComplaints) * 100) : 0;
        $stats['resolution_rate'] = $resolutionRate;

        // Calculate average resolution time
        $resolvedComplaintsWithTime = (clone $complaintsQuery)
            ->whereIn('status', ['resolved', 'closed'])
            ->whereNotNull('closed_at')
            ->get();

        $avgResolutionDays = 0;
        if ($resolvedComplaintsWithTime->count() > 0) {
            $totalDays = $resolvedComplaintsWithTime->sum(function ($complaint) {
                return $complaint->created_at->diffInDays($complaint->closed_at);
            });
            $avgResolutionDays = round($totalDays / $resolvedComplaintsWithTime->count());
        }
        $stats['average_resolution_days'] = $avgResolutionDays;

        // In Progress count
        $stats['in_progress'] = (clone $complaintsQuery)->where('status', 'in_progress')->count();

        // Assigned count
        $stats['assigned'] = (clone $complaintsQuery)->where('status', 'assigned')->count();

        // Closed count
        $stats['closed'] = (clone $complaintsQuery)->where('status', 'closed')->count();

        // Work Performa count
        $stats['work_performa'] = (clone $complaintsQuery)->where('status', 'work_performa')->count();

        // Maintenance Performa count
        $stats['maint_performa'] = (clone $complaintsQuery)->where('status', 'maint_performa')->count();

        // Addressed count (resolved status)
        $stats['addressed'] = (clone $complaintsQuery)->where('status', 'resolved')->count();

        // Un Authorized count
        $stats['un_authorized'] = (clone $complaintsQuery)->where('status', 'un_authorized')->count();

        // Product N/A count
        $stats['product'] = (clone $complaintsQuery)->where('status', 'product_na')->count();

        // Pertains to GE/Const/Isld count
        $stats['pertains_to_ge_const_isld'] = (clone $complaintsQuery)->where('status', 'pertains_to_ge_const_isld')->count();

        $page = request()->get('page', 1);
        $perPage = 5;
        $recentComplaintsQuery = Complaint::with(['client', 'assignedEmployee']);
        $this->filterComplaintsByLocationForFrontend($recentComplaintsQuery, $user, $locationScope);
        $recentComplaints = $recentComplaintsQuery->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        $self = $this; // Store reference for use in closure
        $pendingApprovals = class_exists(\App\Models\SpareApprovalPerforma::class)
            ? \App\Models\SpareApprovalPerforma::with(['complaint.client', 'requestedBy', 'items.spare'])
                ->whereHas('complaint', function ($q) use ($user, $self, $locationScope) {
                    $self->filterComplaintsByLocationForFrontend($q, $user, $locationScope);
                })
                ->where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
            : collect();

        $lowStockItems = collect();
        if (method_exists(Spare::class, 'lowStock')) {
            $lowStockItemsQuery = Spare::lowStock();
            $this->applyFrontendLocationScope($lowStockItemsQuery, $locationScope);
            $lowStockItems = $lowStockItemsQuery
                ->orderBy('stock_quantity', 'asc')
                ->limit(10)
                ->get();
        }

        $overdueComplaintsQuery = Complaint::whereIn('status', ['new', 'assigned', 'in_progress'])
            ->with(['client', 'assignedEmployee']);
        $this->filterComplaintsByLocationForFrontend($overdueComplaintsQuery, $user, $locationScope);
        $overdueComplaints = $overdueComplaintsQuery->orderBy('created_at', 'asc')
            ->limit(10)
            ->get();

        // Get monthly complaints data (current year)
        $monthlyComplaints = [];
        $monthLabels = [];
        for ($i = 0; $i < 12; $i++) { // Jan to Dec
            $date = now()->startOfYear()->addMonths($i);
            $monthLabels[] = $date->format('M');
            $monthQuery = (clone $complaintsQuery)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month);
            $monthlyComplaints[] = $monthQuery->count();
        }

        // Get complaints by status with filters
        $complaintsByStatusQuery = (clone $complaintsQuery);
        $complaintsByStatus = $complaintsByStatusQuery
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Get resolved vs recent ed data (year to date)
        $resolvedVsEdData = [];
        $recentEdData = [];
        $yearTdData = [];
        for ($i = 0; $i < 12; $i++) { // Jan to Dec
            $date = now()->startOfYear()->addMonths($i);
            $monthQuery = (clone $complaintsQuery)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month);

            $recentEdData[] = $monthQuery->count();
            $resolvedData = (clone $monthQuery)
                ->whereIn('status', ['resolved', 'closed'])
                ->count();
            $resolvedVsEdData[] = $resolvedData;

            // Year TD (Year to Date) - cumulative from start of year
            $yearTdQuery = (clone $complaintsQuery)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', '<=', $date->month);
            $yearTdData[] = $yearTdQuery->count();
        }

        $employeePerformanceQuery = Employee::query();
        $this->filterEmployeesByLocation($employeePerformanceQuery, $user);
        $this->applyFrontendLocationScope($employeePerformanceQuery, $locationScope);
        $employeePerformance = $employeePerformanceQuery
            ->withCount([
                'assignedComplaints' => function ($query) use ($user, $self, $locationScope) {
                    $query->where('created_at', '>=', now()->subDays(30));
                    // Apply location filter to assigned complaints count
                    $self->filterComplaintsByLocationForFrontend($query, $user, $locationScope);
                }
            ])
            ->orderBy('assigned_complaints_count', 'desc')
            ->limit(5)
            ->get();

        $slaPerformance = [
            'total' => 0,
            'within_sla' => 0,
            'breached' => 0,
            'sla_percentage' => 0,
        ];

        // Fetch CMES list for dropdown - filter based on user privileges
        $cmesListQuery = \App\Models\Cme::where('status', 'active');

        // Apply CMES filtering based on user privileges
        if ($user && !empty($user->cme_ids)) {
            // User has specific CMES assigned, show only those
            $cmesListQuery->whereIn('id', $user->cme_ids);
        } elseif (!empty($locationScope['restricted'])) {
            // User has restricted location scope (e.g. GE user), derive CMES from assigned cities/sectors
            $derivedCmeIds = [];

            // From assigned cities
            if (!empty($locationScope['city_ids'])) {
                $cityCmeIds = \App\Models\City::whereIn('id', $locationScope['city_ids'])
                    ->pluck('cme_id')
                    ->filter()
                    ->unique()
                    ->toArray();
                $derivedCmeIds = array_merge($derivedCmeIds, $cityCmeIds);
            }

            // From assigned sectors
            if (!empty($locationScope['sector_ids'])) {
                $sectorCmeIds = \App\Models\Sector::whereIn('id', $locationScope['sector_ids'])
                    ->pluck('cme_id')
                    ->filter()
                    ->unique()
                    ->toArray();
                $derivedCmeIds = array_merge($derivedCmeIds, $sectorCmeIds);
            }

            if (!empty($derivedCmeIds)) {
                $cmesListQuery->whereIn('id', array_unique($derivedCmeIds));
            } else {
                // Restricted user but no derived CMES -> show nothing
                $cmesListQuery->whereRaw('1 = 0');
            }
        }

        $cmesList = $cmesListQuery->orderBy('name')->get();

        // Get CME Complaint Stats for Graph
        $cmeGraphLabels = [];
        $cmeGraphData = [];
        $cmeResolvedData = []; // New array for addressed complaints

        $cmeDateRange = $request->get('cme_date_range', $dateRange); // Use specific filter or fallback to global

        foreach ($cmesList as $cme) {
            $cmeGraphLabels[] = $cme->name;

            // Get cities (GE Groups) for this CME
            $cityIdsForCme = \App\Models\City::where('cme_id', $cme->id)->pluck('id')->toArray();

            // Get sectors (GE Nodes) for this CME (either directly or via city)
            $sectorIdsForCme = \App\Models\Sector::where(function ($q) use ($cme, $cityIdsForCme) {
                $q->where('cme_id', $cme->id);
                if (!empty($cityIdsForCme)) {
                    $q->orWhereIn('city_id', $cityIdsForCme);
                }
            })->pluck('id')->toArray();

            // Base query for this CME
            $cmeBaseQuery = \App\Models\Complaint::where(function ($q) use ($cityIdsForCme, $sectorIdsForCme) {
                if (!empty($cityIdsForCme)) {
                    $q->whereIn('city_id', $cityIdsForCme);
                }
                if (!empty($sectorIdsForCme)) {
                    $method = !empty($cityIdsForCme) ? 'orWhereIn' : 'whereIn';
                    $q->{$method}('sector_id', $sectorIdsForCme);
                }
            });

            // Apply Global Filters (Category, Status) - consistent with other stats
            if ($category && $category !== 'all') {
                $cmeBaseQuery->where('category', $category);
            }
            if ($status && $status !== 'all') {
                $cmeBaseQuery->where('status', $status);
            }

            // Apply Date Filter (Specific or Global)
            if ($cmeDateRange) {
                $now = now();
                switch ($cmeDateRange) {
                    case 'yesterday':
                        $cmeBaseQuery->whereDate('created_at', $now->copy()->subDay()->toDateString());
                        break;
                    case 'today':
                        $cmeBaseQuery->whereDate('created_at', $now->toDateString());
                        break;
                    case 'this_week':
                        $cmeBaseQuery->whereBetween('created_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()]);
                        break;
                    case 'last_week':
                        $cmeBaseQuery->whereBetween('created_at', [$now->copy()->subWeek()->startOfWeek(), $now->copy()->subWeek()->endOfWeek()]);
                        break;
                    case 'this_month':
                        $cmeBaseQuery->whereMonth('created_at', $now->month)
                            ->whereYear('created_at', $now->year);
                        break;
                    case 'last_month':
                        $cmeBaseQuery->whereMonth('created_at', $now->copy()->subMonth()->month)
                            ->whereYear('created_at', $now->copy()->subMonth()->year);
                        break;
                    case 'last_6_months':
                        $cmeBaseQuery->where('created_at', '>=', $now->copy()->subMonths(6)->startOfDay());
                        break;
                    case 'this_year':
                        $cmeBaseQuery->whereYear('created_at', $now->year);
                        break;
                    case 'last_year':
                        $cmeBaseQuery->whereYear('created_at', $now->copy()->subYear()->year);
                        break;
                }
            }

            // Count total complaints
            $cmeGraphData[] = (clone $cmeBaseQuery)->count();

            // Count addressed (resolved + closed) complaints
            $cmeResolvedData[] = (clone $cmeBaseQuery)->whereIn('status', ['resolved', 'closed'])->count();
        }

        // If AJAX request, return JSON data
        if ($request->ajax()) {
            return response()->json([
                'stats' => $stats,
                'monthlyComplaints' => $monthlyComplaints,
                'monthLabels' => $monthLabels,
                'complaintsByStatus' => $complaintsByStatus,
                'resolvedVsEdData' => $resolvedVsEdData,
                'recentEdData' => $recentEdData,
                'cmeGraphLabels' => $cmeGraphLabels,
                'cmeGraphData' => $cmeGraphData,
            ]);
        }



        return view('frontend.dashboard', compact(
            'stats',
            'geGroups',
            'geNodes',
            'categories',
            'statuses',
            'monthlyComplaints',
            'monthLabels',
            'complaintsByStatus',
            'resolvedVsEdData',
            'recentEdData',
            'yearTdData',
            'cityId',
            'sectorId',
            'category',
            'status',
            'dateRange',
            'cmesList',
            'cmesId',
            'cmeGraphLabels',
            'cmeGraphData',
            'cmeResolvedData'
        ));
    }
}
