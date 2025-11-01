<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Spare;
use App\Models\SpareStockLog;
use App\Models\ComplaintSpare;
use App\Models\ComplaintCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;

class SpareController extends Controller
{
    public function __construct()
    {
        // Middleware is applied in routes
    }

    /**
     * Display a listing of spare parts
     */
    public function index(Request $request)
    {
        $query = Spare::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('item_name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        // Filter by stock status
        if ($request->has('stock_status') && $request->stock_status) {
            switch ($request->stock_status) {
                case 'in_stock':
                    $query->inStock();
                    break;
                case 'low_stock':
                    $query->lowStock();
                    break;
                case 'out_of_stock':
                    $query->outOfStock();
                    break;
            }
        }

        // Filter by price range
        if ($request->has('price_from') && $request->price_from) {
            $query->where('unit_price', '>=', $request->price_from);
        }

        if ($request->has('price_to') && $request->price_to) {
            $query->where('unit_price', '<=', $request->price_to);
        }

        $spares = $query->with('stockLogs')->orderBy('id', 'desc')->paginate(15);
        $categories = Schema::hasTable('complaint_categories')
            ? ComplaintCategory::where('status', 'active')->orderBy('name')->pluck('name')
            : collect();

        return view('admin.spares.index', compact('spares', 'categories'));
    }

    /**
     * Show the form for creating a new spare part
     */
    public function create()
    {
        $categories = Schema::hasTable('complaint_categories')
            ? ComplaintCategory::where('status', 'active')->orderBy('name')->pluck('name')
            : collect();
        return view('admin.spares.create', compact('categories'));
    }

    /**
     * Store a newly created spare part
     */
    public function store(Request $request)
    {
        // Get categories from ComplaintCategory table
        $categories = Schema::hasTable('complaint_categories')
            ? ComplaintCategory::where('status', 'active')->orderBy('name')->pluck('name')->toArray()
            : [];
        $categoryKeys = implode(',', array_keys(Spare::getCategories()));
        $dbCategories = implode(',', Spare::getCanonicalCategories());
        $allowedCategories = implode(',', array_merge($categories, array_keys(Spare::getCategories()), Spare::getCanonicalCategories()));
        
        $validator = Validator::make($request->all(), [
            'item_name' => 'required|string|max:150',
            'product_code' => 'nullable|string|max:50',
            'brand_name' => 'nullable|string|max:100',
            // Accept categories from ComplaintCategory table and legacy categories
            'category' => 'required|string',
            'unit_price' => 'nullable|numeric|min:0',
            'total_received_quantity' => 'nullable|integer|min:0',
            'issued_quantity' => 'nullable|integer|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
            'threshold_level' => 'nullable|integer|min:0',
            'supplier' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'last_stock_in_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ]);
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Validate category is from allowed list
        if (!in_array($request->category, array_merge($categories, array_keys(Spare::getCategories()), Spare::getCanonicalCategories()))) {
            return redirect()->back()
                ->withErrors(['category' => 'The selected category is invalid.'])
                ->withInput();
        }

        // Use category as is (from ComplaintCategory table)
        // Since category is now a string column, we can save any value directly
        $normalizedCategory = $request->category;

        $spare = Spare::create([
            'item_name' => $request->item_name,
            'product_code' => $request->product_code,
            'brand_name' => $request->brand_name,
            'category' => $request->category,
            'unit_price' => $request->unit_price,
            'total_received_quantity' => (int)($request->total_received_quantity ?? $request->stock_quantity ?? 0),
            'issued_quantity' => (int)($request->issued_quantity ?? 0),
            'stock_quantity' => (int)($request->stock_quantity ?? 0),
            'threshold_level' => (int)($request->threshold_level ?? 0),
            'supplier' => $request->supplier,
            'description' => $request->description,
            'last_stock_in_at' => $request->last_stock_in_at,
        ]);

        // Log initial stock
        if ($request->stock_quantity > 0) {
            SpareStockLog::create([
                'spare_id' => $spare->id,
                'change_type' => 'in',
                'quantity' => $request->stock_quantity,
                'remarks' => 'Initial stock',
            ]);
        }

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Spare part created successfully.',
                'spare' => $spare
            ]);
        }

        return redirect()->route('admin.spares.index')
            ->with('success', 'Spare part created successfully.');
    }

