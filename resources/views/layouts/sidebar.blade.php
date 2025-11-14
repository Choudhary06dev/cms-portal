<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>@yield('title', 'CMS Admin')</title>
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  @stack('head')
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link href="{{ asset('css/sidebar.css') }}" rel="stylesheet">
  <link href="{{ asset('css/themes.css') }}" rel="stylesheet">
  
  <script src="https://unpkg.com/feather-icons"></script>
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
  
  <!-- Error handling for missing scripts -->
  <script>
    // Prevent errors from missing scripts and null element errors
    window.addEventListener('error', function(e) {
      if (e.filename && (e.filename.includes('share-modal.js') || e.filename.includes('share-modal'))) {
        console.warn('share-modal.js error ignored');
        e.preventDefault();
        e.stopPropagation();
        return true;
      }
      // Handle null element errors
      if (e.message && e.message.includes('Cannot read properties of null') && e.message.includes('addEventListener')) {
        console.warn('Null element addEventListener error ignored:', e.message);
        e.preventDefault();
        e.stopPropagation();
        return true;
      }
    }, true); // Use capture phase
    
    // Override addEventListener globally to handle null elements gracefully
    (function() {
      const originalAddEventListener = Element.prototype.addEventListener;
      Element.prototype.addEventListener = function(type, listener, options) {
        if (this === null || this === undefined || !this.nodeType) {
          console.warn('Attempted to add event listener to null/invalid element:', type);
          return;
        }
        try {
          return originalAddEventListener.call(this, type, listener, options);
        } catch (error) {
          console.warn('Error adding event listener:', error.message);
          return;
        }
      };
    })();
    
    // Wrap setTimeout to catch errors
    (function() {
      const originalSetTimeout = window.setTimeout;
      window.setTimeout = function(func, delay, ...args) {
        return originalSetTimeout(function() {
          try {
            if (typeof func === 'function') {
              func.apply(this, args);
            } else {
              eval(func);
            }
          } catch (error) {
            if (error.message && (error.message.includes('share-modal') || error.message.includes('null') && error.message.includes('addEventListener'))) {
              console.warn('setTimeout error ignored:', error.message);
              return;
            }
            throw error;
          }
        }, delay || 0);
      };
    })();
  </script>
  <style>
    :root{
      --glass-bg: rgba(255,255,255,0.08);
      --accent: #3b82f6;
      --accent-hover: #2563eb;
      --muted: #64748b;
      --sidebar-bg: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
      --topbar-bg: linear-gradient(90deg, #3b82f6 0%, #1d4ed8 100%);
    }
    body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%); color:#f1f5f9; min-height:100vh; }
    .sidebar {
      min-height:100vh;
      width: 240px;
      background: var(--sidebar-bg);
      border-right: 1px solid rgba(59, 130, 246, 0.2);
      padding: 16px;
      position: fixed;
      left:0; top:50px; bottom:0;
      box-shadow: 4px 0 20px rgba(0, 0, 0, 0.3);
      z-index: 999;
    }
    .brand { color: var(--accent); font-weight:700; font-size:18px; text-shadow: 0 0 10px rgba(59, 130, 246, 0.3); }
    .nav-link { 
      color: #cbd5e1; 
      border-radius:8px; 
      transition: none !important; 
      white-space: nowrap !important;
      overflow: hidden;
      text-overflow: ellipsis;
      font-size: 0.9rem;
      display: flex !important;
      align-items: center;
      text-align: left !important;
      justify-content: flex-start !important;
    }
    .nav-link:hover, .nav-link.active { 
      background: linear-gradient(90deg, rgba(59, 130, 246, 0.15), rgba(37, 99, 235, 0.1)); 
      color: #fff; 
      transform: none !important; 
    }
    .nav-link i {
      flex-shrink: 0 !important;
      width: 18px !important;
      height: 18px !important;
      min-width: 18px !important;
    }
    .nav-link i[data-feather="file-text"],
    .nav-link i[data-feather="alert-circle"] {
      width: 20px !important;
      height: 20px !important;
      min-width: 20px !important;
      display: inline-block !important;
      visibility: visible !important;
      opacity: 1 !important;
    }
    .nav-link i svg {
      width: 18px !important;
      height: 18px !important;
      display: inline-block !important;
      visibility: visible !important;
    }
    
    /* Ensure all feather icons in submenu are visible */
    .collapse i[data-feather],
    #complaintsManagementSubmenu i[data-feather] {
      display: inline-block !important;
      visibility: visible !important;
      opacity: 1 !important;
    }
    
    .collapse i[data-feather] svg,
    #complaintsManagementSubmenu i[data-feather] svg {
      display: inline-block !important;
      visibility: visible !important;
      opacity: 1 !important;
    }
    .nav-link i[data-feather="file-text"] svg,
    .nav-link i[data-feather="alert-circle"] svg {
      width: 20px !important;
      height: 20px !important;
      display: block !important;
    }
    .nav-link span, .nav-link .text-decoration-none {
      white-space: nowrap !important;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .nav-link .text-decoration-none[href*="complaints"] {
      overflow: visible !important;
      text-overflow: clip !important;
      flex: 1 1 auto !important;
      min-width: 0 !important;
    }
    .nav-link[style*="overflow: visible"] {
      overflow: visible !important;
      text-overflow: clip !important;
    }
    .nav-link[style*="overflow: visible"] .text-decoration-none {
      overflow: visible !important;
      text-overflow: clip !important;
      max-width: none !important;
    }
    .content { margin-left: 250px; padding: 20px; margin-top: 50px; }
    /* Topbar styles are now in the navigation component */
    .card-glass { 
      background: var(--glass-bg); 
      border:1px solid rgba(59, 130, 246, 0.1); 
      border-radius:14px; 
      padding:14px; 
      box-shadow: 0 8px 30px rgba(15, 23, 42, 0.4);
      backdrop-filter: blur(10px);
    }
    .table thead th { 
      background: linear-gradient(90deg, rgba(59, 130, 246, 0.1), rgba(37, 99, 235, 0.05)); 
      color:#e2e8f0; 
      border-bottom: 2px solid rgba(59, 130, 246, 0.2);
      padding: 0.5rem 0.75rem !important;
      font-size: 0.875rem !important;
    }
    .table td { 
      padding: 0.5rem 0.75rem !important;
      font-size: 0.875rem !important;
    }
    .table { 
      font-size: 0.875rem !important;
    }
    .table-sm th,
    .table-sm td {
      padding: 0.4rem 0.5rem !important;
      font-size: 0.8rem !important;
    }
    .compact-table th,
    .compact-table td {
      padding: 0.4rem 0.5rem !important;
      font-size: 0.8rem !important;
    }
    
    /* Zebra striping for all tables - alternating row colors */
    /* Override Bootstrap table-dark default background first */
    .table-dark,
    table.table-dark {
      background-color: transparent !important;
    }
    /* Odd rows (1st, 3rd, 5th...) - very light white background */
    table.table tbody tr:nth-child(odd),
    table.table-dark tbody tr:nth-child(odd),
    .table tbody tr:nth-child(odd),
    .table-dark tbody tr:nth-child(odd),
    table tbody tr:nth-child(odd),
    .table-compact tbody tr:nth-child(odd) {
      background-color: rgba(255, 255, 255, 0.03) !important;
    }
    table.table tbody tr:nth-child(odd) td,
    table.table-dark tbody tr:nth-child(odd) td,
    .table tbody tr:nth-child(odd) td,
    .table-dark tbody tr:nth-child(odd) td,
    table tbody tr:nth-child(odd) td,
    .table-compact tbody tr:nth-child(odd) td {
      background-color: rgba(255, 255, 255, 0.03) !important;
    }
    /* Even rows (2nd, 4th, 6th...) - very light gradient like thead */
    table.table tbody tr:nth-child(even),
    table.table-dark tbody tr:nth-child(even),
    .table tbody tr:nth-child(even),
    .table-dark tbody tr:nth-child(even),
    table tbody tr:nth-child(even),
    .table-compact tbody tr:nth-child(even) {
      background: linear-gradient(90deg, rgba(59, 130, 246, 0.05), rgba(37, 99, 235, 0.03)) !important;
    }
    table.table tbody tr:nth-child(even) td,
    table.table-dark tbody tr:nth-child(even) td,
    .table tbody tr:nth-child(even) td,
    .table-dark tbody tr:nth-child(even) td,
    table tbody tr:nth-child(even) td,
    .table-compact tbody tr:nth-child(even) td {
      background: linear-gradient(90deg, rgba(59, 130, 246, 0.05), rgba(37, 99, 235, 0.03)) !important;
    }
    .btn-accent { 
      background: linear-gradient(135deg, #60a5fa, #3b82f6); 
      border:none; 
      color:#fff; 
      font-weight:700; 
      box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);
      transition: none !important;
    }
    .btn-accent:hover { 
      background: linear-gradient(135deg, #3b82f6, #2563eb); 
      transform: none !important;
      box-shadow: 0 6px 18px rgba(59, 130, 246, 0.3);
    }
    .btn-sm { padding: 6px 12px; font-size: 12px; }
    .status-badge { padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; }
    .status-new { background: rgba(59, 130, 246, 0.2); color: #3b82f6; }
    .status-assigned { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
    .status-in_progress { background: rgba(168, 85, 247, 0.2); color: #a855f7; }
    .status-resolved { background: rgba(34, 197, 94, 0.2); color: #22c55e; }
    .status-closed { background: rgba(107, 114, 128, 0.2); color: #6b7280; }
    .status-pending { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
    .status-approved { background: rgba(34, 197, 94, 0.2); color: #22c55e; }
    .status-rejected { background: rgba(239, 68, 68, 0.2); color: #ef4444; }
    .priority-badge { padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; }
    .priority-high { background: rgba(239, 68, 68, 0.2); color: #ef4444; }
    .priority-medium { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
    .priority-low { background: rgba(34, 197, 94, 0.2); color: #22c55e; }
    .section-title { color: #9fb7d8; font-size:12px; margin-top:18px; margin-bottom:8px; white-space: nowrap; }
    .nav-item-parent .nav-link { position: relative; }
    .nav-arrow-btn,
    .nav-arrow-btn.btn-link { 
      border: none !important; 
      box-shadow: none !important; 
      outline: none !important;
      background: transparent !important;
      padding: 0 !important;
      margin: 0 !important;
      text-decoration: none !important;
    }
    .nav-arrow-btn:hover,
    .nav-arrow-btn.btn-link:hover { 
      opacity: 0.8; 
      border: none !important; 
      box-shadow: none !important; 
      background: transparent !important;
      text-decoration: none !important;
    }
    .nav-arrow-btn:focus,
    .nav-arrow-btn.btn-link:focus { 
      outline: none !important; 
      box-shadow: none !important; 
      border: none !important;
      background: transparent !important;
      text-decoration: none !important;
    }
    .nav-arrow-btn:active,
    .nav-arrow-btn.btn-link:active {
      border: none !important;
      box-shadow: none !important;
      background: transparent !important;
      text-decoration: none !important;
    }
    .text-muted { color: #94a3b8 !important; }
    .text-white { color: #ffffff !important; }
    .text-light { color: #cbd5e1 !important; }
    .h1, .h2, .h3, .h4, .h5, .h6 { color: #ffffff !important; }
    .card-glass h1, .card-glass h2, .card-glass h3, .card-glass h4, .card-glass h5, .card-glass h6 { color: #ffffff !important; }
    .card-glass p { color: #cbd5e1 !important; }
    .card-glass .text-muted { color: #94a3b8 !important; }
    
    /* Modal Styles */
    .modal-content { border-radius: 12px; }
    .modal-header { border-radius: 12px 12px 0 0; }
    .modal-footer { border-radius: 0 0 12px 12px; }
    
    .form-control:focus, .form-select:focus { 
      border-color: #3b82f6; 
      box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25); 
      background: rgba(255,255,255,0.15);
    }
    .form-control::placeholder, .form-select::placeholder { color: #94a3b8; }
    .btn-close { filter: invert(1); }
    .avatar-sm { width: 40px; height: 40px; }
    .avatar-lg { width: 80px; height: 80px; }

    
    /* Adjust content margin for topbar */
    .content {
      margin-top: 50px;
    }
    
    @media (max-width: 991px){
      .sidebar { position: relative; width:100%; min-height:auto; }
      .content { margin-left:0; padding:12px; }
    }
  </style>
  @stack('styles')
</head>
<body>
  <!-- Skip Link for Accessibility -->
  <a href="#main-content" class="skip-link">Skip to main content</a>

  <!-- TOPBAR -->
  @include('layouts.navigation')

  <!-- SIDEBAR -->
  <aside class="sidebar">
    @php
      $user = Auth::user();
      $userRole = $user ? strtolower($user->role->role_name ?? '') : '';
    @endphp
    
    <div class="section-title">Main Menu</div>
    @if($user && ($user->hasPermission('dashboard') || $userRole === 'director' || $userRole === 'admin'))
    <a href="{{ route('admin.dashboard') }}" class="nav-link d-block py-2 px-3 mb-1 {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
      <i data-feather="home" class="me-2"></i> Dashboard
    </a>
    @endif
    
    <div class="section-title">Management</div>
    @if($user && ($user->hasPermission('users') || $userRole === 'director' || $userRole === 'admin'))
    <a href="{{ route('admin.users.index') }}" class="nav-link d-block py-2 px-3 mb-1 {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
      <i data-feather="users" class="me-2"></i> Users
    </a>
    @endif
    @if($user && ($user->hasPermission('employees') || $userRole === 'director' || $userRole === 'admin'))
    <a href="{{ route('admin.city.index') }}" class="nav-link d-block py-2 px-3 mb-1 {{ request()->routeIs('admin.city.*') ? 'active' : '' }}">
      <i data-feather="map" class="me-2"></i> GE Groups
    </a>
    <a href="{{ route('admin.sector.index') }}" class="nav-link d-block py-2 px-3 mb-1 {{ request()->routeIs('admin.sector.*') ? 'active' : '' }}">
      <i data-feather="map-pin" class="me-2"></i> GE Nodes
    </a>
    @endif
    @if($user && ($user->hasPermission('roles') || $userRole === 'director' || $userRole === 'admin'))
    <a href="{{ route('admin.roles.index') }}" class="nav-link d-block py-2 px-3 mb-1 {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
      <i data-feather="shield" class="me-2"></i> Roles
    </a>
    @endif
    @if($user && ($user->hasPermission('employees') || $userRole === 'director' || $userRole === 'admin' || $userRole === 'department_staff'))
    <div class="nav-item-parent mb-1">
      <div class="nav-link d-flex align-items-center justify-content-between py-2 px-3 {{ request()->routeIs('admin.employees.*') || request()->routeIs('admin.designation.*') ? 'active' : '' }}">
        <a href="{{ route('admin.employees.index') }}" class="text-decoration-none text-inherit d-flex align-items-center flex-grow-1">
          <i data-feather="user-check" class="me-2"></i> Employees
        </a>
        <button type="button" class="btn btn-link text-inherit p-0 border-0 nav-arrow-btn" data-bs-toggle="collapse" data-bs-target="#employeesSubmenu" aria-expanded="{{ request()->routeIs('admin.designation.*') ? 'true' : 'false' }}" style="background: none !important; color: inherit; cursor: pointer; border: none !important; box-shadow: none !important; outline: none !important; padding: 0 !important; margin: 0 !important;">
          <i data-feather="chevron-down" class="nav-arrow ms-2" style="font-size: 14px; transition: transform 0.3s;"></i>
        </button>
      </div>
      <div class="collapse {{ request()->routeIs('admin.designation.*') ? 'show' : '' }}" id="employeesSubmenu">
        @if($user && ($user->hasPermission('designation') || $userRole === 'director' || $userRole === 'admin'))
        <a href="{{ route('admin.designation.index') }}" class="nav-link d-block py-2 px-3 mb-2 mt-2 {{ request()->routeIs('admin.designation.*') ? 'active' : '' }}" style="background: rgba(59, 130, 246, 0.08); margin-left: 20px; margin-right: 8px; border-left: 3px solid rgba(59, 130, 246, 0.4); border-radius: 6px;">
          <i data-feather="award" class="me-2"></i> Designations
        </a>
        @endif
      </div>
    </div>
    @endif
    @if($user && ($user->hasPermission('complaints') || $userRole === 'director' || $userRole === 'admin' || $userRole === 'garrison_engineer' || $userRole === 'complaint_center' || $userRole === 'department_staff') || ($user && ($user->hasPermission('approvals') || $userRole === 'director' || $userRole === 'admin' || $userRole === 'garrison_engineer')))
    <div class="nav-item-parent mb-1">
      <div class="nav-link d-flex align-items-center justify-content-between py-2 px-3 {{ request()->routeIs('admin.complaints.*') || request()->routeIs('admin.category.*') || request()->routeIs('admin.complaint-titles.*') || (request()->routeIs('admin.approvals.*') && !request()->routeIs('admin.stock-approval.*')) ? 'active' : '' }}" style="overflow: visible !important; text-overflow: clip !important; cursor: pointer;" id="complaintsManagementToggle" data-bs-toggle="collapse" data-bs-target="#complaintsManagementSubmenu" aria-expanded="{{ request()->routeIs('admin.complaints.*') || request()->routeIs('admin.category.*') || request()->routeIs('admin.complaint-titles.*') || (request()->routeIs('admin.approvals.*') && !request()->routeIs('admin.stock-approval.*')) ? 'true' : 'false' }}">
        <div class="d-flex align-items-center flex-grow-1">
          <i data-feather="file-text" class="me-2"></i> 
          <span style="overflow: visible !important; text-overflow: clip !important; white-space: nowrap !important; display: inline-block;">Complaints Mgmt</span>
        </div>
          <i data-feather="chevron-down" class="nav-arrow ms-2" style="font-size: 14px; transition: transform 0.3s;"></i>
      </div>
      <div class="collapse {{ request()->routeIs('admin.complaints.*') || request()->routeIs('admin.category.*') || request()->routeIs('admin.complaint-titles.*') || (request()->routeIs('admin.approvals.*') && !request()->routeIs('admin.stock-approval.*')) ? 'show' : '' }}" id="complaintsManagementSubmenu">
        @if($user && ($user->hasPermission('category') || $userRole === 'director' || $userRole === 'admin'))
        <a href="{{ route('admin.category.index') }}" class="nav-link d-block py-2 px-3 mb-2 mt-2 {{ request()->routeIs('admin.category.*') ? 'active' : '' }}" style="background: rgba(59, 130, 246, 0.08); margin-left: 20px; margin-right: 8px; border-left: 3px solid rgba(59, 130, 246, 0.4); border-radius: 6px;">
          <i data-feather="tag" class="me-2" style="width: 18px; height: 18px;"></i> Complaint Cat
        </a>
        @endif
        @if($user && ($user->hasPermission('complaint-titles') || $userRole === 'director' || $userRole === 'admin'))
        <a href="{{ route('admin.complaint-titles.index') }}" class="nav-link d-block py-2 px-3 mb-2 {{ request()->routeIs('admin.complaint-titles.*') ? 'active' : '' }}" style="background: rgba(59, 130, 246, 0.08); margin-left: 20px; margin-right: 8px; border-left: 3px solid rgba(59, 130, 246, 0.4); border-radius: 6px;">
          <i data-feather="file-text" class="me-2" style="width: 18px; height: 18px;"></i> Complaint Types
        </a>
        @endif
        @if($user && ($user->hasPermission('complaints') || $userRole === 'director' || $userRole === 'admin' || $userRole === 'garrison_engineer' || $userRole === 'complaint_center' || $userRole === 'department_staff'))
        <a href="{{ route('admin.complaints.index') }}" class="nav-link d-block py-2 px-3 mb-2 {{ request()->routeIs('admin.complaints.*') ? 'active' : '' }}" style="background: rgba(59, 130, 246, 0.08); margin-left: 20px; margin-right: 8px; border-left: 3px solid rgba(59, 130, 246, 0.4); border-radius: 6px;">
          <i data-feather="list" class="me-2" style="width: 18px; height: 18px;"></i> Complaints Regn
        </a>
        @endif
        @if($user && ($user->hasPermission('approvals') || $userRole === 'director' || $userRole === 'admin' || $userRole === 'garrison_engineer'))
        <a href="{{ route('admin.approvals.index') }}" class="nav-link d-block py-2 px-3 mb-2 {{ request()->routeIs('admin.approvals.*') && !request()->routeIs('admin.stock-approval.*') ? 'active' : '' }}" style="background: rgba(59, 130, 246, 0.08); margin-left: 20px; margin-right: 8px; border-left: 3px solid rgba(59, 130, 246, 0.4); border-radius: 6px;">
          <i data-feather="eye" class="me-2" style="width: 18px; height: 18px;"></i> Total Complaints
        </a>
        @endif
      </div>
    </div>
    @endif
    @if($user && ($user->hasPermission('spares') || $userRole === 'director' || $userRole === 'admin'))
    <a href="{{ route('admin.spares.index') }}" class="nav-link d-block py-2 px-3 mb-1 {{ request()->routeIs('admin.spares.*') ? 'active' : '' }}">
      <i data-feather="package" class="me-2"></i> Stock Products
    </a>
    @endif
    @if($user && ($user->hasPermission('reports') || $userRole === 'director' || $userRole === 'admin' || $userRole === 'garrison_engineer'))
    <a href="{{ route('admin.reports.index') }}" class="nav-link d-block py-2 px-3 mb-1 {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
      <i data-feather="bar-chart-2" class="me-2"></i> Reports
    </a>
    @endif
    @if($user && ($user->hasPermission('sla') || $userRole === 'director' || $userRole === 'admin'))
    <a href="{{ route('admin.sla.index') }}" class="nav-link d-block py-2 px-3 mb-1 {{ request()->routeIs('admin.sla.*') ? 'active' : '' }}">
      <i data-feather="clock" class="me-2"></i> SLA Rules
    </a>
    @endif
  </aside>

  <!-- MAIN CONTENT -->
  <main id="main-content" class="content" role="main" aria-label="Main content">
    @yield('content')
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Apply theme immediately to prevent flickering
    (function() {
      const savedTheme = localStorage.getItem('theme') || 'dark';
      document.documentElement.classList.add(`theme-${savedTheme}`);
    })();
    
    feather.replace();

    // Topbar functionality
    document.addEventListener('DOMContentLoaded', function() {
      // Initialize all icons including those in collapsed submenus
      setTimeout(() => {
        feather.replace();
        // Force initialization of all icons in submenus
        const allSubmenuIcons = document.querySelectorAll('.collapse i[data-feather], #complaintsManagementSubmenu i[data-feather]');
        allSubmenuIcons.forEach(icon => {
          if (!icon.querySelector('svg')) {
            // Create a temporary visible container to ensure icon renders
            const tempParent = icon.parentElement;
            if (tempParent) {
              feather.replace();
            }
          }
        });
      }, 300);
      // Global search functionality with autocomplete
      const globalSearch = document.getElementById('globalSearch');
      
      if (globalSearch) {
        let searchTimeout;
        let autocompleteDropdown;
        
        // Create autocomplete dropdown
        function createAutocompleteDropdown() {
          if (autocompleteDropdown) {
            autocompleteDropdown.remove();
          }
          
          autocompleteDropdown = document.createElement('div');
          autocompleteDropdown.className = 'search-autocomplete position-absolute bg-dark border rounded shadow-lg';
          autocompleteDropdown.style.cssText = `
            top: 100%;
            left: 0;
            right: 0;
            z-index: 1000;
            max-height: 300px;
            overflow-y: auto;
            overflow-x: hidden;
            display: none;
            width: 100%;
            max-width: 300px;
            word-wrap: break-word;
          `;
          
          const searchBox = globalSearch.closest('.search-box');
          if (searchBox) {
            searchBox.style.position = 'relative';
            searchBox.appendChild(autocompleteDropdown);
          }
        }
        
        // Show autocomplete dropdown
        function showAutocomplete(results) {
          if (!autocompleteDropdown) {
            createAutocompleteDropdown();
          }
          
          if (results.length === 0) {
            autocompleteDropdown.style.display = 'none';
            return;
          }
          
          autocompleteDropdown.innerHTML = results.map(result => `
            <a href="${result.url}" class="autocomplete-item d-block p-3 text-decoration-none text-white border-bottom" style="border-color: rgba(255,255,255,0.1) !important;">
              <div class="d-flex align-items-center" style="overflow: hidden;">
                <div class="me-3 flex-shrink-0">
                  <i data-feather="${result.icon}" class="text-${result.color}"></i>
                </div>
                <div class="flex-grow-1" style="min-width: 0; overflow: hidden;">
                  <div class="fw-bold" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 200px;">${result.title}</div>
                  <div class="text-muted small" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 150px;">${result.subtitle}</div>
                </div>
                <div class="text-muted small flex-shrink-0 ms-2">${result.type}</div>
              </div>
            </a>
          `).join('');
          
          // Add "View all results" link
          const searchTerm = globalSearch.value.trim();
          autocompleteDropdown.innerHTML += `
            <a href="/admin/search?q=${encodeURIComponent(searchTerm)}" class="autocomplete-item d-block p-3 text-decoration-none text-primary border-0" style="overflow: hidden;">
              <div class="text-center" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                <i data-feather="search" class="me-2"></i>
                View all results for "${searchTerm}"
              </div>
            </a>
          `;
          
          autocompleteDropdown.style.display = 'block';
          feather.replace();
        }
        
        // Hide autocomplete dropdown
        function hideAutocomplete() {
          if (autocompleteDropdown) {
            autocompleteDropdown.style.display = 'none';
          }
        }
        
        // Handle search input
        globalSearch.addEventListener('input', function() {
          const searchTerm = this.value.trim();
          
          // Clear previous timeout
          if (searchTimeout) {
            clearTimeout(searchTimeout);
          }
          
          if (searchTerm.length < 2) {
            hideAutocomplete();
            return;
          }
          
          // Debounce search
          searchTimeout = setTimeout(() => {
            fetch(`/admin/search/api?q=${encodeURIComponent(searchTerm)}`)
              .then(response => response.json())
              .then(data => {
                showAutocomplete(data.results || []);
              })
              .catch(error => {
                console.error('Search error:', error);
                hideAutocomplete();
              });
          }, 300);
        });
        
        // Handle Enter key
        globalSearch.addEventListener('keypress', function(e) {
          if (e.key === 'Enter') {
            const searchTerm = this.value.trim();
            if (searchTerm) {
              hideAutocomplete();
              window.location.href = `/admin/search?q=${encodeURIComponent(searchTerm)}`;
            }
          }
        });
        
        // Handle Escape key
        globalSearch.addEventListener('keydown', function(e) {
          if (e.key === 'Escape') {
            hideAutocomplete();
            this.blur();
          }
        });
        
        // Hide autocomplete when clicking outside
        document.addEventListener('click', function(e) {
          if (!globalSearch.contains(e.target) && !autocompleteDropdown?.contains(e.target)) {
            hideAutocomplete();
          }
        });
        
        // Handle autocomplete item clicks
        document.addEventListener('click', function(e) {
          if (e.target.closest('.autocomplete-item')) {
            hideAutocomplete();
          }
        });
        
        // Handle search button click
        const searchButton = document.getElementById('searchButton');
        
        if (searchButton) {
          searchButton.addEventListener('click', function() {
            const searchTerm = globalSearch.value.trim();
            if (searchTerm) {
              hideAutocomplete();
              window.location.href = `/admin/search?q=${encodeURIComponent(searchTerm)}`;
            }
          });
        }
      }

      // Notification functionality
      loadNotifications();
      
      // Settings and Help buttons now link to actual pages

      // Sidebar toggle for mobile
      const sidebarToggle = document.getElementById('sidebarToggle');
      if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
          const sidebar = document.querySelector('.sidebar');
          sidebar.style.display = sidebar.style.display === 'none' ? 'block' : 'none';
        });
      }

      // Handle Complaints Management submenu - Allow clicking anywhere on the toggle div
      const complaintsManagementToggle = document.getElementById('complaintsManagementToggle');
      const complaintsManagementSubmenu = document.getElementById('complaintsManagementSubmenu');
      
      if (complaintsManagementToggle && complaintsManagementSubmenu) {
        // Allow clicking anywhere on the toggle div to open/close submenu
        complaintsManagementToggle.addEventListener('click', function(e) {
          // Don't toggle if clicking on a submenu link (should be handled separately)
          if (e.target.closest('#complaintsManagementSubmenu')) {
            return;
          }
          
          // Prevent default Bootstrap behavior on arrow button to avoid double toggle
          if (e.target.closest('.nav-arrow-btn')) {
            e.preventDefault();
            e.stopPropagation();
          }
          
          // Toggle the collapse
          const collapseInstance = bootstrap.Collapse.getInstance(complaintsManagementSubmenu) || new bootstrap.Collapse(complaintsManagementSubmenu, {toggle: false});
          if (complaintsManagementSubmenu.classList.contains('show')) {
            collapseInstance.hide();
          } else {
            collapseInstance.show();
          }
        });
        
        // Prevent modal clicks from triggering submenu
        document.addEventListener('click', function(e) {
          // If clicking on any modal or modal content, prevent it from affecting submenu
          const isModalClick = e.target.closest('.modal') !== null || 
                               e.target.closest('[data-bs-toggle="modal"]') !== null ||
                               e.target.closest('[data-bs-target*="Modal"]') !== null;
          
          if (isModalClick) {
            // Ensure submenu stays closed when modals are clicked
            if (complaintsManagementSubmenu.classList.contains('show')) {
              const collapseInstance = bootstrap.Collapse.getInstance(complaintsManagementSubmenu);
              if (collapseInstance) {
                collapseInstance.hide();
              } else {
                complaintsManagementSubmenu.classList.remove('show');
              }
            }
          }
        }, true);
        
        // Prevent submenu links from closing the dropdown
        const submenuLinks = complaintsManagementSubmenu.querySelectorAll('a');
        submenuLinks.forEach(link => {
          link.addEventListener('click', function(e) {
            e.stopPropagation();
            // Prevent Bootstrap collapse from hiding
            e.stopImmediatePropagation();
          }, true);
        });
        
        // Prevent collapse from hiding when clicking on submenu links
        let preventHide = false;
        
        // Track clicks on submenu links
        submenuLinks.forEach(link => {
          link.addEventListener('mousedown', function() {
            preventHide = true;
          });
          link.addEventListener('click', function() {
            preventHide = true;
            // Reset after navigation
            setTimeout(() => {
              preventHide = false;
            }, 100);
          });
        });
        
        // Prevent hide event if clicking on submenu links
        complaintsManagementSubmenu.addEventListener('hide.bs.collapse', function(e) {
          if (preventHide) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            preventHide = false;
            return false;
          }
        });
        
        // Also prevent clicks outside from closing if clicking on submenu
        document.addEventListener('click', function(e) {
          const clickedLink = e.target.closest('#complaintsManagementSubmenu a');
          if (clickedLink) {
            preventHide = true;
            // Don't let Bootstrap collapse handle this click
            e.stopPropagation();
            // Get the collapse instance and prevent hiding
            const collapseInstance = bootstrap.Collapse.getInstance(complaintsManagementSubmenu);
            if (collapseInstance) {
              // Temporarily disable the collapse
              collapseInstance._isTransitioning = false;
            }
          }
        }, true);
        
        // Override Bootstrap collapse hide method for this specific submenu
        const collapseInstance = bootstrap.Collapse.getInstance(complaintsManagementSubmenu) || new bootstrap.Collapse(complaintsManagementSubmenu, {toggle: false});
        const originalHide = collapseInstance.hide;
        collapseInstance.hide = function() {
          if (preventHide) {
            preventHide = false;
            return;
          }
          return originalHide.call(this);
        };
        
        // Ensure submenu starts closed on page load (unless on a related route)
        const isRelatedRoute = @json(request()->routeIs('admin.complaints.*') || request()->routeIs('admin.category.*') || request()->routeIs('admin.complaint-titles.*') || (request()->routeIs('admin.approvals.*') && !request()->routeIs('admin.stock-approval.*')));
        if (!isRelatedRoute) {
          if (complaintsManagementSubmenu.classList.contains('show')) {
            complaintsManagementSubmenu.classList.remove('show');
          }
        }
        
        // Initialize icons - ensure Complaints Management icon is rendered
        const complaintsIcon = complaintsManagementToggle.querySelector('i[data-feather]');
        if (complaintsIcon) {
          // Force icon initialization
          setTimeout(() => {
            feather.replace();
            // Double check and re-render if needed
            if (!complaintsIcon.querySelector('svg')) {
              feather.replace();
            }
          }, 50);
        } else {
          feather.replace();
        }
      }

      // Handle submenu collapse/expand with arrow rotation and icon initialization
      const submenus = ['employeesSubmenu', 'complaintsManagementSubmenu'];
      
      submenus.forEach(submenuId => {
        const submenu = document.getElementById(submenuId);
        if (submenu) {
          const parent = submenu.closest('.nav-item-parent');
          const arrow = parent ? parent.querySelector('.nav-arrow') : null;
          
          if (arrow) {
            submenu.addEventListener('show.bs.collapse', function() {
              arrow.style.transform = 'rotate(180deg)';
              // Re-initialize Feather icons when submenu is shown - multiple attempts
              setTimeout(() => {
                feather.replace();
              }, 10);
              setTimeout(() => {
                feather.replace();
                // Specifically initialize icons in this submenu
                const submenuIcons = submenu.querySelectorAll('i[data-feather]');
                submenuIcons.forEach(icon => {
                  if (!icon.querySelector('svg')) {
                    feather.replace();
                  }
                });
              }, 100);
              setTimeout(() => {
                feather.replace();
              }, 300);
            });
            
            submenu.addEventListener('hide.bs.collapse', function() {
              arrow.style.transform = 'rotate(0deg)';
            });
            
            // Initialize arrow position based on current state
            if (submenu.classList.contains('show')) {
              arrow.style.transform = 'rotate(180deg)';
              // Initialize icons if submenu is already open
              setTimeout(() => {
                const submenuIcons = submenu.querySelectorAll('i[data-feather]');
                submenuIcons.forEach(icon => {
                  if (!icon.querySelector('svg')) {
                    feather.replace();
                  }
                });
              }, 100);
            } else {
              arrow.style.transform = 'rotate(0deg)';
            }
          }
          
          // Also initialize icons immediately for this submenu
          const submenuIcons = submenu.querySelectorAll('i[data-feather]');
          if (submenuIcons.length > 0) {
            setTimeout(() => {
              feather.replace();
            }, 200);
          }
          
          // Initialize icons if submenu is already shown
          if (submenu.classList.contains('show')) {
            feather.replace();
          }
        }
      });

      // View all notifications
      const viewAllNotifications = document.getElementById('viewAllNotifications');
      if (viewAllNotifications) {
        viewAllNotifications.addEventListener('click', function(e) {
          e.preventDefault();
          alert('View all notifications functionality coming soon!');
        });
      }
    });

    // Load notifications
    function loadNotifications() {
      fetch('/admin/notifications/api', { headers: { 'X-Requested-With': 'XMLHttpRequest' }})
        .then(res => res.json())
        .then(data => {
          const list = data.notifications || [];
          const unread = typeof data.unread === 'number' ? data.unread : (list.filter(n => !n.read).length);
          updateNotificationCount(unread);
          updateNotificationList(list);
        })
        .catch(() => {
          // On error, show no notifications to avoid mock data
          updateNotificationCount(0);
          updateNotificationList([]);
        });
    }

    // Update notification count
    function updateNotificationCount(count) {
      const countElement = document.getElementById('notificationCount');
      const totalElement = document.getElementById('notificationTotal');
      
      if (countElement) {
        countElement.textContent = count;
        countElement.style.display = count > 0 ? 'inline' : 'none';
      }
      
      if (totalElement) {
        totalElement.textContent = count;
      }
    }

    // Update notification list
    function updateNotificationList(notifications) {
      const listElement = document.getElementById('notificationList');
      
      if (notifications.length === 0) {
        listElement.innerHTML = `
          <div class="text-center py-3 text-muted">
            <i data-feather="bell-off" class="feather-lg mb-2"></i>
            <div>No notifications</div>
          </div>
        `;
      } else {
        listElement.innerHTML = notifications.map(notification => `
          <a href="${notification.url || '#'}" class="dropdown-item notification-item">
            <div class="d-flex align-items-start">
              <div class="notification-icon me-3">
                <i data-feather="${notification.icon || 'bell'}" class="text-${notification.type || 'primary'}"></i>
              </div>
              <div class="flex-grow-1">
                <div class="notification-title">${notification.title}</div>
                <div class="notification-message text-muted small">${notification.message}</div>
                <div class="notification-time text-muted small">${notification.time}</div>
              </div>
            </div>
          </a>
        `).join('');
      }
      
      feather.replace();
    }

    // Auto-refresh notifications every 30 seconds
    setInterval(loadNotifications, 30000);

    // Auto-hide success messages after 3 seconds
    document.addEventListener('DOMContentLoaded', function() {
      const successAlerts = document.querySelectorAll('.alert-success.alert-dismissible');
      successAlerts.forEach(function(alert) {
        setTimeout(function() {
          const bsAlert = new bootstrap.Alert(alert);
          if (bsAlert && alert.parentNode) {
            bsAlert.close();
          }
        }, 3000); // 3 seconds
      });
    });
  </script>
  

  <script>
    // Generic function to open modal and load content
    function openModal(modalId, modalBodyId, route, title) {
      const modalElement = document.getElementById(modalId);
      const modalBody = document.getElementById(modalBodyId);
      
      if (!modalElement || !modalBody) {
        window.location.href = route;
        return;
      }
      
      modalBody.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';
      
      document.body.classList.add('modal-open-blur');
      
      const modal = new bootstrap.Modal(modalElement, {
        backdrop: false,
        keyboard: true,
        focus: true
      });
      modal.show();
      
      const removeBackdrop = () => {
        const backdrops = document.querySelectorAll('.modal-backdrop');
        backdrops.forEach(backdrop => backdrop.remove());
      };
      
      const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
          mutation.addedNodes.forEach((node) => {
            if (node.nodeType === 1 && node.classList && node.classList.contains('modal-backdrop')) {
              node.remove();
            }
          });
        });
        removeBackdrop();
      });
      
      observer.observe(document.body, {
        childList: true,
        subtree: true
      });
      
      removeBackdrop();
      setTimeout(removeBackdrop, 10);
      setTimeout(removeBackdrop, 50);
      setTimeout(removeBackdrop, 100);
      
      modalElement.addEventListener('hidden.bs.modal', function() {
        observer.disconnect();
        removeBackdrop();
      }, { once: true });
      
      fetch(route + '?format=html', {
        method: 'GET',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'text/html',
        },
        credentials: 'same-origin'
      })
      .then(response => {
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
          return response.json().then(data => {
            throw new Error('Received JSON instead of HTML.');
          });
        }
        return response.text();
      })
      .then(html => {
        if (html.trim().startsWith('{')) {
          console.error('Received JSON instead of HTML');
          modalBody.innerHTML = '<div class="text-center py-5 text-danger">Error: Server returned JSON instead of HTML.</div>';
          return;
        }
        
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        
        let contentSection = doc.querySelector('section.content');
        if (!contentSection) {
          contentSection = doc.querySelector('.content');
        }
        if (!contentSection) {
          const mainContent = doc.querySelector('main') || doc.querySelector('[role="main"]');
          if (mainContent) {
            contentSection = mainContent;
          } else {
            contentSection = doc.body;
          }
        }
        
        let modalContent = '';
        const allCards = contentSection.querySelectorAll('.card-glass');
        const seenCards = new Set();
        
        allCards.forEach(card => {
          const cardHTML = card.outerHTML;
          const cardId = cardHTML.substring(0, 300);
          if (!seenCards.has(cardId)) {
            seenCards.add(cardId);
            modalContent += '<div class="mb-3">' + cardHTML + '</div>';
          }
        });
        
        if (modalContent) {
          modalBody.innerHTML = modalContent;
          
          // Move edit modals outside of modal body to document body so Bootstrap can handle them properly
          // 1) Capture any edit modals present anywhere in the fetched document
          const fetchedEditModals = doc.querySelectorAll('.modal[id^="edit"]');
          fetchedEditModals.forEach(editModal => {
            // If an element with same id already exists in body, replace it
            const existing = document.getElementById(editModal.id);
            if (existing && existing.parentElement) {
              existing.parentElement.removeChild(existing);
            }
            document.body.appendChild(editModal);
            // Set higher z-index for nested modals (parent modal is usually 1055, so edit modals should be higher)
            editModal.style.zIndex = '1070';
            editModal.style.display = '';
          });
          
          // 2) Also move any edit modals that were inside the extracted section (safety)
          const editModals = modalBody.querySelectorAll('.modal[id^="edit"]');
          editModals.forEach(editModal => {
            // Remove from modalBody first
            editModal.remove();
            // Append to document.body
            document.body.appendChild(editModal);
            // Set higher z-index for nested modals (parent modal is usually 1055, so edit modals should be higher)
            editModal.style.zIndex = '1070';
            // Ensure modal is visible and properly initialized
            editModal.style.display = '';
          });
          
          // Extract and execute scripts from the loaded content
          const scripts = contentSection.querySelectorAll('script');
          scripts.forEach(script => {
            const newScript = document.createElement('script');
            if (script.src) {
              newScript.src = script.src;
            } else {
              newScript.textContent = script.textContent;
            }
            document.body.appendChild(newScript);
          });
          
          // Re-initialize edit modal handlers after content is loaded
          setTimeout(() => {
            feather.replace();
            
            // Direct click handlers for City buttons - MUST work
            const cityButtons = modalBody.querySelectorAll('button[data-bs-target="#editCityModal"], button[data-modal-target="#editCityModal"]');
            cityButtons.forEach(btn => {
              // Remove data-bs-toggle to prevent Bootstrap auto-handling
              btn.removeAttribute('data-bs-toggle');
              
              // Add direct click handler with highest priority
              btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const status = this.getAttribute('data-status');
                
                // Wait a bit to ensure modal is in DOM
                setTimeout(() => {
                  const editCityModalEl = document.getElementById('editCityModal');
                  
                  if (!editCityModalEl) {
                    console.error('editCityModal not found in DOM');
                    return;
                  }
                  
                  const form = document.getElementById('editCityForm');
                  const nameInput = document.getElementById('editCityName');
                  const statusSelect = document.getElementById('editCityStatus');

                  if (form && id) {
                    form.action = `${window.location.origin}/admin/city/${id}`;
                  }
                  if (nameInput) nameInput.value = name || '';
                  if (statusSelect) statusSelect.value = status || 'active';
                  
                  // Prevent parent modal from closing
                  const parentModal = modalElement;
                  if (parentModal) {
                    parentModal.classList.add('modal-static');
                    const parentBackdrop = document.querySelector('.modal-backdrop:not(:last-child)');
                    if (parentBackdrop) {
                      parentBackdrop.style.pointerEvents = 'none';
                    }
                  }
                  
                  editCityModalEl.style.zIndex = '1070';
                  let modal = bootstrap.Modal.getInstance(editCityModalEl);
                  if (!modal) {
                    modal = new bootstrap.Modal(editCityModalEl, {
                      backdrop: true,
                      keyboard: true,
                      focus: true
                    });
                  }
                  modal.show();
                  
                  setTimeout(() => {
                    const backdrops = document.querySelectorAll('.modal-backdrop');
                    backdrops.forEach((backdrop, index) => {
                      if (index === backdrops.length - 1) {
                        backdrop.style.zIndex = '1069';
                      } else {
                        backdrop.style.zIndex = '1054';
                      }
                    });
                    if (parentModal) {
                      parentModal.style.zIndex = '1055';
                      parentModal.classList.add('show');
                    }
                  }, 10);
                }, 50);
              }, true); // Use capture phase for highest priority
            });
            
            // Direct click handlers for Designation buttons - MUST work
            const designationButtons = modalBody.querySelectorAll('button[data-bs-target="#editDesignationModal"], button[data-modal-target="#editDesignationModal"]');
            designationButtons.forEach(btn => {
              // Remove data-bs-toggle to prevent Bootstrap auto-handling
              btn.removeAttribute('data-bs-toggle');
              
              // Add direct click handler with highest priority
              btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                
                const id = this.getAttribute('data-id');
                const category = this.getAttribute('data-category');
                const name = this.getAttribute('data-name');
                const status = this.getAttribute('data-status');
                
                // Wait a bit to ensure modal is in DOM
                setTimeout(() => {
                  const editDesignationModalEl = document.getElementById('editDesignationModal');
                  
                  if (!editDesignationModalEl) {
                    console.error('editDesignationModal not found in DOM');
                    return;
                  }
                  
                  const form = document.getElementById('editDesignationForm');
                  const categorySelect = document.getElementById('editDesignationCategory');
                  const nameInput = document.getElementById('editDesignationName');
                  const statusSelect = document.getElementById('editDesignationStatus');

                  if (form && id) {
                    form.action = `${window.location.origin}/admin/designation/${id}`;
                  }
                  if (categorySelect && category) categorySelect.value = category || '';
                  if (nameInput) nameInput.value = name || '';
                  if (statusSelect) statusSelect.value = status || 'active';
                  
                  // Prevent parent modal from closing
                  const parentModal = modalElement;
                  if (parentModal) {
                    parentModal.classList.add('modal-static');
                    const parentBackdrop = document.querySelector('.modal-backdrop:not(:last-child)');
                    if (parentBackdrop) {
                      parentBackdrop.style.pointerEvents = 'none';
                    }
                  }
                  
                  editDesignationModalEl.style.zIndex = '1070';
                  let modal = bootstrap.Modal.getInstance(editDesignationModalEl);
                  if (!modal) {
                    modal = new bootstrap.Modal(editDesignationModalEl, {
                      backdrop: true,
                      keyboard: true,
                      focus: true
                    });
                  }
                  modal.show();
                  
                  setTimeout(() => {
                    const backdrops = document.querySelectorAll('.modal-backdrop');
                    backdrops.forEach((backdrop, index) => {
                      if (index === backdrops.length - 1) {
                        backdrop.style.zIndex = '1069';
                      } else {
                        backdrop.style.zIndex = '1054';
                      }
                    });
                    if (parentModal) {
                      parentModal.style.zIndex = '1055';
                      parentModal.classList.add('show');
                    }
                  }, 10);
                }, 50);
              }, true); // Use capture phase for highest priority
            });
            
            // Global safety: capture clicks on any button with data-bs-target or data-modal-target starting with #edit
            // Works even if inner listeners fail; only runs while a modal is open
            const globalEditHandler = function(e) {
              const btn = e.target.closest('button[data-bs-target^="#edit"], button[data-modal-target^="#edit"]');
              if (!btn) return;
              const openParentModal = document.querySelector('.modal.show');
              if (!openParentModal || !openParentModal.contains(btn)) return;
              e.preventDefault();
              e.stopPropagation();
              const targetSelector = btn.getAttribute('data-bs-target') || btn.getAttribute('data-modal-target');
              const modalEl = document.querySelector(targetSelector);
              if (!modalEl) return;
              
              // Prevent parent modal from closing
              if (openParentModal) {
                openParentModal.classList.add('modal-static');
                const parentBackdrop = document.querySelector('.modal-backdrop:not(:last-child)');
                if (parentBackdrop) {
                  parentBackdrop.style.pointerEvents = 'none';
                }
              }
              
              // Populate form fields based on button data attributes
              if (targetSelector === '#editDesignationModal') {
                const id = btn.getAttribute('data-id');
                const category = btn.getAttribute('data-category');
                const name = btn.getAttribute('data-name');
                const status = btn.getAttribute('data-status');
                const form = document.getElementById('editDesignationForm');
                const categorySelect = document.getElementById('editDesignationCategory');
                const nameInput = document.getElementById('editDesignationName');
                const statusSelect = document.getElementById('editDesignationStatus');
                if (form && id) form.action = `${window.location.origin}/admin/designation/${id}`;
                if (categorySelect && category) categorySelect.value = category || '';
                if (nameInput) nameInput.value = name || '';
                if (statusSelect) statusSelect.value = status || 'active';
              }
              
              // Ensure higher z-index for nested modals
              modalEl.style.zIndex = '1070';
              let modal = bootstrap.Modal.getInstance(modalEl);
              if (!modal) {
                modal = new bootstrap.Modal(modalEl, { backdrop: true, keyboard: true, focus: true });
              }
              modal.show();
              setTimeout(() => {
                const backdrops = document.querySelectorAll('.modal-backdrop');
                backdrops.forEach((backdrop, index) => {
                  if (index === backdrops.length - 1) {
                    backdrop.style.zIndex = '1069';
                  } else {
                    backdrop.style.zIndex = '1054';
                  }
                });
                if (openParentModal) {
                  openParentModal.style.zIndex = '1055';
                  openParentModal.classList.add('show');
                }
              }, 10);
            };
            // Bind once per open cycle
            if (!window.__globalEditHandlerBound) {
              window.__globalEditHandlerBound = true;
              document.addEventListener('click', globalEditHandler, true);
            }
            
            // Use event delegation on modalBody for all edit buttons
            modalBody.addEventListener('click', function(e) {
              // Check for buttons with data-modal-target (our custom attribute) OR data-bs-target
              const buttonWithTarget = e.target.closest('button[data-modal-target], button[data-bs-target]');
              if (buttonWithTarget) {
                const targetSelector = buttonWithTarget.getAttribute('data-modal-target') || buttonWithTarget.getAttribute('data-bs-target');
                // Handle City
                if (targetSelector === '#editCityModal') {
                  const button = buttonWithTarget;
                e.preventDefault();
                e.stopPropagation();
                const id = button.getAttribute('data-id');
                const name = button.getAttribute('data-name');
                const status = button.getAttribute('data-status');
                const editCityModalEl = document.getElementById('editCityModal');
                const form = document.getElementById('editCityForm');
                const nameInput = document.getElementById('editCityName');
                const statusSelect = document.getElementById('editCityStatus');

                if (form && id) {
                  form.action = `${window.location.origin}/admin/city/${id}`;
                }
                if (nameInput) nameInput.value = name || '';
                if (statusSelect) statusSelect.value = status || 'active';
                
                if (editCityModalEl) {
                  // Prevent parent modal from closing
                  const parentModal = modalElement;
                  if (parentModal) {
                    parentModal.classList.add('modal-static');
                    // Prevent backdrop click from closing parent
                    const parentBackdrop = document.querySelector('.modal-backdrop:not(:last-child)');
                    if (parentBackdrop) {
                      parentBackdrop.style.pointerEvents = 'none';
                    }
                  }
                  // Ensure z-index is set
                  editCityModalEl.style.zIndex = '1070';
                  // Check if modal instance already exists
                  let modal = bootstrap.Modal.getInstance(editCityModalEl);
                  if (!modal) {
                    modal = new bootstrap.Modal(editCityModalEl, {
                      backdrop: true,
                      keyboard: true,
                      focus: true
                    });
                  }
                  modal.show();
                  // Ensure backdrop has correct z-index
                  setTimeout(() => {
                    const backdrops = document.querySelectorAll('.modal-backdrop');
                    backdrops.forEach((backdrop, index) => {
                      if (index === backdrops.length - 1) {
                        // Last backdrop (for edit modal) should be on top
                        backdrop.style.zIndex = '1069';
                      } else {
                        // Previous backdrops should be below
                        backdrop.style.zIndex = '1054';
                      }
                    });
                    // Keep parent modal visible
                    if (parentModal) {
                      parentModal.style.zIndex = '1055';
                      parentModal.classList.add('show');
                    }
                  }, 10);
                }
                  return;
                }
                // Handle Sector
                if (targetSelector === '#editSectorModal') {
                  const sectorButton = buttonWithTarget;
                e.preventDefault();
                e.stopPropagation();
                const id = sectorButton.getAttribute('data-id');
                const cityId = sectorButton.getAttribute('data-city-id');
                const name = sectorButton.getAttribute('data-name');
                const status = sectorButton.getAttribute('data-status');
                const editSectorModalEl = document.getElementById('editSectorModal');
                const form = document.getElementById('editSectorForm');
                const nameInput = document.getElementById('editSectorName');
                const citySelect = document.getElementById('editSectorCityId');
                const statusSelect = document.getElementById('editSectorStatus');

                if (form && id) {
                  form.action = `${window.location.origin}/admin/sector/${id}`;
                }
                if (nameInput) nameInput.value = name || '';
                if (citySelect && cityId) citySelect.value = cityId || '';
                if (statusSelect) statusSelect.value = status || 'active';
                
                if (editSectorModalEl) {
                  // Prevent parent modal from closing
                  const parentModal = modalElement;
                  if (parentModal) {
                    parentModal.classList.add('modal-static');
                    const parentBackdrop = document.querySelector('.modal-backdrop:not(:last-child)');
                    if (parentBackdrop) {
                      parentBackdrop.style.pointerEvents = 'none';
                    }
                  }
                  // Ensure z-index is set
                  editSectorModalEl.style.zIndex = '1070';
                  // Check if modal instance already exists
                  let modal = bootstrap.Modal.getInstance(editSectorModalEl);
                  if (!modal) {
                    modal = new bootstrap.Modal(editSectorModalEl, {
                      backdrop: true,
                      keyboard: true,
                      focus: true
                    });
                  }
                  modal.show();
                  // Ensure backdrop has correct z-index
                  setTimeout(() => {
                    const backdrops = document.querySelectorAll('.modal-backdrop');
                    backdrops.forEach((backdrop, index) => {
                      if (index === backdrops.length - 1) {
                        backdrop.style.zIndex = '1069';
                      } else {
                        backdrop.style.zIndex = '1054';
                      }
                    });
                    if (parentModal) {
                      parentModal.style.zIndex = '1055';
                      parentModal.classList.add('show');
                    }
                  }, 10);
                }
                  return;
                }
                // Handle Designation
                if (targetSelector === '#editDesignationModal') {
                  const designationButton = buttonWithTarget;
                  e.preventDefault();
                  e.stopPropagation();
                  e.stopImmediatePropagation();
                  
                  const id = designationButton.getAttribute('data-id');
                  const category = designationButton.getAttribute('data-category');
                  const name = designationButton.getAttribute('data-name');
                  const status = designationButton.getAttribute('data-status');
                  const editDesignationModalEl = document.getElementById('editDesignationModal');
                  
                  if (!editDesignationModalEl) {
                    console.error('editDesignationModal not found');
                    return;
                  }
                  
                  const form = document.getElementById('editDesignationForm');
                  const categorySelect = document.getElementById('editDesignationCategory');
                  const nameInput = document.getElementById('editDesignationName');
                  const statusSelect = document.getElementById('editDesignationStatus');

                  if (form && id) {
                    form.action = `${window.location.origin}/admin/designation/${id}`;
                  }
                  if (categorySelect && category) categorySelect.value = category || '';
                  if (nameInput) nameInput.value = name || '';
                  if (statusSelect) statusSelect.value = status || 'active';
                  
                  // Prevent parent modal from closing
                  const parentModal = modalElement;
                  if (parentModal) {
                    parentModal.classList.add('modal-static');
                    const parentBackdrop = document.querySelector('.modal-backdrop:not(:last-child)');
                    if (parentBackdrop) {
                      parentBackdrop.style.pointerEvents = 'none';
                    }
                  }
                  
                  // Ensure z-index is set
                  editDesignationModalEl.style.zIndex = '1070';
                  
                  // Check if modal instance already exists
                  let modal = bootstrap.Modal.getInstance(editDesignationModalEl);
                  if (!modal) {
                    modal = new bootstrap.Modal(editDesignationModalEl, {
                      backdrop: true,
                      keyboard: true,
                      focus: true
                    });
                  }
                  
                  modal.show();
                  
                  // Ensure backdrop has correct z-index
                  setTimeout(() => {
                    const backdrops = document.querySelectorAll('.modal-backdrop');
                    backdrops.forEach((backdrop, index) => {
                      if (index === backdrops.length - 1) {
                        backdrop.style.zIndex = '1069';
                      } else {
                        backdrop.style.zIndex = '1054';
                      }
                    });
                    if (parentModal) {
                      parentModal.style.zIndex = '1055';
                      parentModal.classList.add('show');
                    }
                  }, 10);
                  return;
                }
                // Handle Category
                if (targetSelector === '#editCategoryModal') {
                  const categoryButton = buttonWithTarget;
                e.preventDefault();
                e.stopPropagation();
                const id = categoryButton.getAttribute('data-id');
                const name = categoryButton.getAttribute('data-name');
                const description = categoryButton.getAttribute('data-description');
                const editCategoryModalEl = document.getElementById('editCategoryModal');
                const form = document.getElementById('editCategoryForm');
                const nameInput = document.getElementById('editCategoryName');
                const descriptionInput = document.getElementById('editCategoryDescription');

                if (form && id) {
                  form.action = `${window.location.origin}/admin/category/${id}`;
                }
                if (nameInput) nameInput.value = name || '';
                if (descriptionInput) descriptionInput.value = description || '';
                
                if (editCategoryModalEl) {
                  // Prevent parent modal from closing
                  const parentModal = modalElement;
                  if (parentModal) {
                    parentModal.classList.add('modal-static');
                    const parentBackdrop = document.querySelector('.modal-backdrop:not(:last-child)');
                    if (parentBackdrop) {
                      parentBackdrop.style.pointerEvents = 'none';
                    }
                  }
                  // Ensure z-index is set
                  editCategoryModalEl.style.zIndex = '1070';
                  // Check if modal instance already exists
                  let modal = bootstrap.Modal.getInstance(editCategoryModalEl);
                  if (!modal) {
                    modal = new bootstrap.Modal(editCategoryModalEl, {
                      backdrop: true,
                      keyboard: true,
                      focus: true
                    });
                  }
                  modal.show();
                  // Ensure backdrop has correct z-index
                  setTimeout(() => {
                    const backdrops = document.querySelectorAll('.modal-backdrop');
                    backdrops.forEach((backdrop, index) => {
                      if (index === backdrops.length - 1) {
                        backdrop.style.zIndex = '1069';
                      } else {
                        backdrop.style.zIndex = '1054';
                      }
                    });
                    if (parentModal) {
                      parentModal.style.zIndex = '1055';
                      parentModal.classList.add('show');
                    }
                  }, 10);
                }
                  return;
                }
              }
              
              // Fallback: Also check for data-bs-target (in case some buttons weren't processed)
              const button = e.target.closest('button[data-bs-target="#editCityModal"]');
              if (button) {
                e.preventDefault();
                e.stopPropagation();
                const id = button.getAttribute('data-id');
                const name = button.getAttribute('data-name');
                const status = button.getAttribute('data-status');
                const editCityModalEl = document.getElementById('editCityModal');
                const form = document.getElementById('editCityForm');
                const nameInput = document.getElementById('editCityName');
                const statusSelect = document.getElementById('editCityStatus');

                if (form && id) {
                  form.action = `${window.location.origin}/admin/city/${id}`;
                }
                if (nameInput) nameInput.value = name || '';
                if (statusSelect) statusSelect.value = status || 'active';
                
                if (editCityModalEl) {
                  // Prevent parent modal from closing
                  const parentModal = modalElement;
                  if (parentModal) {
                    parentModal.classList.add('modal-static');
                    const parentBackdrop = document.querySelector('.modal-backdrop:not(:last-child)');
                    if (parentBackdrop) {
                      parentBackdrop.style.pointerEvents = 'none';
                    }
                  }
                  editCityModalEl.style.zIndex = '1070';
                  let modal = bootstrap.Modal.getInstance(editCityModalEl);
                  if (!modal) {
                    modal = new bootstrap.Modal(editCityModalEl, {
                      backdrop: true,
                      keyboard: true,
                      focus: true
                    });
                  }
                  modal.show();
                  setTimeout(() => {
                    const backdrops = document.querySelectorAll('.modal-backdrop');
                    backdrops.forEach((backdrop, index) => {
                      if (index === backdrops.length - 1) {
                        backdrop.style.zIndex = '1069';
                      } else {
                        backdrop.style.zIndex = '1054';
                      }
                    });
                    if (parentModal) {
                      parentModal.style.zIndex = '1055';
                      parentModal.classList.add('show');
                    }
                  }, 10);
                }
                return;
              }
              
              const sectorButton = e.target.closest('button[data-bs-target="#editSectorModal"]');
              if (sectorButton) {
                e.preventDefault();
                e.stopPropagation();
                const id = sectorButton.getAttribute('data-id');
                const cityId = sectorButton.getAttribute('data-city-id');
                const name = sectorButton.getAttribute('data-name');
                const status = sectorButton.getAttribute('data-status');
                const editSectorModalEl = document.getElementById('editSectorModal');
                const form = document.getElementById('editSectorForm');
                const nameInput = document.getElementById('editSectorName');
                const citySelect = document.getElementById('editSectorCityId');
                const statusSelect = document.getElementById('editSectorStatus');

                if (form && id) {
                  form.action = `${window.location.origin}/admin/sector/${id}`;
                }
                if (nameInput) nameInput.value = name || '';
                if (citySelect && cityId) citySelect.value = cityId || '';
                if (statusSelect) statusSelect.value = status || 'active';
                
                if (editSectorModalEl) {
                  // Prevent parent modal from closing
                  const parentModal = modalElement;
                  if (parentModal) {
                    parentModal.classList.add('modal-static');
                    const parentBackdrop = document.querySelector('.modal-backdrop:not(:last-child)');
                    if (parentBackdrop) {
                      parentBackdrop.style.pointerEvents = 'none';
                    }
                  }
                  editSectorModalEl.style.zIndex = '1070';
                  let modal = bootstrap.Modal.getInstance(editSectorModalEl);
                  if (!modal) {
                    modal = new bootstrap.Modal(editSectorModalEl, {
                      backdrop: true,
                      keyboard: true,
                      focus: true
                    });
                  }
                  modal.show();
                  setTimeout(() => {
                    const backdrops = document.querySelectorAll('.modal-backdrop');
                    backdrops.forEach((backdrop, index) => {
                      if (index === backdrops.length - 1) {
                        backdrop.style.zIndex = '1069';
                      } else {
                        backdrop.style.zIndex = '1054';
                      }
                    });
                    if (parentModal) {
                      parentModal.style.zIndex = '1055';
                      parentModal.classList.add('show');
                    }
                  }, 10);
                }
                return;
              }
              
              const designationButton = e.target.closest('button[data-bs-target="#editDesignationModal"]');
              if (designationButton) {
                e.preventDefault();
                e.stopPropagation();
                const id = designationButton.getAttribute('data-id');
                const category = designationButton.getAttribute('data-category');
                const name = designationButton.getAttribute('data-name');
                const status = designationButton.getAttribute('data-status');
                const editDesignationModalEl = document.getElementById('editDesignationModal');
                const form = document.getElementById('editDesignationForm');
                const categorySelect = document.getElementById('editDesignationCategory');
                const nameInput = document.getElementById('editDesignationName');
                const statusSelect = document.getElementById('editDesignationStatus');

                if (form && id) {
                  form.action = `${window.location.origin}/admin/designation/${id}`;
                }
                if (categorySelect && category) categorySelect.value = category || '';
                if (nameInput) nameInput.value = name || '';
                if (statusSelect) statusSelect.value = status || 'active';
                
                if (editDesignationModalEl) {
                  // Prevent parent modal from closing
                  const parentModal = modalElement;
                  if (parentModal) {
                    parentModal.classList.add('modal-static');
                    const parentBackdrop = document.querySelector('.modal-backdrop:not(:last-child)');
                    if (parentBackdrop) {
                      parentBackdrop.style.pointerEvents = 'none';
                    }
                  }
                  editDesignationModalEl.style.zIndex = '1070';
                  let modal = bootstrap.Modal.getInstance(editDesignationModalEl);
                  if (!modal) {
                    modal = new bootstrap.Modal(editDesignationModalEl, {
                      backdrop: true,
                      keyboard: true,
                      focus: true
                    });
                  }
                  modal.show();
                  setTimeout(() => {
                    const backdrops = document.querySelectorAll('.modal-backdrop');
                    backdrops.forEach((backdrop, index) => {
                      if (index === backdrops.length - 1) {
                        backdrop.style.zIndex = '1069';
                      } else {
                        backdrop.style.zIndex = '1054';
                      }
                    });
                    if (parentModal) {
                      parentModal.style.zIndex = '1055';
                      parentModal.classList.add('show');
                    }
                  }, 10);
                }
                return;
              }
              
              const categoryButton = e.target.closest('button[data-bs-target="#editCategoryModal"]');
              if (categoryButton) {
                e.preventDefault();
                e.stopPropagation();
                const id = categoryButton.getAttribute('data-id');
                const name = categoryButton.getAttribute('data-name');
                const description = categoryButton.getAttribute('data-description');
                const editCategoryModalEl = document.getElementById('editCategoryModal');
                const form = document.getElementById('editCategoryForm');
                const nameInput = document.getElementById('editCategoryName');
                const descriptionInput = document.getElementById('editCategoryDescription');

                if (form && id) {
                  form.action = `${window.location.origin}/admin/category/${id}`;
                }
                if (nameInput) nameInput.value = name || '';
                if (descriptionInput) descriptionInput.value = description || '';
                
                if (editCategoryModalEl) {
                  // Prevent parent modal from closing
                  const parentModal = modalElement;
                  if (parentModal) {
                    parentModal.classList.add('modal-static');
                    const parentBackdrop = document.querySelector('.modal-backdrop:not(:last-child)');
                    if (parentBackdrop) {
                      parentBackdrop.style.pointerEvents = 'none';
                    }
                  }
                  editCategoryModalEl.style.zIndex = '1070';
                  let modal = bootstrap.Modal.getInstance(editCategoryModalEl);
                  if (!modal) {
                    modal = new bootstrap.Modal(editCategoryModalEl, {
                      backdrop: true,
                      keyboard: true,
                      focus: true
                    });
                  }
                  modal.show();
                  setTimeout(() => {
                    const backdrops = document.querySelectorAll('.modal-backdrop');
                    backdrops.forEach((backdrop, index) => {
                      if (index === backdrops.length - 1) {
                        backdrop.style.zIndex = '1069';
                      } else {
                        backdrop.style.zIndex = '1054';
                      }
                    });
                    if (parentModal) {
                      parentModal.style.zIndex = '1055';
                      parentModal.classList.add('show');
                    }
                  }, 10);
                }
                return;
              }

              // Fallback: generic handler if data-bs-target is missing
              const anyBtn = e.target.closest('button');
              if (anyBtn && anyBtn.hasAttribute('data-id')) {
                const id = anyBtn.getAttribute('data-id');
                const name = anyBtn.getAttribute('data-name') || '';
                const status = anyBtn.getAttribute('data-status') || '';
                const cityId = anyBtn.getAttribute('data-city-id') || '';
                const category = anyBtn.getAttribute('data-category') || '';
                const description = anyBtn.getAttribute('data-description');
                
                // Decide which modal to open based on available data attributes and existing modals
                const hasCityModal = !!document.getElementById('editCityModal');
                const hasSectorModal = !!document.getElementById('editSectorModal');
                const hasDesignationModal = !!document.getElementById('editDesignationModal');
                const hasCategoryModal = !!document.getElementById('editCategoryModal');
                
                // Sector: has city-id
                if (hasSectorModal && cityId) {
                  e.preventDefault();
                  e.stopPropagation();
                  const editSectorModalEl = document.getElementById('editSectorModal');
                  const form = document.getElementById('editSectorForm');
                  const nameInput = document.getElementById('editSectorName');
                  const citySelect = document.getElementById('editSectorCityId');
                  const statusSelect = document.getElementById('editSectorStatus');
                  if (form && id) form.action = `${window.location.origin}/admin/sector/${id}`;
                  if (nameInput) nameInput.value = name;
                  if (citySelect) citySelect.value = cityId;
                  if (statusSelect) statusSelect.value = status || 'active';
                  if (editSectorModalEl) {
                    const parentModal = modalElement;
                    if (parentModal) {
                      parentModal.classList.add('modal-static');
                      const parentBackdrop = document.querySelector('.modal-backdrop:not(:last-child)');
                      if (parentBackdrop) {
                        parentBackdrop.style.pointerEvents = 'none';
                      }
                    }
                    editSectorModalEl.style.zIndex = '1070';
                    let modal = bootstrap.Modal.getInstance(editSectorModalEl);
                    if (!modal) modal = new bootstrap.Modal(editSectorModalEl, { backdrop: true, keyboard: true, focus: true });
                    modal.show();
                    setTimeout(() => {
                      const backdrops = document.querySelectorAll('.modal-backdrop');
                      backdrops.forEach((backdrop, index) => {
                        if (index === backdrops.length - 1) {
                          backdrop.style.zIndex = '1069';
                        } else {
                          backdrop.style.zIndex = '1054';
                        }
                      });
                      if (parentModal) {
                        parentModal.style.zIndex = '1055';
                        parentModal.classList.add('show');
                      }
                    }, 10);
                  }
                  return;
                }
                
                // Designation: has category and status
                if (hasDesignationModal && category) {
                  e.preventDefault();
                  e.stopPropagation();
                  const editDesignationModalEl = document.getElementById('editDesignationModal');
                  const form = document.getElementById('editDesignationForm');
                  const categorySelect = document.getElementById('editDesignationCategory');
                  const nameInput = document.getElementById('editDesignationName');
                  const statusSelect = document.getElementById('editDesignationStatus');
                  if (form && id) form.action = `${window.location.origin}/admin/designation/${id}`;
                  if (categorySelect) categorySelect.value = category;
                  if (nameInput) nameInput.value = name;
                  if (statusSelect) statusSelect.value = status || 'active';
                  if (editDesignationModalEl) {
                    const parentModal = modalElement;
                    if (parentModal) {
                      parentModal.classList.add('modal-static');
                      const parentBackdrop = document.querySelector('.modal-backdrop:not(:last-child)');
                      if (parentBackdrop) {
                        parentBackdrop.style.pointerEvents = 'none';
                      }
                    }
                    editDesignationModalEl.style.zIndex = '1070';
                    let modal = bootstrap.Modal.getInstance(editDesignationModalEl);
                    if (!modal) modal = new bootstrap.Modal(editDesignationModalEl, { backdrop: true, keyboard: true, focus: true });
                    modal.show();
                    setTimeout(() => {
                      const backdrops = document.querySelectorAll('.modal-backdrop');
                      backdrops.forEach((backdrop, index) => {
                        if (index === backdrops.length - 1) {
                          backdrop.style.zIndex = '1069';
                        } else {
                          backdrop.style.zIndex = '1054';
                        }
                      });
                      if (parentModal) {
                        parentModal.style.zIndex = '1055';
                        parentModal.classList.add('show');
                      }
                    }, 10);
                  }
                  return;
                }
                
                // Category: has description attribute (even if empty string)
                if (hasCategoryModal && (description !== null)) {
                  e.preventDefault();
                  e.stopPropagation();
                  const editCategoryModalEl = document.getElementById('editCategoryModal');
                  const form = document.getElementById('editCategoryForm');
                  const nameInput = document.getElementById('editCategoryName');
                  const descriptionInput = document.getElementById('editCategoryDescription');
                  if (form && id) form.action = `${window.location.origin}/admin/category/${id}`;
                  if (nameInput) nameInput.value = name;
                  if (descriptionInput) descriptionInput.value = description || '';
                  if (editCategoryModalEl) {
                    const parentModal = modalElement;
                    if (parentModal) {
                      parentModal.classList.add('modal-static');
                      const parentBackdrop = document.querySelector('.modal-backdrop:not(:last-child)');
                      if (parentBackdrop) {
                        parentBackdrop.style.pointerEvents = 'none';
                      }
                    }
                    editCategoryModalEl.style.zIndex = '1070';
                    let modal = bootstrap.Modal.getInstance(editCategoryModalEl);
                    if (!modal) modal = new bootstrap.Modal(editCategoryModalEl, { backdrop: true, keyboard: true, focus: true });
                    modal.show();
                    setTimeout(() => {
                      const backdrops = document.querySelectorAll('.modal-backdrop');
                      backdrops.forEach((backdrop, index) => {
                        if (index === backdrops.length - 1) {
                          backdrop.style.zIndex = '1069';
                        } else {
                          backdrop.style.zIndex = '1054';
                        }
                      });
                      if (parentModal) {
                        parentModal.style.zIndex = '1055';
                        parentModal.classList.add('show');
                      }
                    }, 10);
                  }
                  return;
                }
                
                // City: has status but no cityId/category/description
                if (hasCityModal && status && !cityId && !category && (description === null)) {
                  e.preventDefault();
                  e.stopPropagation();
                  const editCityModalEl = document.getElementById('editCityModal');
                  const form = document.getElementById('editCityForm');
                  const nameInput = document.getElementById('editCityName');
                  const statusSelect = document.getElementById('editCityStatus');
                  if (form && id) form.action = `${window.location.origin}/admin/city/${id}`;
                  if (nameInput) nameInput.value = name;
                  if (statusSelect) statusSelect.value = status || 'active';
                  if (editCityModalEl) {
                    editCityModalEl.style.zIndex = '1070';
                    let modal = bootstrap.Modal.getInstance(editCityModalEl);
                    if (!modal) modal = new bootstrap.Modal(editCityModalEl, { backdrop: true, keyboard: true, focus: true });
                    modal.show();
                    setTimeout(() => {
                      const backdrops = document.querySelectorAll('.modal-backdrop');
                      backdrops.forEach(backdrop => {
                        if (parseInt(backdrop.style.zIndex) < 1069) {
                          backdrop.style.zIndex = '1069';
                        }
                      });
                    }, 10);
                  }
                  return;
                }
              }
            });
            
            // Ensure editTitle function exists for Complaint Types
            if (typeof window.editTitle !== 'function') {
              window.editTitle = function(id, category, title, description) {
                const editForm = document.getElementById('editForm');
                const editCategory = document.getElementById('edit_category');
                const editTitleInput = document.getElementById('edit_title');
                const editDescription = document.getElementById('edit_description');
                
                if (editForm) {
                  editForm.action = `${window.location.origin}/admin/complaint-titles/${id}`;
                }
                if (editCategory) editCategory.value = category || '';
                if (editTitleInput) editTitleInput.value = title || '';
                if (editDescription) editDescription.value = description || '';
                
                const editModal = document.getElementById('editModal');
                if (editModal) {
                  // Prevent parent modal from closing (for Complaint Types)
                  const parentModal = document.querySelector('.modal.show:not(#editModal)');
                  if (parentModal) {
                    parentModal.classList.add('modal-static');
                    const parentBackdrop = document.querySelector('.modal-backdrop:not(:last-child)');
                    if (parentBackdrop) {
                      parentBackdrop.style.pointerEvents = 'none';
                    }
                  }
                  // Ensure z-index is set
                  editModal.style.zIndex = '1070';
                  // Check if modal instance already exists
                  let modal = bootstrap.Modal.getInstance(editModal);
                  if (!modal) {
                    modal = new bootstrap.Modal(editModal, {
                      backdrop: true,
                      keyboard: true,
                      focus: true
                    });
                  }
                  modal.show();
                  // Ensure backdrop has correct z-index
                  setTimeout(() => {
                    const backdrops = document.querySelectorAll('.modal-backdrop');
                    backdrops.forEach((backdrop, index) => {
                      if (index === backdrops.length - 1) {
                        backdrop.style.zIndex = '1069';
                      } else {
                        backdrop.style.zIndex = '1054';
                      }
                    });
                    if (parentModal) {
                      parentModal.style.zIndex = '1055';
                      parentModal.classList.add('show');
                    }
                  }, 10);
                }
              };
            }
            
            // Handle edit form submissions via AJAX to stay in modal
            // Define this function outside so it's accessible everywhere
            if (typeof window.handleEditFormSubmit !== 'function') {
              window.handleEditFormSubmit = function(form, modalId, parentModalId, parentModalBodyId, route) {
                // Check if already bound
                if (form.hasAttribute('data-ajax-bound')) {
                  return;
                }
                form.setAttribute('data-ajax-bound', 'true');
                
                form.addEventListener('submit', function(e) {
                  e.preventDefault();
                
                const formData = new FormData(form);
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalText = submitBtn ? submitBtn.textContent : '';
                
                if (submitBtn) {
                  submitBtn.disabled = true;
                  submitBtn.textContent = 'Saving...';
                }
                
                fetch(form.action, {
                  method: 'POST',
                  headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || formData.get('_token')
                  },
                  credentials: 'same-origin',
                  body: formData
                })
                .then(response => {
                  // Check if response is redirect (302/301) or success
                  if (response.redirected || response.status === 302 || response.status === 301) {
                    // Redirect happened, which means success - reload modal
                    return { success: true, redirected: true };
                  }
                  
                  if (response.ok) {
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                      return response.json();
                    }
                    // If HTML response, treat as success
                    return { success: true };
                  }
                  
                  return response.text().then(text => {
                    try {
                      return JSON.parse(text);
                    } catch {
                      // If it's HTML error page, still reload modal
                      if (text.includes('<!DOCTYPE') || text.includes('<html')) {
                        return { success: true };
                      }
                      throw new Error(text || 'Update failed');
                    }
                  });
                })
                .then(data => {
                  // Close edit modal
                  const editModalEl = document.getElementById(modalId);
                  if (editModalEl) {
                    const editModal = bootstrap.Modal.getInstance(editModalEl);
                    if (editModal) {
                      editModal.hide();
                    }
                  }
                  
                  // Reload parent modal content
                  if (parentModalId && parentModalBodyId && route) {
                    const parentModalBody = document.getElementById(parentModalBodyId);
                    if (parentModalBody) {
                      parentModalBody.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';
                      
                      fetch(route + '?format=html', {
                        method: 'GET',
                        headers: {
                          'X-Requested-With': 'XMLHttpRequest',
                          'Accept': 'text/html',
                        },
                        credentials: 'same-origin'
                      })
                      .then(response => response.text())
                      .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        let contentSection = doc.querySelector('section.content') || doc.querySelector('.content') || doc.querySelector('main') || doc.body;
                        
                        let modalContent = '';
                        const allCards = contentSection.querySelectorAll('.card-glass');
                        const seenCards = new Set();
                        allCards.forEach(card => {
                          const cardHTML = card.outerHTML;
                          const cardId = cardHTML.substring(0, 300);
                          if (!seenCards.has(cardId)) {
                            seenCards.add(cardId);
                            modalContent += '<div class="mb-3">' + cardHTML + '</div>';
                          }
                        });
                        
                        if (modalContent) {
                          parentModalBody.innerHTML = modalContent;
                          
                          // Move edit modals to document.body
                          const fetchedEditModals = doc.querySelectorAll('.modal[id^="edit"]');
                          fetchedEditModals.forEach(editModal => {
                            const existing = document.getElementById(editModal.id);
                            if (existing && existing.parentElement) {
                              existing.parentElement.removeChild(existing);
                            }
                            document.body.appendChild(editModal);
                            editModal.style.zIndex = '1070';
                            editModal.style.display = '';
                          });
                          
                          // Re-initialize all handlers by re-running the same code that runs after modal loads
                          // Extract edit modals and move to document.body
                          const editModalsFromContent = parentModalBody.querySelectorAll('.modal[id^="edit"]');
                          editModalsFromContent.forEach(editModal => {
                            editModal.remove();
                            document.body.appendChild(editModal);
                            editModal.style.zIndex = '1070';
                            editModal.style.display = '';
                          });
                          
                          // Extract and execute scripts
                          const scripts = parentModalBody.querySelectorAll('script');
                          scripts.forEach(script => {
                            const newScript = document.createElement('script');
                            if (script.src) {
                              newScript.src = script.src;
                            } else {
                              newScript.textContent = script.textContent;
                            }
                            document.body.appendChild(newScript);
                          });
                          
                          // Re-initialize handlers after a short delay
                          setTimeout(() => {
                            feather.replace();
                            
                            // Re-apply all the same handlers (City, Designation, etc.)
                            // This is the same code that runs initially, so we need to extract it
                            // For now, just trigger a page reload of the modal by calling openModal again
                            // But we're already in the modal, so we'll just re-run initialization
                            
                            // Re-bind edit form submissions
                            const editCityForm = document.getElementById('editCityForm');
                            if (editCityForm && typeof window.handleEditFormSubmit === 'function') {
                              const cityRoute = window.location.origin + '/admin/city';
                              window.handleEditFormSubmit(editCityForm, 'editCityModal', parentModalId, parentModalBodyId, cityRoute);
                            }
                            
                            const editSectorForm = document.getElementById('editSectorForm');
                            if (editSectorForm && typeof window.handleEditFormSubmit === 'function') {
                              const sectorRoute = window.location.origin + '/admin/sector';
                              window.handleEditFormSubmit(editSectorForm, 'editSectorModal', parentModalId, parentModalBodyId, sectorRoute);
                            }
                            
                            const editDesignationForm = document.getElementById('editDesignationForm');
                            if (editDesignationForm && typeof window.handleEditFormSubmit === 'function') {
                              const designationRoute = window.location.origin + '/admin/designation';
                              window.handleEditFormSubmit(editDesignationForm, 'editDesignationModal', parentModalId, parentModalBodyId, designationRoute);
                            }
                            
                            const editCategoryForm = document.getElementById('editCategoryForm');
                            if (editCategoryForm && typeof window.handleEditFormSubmit === 'function') {
                              const categoryRoute = window.location.origin + '/admin/category';
                              window.handleEditFormSubmit(editCategoryForm, 'editCategoryModal', parentModalId, parentModalBodyId, categoryRoute);
                            }
                            
                            const editForm = document.getElementById('editForm');
                            if (editForm && typeof window.handleEditFormSubmit === 'function') {
                              const complaintTitleRoute = window.location.origin + '/admin/complaint-titles';
                              window.handleEditFormSubmit(editForm, 'editModal', parentModalId, parentModalBodyId, complaintTitleRoute);
                            }
                            
                            // Re-bind delete handlers
                            initDeleteHandlers(parentModalBody);
                            
                            // Re-bind direct click handlers for City and Designation
                            const cityButtons = parentModalBody.querySelectorAll('button[data-bs-target="#editCityModal"], button[data-modal-target="#editCityModal"]');
                            cityButtons.forEach(btn => {
                              if (!btn.hasAttribute('data-handler-bound')) {
                                btn.setAttribute('data-handler-bound', 'true');
                                btn.removeAttribute('data-bs-toggle');
                                btn.addEventListener('click', function(e) {
                                  e.preventDefault();
                                  e.stopPropagation();
                                  e.stopImmediatePropagation();
                                  const id = this.getAttribute('data-id');
                                  const name = this.getAttribute('data-name');
                                  const status = this.getAttribute('data-status');
                                  setTimeout(() => {
                                    const editCityModalEl = document.getElementById('editCityModal');
                                    if (!editCityModalEl) return;
                                    const form = document.getElementById('editCityForm');
                                    const nameInput = document.getElementById('editCityName');
                                    const statusSelect = document.getElementById('editCityStatus');
                                    if (form && id) form.action = `${window.location.origin}/admin/city/${id}`;
                                    if (nameInput) nameInput.value = name || '';
                                    if (statusSelect) statusSelect.value = status || 'active';
                                    const parentModal = document.getElementById(parentModalId);
                                    if (parentModal) {
                                      parentModal.classList.add('modal-static');
                                      const parentBackdrop = document.querySelector('.modal-backdrop:not(:last-child)');
                                      if (parentBackdrop) parentBackdrop.style.pointerEvents = 'none';
                                    }
                                    editCityModalEl.style.zIndex = '1070';
                                    let modal = bootstrap.Modal.getInstance(editCityModalEl);
                                    if (!modal) modal = new bootstrap.Modal(editCityModalEl, { backdrop: true, keyboard: true, focus: true });
                                    modal.show();
                                    setTimeout(() => {
                                      const backdrops = document.querySelectorAll('.modal-backdrop');
                                      backdrops.forEach((b, i) => {
                                        b.style.zIndex = i === backdrops.length - 1 ? '1069' : '1054';
                                      });
                                      if (parentModal) {
                                        parentModal.style.zIndex = '1055';
                                        parentModal.classList.add('show');
                                      }
                                    }, 10);
                                  }, 50);
                                }, true);
                              }
                            });
                            
                            const designationButtons = parentModalBody.querySelectorAll('button[data-bs-target="#editDesignationModal"], button[data-modal-target="#editDesignationModal"]');
                            designationButtons.forEach(btn => {
                              if (!btn.hasAttribute('data-handler-bound')) {
                                btn.setAttribute('data-handler-bound', 'true');
                                btn.removeAttribute('data-bs-toggle');
                                btn.addEventListener('click', function(e) {
                                  e.preventDefault();
                                  e.stopPropagation();
                                  e.stopImmediatePropagation();
                                  const id = this.getAttribute('data-id');
                                  const category = this.getAttribute('data-category');
                                  const name = this.getAttribute('data-name');
                                  const status = this.getAttribute('data-status');
                                  setTimeout(() => {
                                    const editDesignationModalEl = document.getElementById('editDesignationModal');
                                    if (!editDesignationModalEl) return;
                                    const form = document.getElementById('editDesignationForm');
                                    const categorySelect = document.getElementById('editDesignationCategory');
                                    const nameInput = document.getElementById('editDesignationName');
                                    const statusSelect = document.getElementById('editDesignationStatus');
                                    if (form && id) form.action = `${window.location.origin}/admin/designation/${id}`;
                                    if (categorySelect && category) categorySelect.value = category || '';
                                    if (nameInput) nameInput.value = name || '';
                                    if (statusSelect) statusSelect.value = status || 'active';
                                    const parentModal = document.getElementById(parentModalId);
                                    if (parentModal) {
                                      parentModal.classList.add('modal-static');
                                      const parentBackdrop = document.querySelector('.modal-backdrop:not(:last-child)');
                                      if (parentBackdrop) parentBackdrop.style.pointerEvents = 'none';
                                    }
                                    editDesignationModalEl.style.zIndex = '1070';
                                    let modal = bootstrap.Modal.getInstance(editDesignationModalEl);
                                    if (!modal) modal = new bootstrap.Modal(editDesignationModalEl, { backdrop: true, keyboard: true, focus: true });
                                    modal.show();
                                    setTimeout(() => {
                                      const backdrops = document.querySelectorAll('.modal-backdrop');
                                      backdrops.forEach((b, i) => {
                                        b.style.zIndex = i === backdrops.length - 1 ? '1069' : '1054';
                                      });
                                      if (parentModal) {
                                        parentModal.style.zIndex = '1055';
                                        parentModal.classList.add('show');
                                      }
                                    }, 10);
                                  }, 50);
                                }, true);
                              }
                            });
                          }, 200);
                        }
                      })
                      .catch(error => {
                        console.error('Error reloading modal:', error);
                        location.reload();
                      });
                    }
                  } else {
                    // Fallback: reload page
                    location.reload();
                  }
                })
                .catch(error => {
                  console.error('Error:', error);
                  alert('Error updating: ' + (error.message || 'Unknown error'));
                  if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                  }
                });
              });
            };
            
            // Re-initialize delete form handlers
            modalBody.querySelectorAll('form.city-delete-form, form.sector-delete-form, form.designation-delete-form, form.category-delete-form').forEach(function(form) {
              form.addEventListener('submit', function(e) {
                e.preventDefault();
                const row = form.closest('tr');
                const url = form.action;
                const token = form.querySelector('input[name="_token"]').value;
                const method = form.querySelector('input[name="_method"]')?.value || 'DELETE';

                const formData = new FormData();
                formData.append('_method', method);
                formData.append('_token', token);

                fetch(url, {
                  method: 'POST',
                  headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                  },
                  body: formData
                })
                .then(res => res.ok ? res.json() : Promise.reject())
                .then(() => {
                  if (row) {
                    row.style.opacity = '0.4';
                    row.style.transition = 'opacity .2s ease';
                    setTimeout(() => { row.remove(); }, 180);
                  }
                })
                .catch(() => {
                  form.submit();
                });
              });
            });
          };
          
          initDeleteHandlers(modalBody);
          
          // Also bind edit forms when modal first loads
          if (typeof window.handleEditFormSubmit === 'function') {
            const editCityForm = document.getElementById('editCityForm');
            if (editCityForm) {
              const cityRoute = window.location.origin + '/admin/city';
              window.handleEditFormSubmit(editCityForm, 'editCityModal', modalElement.id, modalBody.id, cityRoute);
            }
            
            const editSectorForm = document.getElementById('editSectorForm');
            if (editSectorForm) {
              const sectorRoute = window.location.origin + '/admin/sector';
              window.handleEditFormSubmit(editSectorForm, 'editSectorModal', modalElement.id, modalBody.id, sectorRoute);
            }
            
            const editDesignationForm = document.getElementById('editDesignationForm');
            if (editDesignationForm) {
              const designationRoute = window.location.origin + '/admin/designation';
              window.handleEditFormSubmit(editDesignationForm, 'editDesignationModal', modalElement.id, modalBody.id, designationRoute);
            }
            
            const editCategoryForm = document.getElementById('editCategoryForm');
            if (editCategoryForm) {
              const categoryRoute = window.location.origin + '/admin/category';
              window.handleEditFormSubmit(editCategoryForm, 'editCategoryModal', modalElement.id, modalBody.id, categoryRoute);
            }
            
            const editForm = document.getElementById('editForm');
            if (editForm) {
              const complaintTitleRoute = window.location.origin + '/admin/complaint-titles';
              window.handleEditFormSubmit(editForm, 'editModal', modalElement.id, modalBody.id, complaintTitleRoute);
            }
          }
          }, 200);
        } else {
          console.error('Could not find content in response');
          modalBody.innerHTML = '<div class="text-center py-5 text-danger">Error: Could not load content. Please refresh and try again.</div>';
        }
      })
      .catch(error => {
        console.error('Error loading content:', error);
        modalBody.innerHTML = '<div class="text-center py-5 text-danger">Error loading content: ' + error.message + '. Please try again.</div>';
      });
      
      modalElement.addEventListener('shown.bs.modal', function() {
        feather.replace();
      });
      
      modalElement.addEventListener('hidden.bs.modal', function() {
        document.body.classList.remove('modal-open-blur');
        feather.replace();
      }, { once: true });
    }
  </script>
  
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  @stack('scripts')
</body>
</html>