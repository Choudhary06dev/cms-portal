<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sector;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;

class SectorController extends Controller
{
    public function index()
    {
        if (!Schema::hasTable('sectors')) {
            $sectors = new LengthAwarePaginator([], 0, 15);
            $cities = collect();
            return view('admin.sector.index', compact('sectors', 'cities'))
                ->with('error', 'Run migrations to create sectors table.');
        }

        // Show all sectors; status column indicates active/inactive
        $sectors = Sector::with('city')->orderBy('name', 'asc')->paginate(15);
        $cities = Schema::hasTable('cities')
            ? City::where('status', 'active')->orderBy('name')->get()
            : collect();
        return view('admin.sector.index', compact('sectors', 'cities'));
    }

    public function store(Request $request)
    {
        if (!Schema::hasTable('sectors')) {
            return back()->with('error', 'Run migrations to create sectors table (php artisan migrate).');
        }
        $validated = $request->validate([
            'city_id' => 'required|exists:cities,id',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);
        
        // Check uniqueness: same sector name can exist for different cities
        $exists = Sector::where('name', $request->name)
            ->where('city_id', $request->city_id)
            ->where('status', 'active')
            ->exists();
            
        if ($exists) {
            return back()->withErrors(['name' => 'The sector name has already been taken for this city.'])->withInput();
        }
        Sector::create($validated);
        return back()->with('success', 'Sector created');
    }

    public function update(Request $request, $id)
    {
        if (!Schema::hasTable('sectors')) {
            return back()->with('error', 'Run migrations to create sectors table (php artisan migrate).');
        }
        
        try {
            $sector = Sector::findOrFail($id);
            
            $rules = [
                'city_id' => 'required|exists:cities,id',
                'name' => 'required|string|max:100',
                'description' => 'nullable|string',
                'status' => 'required|in:active,inactive',
            ];
            
            // Only validate uniqueness if name changed and check against active sectors only
            if ($request->name !== $sector->name || $request->city_id != $sector->city_id) {
                $exists = Sector::where('name', $request->name)
                    ->where('city_id', $request->city_id)
                    ->where('status', 'active')
                    ->where('id', '!=', $id)
                    ->exists();
                
                if ($exists) {
                    return back()->withErrors(['name' => 'The name has already been taken for this city.'])->withInput();
                }
            }
            
            $validated = $request->validate($rules);
            $sector->update($validated);
            return back()->with('success', 'Sector updated');
        } catch (\Exception $e) {
            Log::error('Sector update error: ' . $e->getMessage());
            return back()->with('error', 'Error updating sector: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        if (!Schema::hasTable('sectors')) {
            return back()->with('error', 'Run migrations to create sectors table (php artisan migrate).');
        }
        
        try {
            $sector = Sector::findOrFail($id);
            // Soft delete without migration: mark as inactive
            $sector->update([
                'status' => 'inactive'
            ]);
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => true]);
            }
            return back()->with('success', 'Sector removed from list');
        } catch (\Exception $e) {
            Log::error('Sector delete error: ' . $e->getMessage());
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
            }
            return back()->with('error', 'Error deleting sector: ' . $e->getMessage());
        }
    }
}
