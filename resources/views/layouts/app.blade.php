<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - FNB Dashboard</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --success: #27ae60;
            --warning: #f39c12;
            --danger: #e74c3c;
        }
        
        html, body {
        height: 100%;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            display: flex;
            flex-direction: column;
        }

        .wrapper {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        }
        
        .content-area {
            flex-grow: 1;
        }
        
        .navbar-custom {
            background: linear-gradient(135deg, var(--primary) 0%, #34495e 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .nav-link {
            color: #ecf0f1 !important;
            font-weight: 500;
            padding: 0.8rem 1.2rem !important;
            border-radius: 0.25rem;
            margin: 0 0.1rem;
            transition: all 0.3s ease;
        }
        
        .nav-link:hover, .nav-link.active {
            background-color: rgba(255,255,255,0.15);
            color: #fff !important;
            transform: translateY(-1px);
        }
        
        .navbar-brand {
            font-weight: 700;
            color: #fff !important;
            font-size: 1.5rem;
        }
        
        .dashboard-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border: none;
            transition: transform 0.3s ease;
        }
        
        .dashboard-card:hover {
            transform: translateY(-2px);
        }
        
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .order-card {
            border-left: 4px solid var(--secondary);
        }
        
        .kitchen-item {
            background: #fff;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            border-left: 4px solid var(--warning);
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .kitchen-item.urgent {
            border-left-color: var(--danger);
            background: #fff5f5;
        }
        
        .kitchen-item.completed {
            border-left-color: var(--success);
            opacity: 0.8;
        }
    </style>

    @stack('styles')
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <!-- Top Navigation -->
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top">
        <div class="container-fluid">
            <!-- Brand -->
            <a class="navbar-brand" href="">
                <i class="fas fa-utensils me-2"></i>Pak Kumis
            </a>

            <!-- Mobile Toggle -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navigation Menu -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <!-- Dashboard -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard.index') ? 'active' : '' }}" 
                           href="{{ route('dashboard.index') }}">
                            <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                        </a>
                    </li>

                    <!-- Orders -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('orders.*') ? 'active' : '' }}" 
                           href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-shopping-cart me-1"></i>Orders
                            <span class="badge bg-danger ms-1" id="orderCount">0</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('orders.active') }}">Active Orders</a></li>
                            <li><a class="dropdown-item" href="{{ route('orders.history') }}">Order History</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="">Create Manual Order</a></li>
                        </ul>
                    </li>

                    <!-- Kitchen Display -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('kitchen.index*') ? 'active' : '' }}" 
                           href="{{ route('kitchen.index') }}">
                            <i class="fas fa-fire me-1"></i>Kitchen
                            <span class="badge bg-warning ms-1" id="kitchenCount">0</span>
                        </a>
                    </li>

                    <!-- Menu Management -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('customer.menu*') ? 'active' : '' }}" 
                           href="{{ route('customer.menu') }}" target="_blank">
                            <i class="fas fa-book me-1"></i>Menu
                        </a>
                    </li>

                    <!-- Runners -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('runners.*') ? 'active' : '' }}" 
                           href="">
                            <i class="fas fa-running me-1"></i>Runners
                            <span class="badge bg-info ms-1" id="runnerCount">0</span>
                        </a>
                    </li>

                    <!-- Reports -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('reports.*') ? 'active' : '' }}" 
                           href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-chart-bar me-1"></i>Reports
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="">Daily Sales</a></li>
                            <li><a class="dropdown-item" href="">Weekly Report</a></li>
                            <li><a class="dropdown-item" href="">Monthly Report</a></li>
                        </ul>
                    </li>
                </ul>

                <!-- Right Side - User Menu -->
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i>{{ Auth::user()->name ?? 'Admin' }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href=""><i class="fas fa-cog me-2"></i>Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="wrapper">
        <main class="container-fluid py-4">
            <!-- Page Title & Breadcrumb -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="h3 mb-1">@yield('page-title')</h2>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                                    @yield('breadcrumb')
                                </ol>
                            </nav>
                        </div>
                        <div>
                            @yield('header-action')
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content -->
            @yield('content')
        </main>
    </div>
    <!-- Footer -->
    <footer class="bg-dark text-light py-3 mt-5">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0">&copy; {{ date('Y') }} Ayam Pak Kumis. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <span class="badge bg-success">
                        <i class="fas fa-circle me-1"></i>Online
                    </span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Real-time Updates -->
    <script>
        // Update badge counts (example - integrate with WebSockets later)
        function updateBadgeCounts() {
            fetch('/order-counts')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('orderCount').textContent = data.pending;
                    document.getElementById('kitchenCount').textContent = data.kitchen;
                    document.getElementById('runnerCount').textContent = data.runners;
                });
        }

        // Update every 30 seconds
        setInterval(updateBadgeCounts, 30000);
        updateBadgeCounts(); // Initial load
    </script>

    @yield('scripts')
</body>
</html>