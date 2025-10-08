<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - University Voting Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        .stat-card {
            transition: transform 0.2s;
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .main-content {
            background: #f8f9fa;
            min-height: 100vh;
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
                            <a class="nav-link active" href="{{ route('admin.dashboard') }}">
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
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="text-primary">
                            <i class="fas fa-tachometer-alt"></i> Admin Dashboard
                        </h2>
                        <div class="text-muted">
                            <i class="fas fa-clock"></i> 
                            <span id="current-time"></span> (Malawi Time)
                        </div>
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

                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card stat-card border-primary">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title text-muted mb-2">Total Students</h6>
                                            <h3 class="text-primary">{{ $stats['total_students'] }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-users fa-2x text-primary"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card stat-card border-success">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title text-muted mb-2">Total Positions</h6>
                                            <h3 class="text-success">{{ $stats['total_positions'] }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-bullseye fa-2x text-success"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card stat-card border-warning">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title text-muted mb-2">Total Candidates</h6>
                                            <h3 class="text-warning">{{ $stats['total_candidates'] }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-user-friends fa-2x text-warning"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card stat-card border-info">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title text-muted mb-2">Active Elections</h6>
                                            <h3 class="text-info">{{ $stats['active_elections'] }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-vote-yea fa-2x text-info"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($activeElection)
                    <!-- Voting Statistics -->
                    <div class="row mb-4">
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card stat-card border-danger">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title text-muted mb-2">Total Votes</h6>
                                            <h3 class="text-danger">{{ $stats['total_votes'] ?? 0 }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-chart-bar fa-2x text-danger"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card stat-card border-success">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title text-muted mb-2">Voter Turnout</h6>
                                            <h3 class="text-success">{{ number_format($stats['voter_turnout'] ?? 0, 1) }}%</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-percentage fa-2x text-success"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card stat-card border-warning">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title text-muted mb-2">Remaining Students</h6>
                                            <h3 class="text-warning">{{ $stats['total_students'] - ($stats['total_votes'] ?? 0) }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-user-clock fa-2x text-warning"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts -->
                    <div class="row mb-4">
                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-chart-line"></i> Voting Progress
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="votingProgressChart" height="250"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-chart-pie"></i> Vote Distribution
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="voteDistributionChart" height="250"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Recent Activity -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-history"></i> Recent Votes
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($recentVotes->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Student</th>
                                                <th>Candidate</th>
                                                <th>Position</th>
                                                <th>Time</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recentVotes as $vote)
                                                <tr>
                                                    <td>{{ $vote->student->name }}</td>
                                                    <td>{{ $vote->candidate->name }}</td>
                                                    <td>{{ $vote->position->title }}</td>
                                                    <td>{{ $vote->created_at->format('M j, g:i A') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-info-circle fa-2x text-muted mb-3"></i>
                                    <p class="text-muted">No votes recorded yet.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Update current time
        function updateTime() {
            const now = new Date();
            document.getElementById('current-time').textContent = 
                now.toLocaleString('en-US', { 
                    timeZone: 'Africa/Blantyre',
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                });
        }
        setInterval(updateTime, 1000);
        updateTime();

        @if($activeElection)
        // Load voting statistics
        fetch('{{ route("admin.stats") }}')
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error(data.error);
                    return;
                }

                // Voting Progress Chart
                const progressCtx = document.getElementById('votingProgressChart').getContext('2d');
                new Chart(progressCtx, {
                    type: 'bar',
                    data: {
                        labels: data.positions.map(p => p.title),
                        datasets: [{
                            label: 'Votes per Position',
                            data: data.positions.map(p => 
                                p.candidates.reduce((sum, c) => sum + c.votes, 0)
                            ),
                            backgroundColor: '#0d6efd',
                            borderColor: '#0b5ed7',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Number of Votes'
                                }
                            }
                        }
                    }
                });

                // Vote Distribution Chart
                const distributionCtx = document.getElementById('voteDistributionChart').getContext('2d');
                const position = data.positions[0]; // First position for pie chart
                if (position && position.candidates.length > 0) {
                    new Chart(distributionCtx, {
                        type: 'pie',
                        data: {
                            labels: position.candidates.map(c => c.name),
                            datasets: [{
                                data: position.candidates.map(c => c.votes),
                                backgroundColor: [
                                    '#0d6efd', '#198754', '#ffc107', '#dc3545',
                                    '#6f42c1', '#fd7e14', '#20c997', '#e83e8c'
                                ]
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                },
                                title: {
                                    display: true,
                                    text: position.title
                                }
                            }
                        }
                    });
                }
            })
            .catch(error => console.error('Error loading stats:', error));
        @endif
    </script>
</body>
</html>