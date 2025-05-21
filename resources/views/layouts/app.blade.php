<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>IoT Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body {
            background: #f0f4f8;
        }
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #74ebd5 0%, #ACB6E5 100%);
            padding-top: 40px;
            box-shadow: 2px 0 8px rgba(0,0,0,0.05);
        }
        .sidebar .nav-link {
            color: #2563eb;
            font-size: 18px;
            margin-bottom: 12px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: background 0.2s, color 0.2s;
        }
        .sidebar .nav-link.active, .sidebar .nav-link:focus, .sidebar .nav-link:hover {
            background: #fff;
            color: #2563eb;
            font-weight: bold;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        .sidebar .nav-link i {
            font-size: 20px;
        }
        .sidebar-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 2rem;
            text-align: center;
            letter-spacing: 2px;
        }
        @media (max-width: 767px) {
            .sidebar {
                min-height: auto;
                padding-top: 10px;
            }
            .sidebar-title {
                font-size: 1.2rem;
                margin-bottom: 1rem;
            }
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row flex-nowrap">
        <!-- Sidebar -->
        <div class="col-auto col-md-2 px-sm-2 px-0 sidebar">
            <div class="sidebar-title mb-4">
                <i class="bi bi-cpu"></i> IoT Dashboard
            </div>
            <nav class="nav flex-column">
                <a class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="bi bi-house-door"></i> Home
                </a>
                <a class="nav-link {{ request()->is('devices*') ? 'active' : '' }}" href="{{ route('devices.index') }}">
                    <i class="bi bi-hdd-network"></i> Board
                </a>
                <a class="nav-link {{ request()->is('history') ? 'active' : '' }}" href="{{ route('history.index') }}">
                    <i class="bi bi-clock-history"></i> History
                </a>
                <a class="nav-link {{ request()->is('profile') ? 'active' : '' }}" href="{{ route('profile.edit') }}">
                    <i class="bi bi-gear"></i> Settings
                </a>
            </nav>
        </div>
        <!-- Main Content -->
        <div class="col py-4" style="background:#fff;min-height:100vh;">
            @yield('content')
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
