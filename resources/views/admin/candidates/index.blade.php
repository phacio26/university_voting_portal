<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidates - Admin Panel</title>
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
        .candidate-photo {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
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
                            <a class="nav-link" href="{{ route('admin.election-periods.index') }}">
                                <i class="fas fa-calendar-alt"></i> Election Periods
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.positions.index') }}">
                                <i class="fas fa-bullseye"></i> Positions
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="{{ route('admin.candidates.index') }}">
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
                            <i class="fas fa-users"></i> Candidates
                        </h2>
                        <a href="{{ route('admin.candidates.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> New Candidate
                        </a>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="card">
                        <div class="card-body">
                            @if($candidates->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Photo</th>
                                                <th>Name</th>
                                                <th>Position</th>
                                                <th>Bio</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($candidates as $candidate)
                                                <tr>
                                                    <td>
                                                        @if($candidate->photo)
                                                            <img src="/storage/{{ $candidate->photo }}" 
                                                                 class="candidate-photo" alt="{{ $candidate->name }}">
                                                        @else
                                                            <div class="candidate-photo bg-light d-flex align-items-center justify-content-center">
                                                                <i class="fas fa-user text-muted"></i>
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <strong>{{ $candidate->name }}</strong>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-info">{{ $candidate->position->title }}</span>
                                                    </td>
                                                    <td>
                                                        @if($candidate->bio)
                                                            <small class="text-muted">{{ Str::limit($candidate->bio, 50) }}</small>
                                                        @else
                                                            <span class="text-muted">No bio</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($candidate->is_disqualified)
                                                            <span class="badge bg-danger status-badge">Disqualified</span>
                                                            @if($candidate->disqualification_reason)
                                                                <br><small class="text-muted">{{ Str::limit($candidate->disqualification_reason, 30) }}</small>
                                                            @endif
                                                        @else
                                                            <span class="badge bg-success status-badge">Active</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            @if($candidate->is_disqualified)
                                                                <form method="POST" action="{{ route('admin.candidates.reinstate', $candidate) }}" class="d-inline">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-outline-success" 
                                                                            title="Reinstate Candidate">
                                                                        <i class="fas fa-undo"></i>
                                                                    </button>
                                                                </form>
                                                            @else
                                                                <button type="button" class="btn btn-outline-warning" 
                                                                        data-bs-toggle="modal" 
                                                                        data-bs-target="#disqualifyModal{{ $candidate->id }}"
                                                                        title="Disqualify Candidate">
                                                                    <i class="fas fa-ban"></i>
                                                                </button>
                                                            @endif

                                                            <a href="{{ route('admin.candidates.edit', $candidate) }}" 
                                                               class="btn btn-outline-primary" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </a>

                                                            <form method="POST" action="{{ route('admin.candidates.destroy', $candidate) }}" 
                                                                  class="d-inline" 
                                                                  onsubmit="return confirm('Are you sure you want to delete this candidate?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        </div>

                                                        <!-- Disqualify Modal -->
                                                        <div class="modal fade" id="disqualifyModal{{ $candidate->id }}" tabindex="-1">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Disqualify Candidate</h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                    </div>
                                                                    <form method="POST" action="{{ route('admin.candidates.disqualify', $candidate) }}">
                                                                        @csrf
                                                                        <div class="modal-body">
                                                                            <p>Are you sure you want to disqualify <strong>{{ $candidate->name }}</strong>?</p>
                                                                            <div class="mb-3">
                                                                                <label for="disqualification_reason" class="form-label">Reason for Disqualification *</label>
                                                                                <textarea class="form-control" id="disqualification_reason" 
                                                                                          name="disqualification_reason" rows="3" required></textarea>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                            <button type="submit" class="btn btn-danger">Disqualify Candidate</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No Candidates</h5>
                                    <p class="text-muted">Get started by adding your first candidate.</p>
                                    <a href="{{ route('admin.candidates.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Add Candidate
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