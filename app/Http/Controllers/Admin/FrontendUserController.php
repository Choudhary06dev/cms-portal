<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FrontendUser;
use App\Models\City;
use App\Models\Sector;
use App\Models\FrontendUserLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FrontendUserController extends Controller
{
    public function __construct()
    {
        // Middleware is applied in routes
    }

    /**
     * Display a listing of frontend users
     */
    public function index(Request $request)
    {
        $query = FrontendUser::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $users = $query->orderBy('id', 'asc')->paginate(15);

        return view('admin.frontend-users.index', compact('users'));
    }

    /**
     * Show the form for creating a new frontend user
     */
    public function create()
    {
        return view('admin.frontend-users.create');
    }

    /**
     * Store a newly created frontend user
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:100|unique:frontend_users',
            'name' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:150|unique:frontend_users,email',
            'phone' => 'nullable|string|min:11|max:20',
            'password' => 'required|string|min:6|confirmed',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = FrontendUser::create([
            'username' => $request->username,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'status' => $request->status,
            // role_id, city_id, sector_id are not set - will be null
        ]);

        return redirect()->route('admin.frontend-users.index')
            ->with('success', 'Frontend user created successfully.');
    }

    /**
     * Display the specified frontend user
     */
    public function show(FrontendUser $frontend_user)
    {
        // Load assigned locations with cities and sectors
        $assignedLocations = [];
        if (Schema::hasTable('frontend_user_locations')) {
            $assignedLocations = FrontendUserLocation::where('frontend_user_id', $frontend_user->id)
                ->with(['city', 'sector'])
                ->get();
        }

        if (request()->get('format') === 'html') {
            return view('admin.frontend-users.show', compact('frontend_user', 'assignedLocations'));
        }

        return view('admin.frontend-users.show', compact('frontend_user', 'assignedLocations'));
    }

    /**
     * Show the form for editing the frontend user
     */
    public function edit(FrontendUser $frontend_user)
    {
        return view('admin.frontend-users.edit', compact('frontend_user'));
    }

    /**
     * Update the specified frontend user
     */
    public function update(Request $request, FrontendUser $frontend_user)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:100|unique:frontend_users,username,' . $frontend_user->id,
            'name' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:150|unique:frontend_users,email,' . $frontend_user->id,
            'phone' => 'nullable|string|min:11|max:20',
            'password' => 'nullable|string|min:6|confirmed',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $updateData = [
                'username' => $request->username,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'status' => $request->status,
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $frontend_user->update($updateData);

            return redirect()->route('admin.frontend-users.index')
                ->with('success', 'Frontend user updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating frontend user: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified frontend user
     */
    public function destroy(FrontendUser $frontend_user)
    {
        $frontend_user->delete();

        if (request()->expectsJson() || request()->ajax() || request()->header('Accept') === 'application/json') {
            return response()->json([
                'success' => true,
                'message' => 'Frontend user deleted successfully.'
            ]);
        }

        return redirect()->route('admin.frontend-users.index')
            ->with('success', 'Frontend user deleted successfully.');
    }

    /**
     * Toggle frontend user status
     */
    public function toggleStatus(FrontendUser $frontend_user)
    {
        $frontend_user->update([
            'status' => $frontend_user->status === 'active' ? 'inactive' : 'active'
        ]);

        $status = $frontend_user->status === 'active' ? 'activated' : 'deactivated';
        
        return redirect()->back()
            ->with('success', "Frontend user {$status} successfully.");
    }

    /**
     * Get assign locations form data
     */
    public function getAssignForm(FrontendUser $frontend_user)
    {
        $cities = City::where('status', 'active')
            ->with(['sectors' => function($query) {
                $query->where('status', 'active')->orderBy('id', 'asc');
            }])
            ->orderBy('id', 'asc')
            ->get();

        // Get already assigned locations (if table exists)
        $assignedCityIds = [];
        $assignedSectorIds = [];
        
        if (Schema::hasTable('frontend_user_locations')) {
            $assignedLocations = FrontendUserLocation::where('frontend_user_id', $frontend_user->id)->get();
            $assignedCityIds = $assignedLocations->whereNotNull('city_id')->whereNull('sector_id')->pluck('city_id')->toArray();
            $assignedSectorIds = $assignedLocations->whereNotNull('sector_id')->pluck('sector_id')->toArray();
        }

        // Format cities data for JSON response
        $citiesData = $cities->map(function($city) {
            return [
                'id' => $city->id,
                'name' => $city->name,
                'status' => $city->status,
                'sectors' => $city->sectors->map(function($sector) {
                    return [
                        'id' => $sector->id,
                        'name' => $sector->name,
                        'city_id' => $sector->city_id,
                        'status' => $sector->status,
                    ];
                })->toArray(),
            ];
        })->toArray();

        return response()->json([
            'cities' => $citiesData,
            'assignedCityIds' => $assignedCityIds,
            'assignedSectorIds' => $assignedSectorIds,
        ]);
    }

    /**
     * Assign locations (cities and sectors) to frontend user
     */
    public function assignLocations(Request $request, FrontendUser $frontend_user)
    {
        $validator = Validator::make($request->all(), [
            'city_ids' => 'nullable|array',
            'city_ids.*' => 'exists:cities,id',
            'sector_ids' => 'nullable|array',
            'sector_ids.*' => 'exists:sectors,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Delete existing locations
            FrontendUserLocation::where('frontend_user_id', $frontend_user->id)->delete();

            $cityIds = $request->input('city_ids', []);
            $sectorIds = $request->input('sector_ids', []);

            $locations = [];

            // Add city-only assignments (when city is selected but not its sectors)
            foreach ($cityIds as $cityId) {
                // Check if any sector of this city is in the selected sectors
                $citySectors = Sector::where('city_id', $cityId)->pluck('id')->toArray();
                $hasCitySectorSelected = !empty(array_intersect($citySectors, $sectorIds));

                // Only add city-only assignment if no sectors of this city are selected
                if (!$hasCitySectorSelected) {
                    $locations[] = [
                        'frontend_user_id' => $frontend_user->id,
                        'city_id' => $cityId,
                        'sector_id' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            // Add sector assignments (sector always includes its city)
            foreach ($sectorIds as $sectorId) {
                $sector = Sector::find($sectorId);
                if ($sector) {
                    $locations[] = [
                        'frontend_user_id' => $frontend_user->id,
                        'city_id' => $sector->city_id,
                        'sector_id' => $sectorId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            // Remove duplicates
            $uniqueLocations = [];
            $seen = [];
            foreach ($locations as $location) {
                $key = $location['city_id'] . '_' . ($location['sector_id'] ?? 'null');
                if (!isset($seen[$key])) {
                    $seen[$key] = true;
                    $uniqueLocations[] = $location;
                }
            }

            if (!empty($uniqueLocations)) {
                FrontendUserLocation::insert($uniqueLocations);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Locations assigned successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error assigning locations: ' . $e->getMessage()
            ], 500);
        }
    }
}

