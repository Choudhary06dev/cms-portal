<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ComplaintCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryController extends Controller
{
    public function index()
    {
        if (!Schema::hasTable('complaint_categories')) {
            $categories = new LengthAwarePaginator([], 0, 15);
            return view('admin.category.index', compact('categories'))
                ->with('error', 'Run migrations to create complaint_categories table.');
        }

        // Show all categories; status column indicates active/inactive
        $categories = ComplaintCategory::orderBy('name', 'asc')->paginate(15);
        return view('admin.category.index', compact('categories'));
    }

    public function store(Request $request)
    {
        if (!Schema::hasTable('complaint_categories')) {
            return back()->with('error', 'Run migrations to create complaint_categories table (php artisan migrate).');
        }
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:complaint_categories,name,NULL,id,status,active',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);
        ComplaintCategory::create($validated);
        return back()->with('success', 'Category created');
    }

    public function update(Request $request, $id)
    {
        if (!Schema::hasTable('complaint_categories')) {
            return back()->with('error', 'Run migrations to create complaint_categories table (php artisan migrate).');
        }
        
        try {
            $complaint_category = ComplaintCategory::findOrFail($id);
            
            $rules = [
                'name' => 'required|string|max:100',
                'description' => 'nullable|string',
                'status' => 'required|in:active,inactive',
            ];
            
            // Only validate uniqueness if name changed and check against active categories only
            if ($request->name !== $complaint_category->name) {
                $exists = ComplaintCategory::where('name', $request->name)
                    ->where('status', 'active')
                    ->where('id', '!=', $id)
                    ->exists();
                
                if ($exists) {
                    return back()->withErrors(['name' => 'The name has already been taken.'])->withInput();
                }
            }
            
            $validated = $request->validate($rules);
            $complaint_category->update($validated);
            return back()->with('success', 'Category updated');
        } catch (\Exception $e) {
            Log::error('Category update error: ' . $e->getMessage());
            return back()->with('error', 'Error updating category: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        if (!Schema::hasTable('complaint_categories')) {
            return back()->with('error', 'Run migrations to create complaint_categories table (php artisan migrate).');
        }
        
        try {
            $complaint_category = ComplaintCategory::findOrFail($id);
            // Soft delete without migration: mark as inactive
            $complaint_category->update([
                'status' => 'inactive'
            ]);
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => true]);
            }
            return back()->with('success', 'Category removed from list');
        } catch (\Exception $e) {
            Log::error('Category delete error: ' . $e->getMessage());
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
            }
            return back()->with('error', 'Error deleting category: ' . $e->getMessage());
        }
    }
}


