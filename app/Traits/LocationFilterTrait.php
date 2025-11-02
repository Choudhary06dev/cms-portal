<?php

namespace App\Traits;

use App\Models\Complaint;
use App\Models\Client;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Builder;

trait LocationFilterTrait
{
    /**
     * Apply location-based filtering to complaints query
     */
    public function filterComplaintsByLocation(Builder $query, $user): Builder
    {
        if (!$user || !$user->role) {
            return $query;
        }

        $roleName = strtolower($user->role->role_name ?? '');

        switch ($roleName) {
            case 'director':
                // Director can see all complaints - no filter
                break;

            case 'garrison_engineer':
                // GE can see only their city's complaints
                if ($user->city_id && $user->city) {
                    $query->whereHas('client', function ($q) use ($user) {
                        $q->where('city', $user->city->name);
                    });
                } else {
                    // If no city assigned, show nothing
                    $query->whereRaw('1 = 0');
                }
                break;

            case 'complaint_center':
            case 'department_staff':
                // Complaint Center and Department Staff can see only their sector's complaints
                if ($user->sector_id && $user->sector) {
                    $query->whereHas('client', function ($q) use ($user) {
                        $q->where('sector', $user->sector->name);
                    });
                } else {
                    // If no sector assigned, show nothing
                    $query->whereRaw('1 = 0');
                }
                break;
        }

        return $query;
    }

    /**
     * Apply location-based filtering to clients query
     */
    public function filterClientsByLocation(Builder $query, $user): Builder
    {
        if (!$user || !$user->role) {
            return $query;
        }

        $roleName = strtolower($user->role->role_name ?? '');

        switch ($roleName) {
            case 'director':
                // Director can see all clients - no filter
                break;

            case 'garrison_engineer':
                // GE can see only their city's clients
                if ($user->city_id && $user->city) {
                    $query->where('city', $user->city->name);
                } else {
                    $query->whereRaw('1 = 0');
                }
                break;

            case 'complaint_center':
            case 'department_staff':
                // Can see only their sector's clients
                if ($user->sector_id && $user->sector) {
                    $query->where('sector', $user->sector->name);
                } else {
                    $query->whereRaw('1 = 0');
                }
                break;
        }

        return $query;
    }

    /**
     * Apply location-based filtering to employees query
     */
    public function filterEmployeesByLocation(Builder $query, $user): Builder
    {
        if (!$user || !$user->role) {
            return $query;
        }

        $roleName = strtolower($user->role->role_name ?? '');

        switch ($roleName) {
            case 'director':
                // Director can see all employees - no filter
                break;

            case 'garrison_engineer':
                // GE can see only their city's employees
                if ($user->city_id) {
                    $query->where('city_id', $user->city_id);
                } else {
                    $query->whereRaw('1 = 0');
                }
                break;

            case 'complaint_center':
            case 'department_staff':
                // Can see only their sector's employees
                if ($user->sector_id) {
                    $query->where('sector_id', $user->sector_id);
                } else {
                    $query->whereRaw('1 = 0');
                }
                break;
        }

        return $query;
    }

    /**
     * Check if user can view all data (Director)
     */
    public function canViewAllData($user): bool
    {
        if (!$user || !$user->role) {
            return false;
        }

        return strtolower($user->role->role_name ?? '') === 'director';
    }

    /**
     * Get user's accessible city IDs (null means all cities)
     */
    public function getUserCityIds($user): ?array
    {
        if (!$user || !$user->role) {
            return null;
        }

        $roleName = strtolower($user->role->role_name ?? '');

        if ($roleName === 'director') {
            return null; // All cities
        }

        if ($roleName === 'garrison_engineer' && $user->city_id) {
            return [$user->city_id]; // Only their city
        }

        if (in_array($roleName, ['complaint_center', 'department_staff']) && $user->city_id) {
            return [$user->city_id]; // Their city
        }

        return [];
    }

    /**
     * Get user's accessible sector IDs (null means all sectors)
     */
    public function getUserSectorIds($user): ?array
    {
        if (!$user || !$user->role) {
            return null;
        }

        $roleName = strtolower($user->role->role_name ?? '');

        if ($roleName === 'director') {
            return null; // All sectors
        }

        if ($roleName === 'garrison_engineer' && $user->city_id) {
            // GE can see all sectors in their city
            return \App\Models\Sector::where('city_id', $user->city_id)
                ->pluck('id')
                ->toArray();
        }

        if (in_array($roleName, ['complaint_center', 'department_staff']) && $user->sector_id) {
            return [$user->sector_id]; // Only their sector
        }

        return [];
    }
}
