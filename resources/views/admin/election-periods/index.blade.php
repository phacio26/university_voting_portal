<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Election Periods - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            background: #343a40;
            min-height: 100vh;
            color: white;
        }
        .sidebar .nav-link {
            color: #adb5bd;
            padding: 0.75rem 1rem;
            border-left: 3px solid transparent;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background: #495057;
            border-left-color: #0d6efd;
        }
        .sidebar .nav-link i {
            width: 20px;
            margin-right: 10px;
        }
        .main-content {
            background: #f8f9fa;
            min-height: 100vh;
        }
        .status-badge {
            font-size: 0.75rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar p-0">
                <div class="sidebar-sticky">
                    <div class="p-3 border-bottom">
                        <h5 class="text-center mb-0">
                            <i class="fas fa-user-shield"></i> Admin Panel
                        </h5>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="{{ route('admin.election-periods.index') }}">
                                <i class="fas fa-calendar-alt"></i> Election Periods
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.positions.index') }}">
                                <i class="fas fa-bullseye"></i> Positions
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.candidates.index') }}">
                                <i class="fas fa-users"></i> Candidates
                            </a>
                        </li>
                        <li class="nav-item mt-3">
                            <a class="nav-link text-danger" href="{{ route('admin.logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                            <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="text-primary">
                            <i class="fas fa-calendar-alt"></i> Election Periods
                        </h2>
                        <a href="{{ route('admin.election-periods.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> New Election Period
                        </a>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="card">
                        <div class="card-body">
                            @if($electionPeriods->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Title</th>
                                                <th>Start Time</th>
                                                <th>End Time</th>
                                                <th>Status</th>
                                                <th>Results</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($electionPeriods as $period)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $period->title }}</strong>
                                                        @if($period->description)
                                                            <br><small class="text-muted">{{ Str::limit($period->description, 50) }}</small>
                                                        @endif
                                                    </td>
                                                    <td>{{ $period->start_time->format('M j, Y g:i A') }}</td>
                                                    <td>{{ $period->end_time->format('M j, Y g:i A') }}</td>
                                                    <td>
                                                        @if($period->is_active)
                                                            <span class="badge bg-success status-badge">Active</span>
                                                        @else
                                                            <span class="badge bg-secondary status-badge">Inactive</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($period->results_available)
                                                            <span class="badge bg-info status-badge">Available</span>
                                                        @else
                                                            <span class="badge bg-warning status-badge">Hidden</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            @if(!$period->is_active)
                                                                <form method="POST" action="{{ route('admin.election-periods.activate', $period) }}" class="d-inline">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-outline-success" 
                                                                            title="Activate Election">
                                                                        <i class="fas fa-play"></i>
                                                                    </button>
                                                                </form>
                                                            @else
                                                                <form method="POST" action="{{ route('admin.election-periods.deactivate', $period) }}" class="d-inline">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-outline-warning" 
                                                                            title="Deactivate Election">
                                                                        <i class="fas fa-pause"></i>
                                                                    </button>
                                                                </form>
                                                            @endif

                                                            @if(!$period->results_available)
                                                                <form method="POST" action="{{ route('admin.election-periods.results-available', $period) }}" class="d-inline">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-outline-info" 
                                                                            title="Make Results Available">
                                                                        <i class="fas fa-eye"></i>
                                                                    </button>
                                                                </form>
                                                            @endif

                                                            <a href="{{ route('admin.election-periods.edit', $period) }}" 
                                                               class="btn btn-outline-primary" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </a>

                                                            <form method="POST" action="{{ route('admin.election-periods.destroy', $period) }}" 
                                                                  class="d-inline" 
                                                                  onsubmit="return confirm('Are you sure you want to delete this election period?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No Election Periods</h5>
                                    <p class="text-muted">Get started by creating your first election period.</p>
                                    <a href="{{ route('admin.election-periods.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Create Election Period
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>