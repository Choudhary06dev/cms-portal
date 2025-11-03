<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;

class CityController extends Controller
{
    public function index()
    {
        if (!Schema::hasTable('cities')) {
            $cities = new LengthAwarePaginator([], 0, 15);
            return view('admin.city.index', compact('cities'))
                ->with('error', 'Run migrations to create cities table.');
        }

        // Show all cities; status column indicates active/inactive
        $cities = City::orderBy('id', 'desc')->paginate(15);
        return view('admin.city.index', compact('cities'));
    }

    public function store(Request $request)
    {
        if (!Schema::hasTable('cities')) {
            return back()->with('error', 'Run migrations to create cities table (php artisan migrate).');
        }
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:cities,name,NULL,id,status,active',
            'province' => 'nullable|string|max:100|in:Sindh,Punjab,KPK,Balochistan,Federal,Azad Kashmir',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);
        City::create($validated);
        return back()->with('success', 'City created');
    }

    public function update(Request $request, $id)
    {
        if (!Schema::hasTable('cities')) {
            return back()->with('error', 'Run migrations to create cities table (php artisan migrate).');
        }
        
        try {
            $city = City::findOrFail($id);
            
            $rules = [
                'name' => 'required|string|max:100',
                'province' => 'nullable|string|max:100|in:Sindh,Punjab,KPK,Balochistan,Federal,Azad Kashmir',
                'description' => 'nullable|string',
                'status' => 'required|in:active,inactive',
            ];
            
            // Only validate uniqueness if name changed and check against active cities only
            if ($request->name !== $city->name) {
                $exists = City::where('name', $request->name)
                    ->where('status', 'active')
                    ->where('id', '!=', $id)
                    ->exists();
                
                if ($exists) {
                    return back()->withErrors(['name' => 'The name has already been taken.'])->withInput();
                }
            }
            
            $validated = $request->validate($rules);
            $city->update($validated);
            return back()->with('success', 'City updated');
        } catch (\Exception $e) {
            Log::error('City update error: ' . $e->getMessage());
            return back()->with('error', 'Error updating city: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        if (!Schema::hasTable('cities')) {
            return back()->with('error', 'Run migrations to create cities table (php artisan migrate).');
        }
        
        try {
            $city = City::findOrFail($id);
            // Soft delete without migration: mark as inactive
            $city->update([
                'status' => 'inactive'
            ]);
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => true]);
            }
            return back()->with('success', 'City removed from list');
        } catch (\Exception $e) {
            Log::error('City delete error: ' . $e->getMessage());
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
            }
            return back()->with('error', 'Error deleting city: ' . $e->getMessage());
        }
    }
}
