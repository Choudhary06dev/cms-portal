<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;

class DepartmentController extends Controller
{
    public function index()
    {
        if (!Schema::hasTable('departments')) {
            $departments = new LengthAwarePaginator([], 0, 15);
            return view('admin.department.index', compact('departments'))
                ->with('error', 'Run migrations to create departments table.');
        }

        $departments = Department::orderBy('name', 'asc')->paginate(15);
        return view('admin.department.index', compact('departments'));
    }

    public function store(Request $request)
    {
        if (!Schema::hasTable('departments')) {
            return back()->with('error', 'Run migrations to create departments table (php artisan migrate).');
        }
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:departments,name,NULL,id,status,active',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);
        Department::create($validated);
        return back()->with('success', 'Department created');
    }

    public function update(Request $request, $id)
    {
        if (!Schema::hasTable('departments')) {
            return back()->with('error', 'Run migrations to create departments table (php artisan migrate).');
        }
        
        try {
            $department = Department::findOrFail($id);
            
            $rules = [
                'name' => 'required|string|max:100',
                'description' => 'nullable|string',
                'status' => 'required|in:active,inactive',
            ];
            
            if ($request->name !== $department->name) {
                $exists = Department::where('name', $request->name)
                    ->where('status', 'active')
                    ->where('id', '!=', $id)
                    ->exists();
                
                if ($exists) {
                    return back()->withErrors(['name' => 'The name has already been taken.'])->withInput();
                }
            }
            
            $validated = $request->validate($rules);
            $department->update($validated);
            return back()->with('success', 'Department updated');
        } catch (\Exception $e) {
            Log::error('Department update error: ' . $e->getMessage());
            return back()->with('error', 'Error updating department: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        if (!Schema::hasTable('departments')) {
            return back()->with('error', 'Run migrations to create departments table (php artisan migrate).');
        }
        
        try {
            $department = Department::findOrFail($id);
            $department->update([
                'status' => 'inactive'
            ]);
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => true]);
            }
            return back()->with('success', 'Department removed from list');
        } catch (\Exception $e) {
            Log::error('Department delete error: ' . $e->getMessage());
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
            }
            return back()->with('error', 'Error deleting department: ' . $e->getMessage());
        }
    }
}

