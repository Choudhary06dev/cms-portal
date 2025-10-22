<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SlaRule;
use App\Models\User;

class SlaRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin user for notifications
        $adminUser = User::where('username', 'admin')->first();
        
        if (!$adminUser) {
            $this->command->error('Admin user not found. Please run RoleSeeder first.');
            return;
        }

        // Create SLA rules for different complaint types
        $slaRules = [
            [
                'complaint_type' => 'electric',
                'max_response_time' => 2, // 2 hours
                'max_resolution_time' => 24, // 24 hours
                'escalation_level' => 1,
                'notify_to' => $adminUser->id,
                'status' => 'active',
            ],
            [
                'complaint_type' => 'sanitary',
                'max_response_time' => 4, // 4 hours
                'max_resolution_time' => 48, // 48 hours
                'escalation_level' => 1,
                'notify_to' => $adminUser->id,
                'status' => 'active',
            ],
            [
                'complaint_type' => 'kitchen',
                'max_response_time' => 6, // 6 hours
                'max_resolution_time' => 72, // 72 hours
                'escalation_level' => 1,
                'notify_to' => $adminUser->id,
                'status' => 'active',
            ],
            [
                'complaint_type' => 'general',
                'max_response_time' => 8, // 8 hours
                'max_resolution_time' => 96, // 96 hours
                'escalation_level' => 1,
                'notify_to' => $adminUser->id,
                'status' => 'active',
            ],
        ];

        foreach ($slaRules as $rule) {
            SlaRule::create($rule);
        }

        $this->command->info('SLA rules created successfully!');
    }
}
