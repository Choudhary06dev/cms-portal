<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ComplaintCrudController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $complaints = Complaint::with(['user','assignedEmployee'])->latest()->paginate(15);
        return view('admin.complaints.index', compact('complaints'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $employees = Employee::orderBy('id','desc')->get(['id']);
        return view('admin.complaints.create', compact('employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['nullable','string','max:100'],
            'description' => ['nullable','string'],
            'location' => ['nullable','string','max:150'],
            'assigned_employee_id' => ['nullable','exists:employees,id'],
            'status' => ['nullable','string','max:30'],
        ]);
        $data['user_id'] = auth()->id();
        $data['status'] = $data['status'] ?? 'NEW';
        Complaint::create($data);
        return redirect()->route('admin.complaints.index')->with('status','Complaint created');
    }

    /**
     * Display the specified resource.
     */
    public function show(Complaint $complaint): View
    {
        $complaint->load(['user','assignedEmployee','logs.user']);
        return view('admin.complaints.show', compact('complaint'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Complaint $complaint): View
    {
        $employees = Employee::orderBy('id','desc')->get(['id']);
        return view('admin.complaints.edit', compact('complaint','employees'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Complaint $complaint): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['nullable','string','max:100'],
            'description' => ['nullable','string'],
            'location' => ['nullable','string','max:150'],
            'assigned_employee_id' => ['nullable','exists:employees,id'],
            'status' => ['nullable','string','max:30'],
        ]);
        $complaint->update($data);
        return redirect()->route('admin.complaints.index')->with('status','Complaint updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Complaint $complaint): RedirectResponse
    {
        $complaint->delete();
        return redirect()->route('admin.complaints.index')->with('status','Complaint removed');
    }
}