    /**
     * Display the specified spare part
     */
    public function show(Spare $spare)
    {
        if (request()->ajax()) {
            // Return JSON for modal
        return response()->json([
            'id' => $spare->id,
            'name' => $spare->item_name,
            'product_code' => $spare->product_code,
            'brand_name' => $spare->brand_name,
            'category' => $spare->category,
            'price' => $spare->unit_price,
            'total_received_quantity' => $spare->total_received_quantity,
            'issued_quantity' => $spare->issued_quantity,
            'stock_quantity' => $spare->stock_quantity,
            'threshold_level' => $spare->threshold_level,
            'supplier' => $spare->supplier,
            'description' => $spare->description,
            'last_stock_in_at' => $spare->last_stock_in_at ? $spare->last_stock_in_at->format('M d, Y H:i') : 'N/A',
            'updated_at' => $spare->updated_at ? $spare->updated_at->format('M d, Y H:i') : 'N/A',
            'status' => $spare->stock_quantity > 0 ? 'in_stock' : 'out_of_stock',
            'stock_status' => $spare->stock_quantity <= $spare->threshold_level ? 'low_stock' : 'normal',
        ]);
        }

        $spare->load(['complaintSpares.complaint.client', 'approvalItems.performa']);

        return view('admin.spares.show', compact('spare'));
    }

    /**
     * Print spare part slip
     */
    public function printSlip(Spare $spare)
    {
        $spare->load(['stockLogs', 'complaintSpares.complaint.client', 'approvalItems.performa']);
        
        return view('admin.spares.print-slip', compact('spare'));
    }

    /**
     * Show the form for editing the spare part
     */
    public function edit(Spare $spare)
    {
        $categories = Schema::hasTable('complaint_categories')
            ? ComplaintCategory::where('status', 'active')->orderBy('name')->pluck('name')
            : collect();
        return view('admin.spares.edit', compact('spare', 'categories'));
    }

    /**
     * Get spare data for editing (AJAX)
     */
    public function editData(Spare $spare)
    {
        return response()->json([
            'id' => $spare->id,
            'name' => $spare->item_name,
            'product_code' => $spare->product_code,
            'brand_name' => $spare->brand_name,
            'category' => $spare->category,
            'price' => $spare->unit_price,
            'total_received_quantity' => $spare->total_received_quantity,
            'issued_quantity' => $spare->issued_quantity,
            'stock_quantity' => $spare->stock_quantity,
            'threshold_level' => $spare->threshold_level,
            'supplier' => $spare->supplier ?? '',
            'description' => $spare->description ?? '',
            'last_stock_in_at' => $spare->last_stock_in_at,
            'status' => $spare->stock_quantity > 0 ? 'active' : 'inactive',
        ]);
    }

    /**
     * Update the specified spare part
     */
    public function update(Request $request, Spare $spare)
    {
        // Get categories from ComplaintCategory table
        $categories = Schema::hasTable('complaint_categories')
            ? ComplaintCategory::where('status', 'active')->orderBy('name')->pluck('name')->toArray()
            : [];
        $categoryKeys = implode(',', array_keys(Spare::getCategories()));
        $dbCategories = implode(',', Spare::getCanonicalCategories());
        
        $validator = Validator::make($request->all(), [
            'item_name' => 'required|string|max:150',
            'product_code' => 'nullable|string|max:50',
            'brand_name' => 'nullable|string|max:100',
            // Accept categories from ComplaintCategory table and legacy categories
            'category' => 'required|string',
            'unit_price' => 'nullable|numeric|min:0',
            'total_received_quantity' => 'nullable|integer|min:0',
            'issued_quantity' => 'nullable|integer|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'threshold_level' => 'required|integer|min:0',
            'supplier' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'last_stock_in_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ]);
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Validate category is from allowed list
        $allAllowedCategories = array_merge($categories, array_keys(Spare::getCategories()), Spare::getCanonicalCategories());
        if (!in_array($request->category, $allAllowedCategories)) {
            return redirect()->back()
                ->withErrors(['category' => 'The selected category is invalid.'])
                ->withInput();
        }

        // Use category as is (from ComplaintCategory table)
        // Since category is now a string column, we can save any value directly
        $normalizedCategory = $request->category;

        // Compute safe values
        $newTotalReceived = $request->has('total_received_quantity')
            ? (int) $request->total_received_quantity
            : $spare->total_received_quantity;

        $newIssued = $request->has('issued_quantity')
            ? (int) $request->issued_quantity
            : $spare->issued_quantity;

        // If stock_quantity not explicitly provided but totals changed, auto-balance
        $newStock = $request->has('stock_quantity')
            ? (int) $request->stock_quantity
            : (($request->has('total_received_quantity') || $request->has('issued_quantity'))
                ? max($newTotalReceived - $newIssued, 0)
                : $spare->stock_quantity);

        $spare->update([
            'item_name' => $request->item_name,
            'product_code' => $request->product_code,
            'brand_name' => $request->brand_name,
            'category' => $normalizedCategory,
            'unit_price' => $request->unit_price,
            'total_received_quantity' => $newTotalReceived,
            'issued_quantity' => $newIssued,
            'stock_quantity' => $newStock,
            'threshold_level' => $request->has('threshold_level') ? (int) $request->threshold_level : $spare->threshold_level,
            'supplier' => $request->supplier,
            'description' => $request->description,
            'last_stock_in_at' => $request->last_stock_in_at,
        ]);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Spare part updated successfully.',
                'spare' => $spare
            ]);
        }

