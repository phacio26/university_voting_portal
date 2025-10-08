<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Election Results - University Voting Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .winner-card {
            border: 3px solid #28a745;
            background: linear-gradient(135deg, #f8fff8 0%, #e8f5e8 100%);
        }
        .candidate-photo {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 50%;
        }
        .progress {
            height: 20px;
        }
        .position-section {
            margin-bottom: 3rem;
        }
        .tie-alert {
            border-left: 4px solid #ffc107;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container">
            <a class="navbar-brand" href="{{ route('student.dashboard') }}">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
            <div class="navbar-nav ms-auto">
                <a href="{{ route('student.results.download') }}" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-download"></i> Download PDF
                </a>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="text-center mb-5">
                    <h1 class="text-success mb-3">
                        <i class="fas fa-trophy"></i> Election Results
                    </h1>
                    <h3 class="text-dark">{{ $election->title }}</h3>
                    <p class="text-muted">
                        Election Period: 
                        {{ $election->start_time->format('M j, Y g:i A') }} - 
                        {{ $election->end_time->format('M j, Y g:i A') }}
                    </p>
                    <p class="text-muted">
                        Total Votes Cast: <strong>{{ $election->getTotalVotesCount() }}</strong>
                    </p>
                </div>

                @foreach($positions as $position)
                    <div class="position-section">
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-white">
                                <h4 class="mb-0 text-primary">
                                    <i class="fas fa-bullseye"></i> {{ $position->title }}
                                </h4>
                                @if($position->description)
                                    <p class="text-muted mb-0 mt-1">{{ $position->description }}</p>
                                @endif
                            </div>
                            <div class="card-body">
                                @if(isset($position->winner))
                                    <div class="alert alert-success mb-4">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-crown fa-2x text-warning me-3"></i>
                                            <div>
                                                <h5 class="alert-heading mb-1">Winner</h5>
                                                <p class="mb-0">
                                                    <strong>{{ $position->winner->name }}</strong> 
                                                    with {{ $position->winner->vote_count }} votes 
                                                    ({{ $position->winner->vote_percentage }}%)
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @elseif(isset($position->is_tie))
                                    <div class="alert alert-warning tie-alert mb-4">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                                            <div>
                                                <h5 class="alert-heading mb-1">Tie Result</h5>
                                                <p class="mb-0">
                                                    There is a tie between multiple candidates. 
                                                    A revote may be required for this position.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 50px;">Rank</th>
                                                <th style="width: 80px;">Photo</th>
                                                <th>Candidate</th>
                                                <th class="text-center">Votes</th>
                                                <th class="text-center">Percentage</th>
                                                <th style="width: 200px;">Progress</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($position->candidates as $index => $candidate)
                                                <tr class="{{ $index === 0 && !isset($position->is_tie) ? 'table-success' : '' }}">
                                                    <td class="text-center">
                                                        <span class="badge bg-{{ $index === 0 && !isset($position->is_tie) ? 'success' : 'secondary' }}">
                                                            {{ $index + 1 }}
                                                        </span>
                                                    </td>
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
                                                        @if($candidate->bio)
                                                            <br><small class="text-muted">{{ $candidate->bio }}</small>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <strong>{{ $candidate->vote_count }}</strong>
                                                    </td>
                                                    <td class="text-center">
                                                        {{ number_format($candidate->vote_percentage, 1) }}%
                                                    </td>
                                                    <td>
                                                        <div class="progress">
                                                            <div class="progress-bar bg-{{ $index === 0 && !isset($position->is_tie) ? 'success' : 'primary' }}" 
                                                                 role="progressbar" 
                                                                 style="width: {{ $candidate->vote_percentage }}%"
                                                                 aria-valuenow="{{ $candidate->vote_percentage }}" 
                                                                 aria-valuemin="0" aria-valuemax="100">
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                @if($position->candidates->isEmpty())
                                    <div class="text-center py-4">
                                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">No Candidates</h5>
                                        <p class="text-muted">There were no candidates for this position.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach

                @if($positions->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-chart-bar fa-4x text-muted mb-4"></i>
                        <h3 class="text-muted">No Election Results Available</h3>
                        <p class="text-muted">There are no results to display at the moment.</p>
                        <a href="{{ route('student.dashboard') }}" class="btn btn-primary mt-3">
                            <i class="fas fa-arrow-left"></i> Back to Dashboard
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <footer class="bg-light mt-5 py-4">
        <div class="container text-center">
            <p class="text-muted mb-0">
                &copy; {{ date('Y') }} University Voting Portal. Election Results.
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>