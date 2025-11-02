<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\City;
use App\Models\Sector;
use App\Traits\DatabaseTimeHelpers;
use App\Traits\LocationFilterTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;

class ClientController extends Controller
{
    use DatabaseTimeHelpers, LocationFilterTrait;
    public function __construct()
    {
        // Middleware is applied in routes
    }

    /**
     * Display a listing of clients
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Client::withCount('complaints');

        // Apply location-based filtering
        $this->filterClientsByLocation($query, $user);

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('client_name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by city
        if ($request->has('city') && $request->city) {
            $query->where('city', $request->city);
        }


        $clients = $query->orderBy('id', 'desc')->paginate(15);
        
        $cities = Schema::hasTable('cities')
            ? City::where('status', 'active')->orderBy('name')->get()
            : collect();
        
        $sectors = Schema::hasTable('sectors')
            ? Sector::where('status', 'active')->orderBy('name')->pluck('name')
            : collect();

        return view('admin.clients.index', compact('clients', 'cities', 'sectors'));
    }

    /**
     * Show the form for creating a new client
     */
    public function create()
    {
        // Clear any old input data to ensure clean form
        request()->session()->forget('_old_input');
        
        $cities = Schema::hasTable('cities')
            ? City::where('status', 'active')->orderBy('name')->get()
            : collect();
        
        $sectors = Schema::hasTable('sectors')
            ? Sector::where('status', 'active')->orderBy('name')->pluck('name', 'name')
            : collect();
        
        return view('admin.clients.create', compact('cities', 'sectors'));
    }

    /**
     * Store a newly created client
     */
    public function store(Request $request)
    {
        $cityRule = 'required|string|max:50';
        if (Schema::hasTable('cities')) {
            $cityRule .= '|exists:cities,name';
        }
        
        $validator = Validator::make($request->all(), [
            'client_name' => 'required|string|max:100|unique:clients',
            'contact_person' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:100',
            'phone' => 'nullable|string|max:20',
            'address' => 'required|string|max:255',
            'city' => $cityRule,
            'sector' => 'required|string|max:100' . (Schema::hasTable('sectors') ? '|exists:sectors,name' : ''),
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $client = Client::create($request->all());

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Client created successfully.',
                'client' => $client
            ]);
        }