        return redirect()->route('admin.spares.index')
            ->with('success', 'Spare part updated successfully.');
    }

    /**
     * Remove the specified spare part
     */
    public function destroy(Spare $spare)
    {
        // Soft delete - no need to manually delete related records as soft delete preserves them
        try {
            $spare->delete(); // This will now soft delete due to SoftDeletes trait

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Spare part deleted successfully.'
                ]);
            }

            return redirect()->route('admin.spares.index')
                ->with('success', 'Spare part deleted successfully.');
        } catch (\Throwable $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete spare part. Please try again.',
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to delete spare part. Please try again.');
        }
    }

    /**
     * Add stock to spare part
     */
    public function addStock(Request $request, Spare $spare)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1',
            'remarks' => 'nullable|string',
            'reference_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        $spare->addStock(
            $request->quantity,
            $request->remarks,
            $request->reference_id
        );

        return redirect()->back()
            ->with('success', 'Stock added successfully.');
    }

    /**
     * Remove stock from spare part
     */
    public function removeStock(Request $request, Spare $spare)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1',
            'remarks' => 'nullable|string',
            'reference_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        if (!$spare->isStockSufficient($request->quantity)) {
            return redirect()->back()
                ->with('error', 'Insufficient stock available.');
        }

        $spare->removeStock(
            $request->quantity,
            $request->remarks,
            $request->reference_id
        );

        return redirect()->back()
            ->with('success', 'Stock removed successfully.');
    }

    /**
     * Get low stock items
     */
    public function getLowStock()
    {
        $lowStockItems = Spare::lowStock()
            ->orderBy('stock_quantity', 'asc')
            ->get();

        return response()->json($lowStockItems);
    }

    /**
     * Get out of stock items
     */
    public function getOutOfStock()
    {
        $outOfStockItems = Spare::outOfStock()
            ->orderBy('item_name')
            ->get();

        return response()->json($outOfStockItems);
    }

    /**
     * Get stock alerts
     */
    public function getStockAlerts()
    {
        $alerts = [
            'low_stock' => Spare::lowStock()->count(),
            'out_of_stock' => Spare::outOfStock()->count(),
            'items' => Spare::lowStock()
                ->orWhere('stock_quantity', '<=', 0)
                ->orderBy('stock_quantity', 'asc')
                ->limit(10)
                ->get()
        ];

        return response()->json($alerts);
    }

    /**
     * Get stock movement chart data
     */
    public function getStockMovementChart(Spare $spare, Request $request)
    {
        $days = $request->get('days', 30);

        $data = $spare->stockLogs()
            ->where('created_at', '>=', now()->subDays($days))
            ->selectRaw('DATE(created_at) as date, change_type, SUM(quantity) as total_quantity')
            ->groupBy('date', 'change_type')
            ->orderBy('date')
            ->get();

        return response()->json($data);
    }

    /**
     * Get usage statistics
     */
    public function getUsageStatistics(Spare $spare, Request $request)
    {
        $period = $request->get('period', '30'); // days

        $usage = $spare->complaintSpares()
            ->where('used_at', '>=', now()->subDays($period))
            ->selectRaw('DATE(used_at) as date, SUM(quantity) as total_quantity, SUM(quantity * (SELECT unit_price FROM spares WHERE id = complaint_spares.spare_id)) as total_cost')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json($usage);
    }

    /**
     * Get top used spares
     */
    public function getTopUsedSpares(Request $request)
    {
        $period = $request->get('period', '30'); // days

        $topUsed = Spare::join('complaint_spares', 'spares.id', '=', 'complaint_spares.spare_id')
            ->where('complaint_spares.used_at', '>=', now()->subDays($period))
            ->selectRaw('spares.*, SUM(complaint_spares.quantity) as total_used, SUM(complaint_spares.quantity * spares.unit_price) as total_cost')
            ->groupBy('spares.id')
            ->orderBy('total_used', 'desc')
            ->limit(10)
            ->get();

        return response()->json($topUsed);
    }

    /**
     * Get category-wise statistics
     */
    public function getCategoryStatistics()
    {
        $stats = Spare::selectRaw('category, COUNT(*) as count, SUM(stock_quantity) as total_stock, SUM(stock_quantity * unit_price) as total_value')
            ->groupBy('category')
            ->get();

        return response()->json($stats);
    }

    /**
     * Bulk actions on spares
     */
    public function bulkAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:add_stock,remove_stock,change_category,change_threshold,delete',
            'spare_ids' => 'required|array|min:1',
            'spare_ids.*' => 'exists:spares,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        $spareIds = $request->spare_ids;
        $action = $request->action;

        switch ($action) {
            case 'add_stock':
                $validator = Validator::make($request->all(), [
                    'quantity' => 'required|integer|min:1',
                    'remarks' => 'nullable|string',
                ]);
                
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator);
                }
                
                Spare::whereIn('id', $spareIds)->get()->each(function($spare) use ($request) {
                    $spare->addStock($request->quantity, $request->remarks);
                });
                $message = 'Stock added to selected spare parts successfully.';
                break;

            case 'remove_stock':
                $validator = Validator::make($request->all(), [
                    'quantity' => 'required|integer|min:1',
                    'remarks' => 'nullable|string',
                ]);
                
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator);
                }
                
                $spares = Spare::whereIn('id', $spareIds)->get();
                foreach ($spares as $spare) {
                    if ($spare->isStockSufficient($request->quantity)) {
                        $spare->removeStock($request->quantity, $request->remarks);
                    }
                }
                $message = 'Stock removed from selected spare parts successfully.';
                break;

            case 'change_category':
                $validCategories = Schema::hasTable('complaint_categories')
                    ? ComplaintCategory::where('status', 'active')->pluck('name')->toArray()
                    : [];
                $validator = Validator::make($request->all(), [
                    'category' => 'required|string|max:100' . (!empty($validCategories) ? '|in:' . implode(',', $validCategories) : ''),
                ]);
                
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator);
                }
                
                Spare::whereIn('id', $spareIds)->update(['category' => $request->category]);
                $message = 'Category changed for selected spare parts successfully.';
                break;

            case 'change_threshold':
                $validator = Validator::make($request->all(), [
                    'threshold_level' => 'required|integer|min:0',
                ]);
                
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator);
                }
                
                Spare::whereIn('id', $spareIds)->update(['threshold_level' => $request->threshold_level]);
                $message = 'Threshold level changed for selected spare parts successfully.';
                break;

            case 'delete':
                // Check for related records
                $sparesWithRecords = Spare::whereIn('id', $spareIds)
                    ->where(function($q) {
                        $q->whereHas('complaintSpares')
                          ->orWhereHas('approvalItems');
                    })
                    ->count();

                if ($sparesWithRecords > 0) {
                    return redirect()->back()
                        ->with('error', 'Some spare parts cannot be deleted due to existing usage records.');
                }

                Spare::whereIn('id', $spareIds)->delete();
                $message = 'Selected spare parts deleted successfully.';
                break;
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Export spares data
     */
    public function export(Request $request)
    {
        $query = Spare::query();

        // Apply same filters as index
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('item_name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        $spares = $query->get();

        // Implementation for export
        return response()->json(['message' => 'Export functionality not implemented yet']);
    }
}
