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
    // Prevent errors from missing scripts
    window.addEventListener('error', function(e) {
      if (e.filename && (e.filename.includes('share-modal.js') || e.filename.includes('share-modal'))) {
        console.warn('share-modal.js not found - ignoring error');
        e.preventDefault();
        return true;
      }
    });
    
    // Handle null element errors
    document.addEventListener('DOMContentLoaded', function() {
      // Override addEventListener to handle null elements gracefully
      const originalAddEventListener = Element.prototype.addEventListener;
      Element.prototype.addEventListener = function(type, listener, options) {
        if (this === null || this === undefined) {
          console.warn('Attempted to add event listener to null element');
          return;
        }
        return originalAddEventListener.call(this, type, listener, options);
      };
    });
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
    .nav-link { color: #cbd5e1; border-radius:8px; transition: none !important; }
    .nav-link:hover, .nav-link.active { background: linear-gradient(90deg, rgba(59, 130, 246, 0.15), rgba(37, 99, 235, 0.1)); color: #fff; transform: none !important; }
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
      transition: none !important;
    }
    .btn-accent:hover { 
      background: linear-gradient(135deg, #2563eb, #1e40af); 
      transform: none !important;
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
    .nav-item-parent .nav-link { position: relative; }
    .nav-arrow-btn:hover { opacity: 0.8; }
    .nav-arrow-btn:focus { outline: none; box-shadow: none; }
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
    <div class="nav-item-parent mb-1">
      <div class="nav-link d-flex align-items-center justify-content-between py-2 px-3 {{ request()->routeIs('admin.employees.*') || request()->routeIs('admin.department.*') || request()->routeIs('admin.designation.*') || request()->routeIs('admin.sector.*') || request()->routeIs('admin.city.*') ? 'active' : '' }}">
        <a href="{{ route('admin.employees.index') }}" class="text-decoration-none text-inherit d-flex align-items-center flex-grow-1">
          <i data-feather="user-check" class="me-2"></i> Employees
        </a>
        <button type="button" class="btn btn-link text-inherit p-0 border-0 nav-arrow-btn" data-bs-toggle="collapse" data-bs-target="#employeesSubmenu" aria-expanded="{{ request()->routeIs('admin.department.*') || request()->routeIs('admin.designation.*') || request()->routeIs('admin.sector.*') || request()->routeIs('admin.city.*') ? 'true' : 'false' }}" style="background: none; color: inherit; cursor: pointer;">
          <i data-feather="chevron-down" class="nav-arrow ms-2" style="font-size: 14px; transition: transform 0.3s;"></i>
        </button>
      </div>
      <div class="collapse {{ request()->routeIs('admin.department.*') || request()->routeIs('admin.designation.*') || request()->routeIs('admin.sector.*') || request()->routeIs('admin.city.*') ? 'show' : '' }}" id="employeesSubmenu">
        <a href="{{ route('admin.department.index') }}" class="nav-link d-block py-2 px-3 mb-2 mt-2 {{ request()->routeIs('admin.department.*') ? 'active' : '' }}" style="background: rgba(59, 130, 246, 0.08); margin-left: 20px; margin-right: 8px; border-left: 3px solid rgba(59, 130, 246, 0.4); border-radius: 6px;">
          <i data-feather="briefcase" class="me-2"></i> Departments
        </a>
        <a href="{{ route('admin.designation.index') }}" class="nav-link d-block py-2 px-3 mb-2 mt-2 {{ request()->routeIs('admin.designation.*') ? 'active' : '' }}" style="background: rgba(59, 130, 246, 0.08); margin-left: 20px; margin-right: 8px; border-left: 3px solid rgba(59, 130, 246, 0.4); border-radius: 6px;">
          <i data-feather="award" class="me-2"></i> Designations
        </a>

         <a href="{{ route('admin.city.index') }}" class="nav-link d-block py-2 px-3 mb-2 mt-2 {{ request()->routeIs('admin.city.*') ? 'active' : '' }}" style="background: rgba(59, 130, 246, 0.08); margin-left: 20px; margin-right: 8px; border-left: 3px solid rgba(59, 130, 246, 0.4); border-radius: 6px;">
          <i data-feather="map" class="me-2"></i> Cities
        </a>
        <a href="{{ route('admin.sector.index') }}" class="nav-link d-block py-2 px-3 mb-2 mt-2 {{ request()->routeIs('admin.sector.*') ? 'active' : '' }}" style="background: rgba(59, 130, 246, 0.08); margin-left: 20px; margin-right: 8px; border-left: 3px solid rgba(59, 130, 246, 0.4); border-radius: 6px;">
          <i data-feather="map-pin" class="me-2"></i> Sectors
        </a>
       
      </div>
    </div>
    <a href="{{ route('admin.clients.index') }}" class="nav-link d-block py-2 px-3 mb-1 {{ request()->routeIs('admin.clients.*') ? 'active' : '' }}">
      <i data-feather="briefcase" class="me-2"></i> Complainant
    </a>
    <div class="nav-item-parent mb-1">
      <div class="nav-link d-flex align-items-center justify-content-between py-2 px-3 {{ request()->routeIs('admin.complaints.*') || request()->routeIs('admin.category.*') ? 'active' : '' }}">
        <a href="{{ route('admin.complaints.index') }}" class="text-decoration-none text-inherit d-flex align-items-center flex-grow-1">
          <i data-feather="alert-circle" class="me-2"></i> Complaints
        </a>
        <button type="button" class="btn btn-link text-inherit p-0 border-0 nav-arrow-btn" data-bs-toggle="collapse" data-bs-target="#complaintsSubmenu" aria-expanded="{{ request()->routeIs('admin.category.*') ? 'true' : 'false' }}" style="background: none; color: inherit; cursor: pointer;">
          <i data-feather="chevron-down" class="nav-arrow ms-2" style="font-size: 14px; transition: transform 0.3s;"></i>
        </button>
      </div>
      <div class="collapse {{ request()->routeIs('admin.category.*') ? 'show' : '' }}" id="complaintsSubmenu">
        <a href="{{ route('admin.category.index') }}" class="nav-link d-block py-2 px-3 mb-2 mt-2 {{ request()->routeIs('admin.category.*') ? 'active' : '' }}" style="background: rgba(59, 130, 246, 0.08); margin-left: 20px; margin-right: 8px; border-left: 3px solid rgba(59, 130, 246, 0.4); border-radius: 6px;">
          <i data-feather="tag" class="me-2"></i> Categories
        </a>
      </div>
    </div>
    <a href="{{ route('admin.spares.index') }}" class="nav-link d-block py-2 px-3 mb-1 {{ request()->routeIs('admin.spares.*') ? 'active' : '' }}">
      <i data-feather="package" class="me-2"></i> Products
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

      // Handle submenu collapse/expand with arrow rotation
      const submenus = ['complaintsSubmenu', 'employeesSubmenu'];
      
      submenus.forEach(submenuId => {
        const submenu = document.getElementById(submenuId);
        if (submenu) {
          const parent = submenu.closest('.nav-item-parent');
          const arrow = parent ? parent.querySelector('.nav-arrow') : null;
          
          if (arrow) {
            submenu.addEventListener('show.bs.collapse', function() {
              arrow.style.transform = 'rotate(180deg)';
            });
            
            submenu.addEventListener('hide.bs.collapse', function() {
              arrow.style.transform = 'rotate(0deg)';
            });
            
            // Initialize arrow position based on current state
            if (submenu.classList.contains('show')) {
              arrow.style.transform = 'rotate(180deg)';
            } else {
              arrow.style.transform = 'rotate(0deg)';
            }
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
  </script>
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  @stack('scripts')
</body>
</html>