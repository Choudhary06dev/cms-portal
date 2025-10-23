<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Client;
use App\Models\Employee;
use App\Models\Complaint;
use App\Models\Spare;
use App\Models\SlaRule;
use App\Models\ReportsSummary;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class QuickDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create additional users if they don't exist
        $this->createUsers();
        
        // Create clients
        $this->createClients();
        
        // Create employees
        $this->createEmployees();
        
        // Create SLA rules
        $this->createSlaRules();
        
        // Create spares
        $this->createSpares();
        
        // Create complaints
        $this->createComplaints();
        
        // Create reports summary
        $this->createReportsSummary();
    }

    private function createUsers()
    {
        $existingUsers = User::count();
        if ($existingUsers < 5) {
            $users = [
                [
                    'username' => 'john_doe',
                    'email' => 'john.doe@company.com',
                    'phone' => '+1234567890',
                    'password' => Hash::make('password'),
                    'role_id' => 2,
                    'status' => 'active',
                    'theme' => 'light',
                ],
                [
                    'username' => 'jane_smith',
                    'email' => 'jane.smith@company.com',
                    'phone' => '+1234567891',
                    'password' => Hash::make('password'),
                    'role_id' => 2,
                    'status' => 'active',
                    'theme' => 'dark',
                ],
                [
                    'username' => 'mike_wilson',
                    'email' => 'mike.wilson@company.com',
                    'phone' => '+1234567892',
                    'password' => Hash::make('password'),
                    'role_id' => 2,
                    'status' => 'active',
                    'theme' => 'night',
                ],
            ];

            foreach ($users as $userData) {
                if (!User::where('username', $userData['username'])->exists()) {
                    User::create($userData);
                }
            }
        }
    }

    private function createClients()
    {
        if (Client::count() < 5) {
            $clients = [
                [
                    'client_name' => 'TechCorp Solutions',
                    'contact_person' => 'David Brown',
                    'email' => 'david.brown@techcorp.com',
                    'phone' => '+1987654321',
                    'address' => '123 Business Ave, Tech City, TC 12345',
                    'city' => 'Tech City',
                    'state' => 'Tech State',
                    'pincode' => '12345',
                    'status' => 'active',
                ],
                [
                    'client_name' => 'Global Industries Ltd',
                    'contact_person' => 'Lisa Anderson',
                    'email' => 'lisa.anderson@globalind.com',
                    'phone' => '+1987654322',
                    'address' => '456 Corporate Blvd, Metro City, MC 67890',
                    'city' => 'Metro City',
                    'state' => 'Metro State',
                    'pincode' => '67890',
                    'status' => 'active',
                ],
                [
                    'client_name' => 'StartupXYZ',
                    'contact_person' => 'Mark Johnson',
                    'email' => 'mark.johnson@startupxyz.com',
                    'phone' => '+1987654323',
                    'address' => '789 Innovation St, Startup Valley, SV 11111',
                    'city' => 'Startup Valley',
                    'state' => 'Innovation State',
                    'pincode' => '11111',
                    'status' => 'active',
                ],
                [
                    'client_name' => 'Enterprise Systems',
                    'contact_person' => 'Emily Davis',
                    'email' => 'emily.davis@enterprise.com',
                    'phone' => '+1987654324',
                    'address' => '321 Enterprise Way, Business District, BD 22222',
                    'city' => 'Business District',
                    'state' => 'Enterprise State',
                    'pincode' => '22222',
                    'status' => 'inactive',
                ],
                [
                    'client_name' => 'Digital Dynamics',
                    'contact_person' => 'Robert Wilson',
                    'email' => 'robert.wilson@digitaldynamics.com',
                    'phone' => '+1987654325',
                    'address' => '654 Digital Drive, Tech Hub, TH 33333',
                    'city' => 'Tech Hub',
                    'state' => 'Digital State',
                    'pincode' => '33333',
                    'status' => 'active',
                ],
            ];

            foreach ($clients as $clientData) {
                if (!Client::where('client_name', $clientData['client_name'])->exists()) {
                    Client::create($clientData);
                }
            }
        }
    }

    private function createEmployees()
    {
        $users = User::where('role_id', 2)->get();
        $existingEmployees = Employee::count();
        
        if ($existingEmployees < $users->count()) {
            $departments = ['IT Support', 'Customer Service', 'Technical Support', 'Sales', 'Marketing'];
            $designations = ['Support Engineer', 'Customer Representative', 'Technical Specialist', 'Sales Executive', 'Marketing Coordinator'];

            foreach ($users as $index => $user) {
                if (!Employee::where('user_id', $user->id)->exists()) {
                    Employee::create([
                        'user_id' => $user->id,
                        'department' => $departments[$index % count($departments)],
                        'designation' => $designations[$index % count($designations)],
                        'biometric_id' => 'EMP' . str_pad($user->id, 4, '0', STR_PAD_LEFT),
                    ]);
                }
            }
        }
    }

    private function createSlaRules()
    {
        if (SlaRule::count() < 5) {
            $complaintTypes = ['hardware', 'software', 'network', 'billing', 'general'];
            $users = User::where('role_id', 2)->get();

            foreach ($complaintTypes as $index => $type) {
                if (!SlaRule::where('complaint_type', $type)->exists()) {
                    SlaRule::create([
                        'complaint_type' => $type,
                        'max_response_time' => rand(1, 24),
                        'max_resolution_time' => rand(24, 168),
                        'notify_to' => $users->random()->id,
                        'escalation_level' => rand(1, 3),
                        'status' => 'active',
                        'description' => "SLA rule for {$type} complaints with specific response and resolution times.",
                    ]);
                }
            }
        }
    }

    private function createSpares()
    {
        if (Spare::count() < 10) {
            $spareTypes = ['Hardware', 'Software', 'Network', 'Accessories'];
            $suppliers = ['TechSupply Inc', 'Hardware Solutions', 'Network Pro', 'Accessory World'];

            for ($i = 1; $i <= 10; $i++) {
                if (!Spare::where('spare_name', "Spare Part {$i}")->exists()) {
                    Spare::create([
                        'spare_name' => "Spare Part {$i}",
                        'spare_type' => $spareTypes[array_rand($spareTypes)],
                        'description' => "Description for spare part {$i} - high quality component",
                        'supplier' => $suppliers[array_rand($suppliers)],
                        'unit_price' => rand(50, 2000),
                        'current_stock' => rand(0, 100),
                        'min_stock_level' => rand(5, 20),
                        'status' => rand(0, 1) ? 'active' : 'inactive',
                    ]);
                }
            }
        }
    }

    private function createComplaints()
    {
        if (Complaint::count() < 20) {
            $clients = Client::all();
            $users = User::where('role_id', 2)->get();
            $slaRules = SlaRule::all();
            $statuses = ['open', 'in_progress', 'resolved', 'closed'];
            $priorities = ['low', 'medium', 'high', 'urgent'];

            for ($i = 1; $i <= 20; $i++) {
                if (!Complaint::where('ticket_number', 'TKT-' . str_pad($i, 6, '0', STR_PAD_LEFT))->exists()) {
                    $client = $clients->random();
                    $assignedTo = $users->random();
                    $slaRule = $slaRules->random();
                    $status = $statuses[array_rand($statuses)];
                    $priority = $priorities[array_rand($priorities)];

                    Complaint::create([
                        'ticket_number' => 'TKT-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                        'client_id' => $client->id,
                        'assigned_to' => $assignedTo->id,
                        'category' => $slaRule->complaint_type,
                        'priority' => $priority,
                        'status' => $status,
                        'subject' => "Complaint Subject {$i} - " . ucfirst($slaRule->complaint_type) . " Issue",
                        'description' => "Detailed description of the complaint issue. This is complaint number {$i} related to {$slaRule->complaint_type} problems.",
                        'resolution_notes' => $status === 'resolved' || $status === 'closed' ? "Issue has been resolved successfully. Resolution details for complaint {$i}." : null,
                        'created_at' => Carbon::now()->subDays(rand(1, 90)),
                        'updated_at' => Carbon::now()->subDays(rand(0, 30)),
                    ]);
                }
            }
        }
    }

    private function createReportsSummary()
    {
        if (ReportsSummary::count() < 5) {
            $reportTypes = ['complaints', 'spares', 'employees', 'financial', 'sla'];
            
            foreach ($reportTypes as $type) {
                if (!ReportsSummary::where('report_type', $type)->exists()) {
                    ReportsSummary::create([
                        'report_type' => $type,
                        'title' => ucfirst($type) . ' Report',
                        'description' => "Monthly {$type} report with comprehensive data analysis",
                        'generated_by' => 1,
                        'generated_at' => Carbon::now()->subDays(rand(1, 30)),
                        'data' => json_encode([
                            'total_records' => rand(50, 500),
                            'active_records' => rand(30, 400),
                            'inactive_records' => rand(5, 50),
                            'last_updated' => Carbon::now()->subDays(rand(1, 7))->toISOString(),
                        ]),
                    ]);
                }
            }
        }
    }
}
