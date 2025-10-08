<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - University Voting Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .dashboard-card {
            transition: transform 0.2s;
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
        }
        .status-badge {
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-vote-yea"></i> University Voting Portal
            </a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">
                    Welcome, {{ $student->name }}
                </span>
                <a href="{{ route('student.logout') }}" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
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

        <div class="row">
            <div class="col-md-8">
                <div class="card dashboard-card">
                    <div class="card-header bg-white">
                        <h4 class="mb-0 text-primary">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </h4>
                    </div>
                    <div class="card-body">
                        @if($activeElection)
                            <div class="alert alert-info">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h5 class="alert-heading">
                                            <i class="fas fa-calendar-alt"></i> {{ $activeElection->title }}
                                        </h5>
                                        <p class="mb-1">{{ $activeElection->description }}</p>
                                        <small class="d-block">
                                            <strong>Voting Period:</strong> 
                                            {{ $activeElection->start_time->format('M j, Y g:i A') }} - 
                                            {{ $activeElection->end_time->format('M j, Y g:i A') }}
                                        </small>
                                        <small class="text-muted">
                                            <i class="fas fa-clock"></i> Malawi Time
                                        </small>
                                    </div>
                                    @if($activeElection->isVotingOpen())
                                        <span class="badge bg-success status-badge">Active</span>
                                    @elseif($activeElection->hasEnded())
                                        <span class="badge bg-secondary status-badge">Ended</span>
                                    @else
                                        <span class="badge bg-warning status-badge">Upcoming</span>
                                    @endif
                                </div>
                            </div>

                            @if($hasVoted)
                                <div class="alert alert-success text-center">
                                    <i class="fas fa-check-circle fa-2x mb-3"></i>
                                    <h5>Thank You for Voting!</h5>
                                    <p class="mb-0">You have successfully cast your vote in this election.</p>
                                </div>
                            @elseif($canVote)
                                <div class="text-center py-4">
                                    <a href="{{ route('student.voting.index') }}" class="btn btn-primary btn-lg px-5">
                                        <i class="fas fa-vote-yea"></i> Cast Your Vote Now
                                    </a>
                                    <p class="text-muted mt-2">
                                        Make your voice heard! Vote for your preferred candidates.
                                    </p>
                                </div>
                            @elseif($activeElection->hasEnded())
                                <div class="alert alert-warning text-center">
                                    <i class="fas fa-clock fa-2x mb-3"></i>
                                    <h5>Voting Period Has Ended</h5>
                                    <p class="mb-0">The voting period for this election has concluded.</p>
                                </div>
                            @else
                                <div class="alert alert-warning text-center">
                                    <i class="fas fa-clock fa-2x mb-3"></i>
                                    <h5>Voting Not Yet Open</h5>
                                    <p class="mb-0">
                                        Voting will open on 
                                        <strong>{{ $activeElection->start_time->format('M j, Y g:i A') }}</strong>
                                    </p>
                                </div>
                            @endif
                        @else
                            <div class="alert alert-warning text-center py-4">
                                <i class="fas fa-info-circle fa-2x mb-3"></i>
                                <h5>No Active Election</h5>
                                <p class="mb-0">There are no active elections at the moment. Please check back later.</p>
                            </div>
                        @endif

                        @if($showResults)
                            <div class="text-center mt-4">
                                <a href="{{ route('student.results.index') }}" class="btn btn-success btn-lg px-5">
                                    <i class="fas fa-chart-bar"></i> View Election Results
                                </a>
                                <p class="text-muted mt-2">
                                    See the final results of the election.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card dashboard-card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0 text-primary">
                            <i class="fas fa-user-circle"></i> Student Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 50px; height: 50px;">
                                <i class="fas fa-user text-white"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="mb-0">{{ $student->name }}</h6>
                                <small class="text-muted">Student</small>
                            </div>
                        </div>
                        
                        <div class="mb-2">
                            <strong>Registration No:</strong>
                            <span class="float-end">{{ $student->registration_number }}</span>
                        </div>
                        <div class="mb-2">
                            <strong>Email:</strong>
                            <span class="float-end">{{ $student->email }}</span>
                        </div>
                        <div class="mb-0">
                            <strong>Status:</strong>
                            <span class="float-end badge bg-success">Active</span>
                        </div>
                    </div>
                </div>

                <div class="card dashboard-card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0 text-primary">
                            <i class="fas fa-info-circle"></i> Quick Actions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            @if($activeElection && $canVote)
                                <a href="{{ route('student.voting.index') }}" class="btn btn-primary">
                                    <i class="fas fa-vote-yea"></i> Vote Now
                                </a>
                            @endif
                            
                            @if($showResults)
                                <a href="{{ route('student.results.index') }}" class="btn btn-success">
                                    <i class="fas fa-chart-bar"></i> View Results
                                </a>
                            @endif
                            
                            <a href="{{ route('student.logout') }}" class="btn btn-outline-danger">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-light mt-5 py-4">
        <div class="container text-center">
            <p class="text-muted mb-0">
                &copy; {{ date('Y') }} University Voting Portal. All rights reserved.
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>