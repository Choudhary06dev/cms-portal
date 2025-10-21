<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>@yield('title', 'CMS Admin')</title>
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    /* Simple theme system - no flickering */
    body {
      background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
      color: #f1f5f9;
      transition: none;
    }
    
    .theme-light body {
      background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
      color: #1e293b !important;
    }
    
    .theme-light .text-white {
      color: #1e293b !important;
    }
    
    .theme-light .text-muted {
      color: #64748b !important;
    }
    
    .theme-light .card-glass {
      background: rgba(255, 255, 255, 0.8) !important;
      color: #1e293b !important;
    }
    
    .theme-light .sidebar {
      background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%) !important;
      color: #1e293b !important;
    }
    
    .theme-light .topbar {
      background: rgba(255, 255, 255, 0.9) !important;
      color: #1e293b !important;
    }
    
    .theme-light .nav-link {
      color: #1e293b !important;
    }
    
    .theme-light .nav-link:hover {
      color: #0f172a !important;
    }
    
    .theme-light .nav-link.active {
      color: #3b82f6 !important;
    }
    
    .theme-light h1, .theme-light h2, .theme-light h3, 
    .theme-light h4, .theme-light h5, .theme-light h6 {
      color: #1e293b !important;
    }
    
    .theme-light p, .theme-light span, .theme-light div {
      color: #1e293b !important;
    }
    
    .theme-light .section-title {
      color: #1e293b !important;
    }
    
    .theme-light .brand {
      color: #1e293b !important;
    }
    
    .theme-light .user-name {
      color: #1e293b !important;
    }
    
    .theme-light .user-role {
      color: #64748b !important;
    }
    
    .theme-light .breadcrumb-item {
      color: #64748b !important;
    }
    
    .theme-light .breadcrumb-item.active {
      color: #1e293b !important;
    }
    
    /* NUCLEAR OPTION - Force ALL text to be black in light theme */
    .theme-light * {
      color: #000000 !important;
    }
    
    /* Preserve colored statistics in light theme */
    .theme-light .text-primary {
      color: #3b82f6 !important;
    }
    
    .theme-light .text-success {
      color: #22c55e !important;
    }
    
    .theme-light .text-warning {
      color: #f59e0b !important;
    }
    
    .theme-light .text-danger {
      color: #ef4444 !important;
    }
    
    .theme-light .text-info {
      color: #06b6d4 !important;
    }
    
    .theme-light .text-muted {
      color: #6b7280 !important;
    }
    
    .theme-light .text-secondary {
      color: #6b7280 !important;
    }
    
    /* Override ALL inline styles with maximum specificity */
    .theme-light h1[style*="color"],
    .theme-light h2[style*="color"],
    .theme-light h3[style*="color"],
    .theme-light h4[style*="color"],
    .theme-light h5[style*="color"],
    .theme-light h6[style*="color"],
    .theme-light p[style*="color"],
    .theme-light span[style*="color"],
    .theme-light div[style*="color"] {
      color: #000000 !important;
    }
    
    /* Force dashboard header to be black */
    .theme-light .dashboard-header h2[style*="color: #ffffff"] {
      color: #000000 !important;
    }
    
    .theme-light .dashboard-header p[style*="color: #cbd5e1"] {
      color: #000000 !important;
    }
    
    /* Force all card content to be black */
    .theme-light .card-glass h1[style*="color"],
    .theme-light .card-glass h2[style*="color"],
    .theme-light .card-glass h3[style*="color"],
    .theme-light .card-glass h4[style*="color"],
    .theme-light .card-glass h5[style*="color"],
    .theme-light .card-glass h6[style*="color"],
    .theme-light .card-glass p[style*="color"],
    .theme-light .card-glass span[style*="color"],
    .theme-light .card-glass div[style*="color"] {
      color: #000000 !important;
    }
    
    /* Override Bootstrap classes with maximum specificity */
    .theme-light .text-white {
      color: #000000 !important;
    }
    
    .theme-light .text-light {
      color: #000000 !important;
    }
    
    .theme-light .text-dark {
      color: #000000 !important;
    }
    
    .theme-light .text-body {
      color: #000000 !important;
    }
    
    .theme-light .text-reset {
      color: #000000 !important;
    }
    
    /* Force all headings to be black */
    .theme-light h1,
    .theme-light h2,
    .theme-light h3,
    .theme-light h4,
    .theme-light h5,
    .theme-light h6 {
      color: #000000 !important;
    }
    
    /* Force all paragraphs to be black */
    .theme-light p {
      color: #000000 !important;
    }
    
    /* Force all spans to be black */
    .theme-light span {
      color: #000000 !important;
    }
    
    /* Force all divs to be black */
    .theme-light div {
      color: #000000 !important;
    }
    
    /* Force all dashboard content to be black */
    .theme-light .dashboard-header * {
      color: #000000 !important;
    }
    
    .theme-light .card-glass * {
      color: #000000 !important;
    }
    
    .theme-light .content * {
      color: #000000 !important;
    }
    
    .theme-light .mb-4 * {
      color: #000000 !important;
    }
    
    .theme-light .row * {
      color: #000000 !important;
    }
    
    .theme-light .col-md-3 * {
      color: #000000 !important;
    }
    
    .theme-light .col-md-6 * {
      color: #000000 !important;
    }
    
    .theme-light .col-md-4 * {
      color: #000000 !important;
    }
    
    .theme-light .col-md-8 * {
      color: #000000 !important;
    }
    
    .theme-light .col-md-12 * {
      color: #000000 !important;
    }
    
    /* Dashboard light theme styles */
    .theme-light .card-glass {
      background: rgba(255, 255, 255, 0.9) !important;
      border: 1px solid rgba(0, 0, 0, 0.1) !important;
      color: #1e293b !important;
    }
    
    .theme-light .card-glass h1,
    .theme-light .card-glass h2,
    .theme-light .card-glass h3,
    .theme-light .card-glass h4,
    .theme-light .card-glass h5,
    .theme-light .card-glass h6 {
      color: #1e293b !important;
    }
    
    .theme-light .card-glass p,
    .theme-light .card-glass span,
    .theme-light .card-glass div {
      color: #1e293b !important;
    }
    
    .theme-light .card-glass .text-white {
      color: #1e293b !important;
    }
    
    .theme-light .card-glass .text-light {
      color: #64748b !important;
    }
    
    .theme-light .card-glass .text-muted {
      color: #64748b !important;
    }
    
    .theme-light .table {
      color: #1e293b !important;
    }
    
    .theme-light .table th {
      color: #1e293b !important;
      background: rgba(0, 0, 0, 0.05) !important;
    }
    
    .theme-light .table td {
      color: #1e293b !important;
    }
    
    .theme-light .table-striped > tbody > tr:nth-of-type(odd) > td {
      background: rgba(0, 0, 0, 0.02) !important;
    }
    
    .theme-light .btn {
      color: #1e293b !important;
    }
    
    .theme-light .btn-primary {
      background: #3b82f6 !important;
      border-color: #3b82f6 !important;
      color: #ffffff !important;
    }
    
    .theme-light .btn-outline-primary {
      color: #3b82f6 !important;
      border-color: #3b82f6 !important;
    }
    
    .theme-light .btn-outline-primary:hover {
      background: #3b82f6 !important;
      color: #ffffff !important;
    }
    
    .theme-light .badge {
      color: #ffffff !important;
    }
    
    .theme-light .text-primary {
      color: #3b82f6 !important;
    }
    
    .theme-light .text-success {
      color: #22c55e !important;
    }
    
    .theme-light .text-warning {
      color: #f59e0b !important;
    }
    
    .theme-light .text-danger {
      color: #ef4444 !important;
    }
    
    .theme-light .text-info {
      color: #06b6d4 !important;
    }
    
    /* Dashboard specific light theme overrides */
    .theme-light .content {
      color: #1e293b !important;
    }
    
    .theme-light .content h1,
    .theme-light .content h2,
    .theme-light .content h3,
    .theme-light .content h4,
    .theme-light .content h5,
    .theme-light .content h6 {
      color: #1e293b !important;
    }
    
    .theme-light .content p {
      color: #1e293b !important;
    }
    
    .theme-light .content .text-white {
      color: #1e293b !important;
    }
    
    .theme-light .content .text-light {
      color: #64748b !important;
    }
    
    .theme-light .content .text-muted {
      color: #64748b !important;
    }
    
    /* Override inline styles in light theme */
    .theme-light [style*="color: #ffffff"] {
      color: #1e293b !important;
    }
    
    .theme-light [style*="color: #cbd5e1"] {
      color: #64748b !important;
    }
    
    .theme-light [style*="color: #94a3b8"] {
      color: #64748b !important;
    }
    
    /* Chart containers */
    .theme-light .chart-container {
      background: rgba(255, 255, 255, 0.9) !important;
      border: 1px solid rgba(0, 0, 0, 0.1) !important;
    }
    
    /* Form elements in light theme */
    .theme-light .form-control {
      background: rgba(255, 255, 255, 0.9) !important;
      border: 1px solid rgba(0, 0, 0, 0.2) !important;
      color: #1e293b !important;
    }
    
    .theme-light .form-control:focus {
      background: rgba(255, 255, 255, 1) !important;
      border-color: #3b82f6 !important;
      color: #1e293b !important;
    }
    
    .theme-light .form-label {
      color: #1e293b !important;
    }
    
    /* Additional dashboard elements */
    .theme-light .alert {
      background: rgba(255, 255, 255, 0.9) !important;
      border: 1px solid rgba(0, 0, 0, 0.1) !important;
      color: #1e293b !important;
    }
    
    .theme-light .alert-success {
      background: rgba(34, 197, 94, 0.1) !important;
      border-color: #22c55e !important;
      color: #166534 !important;
    }
    
    .theme-light .alert-warning {
      background: rgba(245, 158, 11, 0.1) !important;
      border-color: #f59e0b !important;
      color: #92400e !important;
    }
    
    .theme-light .alert-danger {
      background: rgba(239, 68, 68, 0.1) !important;
      border-color: #ef4444 !important;
      color: #991b1b !important;
    }
    
    .theme-light .alert-info {
      background: rgba(6, 182, 212, 0.1) !important;
      border-color: #06b6d4 !important;
      color: #155e75 !important;
    }
    
    /* Progress bars */
    .theme-light .progress {
      background: rgba(0, 0, 0, 0.1) !important;
    }
    
    .theme-light .progress-bar {
      background: #3b82f6 !important;
    }
    
    /* List groups */
    .theme-light .list-group-item {
      background: rgba(255, 255, 255, 0.9) !important;
      border: 1px solid rgba(0, 0, 0, 0.1) !important;
      color: #1e293b !important;
    }
    
    .theme-light .list-group-item:hover {
      background: rgba(0, 0, 0, 0.05) !important;
    }
    
    .theme-dark body {
      background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
      color: #f1f5f9;
    }
    
    .theme-night body {
      background: linear-gradient(135deg, #000000 0%, #111111 100%);
      color: #e5e5e5;
    }
    
    .theme-night .sidebar {
      background: linear-gradient(180deg, #000000 0%, #111111 100%) !important;
      color: #e5e5e5 !important;
    }
    
    .theme-night .topbar {
      background: rgba(0, 0, 0, 0.95) !important;
      color: #e5e5e5 !important;
    }
    
    .theme-night .card-glass {
      background: rgba(0, 0, 0, 0.8) !important;
      border: 1px solid rgba(255, 255, 255, 0.1) !important;
      color: #e5e5e5 !important;
    }
    
    .theme-night .nav-link {
      color: #e5e5e5 !important;
    }
    
    .theme-night .nav-link:hover {
      color: #ffffff !important;
    }
    
    .theme-night .nav-link.active {
      color: #60a5fa !important;
    }
    
    .theme-night h1, .theme-night h2, .theme-night h3, 
    .theme-night h4, .theme-night h5, .theme-night h6 {
      color: #e5e5e5 !important;
    }
    
    .theme-night p, .theme-night span, .theme-night div {
      color: #e5e5e5 !important;
    }
    
    .theme-night .text-white {
      color: #e5e5e5 !important;
    }
    
    .theme-night .text-muted {
      color: #9ca3af !important;
    }
    
    .theme-night .text-secondary {
      color: #9ca3af !important;
    }
    
    .theme-night .brand {
      color: #e5e5e5 !important;
    }
    
    .theme-night .user-name {
      color: #e5e5e5 !important;
    }
    
    .theme-night .user-role {
      color: #9ca3af !important;
    }
    
    .theme-night .breadcrumb-item {
      color: #9ca3af !important;
    }
    
    .theme-night .breadcrumb-item.active {
      color: #e5e5e5 !important;
    }
    
    .theme-night .section-title {
      color: #e5e5e5 !important;
    }
    
    .theme-night .table {
      color: #e5e5e5 !important;
    }
    
    .theme-night .table th {
      color: #e5e5e5 !important;
      background: rgba(0, 0, 0, 0.5) !important;
    }
    
    .theme-night .table td {
      color: #e5e5e5 !important;
    }
    
    .theme-night .btn {
      color: #e5e5e5 !important;
    }
    
    .theme-night .btn-primary {
      background: #1d4ed8 !important;
      border-color: #1d4ed8 !important;
      color: #ffffff !important;
    }
    
    .theme-night .btn-outline-primary {
      color: #60a5fa !important;
      border-color: #60a5fa !important;
    }
    
    .theme-night .btn-outline-primary:hover {
      background: #60a5fa !important;
      color: #000000 !important;
    }
    
    .theme-night .badge {
      color: #ffffff !important;
    }
    
    .theme-night .text-primary {
      color: #60a5fa !important;
    }
    
    .theme-night .text-success {
      color: #34d399 !important;
    }
    
    .theme-night .text-warning {
      color: #fbbf24 !important;
    }
    
    .theme-night .text-danger {
      color: #f87171 !important;
    }
    
    .theme-night .text-info {
      color: #22d3ee !important;
    }
    
    .theme-night .form-control {
      background: rgba(0, 0, 0, 0.8) !important;
      border: 1px solid rgba(255, 255, 255, 0.2) !important;
      color: #e5e5e5 !important;
    }
    
    .theme-night .form-control:focus {
      background: rgba(0, 0, 0, 0.9) !important;
      border-color: #60a5fa !important;
      color: #e5e5e5 !important;
    }
    
    .theme-night .form-label {
      color: #e5e5e5 !important;
    }
    
    .theme-night .alert {
      background: rgba(0, 0, 0, 0.8) !important;
      border: 1px solid rgba(255, 255, 255, 0.1) !important;
      color: #e5e5e5 !important;
    }
    
    .theme-night .alert-success {
      background: rgba(52, 211, 153, 0.1) !important;
      border-color: #34d399 !important;
      color: #34d399 !important;
    }
    
    .theme-night .alert-warning {
      background: rgba(251, 191, 36, 0.1) !important;
      border-color: #fbbf24 !important;
      color: #fbbf24 !important;
    }
    
    .theme-night .alert-danger {
      background: rgba(248, 113, 113, 0.1) !important;
      border-color: #f87171 !important;
      color: #f87171 !important;
    }
    
    .theme-night .alert-info {
      background: rgba(34, 211, 238, 0.1) !important;
      border-color: #22d3ee !important;
      color: #22d3ee !important;
    }
    
    .theme-night .progress {
      background: rgba(0, 0, 0, 0.5) !important;
    }
    
    .theme-night .progress-bar {
      background: #60a5fa !important;
    }
    
    .theme-night .list-group-item {
      background: rgba(0, 0, 0, 0.8) !important;
      border: 1px solid rgba(255, 255, 255, 0.1) !important;
      color: #e5e5e5 !important;
    }
    
    .theme-night .list-group-item:hover {
      background: rgba(255, 255, 255, 0.05) !important;
    }
  </style>
  <script src="https://unpkg.com/feather-icons"></script>
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
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
      width: 260px;
      background: var(--sidebar-bg);
      border-right: 1px solid rgba(59, 130, 246, 0.2);
      padding: 22px;
      position: fixed;
      left:0; top:50px; bottom:0;
      box-shadow: 4px 0 20px rgba(0, 0, 0, 0.3);
      z-index: 999;
    }
    .brand { color: var(--accent); font-weight:700; font-size:18px; text-shadow: 0 0 10px rgba(59, 130, 246, 0.3); }
    .nav-link { color: #cbd5e1; border-radius:8px; transition: all 0.3s ease; }
    .nav-link:hover, .nav-link.active { background: linear-gradient(90deg, rgba(59, 130, 246, 0.15), rgba(37, 99, 235, 0.1)); color: #fff; transform: translateX(5px); }
    .content { margin-left: 280px; padding: 28px; margin-top: 50px; }
    /* Topbar styles are now in the navigation component */
    .card-glass { 
      background: var(--glass-bg); 
      border:1px solid rgba(59, 130, 246, 0.1); 
      border-radius:14px; 
      padding:18px; 
      box-shadow: 0 8px 30px rgba(15, 23, 42, 0.4);
      backdrop-filter: blur(10px);
    }
    .table thead th { 
      background: linear-gradient(90deg, rgba(59, 130, 246, 0.1), rgba(37, 99, 235, 0.05)); 
      color:#e2e8f0; 
      border-bottom: 2px solid rgba(59, 130, 246, 0.2);
    }
    .btn-accent { 
      background: linear-gradient(135deg, #3b82f6, #1d4ed8); 
      border:none; 
      color:#fff; 
      font-weight:700; 
      box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
      transition: all 0.3s ease;
    }
    .btn-accent:hover { 
      background: linear-gradient(135deg, #2563eb, #1e40af); 
      transform: translateY(-1px);
      box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
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
    .section-title { color: #9fb7d8; font-size:12px; margin-top:18px; margin-bottom:8px; }
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
    
    <div class="section-title">Main Menu</div>
    <a href="{{ route('admin.dashboard') }}" class="nav-link d-block py-2 px-3 mb-1 {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
      <i data-feather="home" class="me-2"></i> Dashboard
    </a>
    
    <div class="section-title">Management</div>
    <a href="{{ route('admin.users.index') }}" class="nav-link d-block py-2 px-3 mb-1 {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
      <i data-feather="users" class="me-2"></i> Users
    </a>
    <a href="{{ route('admin.roles.index') }}" class="nav-link d-block py-2 px-3 mb-1 {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
      <i data-feather="shield" class="me-2"></i> Roles
    </a>
    <a href="{{ route('admin.employees.index') }}" class="nav-link d-block py-2 px-3 mb-1 {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}">
      <i data-feather="user-check" class="me-2"></i> Employees
    </a>
    <a href="{{ route('admin.clients.index') }}" class="nav-link d-block py-2 px-3 mb-1 {{ request()->routeIs('admin.clients.*') ? 'active' : '' }}">
      <i data-feather="briefcase" class="me-2"></i> Clients
    </a>
    <a href="{{ route('admin.complaints.index') }}" class="nav-link d-block py-2 px-3 mb-1 {{ request()->routeIs('admin.complaints.*') ? 'active' : '' }}">
      <i data-feather="alert-circle" class="me-2"></i> Complaints
    </a>
    <a href="{{ route('admin.spares.index') }}" class="nav-link d-block py-2 px-3 mb-1 {{ request()->routeIs('admin.spares.*') ? 'active' : '' }}">
      <i data-feather="package" class="me-2"></i> Spare Parts
    </a>
    <a href="{{ route('admin.approvals.index') }}" class="nav-link d-block py-2 px-3 mb-1 {{ request()->routeIs('admin.approvals.*') ? 'active' : '' }}">
      <i data-feather="check-circle" class="me-2"></i> Approvals
    </a>
    <a href="{{ route('admin.reports.index') }}" class="nav-link d-block py-2 px-3 mb-1 {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
      <i data-feather="bar-chart-2" class="me-2"></i> Reports
    </a>
    <a href="{{ route('admin.sla.index') }}" class="nav-link d-block py-2 px-3 mb-1 {{ request()->routeIs('admin.sla.*') ? 'active' : '' }}">
      <i data-feather="clock" class="me-2"></i> SLA Rules
    </a>
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
      // Global search functionality
      const globalSearch = document.getElementById('globalSearch');
      if (globalSearch) {
        globalSearch.addEventListener('keypress', function(e) {
          if (e.key === 'Enter') {
            const searchTerm = this.value.trim();
            if (searchTerm) {
              // Implement global search
              window.location.href = `/admin/search?q=${encodeURIComponent(searchTerm)}`;
            }
          }
        });
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
      // Mock notifications for now
      const mockNotifications = [
        {
          id: 1,
          title: 'New Complaint',
          message: 'A new complaint has been submitted',
          type: 'info',
          icon: 'alert-circle',
          time: '2 minutes ago',
          read: false
        },
        {
          id: 2,
          title: 'Low Stock Alert',
          message: 'Spare parts running low',
          type: 'warning',
          icon: 'package',
          time: '15 minutes ago',
          read: false
        },
        {
          id: 3,
          title: 'Approval Required',
          message: 'Spare approval request pending',
          type: 'primary',
          icon: 'check-circle',
          time: '1 hour ago',
          read: true
        }
      ];

      const unreadCount = mockNotifications.filter(n => !n.read).length;
      updateNotificationCount(unreadCount);
      updateNotificationList(mockNotifications);
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
          <a href="#" class="dropdown-item notification-item">
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
  </script>
  @stack('scripts')
</body>
</html>