        return redirect()->route('admin.clients.index')
            ->with('success', 'Client created successfully.');
    }

    /**
     * Display the specified client
     */
    public function show(Client $client)
    {
        $client->load(['complaints.assignedEmployee', 'complaints.attachments']);

        // Get recent complaints
        $recentComplaints = $client->complaints()
            ->with(['assignedEmployee', 'attachments'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'client' => $client,
                'recentComplaints' => $recentComplaints
            ]);
        }

        return view('admin.clients.show', compact('client', 'recentComplaints'));
    }

    /**
     * Show the form for editing the client
     */
    public function edit(Client $client)
    {
        $cities = Schema::hasTable('cities')
            ? City::where('status', 'active')->orderBy('name')->get()
            : collect();
        
        $sectors = Schema::hasTable('sectors')
            ? Sector::where('status', 'active')->orderBy('name')->pluck('name', 'name')
            : collect();
        
        return view('admin.clients.edit', compact('client', 'cities', 'sectors'));
    }

    /**
     * Update the specified client
     */
    public function update(Request $request, Client $client)
    {
        $cityRule = 'required|string|max:50';
        if (Schema::hasTable('cities')) {
            $cityRule .= '|exists:cities,name';
        }
        
        $validator = Validator::make($request->all(), [
            'client_name' => 'required|string|max:100|unique:clients,client_name,' . $client->id,
            'contact_person' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:100',
            'phone' => 'nullable|string|max:20',
            'address' => 'required|string|max:255',
            'city' => $cityRule,
            'sector' => 'required|string|max:100' . (Schema::hasTable('sectors') ? '|exists:sectors,name' : ''),
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $client->update($request->all());

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Client updated successfully.',
                'client' => $client
            ]);
        }

        return redirect()->route('admin.clients.index')
            ->with('success', 'Client updated successfully.');
    }

    /**
     * Get sectors by city (AJAX)
     */
    public function getSectorsByCity(Request $request)
    {
        if (!Schema::hasTable('sectors')) {
            return response()->json(['sectors' => []]);
        }

        $cityId = $request->input('city_id');
        
        // If city_name is provided instead of city_id, find the city
        if ($request->has('city_name') && $request->city_name && !$cityId) {
            $city = City::where('name', $request->city_name)->first();
            if ($city) {
                $cityId = $city->id;
            }
        }

        // Convert to integer if it's a string
        if ($cityId) {
            $cityId = (int) $cityId;
        }

        if (!$cityId || $cityId <= 0) {
            Log::info('No valid city ID provided', [
                'city_id' => $request->input('city_id'),
                'city_name' => $request->input('city_name'),
                'all_request' => $request->all()
            ]);
            return response()->json(['sectors' => []]);
        }

        // Check if city exists
        $city = City::find($cityId);
        if (!$city) {
            Log::warning('City not found', ['city_id' => $cityId]);
            return response()->json(['sectors' => []]);
        }

        // Filter sectors by city_id - return sector names
        $sectors = Sector::where('city_id', '=', $cityId)
            ->where('status', '=', 'active')
            ->orderBy('name')
            ->get(['id', 'name']);
        
        Log::info('Sectors fetched for client', [
            'requested_city_id' => $cityId,
            'city_name' => $city->name,
            'filtered_sectors_count' => $sectors->count()
        ]);
        
        return response()->json(['sectors' => $sectors]);
    }

    /**
     * Remove the specified client
     */
    public function destroy(Client $client)
    {
        // Soft delete - no need to check for related records as soft delete preserves them
        $client->delete(); // This will now soft delete due to SoftDeletes trait

        return redirect()->route('admin.clients.index')
            ->with('success', 'Client deleted successfully.');
    }

    /**
     * Toggle client status
     */
    public function toggleStatus(Client $client)
    {
        $client->update([
            'status' => $client->status === 'active' ? 'inactive' : 'active'
        ]);

        $status = $client->status === 'active' ? 'activated' : 'deactivated';
        
        return redirect()->back()
            ->with('success', "Client {$status} successfully.");
    }

    /**
     * Get client statistics
     */
    public function getStatistics(Request $request)
    {
        $period = $request->get('period', '30'); // days

        $stats = [
            'total' => Client::count(),
            'active' => Client::where('status', 'active')->count(),
            'inactive' => Client::where('status', 'inactive')->count(),
            'with_complaints' => Client::whereHas('complaints')->count(),
            'new_clients' => Client::where('created_at', '>=', now()->subDays($period))->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Get client chart data
     */
    public function getChartData(Request $request)
    {
        $period = $request->get('period', '30'); // days

        $data = Client::where('created_at', '>=', now()->subDays($period))
            ->selectRaw('client_type, COUNT(*) as count')
            ->groupBy('client_type')
            ->get();

        return response()->json($data);
    }

    /**
     * Get top clients by complaints
     */
    public function getTopClients(Request $request)
    {
        $period = $request->get('period', '30'); // days

        $topClients = Client::withCount(['complaints' => function($query) use ($period) {
            $query->where('created_at', '>=', now()->subDays($period));
        }])
        ->orderBy('complaints_count', 'desc')
        ->limit(10)
        ->get();

        return response()->json($topClients);
    }

    /**
     * Get client complaints
     */
    public function getComplaints(Client $client, Request $request)
    {
        $query = $client->complaints()->with(['assignedEmployee', 'attachments']);

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->has('complaint_type') && $request->complaint_type) {
            $query->where('category', $request->complaint_type);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $complaints = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json($complaints);
    }

    /**
     * Get client performance metrics
     */
    public function getPerformanceMetrics(Client $client, Request $request)
    {
        $period = $request->get('period', '30'); // days

        $metrics = [
            'total_complaints' => $client->complaints()->where('created_at', '>=', now()->subDays($period))->count(),
            'resolved_complaints' => $client->complaints()->where('created_at', '>=', now()->subDays($period))->whereIn('status', ['resolved', 'closed'])->count(),
            'pending_complaints' => $client->complaints()->where('created_at', '>=', now()->subDays($period))->whereIn('status', ['new', 'assigned', 'in_progress'])->count(),
            'overdue_complaints' => $client->complaints()->where('created_at', '>=', now()->subDays($period))->overdue()->count(),
            'avg_resolution_time' => $client->complaints()
                ->where('created_at', '>=', now()->subDays($period))
                ->whereIn('status', ['resolved', 'closed'])
                ->selectRaw('AVG(' . $this->getTimeDiffInHours('created_at', 'updated_at') . ') as avg_time')
                ->value('avg_time'),
        ];

        return response()->json($metrics);
    }

    /**
     * Bulk actions on clients
     */
    public function bulkAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:activate,deactivate,delete,change_type',
            'client_ids' => 'required|array|min:1',
            'client_ids.*' => 'exists:clients,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        $clientIds = $request->client_ids;
        $action = $request->action;

        switch ($action) {
            case 'activate':
                Client::whereIn('id', $clientIds)->update(['status' => 'active']);
                $message = 'Selected clients activated successfully.';
                break;

            case 'deactivate':
                Client::whereIn('id', $clientIds)->update(['status' => 'inactive']);
                $message = 'Selected clients deactivated successfully.';
                break;

            case 'change_type':
                $validator = Validator::make($request->all(), [
                    'client_type' => 'required|in:individual,company,government',
                ]);
                
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator);
                }
                
                Client::whereIn('id', $clientIds)->update(['client_type' => $request->client_type]);
                $message = 'Selected clients type updated successfully.';
                break;

            case 'delete':
                // Check for complaints
                $clientsWithComplaints = Client::whereIn('id', $clientIds)
                    ->whereHas('complaints')
                    ->count();

                if ($clientsWithComplaints > 0) {
                    return redirect()->back()
                        ->with('error', 'Some clients cannot be deleted due to existing complaints.');
                }

                Client::whereIn('id', $clientIds)->delete();
                $message = 'Selected clients deleted successfully.';
                break;
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Export clients data
     */
    public function export(Request $request)
    {
        $query = Client::withCount('complaints');

        // Apply same filters as index
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('client_name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('client_type') && $request->client_type) {
            $query->where('client_type', $request->client_type);
        }

        $clients = $query->get();

        // Implementation for export
        return response()->json(['message' => 'Export functionality not implemented yet']);
    }
}