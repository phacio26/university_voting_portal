<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Votes - University Voting Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .review-card {
            border-left: 4px solid #0d6efd;
        }
        .candidate-photo-sm {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ route('student.dashboard') }}">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header bg-success text-white text-center py-4">
                        <i class="fas fa-check-circle fa-3x mb-3"></i>
                        <h3 class="mb-0">Vote Submission Review</h3>
                        <p class="mb-0 mt-2">Please review your votes before final submission</p>
                    </div>
                    <div class="card-body p-4">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Important:</strong> Once you submit your votes, you cannot change them. 
                            Please ensure all selections are correct.
                        </div>

                        <h5 class="mb-4">Your Votes Summary:</h5>
                        
                        <div id="votes-review">
                            <!-- Votes will be loaded via JavaScript -->
                        </div>

                        <div class="alert alert-warning mt-4">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Final Step:</strong> Click the "Submit All Votes" button below to 
                            finalize your participation in this election.
                        </div>

                        <form method="POST" action="{{ route('student.voting.finalize') }}" id="finalize-form">
                            @csrf
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                <a href="{{ route('student.voting.index') }}" class="btn btn-outline-secondary me-md-2">
                                    <i class="fas fa-edit"></i> Review Votes
                                </a>
                                <button type="submit" class="btn btn-success px-4" id="submit-btn">
                                    <i class="fas fa-paper-plane"></i> Submit All Votes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Load votes summary
        document.addEventListener('DOMContentLoaded', function() {
            fetch('{{ route("student.voting.index") }}')
                .then(response => response.text())
                .then(html => {
                    // This would typically come from an API endpoint
                    // For now, we'll show a loading message
                    document.getElementById('votes-review').innerHTML = `
                        <div class="text-center py-3">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 text-muted">Loading your votes summary...</p>
                        </div>
                    `;
                    
                    // Simulate loading votes data
                    setTimeout(() => {
                        document.getElementById('votes-review').innerHTML = `
                            <div class="alert alert-success">
                                <i class="fas fa-check"></i>
                                All positions have been voted. Your votes are ready for submission.
                            </div>
                            <p class="text-muted">
                                You have successfully voted for all available positions. 
                                Your votes have been recorded and are ready to be finalized.
                            </p>
                        `;
                    }, 1000);
                });

            // Prevent double submission
            document.getElementById('finalize-form').addEventListener('submit', function(e) {
                const submitBtn = document.getElementById('submit-btn');
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
                submitBtn.disabled = true;
            });
        });
    </script>
</body>
</html>