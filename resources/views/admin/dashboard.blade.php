@extends('layouts.sidebar')

@section('title', 'Dashboard — CMS Admin')

@section('content')
  <style>
  /* Enhanced & Attractive Light Theme for Dashboard */
  .theme-light {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%) !important;
    color: #1e293b !important;
  }
  
  .theme-light body {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%) !important;
    color: #1e293b !important;
  }
  
  .theme-light .content {
    background: transparent !important;
    color: #1e293b !important;
  }
  
  .theme-light .card-glass {
    background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%) !important;
    border: 1px solid rgba(59, 130, 246, 0.15) !important;
    border-radius: 12px !important;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03) !important;
    color: #1e293b !important;
    transition: all 0.3s ease !important;
  }
  
  .theme-light .card-glass:hover {
    box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.1), 0 4px 6px -2px rgba(59, 130, 246, 0.05) !important;
    transform: translateY(-2px) !important;
  }
  
  .theme-light h1, .theme-light h2, .theme-light h3, 
  .theme-light h4, .theme-light h5, .theme-light h6 {
    color: #0f172a !important;
    font-weight: 600 !important;
  }
  
  .theme-light .dashboard-header h2 {
    color: #0f172a !important;
    font-weight: 700 !important;
    font-size: 1.875rem !important;
  }
  
  .theme-light .dashboard-header p {
    color: #475569 !important;
    font-size: 0.95rem !important;
  }
  
  .theme-light p, .theme-light span, .theme-light div {
    color: #1e293b !important;
  }
  
  .theme-light .text-white {
    color: #1e293b !important;
    font-weight: 700 !important;
  }
  
  .theme-light .text-light {
    color: #64748b !important;
  }
  
  .theme-light .text-muted {
    color: #64748b !important;
    font-size: 0.875rem !important;
    font-weight: 500 !important;
  }
  
  .theme-light .text-secondary {
    color: #64748b !important;
  }
  
  /* Enhanced table styling */
  .theme-light .table {
    color: #1e293b !important;
    background: transparent !important;
  }
  
  .theme-light .table th {
    color: #0f172a !important;
    background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%) !important;
    font-weight: 600 !important;
    border-bottom: 2px solid #cbd5e1 !important;
    padding: 0.875rem !important;
  }
  
  .theme-light .table td {
    color: #334155 !important;
    border-bottom: 1px solid #e2e8f0 !important;
    padding: 0.875rem !important;
  }
  
  .theme-light .table tbody tr:hover {
    background: #f8fafc !important;
    transition: background 0.2s ease !important;
  }
  
  .theme-light .table-dark {
    background: transparent !important;
  }
  
  .theme-light .table-dark thead {
    background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%) !important;
  }
  
  /* Enhanced button styling */
  .theme-light .btn {
    font-weight: 500 !important;
    border-radius: 8px !important;
    transition: all 0.3s ease !important;
  }
  
  .theme-light .btn-primary,
  .theme-light .btn-accent {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
    border-color: #3b82f6 !important;
    color: #ffffff !important;
    box-shadow: 0 2px 4px rgba(59, 130, 246, 0.2) !important;
  }
  
  .theme-light .btn-primary:hover,
  .theme-light .btn-accent:hover {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
    box-shadow: 0 4px 8px rgba(59, 130, 246, 0.3) !important;
    transform: translateY(-1px) !important;
  }
  
  .theme-light .btn-outline-primary,
  .theme-light .btn-outline-warning {
    color: #3b82f6 !important;
    border-color: #3b82f6 !important;
    background: transparent !important;
  }
  
  .theme-light .btn-outline-primary:hover,
  .theme-light .btn-outline-warning:hover {
    background: #3b82f6 !important;
    color: #ffffff !important;
    box-shadow: 0 2px 4px rgba(59, 130, 246, 0.2) !important;
  }
  
  /* Enhanced color classes */
  .theme-light .text-primary {
    color: #2563eb !important;
    font-weight: 600 !important;
  }
  
  .theme-light .text-success {
    color: #16a34a !important;
    font-weight: 600 !important;
  }
  
  .theme-light .text-warning {
    color: #ea580c !important;
    font-weight: 600 !important;
  }
  
  .theme-light .text-danger {
    color: #dc2626 !important;
    font-weight: 600 !important;
  }
  
  .theme-light .text-info {
    color: #0891b2 !important;
    font-weight: 600 !important;
  }
  
  /* Enhanced badge styling */
  .theme-light .badge {
    font-weight: 600 !important;
    padding: 0.375rem 0.75rem !important;
    border-radius: 6px !important;
    font-size: 0.75rem !important;
  }
  
  /* Statistics cards with colored accents */
  .theme-light .card-glass .text-primary {
    color: #2563eb !important;
  }
  
  .theme-light .card-glass .text-warning {
    color: #ea580c !important;
  }
  
  .theme-light .card-glass .text-success {
    color: #16a34a !important;
  }
  
  .theme-light .card-glass .text-danger {
    color: #dc2626 !important;
  }
  
  .theme-light .card-glass .text-info {
    color: #0891b2 !important;
  }
  
  .theme-light .form-control {
    background: #ffffff !important;
    border: 1px solid #d1d5db !important;
    color: #000000 !important;
  }
  
  .theme-light .form-control:focus {
    background: #ffffff !important;
    border-color: #3b82f6 !important;
    color: #000000 !important;
  }
  
  .theme-light .form-label {
    color: #000000 !important;
  }
  
  .theme-light .alert {
    background: #ffffff !important;
    border: 1px solid #e5e7eb !important;
    color: #000000 !important;
  }
  
  .theme-light .alert-success {
    background: #f0fdf4 !important;
    border-color: #22c55e !important;
    color: #166534 !important;
  }
  
  .theme-light .alert-warning {
    background: #fffbeb !important;
    border-color: #f59e0b !important;
    color: #92400e !important;
  }
  
  .theme-light .alert-danger {
    background: #fef2f2 !important;
    border-color: #ef4444 !important;
    color: #991b1b !important;
  }
  
  .theme-light .alert-info {
    background: #f0f9ff !important;
    border-color: #06b6d4 !important;
    color: #155e75 !important;
  }
  
  .theme-light .progress {
    background: #f3f4f6 !important;
  }
  
  .theme-light .progress-bar {
    background: #3b82f6 !important;
  }
  
  .theme-light .list-group-item {
    background: #ffffff !important;
    border: 1px solid #e5e7eb !important;
    color: #000000 !important;
  }
  
  .theme-light .list-group-item:hover {
    background: #f9fafb !important;
  }
  
  /* Table hover effects removed - no hover styling */
  
  /* Chart toolbar dropdown visibility */
  .apexcharts-toolbar {
    z-index: 1000 !important;
  }
  
  .apexcharts-menu {
    background: #1f2937 !important;
    border: 1px solid #374151 !important;
    border-radius: 6px !important;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
  }
  
  .apexcharts-menu-item {
    color: #ffffff !important;
    background: transparent !important;
  }
  
  .apexcharts-menu-item:hover {
    background: #374151 !important;
    color: #ffffff !important;
  }
  
  /* Light theme chart toolbar */
  .theme-light .apexcharts-menu {
    background: #ffffff !important;
    border: 1px solid #d1d5db !important;
    color: #000000 !important;
  }
  
  .theme-light .apexcharts-menu-item {
    color: #000000 !important;
  }
  
  .theme-light .apexcharts-menu-item:hover {
    background: #f3f4f6 !important;
    color: #000000 !important;
  }
  
  /* Chart tooltip styling for light theme */
  .theme-light .apexcharts-tooltip {
    background: #ffffff !important;
    color: #000000 !important;
    border: 1px solid #d1d5db !important;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
  }
  
  .theme-light .apexcharts-tooltip-title {
    background: #f9fafb !important;
    color: #000000 !important;
    border-bottom: 1px solid #d1d5db !important;
  }
  
  .theme-light .apexcharts-tooltip-series-group {
    background: #ffffff !important;
    color: #000000 !important;
  }
  
  .theme-light .apexcharts-tooltip-y-group {
    color: #000000 !important;
  }
  
  .theme-light .apexcharts-tooltip-marker {
    background: #ffffff !important;
  }
  
  /* Chart tooltip content styling for light theme */
  .theme-light .apexcharts-tooltip .apexcharts-tooltip-title {
    background: #f9fafb !important;
    color: #000000 !important;
    border-bottom: 1px solid #d1d5db !important;
    font-weight: 600 !important;
  }
  
  .theme-light .apexcharts-tooltip .apexcharts-tooltip-series-group {
    background: #ffffff !important;
    color: #000000 !important;
  }
  
  .theme-light .apexcharts-tooltip .apexcharts-tooltip-series-group .apexcharts-tooltip-y-group {
    color: #000000 !important;
  }
  
  .theme-light .apexcharts-tooltip .apexcharts-tooltip-series-group .apexcharts-tooltip-y-group .apexcharts-tooltip-y-label {
    color: #6b7280 !important;
  }
  
  .theme-light .apexcharts-tooltip .apexcharts-tooltip-series-group .apexcharts-tooltip-y-group .apexcharts-tooltip-y-value {
    color: #000000 !important;
    font-weight: 600 !important;
  }
  
  /* Force all tooltip text to be black in light theme */
  .theme-light .apexcharts-tooltip * {
    color: #000000 !important;
  }
  
  .theme-light .apexcharts-tooltip .apexcharts-tooltip-title {
    color: #000000 !important;
  }
  
  .theme-light .apexcharts-tooltip .apexcharts-tooltip-series-group * {
    color: #000000 !important;
  }
  
  /* Specific styling for month/date in tooltip */
  .theme-light .apexcharts-tooltip .apexcharts-tooltip-title {
    background: #f9fafb !important;
    color: #000000 !important;
    border-bottom: 1px solid #d1d5db !important;
  }
  
  .theme-light .apexcharts-tooltip .apexcharts-tooltip-title * {
    color: #000000 !important;
    background: transparent !important;
  }
  
  /* Force tooltip title background to be light */
  .theme-light .apexcharts-tooltip .apexcharts-tooltip-title {
    background: #f9fafb !important;
    background-color: #f9fafb !important;
  }
  
  /* Override all inline styles */
  .theme-light [style*="color: #ffffff"] {
    color: #000000 !important;
  }
  
  /* Force tooltip title to have light background and black text */
  .theme-light .apexcharts-tooltip .apexcharts-tooltip-title[style*="background"] {
    background: #f9fafb !important;
    background-color: #f9fafb !important;
    color: #000000 !important;
  }
  
  .theme-light .apexcharts-tooltip .apexcharts-tooltip-title[style*="color"] {
    color: #000000 !important;
  }
  
  /* Override any dark background styles */
  .theme-light .apexcharts-tooltip .apexcharts-tooltip-title[style*="background-color: #000000"],
  .theme-light .apexcharts-tooltip .apexcharts-tooltip-title[style*="background: #000000"] {
    background: #f9fafb !important;
    background-color: #f9fafb !important;
    color: #000000 !important;
  }
  
  /* Force all ApexCharts tooltips to be light theme */
  .apexcharts-tooltip {
    background: #ffffff !important;
    color: #000000 !important;
    border: 1px solid #d1d5db !important;
  }
  
  .apexcharts-tooltip .apexcharts-tooltip-title {
    background: #f9fafb !important;
    color: #000000 !important;
    border-bottom: 1px solid #d1d5db !important;
  }
  
  .apexcharts-tooltip .apexcharts-tooltip-series-group {
    background: #ffffff !important;
    color: #000000 !important;
  }
  
  .apexcharts-tooltip .apexcharts-tooltip-series-group * {
    color: #000000 !important;
  }
  
  .theme-light [style*="color: #cbd5e1"] {
    color: #000000 !important;
  }
  
  .theme-light [style*="color: #94a3b8"] {
    color: #6b7280 !important;
  }
  
  .theme-light [style*="color: #1e293b"] {
    color: #000000 !important;
  }
  
  .theme-light [style*="color: #f1f5f9"] {
    color: #000000 !important;
  }
  
  .theme-light [style*="color: #64748b"] {
    color: #6b7280 !important;
  }
  
  /* Force ALL headings to be black in light theme */
  .theme-light h1, .theme-light h2, .theme-light h3, 
  .theme-light h4, .theme-light h5, .theme-light h6 {
    color: #000000 !important;
  }
  
  .theme-light .dashboard-header h2 {
    color: #000000 !important;
  }
  
  .theme-light .dashboard-header p {
    color: #000000 !important;
  }
  
  .theme-light .mb-4 h2 {
    color: #000000 !important;
  }
  
  .theme-light .mb-4 p {
    color: #000000 !important;
  }
  
  .theme-light .card-glass h1,
  .theme-light .card-glass h2,
  .theme-light .card-glass h3,
  .theme-light .card-glass h4,
  .theme-light .card-glass h5,
  .theme-light .card-glass h6 {
    color: #000000 !important;
  }
  
  .theme-light .content h1,
  .theme-light .content h2,
  .theme-light .content h3,
  .theme-light .content h4,
  .theme-light .content h5,
  .theme-light .content h6 {
    color: #000000 !important;
  }
  
  /* Override any remaining white text */
  .theme-light * {
    color: #000000 !important;
  }
  
  .theme-light .text-muted {
    color: #6b7280 !important;
  }
  
  .theme-light .text-secondary {
    color: #6b7280 !important;
  }
  
  /* Force all text in light theme to be black */
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
  
  /* Force override of ALL inline styles in light theme */
  .theme-light [style*="color: #ffffff"] {
    color: #000000 !important;
  }
  
  .theme-light [style*="color: #cbd5e1"] {
    color: #000000 !important;
  }
  
  .theme-light [style*="color: #94a3b8"] {
    color: #6b7280 !important;
  }
  
  .theme-light [style*="color: #1e293b"] {
    color: #000000 !important;
  }
  
  .theme-light [style*="color: #f1f5f9"] {
    color: #000000 !important;
  }
  
  .theme-light [style*="color: #64748b"] {
    color: #6b7280 !important;
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
  
  /* Override specific dashboard elements */
  .theme-light .dashboard-header h2[style*="color: #ffffff"] {
    color: #000000 !important;
  }
  
  .theme-light .dashboard-header p[style*="color: #cbd5e1"] {
    color: #000000 !important;
  }
  
  .theme-light .card-glass div[style*="color: #ffffff"] {
    color: #000000 !important;
  }
  
  .theme-light .card-glass div[style*="color: #94a3b8"] {
    color: #000000 !important;
  }
  
  .theme-light .card-glass div[style*="color: #cbd5e1"] {
    color: #000000 !important;
  }
  
  .theme-light .card-glass .h4[style*="color: #ffffff"] {
    color: #000000 !important;
  }
  
  .theme-light .card-glass .h4[style*="color: #f59e0b"] {
    color: #f59e0b !important;
  }
  
  .theme-light .card-glass .h4[style*="color: #22c55e"] {
    color: #22c55e !important;
  }
  
  .theme-light .card-glass .h4[style*="color: #ef4444"] {
    color: #ef4444 !important;
  }
  
  .theme-light .card-glass .h4[style*="color: #3b82f6"] {
    color: #3b82f6 !important;
  }
  
  .theme-light .card-glass .h4[style*="color: #06b6d4"] {
    color: #06b6d4 !important;
  }
  
  /* Status and priority badges in light theme */
  .theme-light .status-badge,
  .theme-light .priority-badge {
    font-weight: 600 !important;
    padding: 0.375rem 0.75rem !important;
    border-radius: 6px !important;
    font-size: 0.75rem !important;
    text-transform: capitalize !important;
  }
  
  .theme-light .status-badge.status-new,
  .theme-light .status-badge.status-pending {
    background: rgba(234, 88, 12, 0.1) !important;
    color: #ea580c !important;
    border: 1px solid rgba(234, 88, 12, 0.2) !important;
  }
  
  .theme-light .status-badge.status-resolved,
  .theme-light .status-badge.status-closed {
    background: rgba(22, 163, 74, 0.1) !important;
    color: #16a34a !important;
    border: 1px solid rgba(22, 163, 74, 0.2) !important;
  }
  
  .theme-light .priority-badge.priority-high,
  .theme-light .priority-badge.priority-urgent,
  .theme-light .priority-badge.priority-emergency {
    background: rgba(220, 38, 38, 0.1) !important;
    color: #dc2626 !important;
    border: 1px solid rgba(220, 38, 38, 0.2) !important;
  }
  
  /* Keep colored numbers visible and attractive */
  .theme-light .card-glass .h4[style*="color: #f59e0b"],
  .theme-light .card-glass .h5[style*="color: #f59e0b"] {
    color: #ea580c !important;
  }
  
  .theme-light .card-glass .h4[style*="color: #22c55e"],
  .theme-light .card-glass .h5[style*="color: #22c55e"] {
    color: #16a34a !important;
  }
  
  .theme-light .card-glass .h4[style*="color: #ef4444"],
  .theme-light .card-glass .h5[style*="color: #ef4444"] {
    color: #dc2626 !important;
  }
  
  .theme-light .card-glass .h4[style*="color: #3b82f6"],
  .theme-light .card-glass .h5[style*="color: #3b82f6"] {
    color: #2563eb !important;
  }
  
  .theme-light .card-glass .h4[style*="color: #06b6d4"],
  .theme-light .card-glass .h5[style*="color: #06b6d4"] {
    color: #0891b2 !important;
  }
  
  /* Override plain text but keep colored stats */
  .theme-light .card-glass div[style*="color: #94a3b8"] {
    color: #64748b !important;
  }
  
  .theme-light .card-glass div[style*="color: #cbd5e1"] {
    color: #64748b !important;
  }
  
  .theme-light .card-glass div[style*="color: #ffffff"] {
    color: #1e293b !important;
  }
  
  /* Night theme styles */
  .theme-night body {
    background: linear-gradient(135deg, #000000 0%, #111111 100%);
    color: #e5e5e5;
  }
  
  .theme-night .content {
    color: #e5e5e5 !important;
  }
  
  .theme-night .card-glass {
    background: rgba(0, 0, 0, 0.8) !important;
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
    color: #e5e5e5 !important;
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
  
  .theme-night .badge {
    color: #ffffff !important;
  }
  
  /* Dark theme styles */
  .theme-dark body {
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    color: #e2e8f0;
  }
  
  .theme-dark .content {
    color: #e2e8f0 !important;
  }
  
  .theme-dark .card-glass {
    background: rgba(30, 41, 59, 0.8) !important;
    border: 1px solid rgba(148, 163, 184, 0.1) !important;
    color: #e2e8f0 !important;
  }
  
  .theme-dark h1, .theme-dark h2, .theme-dark h3, 
  .theme-dark h4, .theme-dark h5, .theme-dark h6 {
    color: #e2e8f0 !important;
  }
  
  .theme-dark .text-white {
    color: #e2e8f0 !important;
  }
  
  .theme-dark .text-muted {
    color: #94a3b8 !important;
  }
  
  .theme-dark .text-secondary {
    color: #94a3b8 !important;
  }
  
  .theme-dark .btn {
    color: #e2e8f0 !important;
  }
  
  /* Filter labels styling for dark and night themes - white text (override inline !important) */
  /* Use maximum specificity to override inline styles - inline styles with !important require JavaScript */
  /* CSS fallback with maximum specificity */
  .theme-dark .card-glass.mb-4 .row .col-md-3 label.form-label.small.mb-1,
  .theme-night .card-glass.mb-4 .row .col-md-3 label.form-label.small.mb-1,
  .theme-dark .card-glass.mb-4 .row .col-sm-6 label.form-label.small.mb-1,
  .theme-night .card-glass.mb-4 .row .col-sm-6 label.form-label.small.mb-1,
  .theme-dark .card-glass.mb-4 form .row label,
  .theme-night .card-glass.mb-4 form .row label,
  .theme-dark .card-glass.mb-4 form .row .form-label,
  .theme-night .card-glass.mb-4 form .row .form-label {
    color: #ffffff !important;
  }
  
  /* Override inline styles with !important - use attribute selector */
  .theme-dark .card-glass.mb-4 label[style*="color: #1e293b"],
  .theme-night .card-glass.mb-4 label[style*="color: #1e293b"],
  .theme-dark .card-glass.mb-4 .form-label[style*="color: #1e293b"],
  .theme-night .card-glass.mb-4 .form-label[style*="color: #1e293b"] {
    color: #ffffff !important;
  }
  
  /* Force all filter box labels to white in dark/night theme */
  .theme-dark .card-glass.mb-4 label,
  .theme-night .card-glass.mb-4 label,
  .theme-dark .card-glass.mb-4 .form-label,
  .theme-night .card-glass.mb-4 .form-label {
    color: #ffffff !important;
  }
  
  /* NUCLEAR OPTION - Force ALL text to be black in light theme */
  .theme-light * {
    color: #000000 !important;
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
</style>
<!-- DASHBOARD HEADER -->
<div class="mb-4 dashboard-header">
  <h2 class="text-white mb-2">Dashboard Overview</h2>
  <p class="text-light">Real-time complaint management system</p>
    </div>

    <!-- FILTERS SECTION -->
    @php
      $user = Auth::user();
      // Check user table: if city_id is null, user can see all cities, so show city filter
      // If city_id is not null, user is assigned to specific city, so don't show city filter
      $showCityFilter = $user && !$user->city_id;
      // Check user table: if sector_id is null, user can see sectors (all or in their city), so show sector filter
      // If sector_id is not null, user is assigned to specific sector, so don't show sector filter
      $showSectorFilter = $user && !$user->sector_id;
    @endphp
    
    @if($showCityFilter || $showSectorFilter || $categories->count() > 0 || (isset($complaintStatuses) && count($complaintStatuses) > 0) || true)
    <div class="mb-4 d-flex justify-content-center">
      <div class="card-glass" style="display: inline-block; width: fit-content; padding: 1.5rem; background: linear-gradient(135deg,rgb(154, 205, 239) 0%,rgb(153, 207, 237) 100%) !important; border: 1px solid #7dd3fc;">
        <form id="dashboardFiltersForm" method="GET" action="{{ route('admin.dashboard') }}">
          <div class="row g-2 align-items-end">
          @if($showCityFilter && $cities->count() > 0)
          <div class="col-auto">
            <label class="form-label small mb-1" style="font-size: 0.85rem; color: #1e293b !important; font-weight: 600;">GE (City)</label>
            <select class="form-select" id="cityFilter" name="city_id" style="font-size: 0.9rem; width: 180px; border: 1px solid #d1d5db; background: #ffffff; color: #1e293b;">
              <option value=""> Select GE</option>
              @foreach($cities as $city)
                <option value="{{ $city->id }}" {{ (request('city_id') == $city->id || $cityId == $city->id) ? 'selected' : '' }}>{{ $city->name }}</option>
              @endforeach
            </select>
          </div>
          @endif
          
          @if($showSectorFilter && $sectors->count() > 0)
          <div class="col-auto">
            <label class="form-label small mb-1" style="font-size: 0.85rem; color: #1e293b !important; font-weight: 600;">Sector</label>
            <select class="form-select" id="sectorFilter" name="sector_id" style="font-size: 0.9rem; width: 180px; border: 1px solid #d1d5db; background: #ffffff; color: #1e293b;">
              <option value="">All Sectors</option>
              @foreach($sectors as $sector)
                <option value="{{ $sector->id }}" {{ (request('sector_id') == $sector->id || $sectorId == $sector->id) ? 'selected' : '' }}>{{ $sector->name }}</option>
              @endforeach
            </select>
          </div>
          @endif
          
          @if($categories->count() > 0)
          <div class="col-auto">
            <label class="form-label small mb-1" style="font-size: 0.85rem; color: #1e293b !important; font-weight: 600;">Complaint Category</label>
            <select class="form-select" id="categoryFilter" name="category" style="font-size: 0.9rem; width: 180px; border: 1px solid #d1d5db; background: #ffffff; color: #1e293b;">
              <option value="">All Categories</option>
              @foreach($categories as $cat)
                <option value="{{ $cat }}" {{ (request('category') == $cat || $category == $cat) ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
              @endforeach
            </select>
          </div>
          @endif
          
          @if(isset($complaintStatuses) && count($complaintStatuses) > 0)
          <div class="col-auto">
            <label class="form-label small mb-1" style="font-size: 0.85rem; color: #1e293b !important; font-weight: 600;">Complaint Status</label>
            <select class="form-select" id="complaintStatusFilter" name="complaint_status" style="font-size: 0.9rem; width: 180px; border: 1px solid #d1d5db; background: #ffffff; color: #1e293b;">
              <option value="">All Statuses</option>
              @foreach($complaintStatuses as $statusKey => $statusLabel)
                <option value="{{ $statusKey }}" {{ (request('complaint_status') == $statusKey || $complaintStatus == $statusKey) ? 'selected' : '' }}>{{ $statusLabel }}</option>
              @endforeach
            </select>
          </div>
          @endif
          
          <div class="col-auto">
            <label class="form-label small mb-1" style="font-size: 0.85rem; color: #1e293b !important; font-weight: 600;">Date Range</label>
            <select class="form-select" id="dateRangeFilter" name="date_range" style="font-size: 0.9rem; width: 180px; border: 1px solid #d1d5db; background: #ffffff; color: #1e293b;">
              <option value="">All Time</option>
              <option value="yesterday" {{ (request('date_range') == 'yesterday' || $dateRange == 'yesterday') ? 'selected' : '' }}>Yesterday</option>
              <option value="today" {{ (request('date_range') == 'today' || $dateRange == 'today') ? 'selected' : '' }}>Today</option>
              <option value="this_week" {{ (request('date_range') == 'this_week' || $dateRange == 'this_week') ? 'selected' : '' }}>This Week</option>
              <option value="last_week" {{ (request('date_range') == 'last_week' || $dateRange == 'last_week') ? 'selected' : '' }}>Last Week</option>
              <option value="this_month" {{ (request('date_range') == 'this_month' || $dateRange == 'this_month') ? 'selected' : '' }}>This Month</option>
              <option value="last_month" {{ (request('date_range') == 'last_month' || $dateRange == 'last_month') ? 'selected' : '' }}>Last Month</option>
              <option value="last_6_months" {{ (request('date_range') == 'last_6_months' || $dateRange == 'last_6_months') ? 'selected' : '' }}>Last 6 Months</option>
            </select>
          </div>
          
          <div class="col-auto d-flex align-items-end">
            <button type="button" class="btn" onclick="resetDashboardFilters()" style="font-size: 0.85rem; padding: 0.4rem 0.9rem; font-weight: 600; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important; border: none; color: #ffffff !important; box-shadow: 0 2px 4px rgba(239, 68, 68, 0.2); transition: all 0.2s ease;">
              <i data-feather="refresh-cw" style="width: 13px; height: 13px; margin-right: 3px;"></i>Reset
            </button>
          </div>
          </div>
        </form>
      </div>
    </div>
    @endif

    <!-- STATISTICS CARDS -->
    <div class="row mb-4">
      <div class="col-md-3 mb-3">
    <div class="card-glass">
      <div class="d-flex align-items-center">
        <div class="flex-grow-1">
          <div class="h4 mb-1 text-white" style="font-size: 2rem; font-weight: bold;">{{ $stats['total_complaints'] ?? 0 }}</div>
          <div class="text-muted" style="font-size: 0.9rem;">Total Complaints</div>
        </div>
        <div class="text-primary">
          <i data-feather="alert-circle" class="feather-lg"></i>
        </div>
      </div>
        </div>
      </div>
      <div class="col-md-3 mb-3">
    <div class="card-glass">
      <div class="d-flex align-items-center">
        <div class="flex-grow-1">
          <div class="h4 mb-1 text-warning" style="font-size: 2rem; font-weight: bold;">{{ $stats['pending_complaints'] ?? 0 }}</div>
          <div class="text-muted" style="font-size: 0.9rem;">Pending</div>
        </div>
        <div class="text-warning">
          <i data-feather="clock" class="feather-lg"></i>
        </div>
      </div>
        </div>
      </div>
      <div class="col-md-3 mb-3">
    <div class="card-glass">
      <div class="d-flex align-items-center">
        <div class="flex-grow-1">
          <div class="h4 mb-1 text-success" style="font-size: 2rem; font-weight: bold;">{{ $stats['resolved_complaints'] ?? 0 }}</div>
          <div class="text-muted" style="font-size: 0.9rem;">Resolved</div>
        </div>
        <div class="text-success">
          <i data-feather="check-circle" class="feather-lg"></i>
        </div>
      </div>
        </div>
            </div>
      <div class="col-md-3 mb-3">
    <div class="card-glass">
      <div class="d-flex align-items-center">
        <div class="flex-grow-1">
          <div class="h4 mb-1 text-danger" style="font-size: 2rem; font-weight: bold;">{{ $stats['overdue_complaints'] ?? 0 }}</div>
          <div class="text-muted" style="font-size: 0.9rem;">Overdue</div>
        </div>
        <div class="text-danger">
          <i data-feather="alert-triangle" class="feather-lg"></i>
        </div>
      </div>
        </div>
      </div>
    </div>

    

    <!-- ADDITIONAL STATS -->
    <div class="row mb-4 justify-content-center">
      <div class="col-md-2 mb-3">
    <div class="card-glass text-center">
      <div class="h5 mb-1 text-white" style="font-size: 1.5rem; font-weight: bold;">{{ $stats['total_users'] ?? 0 }}</div>
      <div class="text-muted" style="font-size: 0.8rem;">Users</div>
        </div>
      </div>
      <div class="col-md-2 mb-3">
    <div class="card-glass text-center">
      <div class="h5 mb-1 text-white" style="font-size: 1.5rem; font-weight: bold;">{{ $stats['total_employees'] ?? 0 }}</div>
      <div class="text-muted" style="font-size: 0.8rem;">Employees</div>
        </div>
      </div>
      <div class="col-md-2 mb-3">
    <div class="card-glass text-center">
      <div class="h5 mb-1 text-white" style="font-size: 1.5rem; font-weight: bold;">{{ $stats['total_complaints'] ?? 0 }}</div>
      <div class="text-muted" style="font-size: 0.8rem;">Complaints</div>
        </div>
      </div>
      <div class="col-md-2 mb-3">
    <div class="card-glass text-center">
      <div class="h5 mb-1 text-warning" style="font-size: 1.5rem; font-weight: bold;">{{ $stats['low_stock_items'] ?? 0 }}</div>
      <div class="text-muted" style="font-size: 0.8rem;">Low Stock</div>
        </div>
      </div>
      <div class="col-md-2 mb-3">
    <div class="card-glass text-center">
      <div class="h5 mb-1 text-success" style="font-size: 1.5rem; font-weight: bold;">{{ $slaPerformance['sla_percentage'] ?? 0 }}%</div>
      <div class="text-muted" style="font-size: 0.8rem;">SLA Performance</div>
        </div>
      </div>
    </div>

    <!-- CHARTS ROW -->
    <div class="row mb-4">
      <div class="col-md-6">
    <div class="card-glass">
      <h5 class="mb-3 text-white" style="font-weight: bold;">Complaints by Status</h5>
      <div id="complaintsStatusChart" style="height: 300px;"></div>
        </div>
      </div>
      <div class="col-md-6">
    <div class="card-glass">
      <h5 class="mb-3 text-white" style="font-weight: bold;">Complaints by Type</h5>
      <div id="complaintsTypeChart" style="height: 300px;"></div>
        </div>
      </div>
        </div>

    <!-- MONTHLY TRENDS CHART -->
    <div class="row mb-4">
      <div class="col-12">
    <div class="card-glass">
      <h5 class="mb-3 text-white" style="font-weight: bold;">Monthly Trends</h5>
      <div id="monthlyTrendsChart" style="height: 350px;"></div>
        </div>
      </div>
            </div>

    <!-- TABLES ROW -->
    <div class="row">
      <div class="col-12">
        <div class="card-glass">
          <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0 text-white" style="font-weight: bold;">Recent Complaints</h5>
        <a href="{{ route('admin.complaints.index') }}" class="btn btn-accent btn-sm">View All</a>
          </div>
          <div class="table-responsive">
            <table class="table table-dark ">
                <thead>
                <tr>
                  <th>Ticket</th>
                  <th>Complaintant</th>
                  <th>Type</th>
                  <th>Status</th>
                  <th>Priority</th>
                </tr>
                </thead>
              <tbody>
            @forelse($recentComplaints ?? [] as $complaint)
                <tr>
                  <td>{{ $complaint->getTicketNumberAttribute() }}</td>
                  <td>{{ $complaint->client->client_name }}</td>
                  <td>{{ $complaint->getCategoryDisplayAttribute() }}</td>
                  <td>
                    @if($complaint->status === 'resolved' || $complaint->status === 'closed')
                      <span class="status-badge status-{{ $complaint->status }}" style="background-color: #15803d !important; color: #ffffff !important; border: 1px solid #166534 !important; padding: 0.375rem 0.75rem; border-radius: 6px; font-weight: 600; font-size: 0.75rem;">{{ $complaint->getStatusDisplayAttribute() }}</span>
                    @elseif($complaint->status === 'in_progress')
                      <span class="status-badge status-{{ $complaint->status }}" style="background-color: #b91c1c !important; color: #ffffff !important; border: 1px solid #991b1b !important; padding: 0.375rem 0.75rem; border-radius: 6px; font-weight: 600; font-size: 0.75rem;">{{ $complaint->getStatusDisplayAttribute() }}</span>
                    @elseif($complaint->status === 'new' || $complaint->status === 'assigned')
                      <span class="status-badge status-{{ $complaint->status }}" style="background-color: #64748b !important; color: #ffffff !important; border: 1px solid #475569 !important; padding: 0.375rem 0.75rem; border-radius: 6px; font-weight: 600; font-size: 0.75rem;">{{ $complaint->getStatusDisplayAttribute() }}</span>
                    @else
                      <span class="status-badge status-{{ $complaint->status }}" style="color: #ffffff !important;">{{ $complaint->getStatusDisplayAttribute() }}</span>
                    @endif
                  </td>
                  <td><span class="priority-badge priority-{{ $complaint->priority }}">{{ $complaint->getPriorityDisplayAttribute() }}</span></td>
                </tr>
                @empty
                <tr>
                  <td colspan="5" class="text-center py-3">No recent complaints</td>
                </tr>
                @endforelse
                </tbody>
              </table>
        </div>
      </div>
    </div>

    <!-- APPROVALS SECTION -->
    @if(isset($pendingApprovals) && $pendingApprovals->count() > 0)
    <div class="row mt-4">
      <div class="col-12">
        <div class="card-glass">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0 text-warning">
              <i data-feather="clock" class="me-2"></i>
              @if($approvalStatus)
                {{ ucfirst($approvalStatus) }} Approvals
              @else
                In-progress Complaints
              @endif
            </h5>
            <a href="{{ route('admin.approvals.index') }}" class="btn btn-outline-warning btn-sm">View All</a>
          </div>
          <div class="table-responsive">
            <table class="table table-dark">
              <thead>
                <tr>
                  <th>Approval ID</th>
                  <th>Complaint</th>
                  <th>Complaintant</th>
                  <th>Requested By</th>
                  <th>Status</th>
                  <th>Items</th>
                  <th>Created</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($pendingApprovals as $approval)
                <tr>
                  <td>#{{ $approval->id }}</td>
                  <td>{{ $approval->complaint ? $approval->complaint->getTicketNumberAttribute() : 'N/A' }}</td>
                  <td>{{ $approval->complaint && $approval->complaint->client ? $approval->complaint->client->client_name : 'N/A' }}</td>
                  <td>{{ $approval->requestedBy->name ?? 'N/A' }}</td>
                  <td>
                    @php
                      $statusColors = [
                        'pending' => ['bg' => '#f59e0b', 'text' => '#ffffff', 'border' => '#d97706'],
                        'approved' => ['bg' => '#22c55e', 'text' => '#ffffff', 'border' => '#16a34a'],
                        'rejected' => ['bg' => '#ef4444', 'text' => '#ffffff', 'border' => '#dc2626'],
                      ];
                      $statusColor = $statusColors[$approval->status] ?? ['bg' => '#6b7280', 'text' => '#ffffff', 'border' => '#4b5563'];
                    @endphp
                    <span class="badge" style="background-color: {{ $statusColor['bg'] }}; color: {{ $statusColor['text'] }}; border: 1px solid {{ $statusColor['border'] }}; padding: 0.25rem 0.5rem; font-size: 0.75rem; font-weight: 600;">
                      {{ $approval->getStatusDisplayAttribute() }}
                    </span>
                  </td>
                  <td>{{ $approval->items ? $approval->items->count() : 0 }} items</td>
                  <td>{{ $approval->created_at->format('M d, Y H:i') }}</td>
                  <td>
                    <a href="{{ route('admin.approvals.show', $approval->id) }}" class="btn btn-sm btn-outline-primary">
                      <i data-feather="eye" class="me-1"></i>View
                    </a>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    @endif

    <!-- LOW STOCK ALERTS -->
@if(isset($lowStockItems) && $lowStockItems->count() > 0)
    <div class="row mt-4">
      <div class="col-12">
        <div class="card-glass">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0 text-warning">
              <i data-feather="alert-triangle" class="me-2"></i>Low Stock Alerts
            </h5>
            <a href="{{ route('admin.spares.index') }}" class="btn btn-outline-warning btn-sm">Manage Stock</a>
        </div>
        <div class="table-responsive">
            <table class="table table-dark ">
            <thead>
                <tr>
                  <th>Item</th>
                  <th>Category</th>
                  <th>Current Stock</th>
                  <th>Threshold</th>
                  <th>Status</th>
                </tr>
            </thead>
              <tbody>
                @foreach($lowStockItems as $item)
                <tr>
                  <td>{{ $item->item_name }}</td>
                  <td>{{ ucfirst($item->category) }}</td>
                  <td>{{ $item->stock_quantity }}</td>
                  <td>{{ $item->threshold_level }}</td>
                  <td>
                    @if($item->stock_quantity <= 0)
                      <span class="badge bg-danger" style="color: #ffffff !important;">Out of Stock</span>
                    @else
                      <span class="badge bg-warning" style="color: #ffffff !important;">Low Stock</span>
                    @endif
                  </td>
                </tr>
                @endforeach
              </tbody>
          </table>
          </div>
        </div>
      </div>
        </div>
    @endif
@endsection

@push('scripts')
<script>
  feather.replace();

  // Define chart data
  @php
    $defaultComplaints = [0,0,0,0,0,0,0,0,0,0,0,0];
    $defaultResolutions = [0,0,0,0,0,0,0,0,0,0,0,0];
    $defaultMonths = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
  @endphp
  var complaintsData = @json($monthlyTrends['complaints'] ?? $defaultComplaints);
  var resolutionsData = @json($monthlyTrends['resolutions'] ?? $defaultResolutions);
  var monthsData = @json($monthlyTrends['months'] ?? $defaultMonths);

    // Complaints by Status Chart
  @php
    $statusData = isset($complaintsByStatus) ? array_values($complaintsByStatus) : [0, 0, 0, 0, 0];
    $statusLabels = isset($complaintsByStatus) ? array_map(function($status) { return ucfirst(str_replace('_', ' ', $status)); }, array_keys($complaintsByStatus)) : ['New', 'Assigned', 'In Progress', 'Resolved', 'Closed'];
  @endphp
    var complaintsStatusOptions = {
    series: @json($statusData),
      chart: {
        type: 'donut',
        height: 300,
        background: 'transparent'
      },
    labels: @json($statusLabels),
      colors: ['#3b82f6', '#f59e0b', '#a855f7', '#22c55e', '#6b7280'],
      legend: {
        position: 'bottom',
        labels: {
          colors: '#e2e8f0'
        }
      },
      dataLabels: {
        enabled: true,
        style: {
          colors: ['#fff']
        }
      },
      tooltip: {
        theme: document.body.classList.contains('theme-light') ? 'light' : 'dark',
        style: {
          fontSize: '12px',
          fontFamily: 'inherit'
        }
      }
    };

    var complaintsStatusChart = new ApexCharts(document.querySelector("#complaintsStatusChart"), complaintsStatusOptions);
    complaintsStatusChart.render();

    // Complaints by Type Chart
  @php
    $typeData = isset($complaintsByType) ? array_values($complaintsByType) : [0, 0, 0, 0];
    $typeLabels = isset($complaintsByType) ? array_map(function($type) { return ucfirst($type); }, array_keys($complaintsByType)) : ['Electric', 'Sanitary', 'Kitchen', 'General'];
  @endphp
    var complaintsTypeOptions = {
    series: @json($typeData),
      chart: {
        type: 'pie',
        height: 300,
        background: 'transparent'
      },
    labels: @json($typeLabels),
      colors: ['#3b82f6', '#f59e0b', '#a855f7', '#22c55e'],
      legend: {
        position: 'bottom',
        labels: {
          colors: '#e2e8f0'
        }
      },
      dataLabels: {
        enabled: true,
        style: {
          colors: ['#fff']
        }
      },
      tooltip: {
        theme: document.body.classList.contains('theme-light') ? 'light' : 'dark',
        style: {
          fontSize: '12px',
          fontFamily: 'inherit'
        }
      }
    };

    var complaintsTypeChart = new ApexCharts(document.querySelector("#complaintsTypeChart"), complaintsTypeOptions);
    complaintsTypeChart.render();

    // Monthly Trends Chart
    const isLightTheme = document.documentElement.classList.contains('theme-light');
    var monthlyTrendsOptions = {
      series: [{
        name: 'Complaints',
      data: complaintsData
      }, {
        name: 'Resolutions',
      data: resolutionsData
      }],
      chart: {
        type: 'line',
        height: 350,
        background: 'transparent',
        toolbar: {
          show: true,
          tools: {
            download: true,
            selection: true,
            zoom: true,
            zoomin: true,
            zoomout: true,
            pan: true,
            reset: true
          }
        }
      },
      colors: ['#3b82f6', '#22c55e'],
      xaxis: {
      categories: monthsData,
        labels: {
          style: {
            colors: isLightTheme ? '#000000' : '#e2e8f0'
          }
        }
      },
      yaxis: {
        labels: {
          style: {
            colors: isLightTheme ? '#000000' : '#e2e8f0'
          }
        }
      },
      legend: {
        labels: {
          colors: isLightTheme ? '#000000' : '#e2e8f0'
        }
      },
      grid: {
        borderColor: isLightTheme ? '#d1d5db' : '#374151'
      },
      stroke: {
        width: 3
      },
      markers: {
        size: 5
      },
      tooltip: {
        theme: isLightTheme ? 'light' : 'dark',
        style: {
          fontSize: '12px',
          fontFamily: 'inherit'
        }
      }
    };

    var monthlyTrendsChart = new ApexCharts(document.querySelector("#monthlyTrendsChart"), monthlyTrendsOptions);
    monthlyTrendsChart.render();

    // Refresh dashboard function
    function refreshDashboard() {
      location.reload();
    }

    // Auto-refresh every 5 minutes
    setInterval(function() {
      fetch('{{ route("admin.dashboard.real-time-updates") }}')
        .then(response => response.json())
        .then(data => {
          // Update real-time data here if needed
          console.log('Dashboard updated:', data);
        })
        .catch(error => console.error('Error updating dashboard:', error));
    }, 300000); // 5 minutes

    // Dashboard Filters Functions
    function applyDashboardFilters() {
      const form = document.getElementById('dashboardFiltersForm');
      const formData = new FormData(form);
      const params = new URLSearchParams();
      
      // Add filter values to params
      if (formData.get('city_id')) {
        params.append('city_id', formData.get('city_id'));
      }
      if (formData.get('sector_id')) {
        params.append('sector_id', formData.get('sector_id'));
      }
      if (formData.get('category')) {
        params.append('category', formData.get('category'));
      }
      if (formData.get('complaint_status')) {
        params.append('complaint_status', formData.get('complaint_status'));
      }
      if (formData.get('date_range')) {
        params.append('date_range', formData.get('date_range'));
      }
      
      // Reload dashboard with filters
      window.location.href = '{{ route("admin.dashboard") }}?' + params.toString();
    }

    function resetDashboardFilters() {
      window.location.href = '{{ route("admin.dashboard") }}';
    }

    // Dynamic sector loading for Director when city changes and auto-apply filters
    const cityFilter = document.getElementById('cityFilter');
    const sectorFilter = document.getElementById('sectorFilter');
    const categoryFilter = document.getElementById('categoryFilter');
    
    // Auto-apply filters on change (like other modules)
    if (cityFilter) {
      cityFilter.addEventListener('change', function() {
        @if($user && !$user->city_id)
        // User can see all cities: Load sectors dynamically when city changes
        const cityId = this.value;
        
        if (sectorFilter) {
          sectorFilter.innerHTML = '<option value="">Loading sectors...</option>';
          sectorFilter.disabled = true;
          
          if (cityId) {
            // Fetch sectors for selected city
            fetch(`{{ route('admin.sectors.by-city') }}?city_id=${cityId}`, {
              headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
              },
              credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
              sectorFilter.innerHTML = '<option value="">All Sectors</option>';
              const sectors = Array.isArray(data) ? data : (data.sectors || []);
              if (sectors && sectors.length > 0) {
                sectors.forEach(function(sector) {
                  const option = document.createElement('option');
                  option.value = sector.id;
                  option.textContent = sector.name;
                  sectorFilter.appendChild(option);
                });
              }
              sectorFilter.disabled = false;
              // Auto-apply filters after loading sectors
              applyDashboardFilters();
            })
            .catch(error => {
              console.error('Error loading sectors:', error);
              sectorFilter.innerHTML = '<option value="">All Sectors</option>';
              sectorFilter.disabled = false;
              // Auto-apply filters even on error
              applyDashboardFilters();
            });
          } else {
            // Show all sectors if no city selected (Director) - reload page to get all sectors
            sectorFilter.innerHTML = '<option value="">All Sectors</option>';
            sectorFilter.disabled = false;
            // Auto-apply filters
            applyDashboardFilters();
          }
        } else {
          // Auto-apply filters when city changes
          applyDashboardFilters();
        }
        @else
        // For GE: Auto-apply filters when city changes
        applyDashboardFilters();
        @endif
      });
    }
    
    // Auto-apply filters when sector changes
    if (sectorFilter) {
      sectorFilter.addEventListener('change', function() {
        applyDashboardFilters();
      });
    }
    
    // Auto-apply filters when category changes
    if (categoryFilter) {
      categoryFilter.addEventListener('change', function() {
        applyDashboardFilters();
      });
    }
    
    // Auto-apply filters when complaint status changes
    const complaintStatusFilter = document.getElementById('complaintStatusFilter');
    if (complaintStatusFilter) {
      complaintStatusFilter.addEventListener('change', function() {
        applyDashboardFilters();
      });
    }
    
    // Auto-apply filters when date range changes
    const dateRangeFilter = document.getElementById('dateRangeFilter');
    if (dateRangeFilter) {
      dateRangeFilter.addEventListener('change', function() {
        applyDashboardFilters();
      });
    }
    
    // Override inline styles for filter labels in dark/night theme
    function updateFilterLabelsColor() {
      const body = document.body;
      const isDarkTheme = body.classList.contains('theme-dark');
      const isNightTheme = body.classList.contains('theme-night');
      
      if (isDarkTheme || isNightTheme) {
        const filterLabels = document.querySelectorAll('.card-glass.mb-4 label.form-label');
        filterLabels.forEach(function(label) {
          if (label.style.color && label.style.color.includes('#1e293b')) {
            label.style.setProperty('color', '#ffffff', 'important');
          }
        });
      }
    }
    
    // Run on page load
    updateFilterLabelsColor();
    
    // Watch for theme changes
    const observer = new MutationObserver(function(mutations) {
      mutations.forEach(function(mutation) {
        if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
          updateFilterLabelsColor();
        }
      });
    });
    
    if (document.body) {
      observer.observe(document.body, {
        attributes: true,
        attributeFilter: ['class']
      });
    }
</script>
@endpush