<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Complaint;
use App\Traits\DatabaseTimeHelpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{
    use DatabaseTimeHelpers;
    public function __construct()
    {
        // Middleware is applied in routes
    }

    /**
     * Display a listing of clients
     */
    public function index(Request $request)
    {
        $query = Client::withCount('complaints');

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('client_name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('state', 'like', "%{$search}%");
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

        // Filter by state
        if ($request->has('state') && $request->state) {
            $query->where('state', $request->state);
        }

        $clients = $query->orderBy('client_name')->paginate(15);

        return view('admin.clients.index', compact('clients'));
    }

    /**
     * Show the form for creating a new client
     */
    public function create()
    {
        // Clear any old input data to ensure clean form
        request()->session()->forget('_old_input');
        
        return view('admin.clients.create');
    }

    /**
     * Store a newly created client
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_name' => 'required|string|max:100|unique:clients',
            'contact_person' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:100',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:50',
            'state' => 'nullable|string|max:50|in:sindh,punjab,kpk,balochistan,other',
            // Pincode must be exactly 4 digits if provided
            'pincode' => 'nullable|digits:4',
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
        $client->load(['complaints.assignedEmployee.user', 'complaints.attachments']);
        
        // Get client statistics
        $stats = [
            'total_complaints' => $client->complaints()->count(),
            'pending_complaints' => $client->complaints()->whereIn('status', ['new', 'assigned', 'in_progress'])->count(),
            'resolved_complaints' => $client->complaints()->whereIn('status', ['resolved', 'closed'])->count(),
            'overdue_complaints' => $client->complaints()->overdue()->count(),
        ];

        // Get recent complaints
        $recentComplaints = $client->complaints()
            ->with(['assignedEmployee.user', 'attachments'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get complaints by category
        $complaintsByType = $client->complaints()
            ->selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->get();

        // Get complaints by status
        $complaintsByStatus = $client->complaints()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'client' => $client,
                'stats' => $stats,
                'recentComplaints' => $recentComplaints,
                'complaintsByType' => $complaintsByType,
                'complaintsByStatus' => $complaintsByStatus
            ]);
        }

        return view('admin.clients.show', compact('client', 'stats', 'recentComplaints', 'complaintsByType', 'complaintsByStatus'));
    }

    /**
     * Show the form for editing the client
     */
    public function edit(Client $client)
    {
        return view('admin.clients.edit', compact('client'));
    }

    /**
     * Update the specified client
     */
    public function update(Request $request, Client $client)
    {
        $validator = Validator::make($request->all(), [
            'client_name' => 'required|string|max:100|unique:clients,client_name,' . $client->id,
            'contact_person' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:100',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:50',
            'state' => 'nullable|string|max:50|in:sindh,punjab,kpk,balochistan,other',
            // Pincode must be exactly 4 digits if provided
            'pincode' => 'nullable|digits:4',
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
     * Remove the specified client
     */
    public function destroy(Client $client)
    {
        // Check if client has complaints
        if ($client->complaints()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete client with existing complaints.');
        }

        $client->delete();

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
        $query = $client->complaints()->with(['assignedEmployee.user', 'attachments']);

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