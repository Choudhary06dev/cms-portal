<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Client;
use App\Models\Employee;
use App\Models\Complaint;
use App\Models\ComplaintLog;
use App\Models\Spare;
use App\Models\SpareStockLog;
use App\Models\SpareApprovalPerforma;
use App\Models\SpareApprovalItem;
use App\Models\SlaRule;
use App\Models\ReportsSummary;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class CompleteDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create SLA rules first
        $this->createSlaRules();
        
        // Create spares
        $this->createSpares();
        
        // Create complaints
        $this->createComplaints();
        
        // Create spare approvals
        $this->createSpareApprovals();
        
        // Create reports summary
        $this->createReportsSummary();
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
                    ]);
                }
            }
        }
    }

    private function createSpares()
    {
        if (Spare::count() < 15) {
            $categories = ['electrical', 'plumbing', 'kitchen', 'general', 'tools', 'consumables'];
            $suppliers = ['TechSupply Inc', 'Hardware Solutions', 'Network Pro', 'Accessory World'];
            $units = ['pcs', 'kg', 'liters', 'meters', 'boxes'];

            for ($i = 1; $i <= 15; $i++) {
                if (!Spare::where('item_name', "Item {$i}")->exists()) {
                    $spare = Spare::create([
                        'item_name' => "Item {$i}",
                        'category' => $categories[array_rand($categories)],
                        'unit' => $units[array_rand($units)],
                        'unit_price' => rand(50, 2000),
                        'stock_quantity' => rand(0, 100),
                        'threshold_level' => rand(5, 20),
                        'supplier' => $suppliers[array_rand($suppliers)],
                        'description' => "Description for item {$i} - high quality component",
                    ]);

                    // Create stock logs
                    $this->createStockLogs($spare);
                }
            }
        }
    }

    private function createStockLogs($spare)
    {
        $changeTypes = ['in', 'out'];
        $remarks = ['Purchase', 'Sale', 'Return', 'Adjustment', 'Transfer'];

        for ($i = 0; $i < rand(3, 8); $i++) {
            $quantity = rand(1, 20);
            $changeType = $changeTypes[array_rand($changeTypes)];
            
            SpareStockLog::create([
                'spare_id' => $spare->id,
                'change_type' => $changeType,
                'quantity' => $quantity,
                'reference_id' => rand(1, 10),
                'remarks' => $remarks[array_rand($remarks)],
                'created_at' => Carbon::now()->subDays(rand(1, 30)),
            ]);
        }
    }

    private function createComplaints()
    {
        if (Complaint::count() < 25) {
            $clients = Client::all();
            $employees = Employee::all();
            $categories = ['technical', 'service', 'billing', 'other'];
            $statuses = ['new', 'assigned', 'in_progress', 'resolved', 'closed'];
            $priorities = ['low', 'medium', 'high', 'urgent'];

            for ($i = 1; $i <= 25; $i++) {
                $client = $clients->random();
                $assignedEmployee = $employees->random();
                $category = $categories[array_rand($categories)];
                $status = $statuses[array_rand($statuses)];
                $priority = $priorities[array_rand($priorities)];

                $complaint = Complaint::create([
                    'title' => "Complaint {$i} - " . ucfirst($category) . " Issue",
                    'client_id' => $client->id,
                    'category' => $category,
                    'description' => "Detailed description of the complaint issue. This is complaint number {$i} related to {$category} problems.",
                    'status' => $status,
                    'assigned_employee_id' => $assignedEmployee->id,
                    'priority' => $priority,
                    'closed_at' => $status === 'resolved' || $status === 'closed' ? Carbon::now()->subDays(rand(1, 30)) : null,
                    'created_at' => Carbon::now()->subDays(rand(1, 90)),
                    'updated_at' => Carbon::now()->subDays(rand(0, 30)),
                ]);

                // Create complaint logs
                $this->createComplaintLogs($complaint, $assignedEmployee);
            }
        }
    }

    private function createComplaintLogs($complaint, $assignedEmployee)
    {
        $actions = ['created', 'assigned', 'updated', 'resolved', 'closed'];
        $remarks = [
            'Complaint created and logged in system',
            'Complaint assigned to support team',
            'Status updated by support team',
            'Complaint resolved successfully',
            'Complaint closed and archived',
            'Additional information provided by client',
            'Follow-up call made to client',
            'Resolution implemented',
        ];

        $numLogs = rand(2, 6);
        for ($i = 0; $i < $numLogs; $i++) {
            ComplaintLog::create([
                'complaint_id' => $complaint->id,
                'action_by' => $assignedEmployee->id,
                'action' => $actions[array_rand($actions)],
                'remarks' => $remarks[array_rand($remarks)],
                'created_at' => $complaint->created_at->addHours($i * 2),
            ]);
        }
    }

    private function createSpareApprovals()
    {
        if (SpareApprovalPerforma::count() < 10) {
            $complaints = Complaint::all();
            $employees = Employee::all();

            for ($i = 1; $i <= 10; $i++) {
                $complaint = $complaints->random();
                $requestedBy = $employees->random();
                $approvedBy = rand(0, 1) ? $employees->random() : null;
                $status = ['pending', 'approved', 'rejected'][array_rand([0, 1, 2])];

                $approval = SpareApprovalPerforma::create([
                    'complaint_id' => $complaint->id,
                    'requested_by' => $requestedBy->id,
                    'approved_by' => $approvedBy ? $approvedBy->id : null,
                    'status' => $status,
                    'approved_at' => $status === 'approved' ? Carbon::now()->subDays(rand(1, 30)) : null,
                    'remarks' => "Spare parts approval request for complaint #{$complaint->id}",
                ]);
            }
        }
    }

    private function createReportsSummary()
    {
        if (ReportsSummary::count() < 3) {
            $reportTypes = ['complaints', 'spares', 'employees'];
            
            foreach ($reportTypes as $type) {
                if (!ReportsSummary::where('report_type', $type)->exists()) {
                    ReportsSummary::create([
                        'report_type' => $type,
                        'period_start' => Carbon::now()->subMonth()->startOfMonth(),
                        'period_end' => Carbon::now()->subMonth()->endOfMonth(),
                        'generated_at' => Carbon::now()->subDays(rand(1, 30)),
                        'data_json' => json_encode([
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
