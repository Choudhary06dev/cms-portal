<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Designation;
use App\Models\ComplaintCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;

class DesignationController extends Controller
{
    public function index()
    {
        if (!Schema::hasTable('designations')) {
            $designations = new LengthAwarePaginator([], 0, 15);
            return view('admin.designation.index', compact('designations'))
                ->with('error', 'Run migrations to create designations table.');
        }

        $designations = Designation::orderBy('name', 'asc')->paginate(15);
        $categories = Schema::hasTable('complaint_categories')
            ? ComplaintCategory::orderBy('name')->pluck('name')
            : collect();
        
        return view('admin.designation.index', compact('designations', 'categories'));
    }

    public function store(Request $request)
    {
        if (!Schema::hasTable('designations')) {
            return back()->with('error', 'Run migrations to create designations table (php artisan migrate).');
        }
        $validated = $request->validate([
            'category' => 'required|string|max:100',
            'name' => [
                'required',
                'string',
                'max:100',
                function ($attribute, $value, $fail) use ($request) {
                    $exists = Designation::where('name', $value)
                        ->where('category', $request->category)
                        ->where('status', 'active')
                        ->exists();
                    if ($exists) {
                        $fail('The designation name has already been taken for this category.');
                    }
                }
            ],
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);
        Designation::create($validated);
        return back()->with('success', 'Designation created');
    }

    public function update(Request $request, $id)
    {
        if (!Schema::hasTable('designations')) {
            return back()->with('error', 'Run migrations to create designations table (php artisan migrate).');
        }
        
        try {
            $designation = Designation::findOrFail($id);
            
            $rules = [
                'category' => 'required|string|max:100',
                'name' => [
                    'required',
                    'string',
                    'max:100',
                    function ($attribute, $value, $fail) use ($request, $id) {
                        $exists = Designation::where('name', $value)
                            ->where('category', $request->category)
                            ->where('status', 'active')
                            ->where('id', '!=', $id)
                            ->exists();
                        if ($exists) {
                            $fail('The designation name has already been taken for this category.');
                        }
                    }
                ],
                'description' => 'nullable|string',
                'status' => 'required|in:active,inactive',
            ];
            
            $validated = $request->validate($rules);
            $designation->update($validated);
            return back()->with('success', 'Designation updated');
        } catch (\Exception $e) {
            Log::error('Designation update error: ' . $e->getMessage());
            return back()->with('error', 'Error updating designation: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        if (!Schema::hasTable('designations')) {
            return back()->with('error', 'Run migrations to create designations table (php artisan migrate).');
        }
        
        try {
            $designation = Designation::findOrFail($id);
            $designation->update([
                'status' => 'inactive'
            ]);
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => true]);
            }
            return back()->with('success', 'Designation removed from list');
        } catch (\Exception $e) {
            Log::error('Designation delete error: ' . $e->getMessage());
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
            }
            return back()->with('error', 'Error deleting designation: ' . $e->getMessage());
        }
    }
}

