<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voting - University Voting Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .candidate-card {
            transition: all 0.3s ease;
            cursor: pointer;
            border: 2px solid transparent;
        }
        .candidate-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .candidate-card.selected {
            border-color: #0d6efd;
            background-color: #f8f9fa;
            box-shadow: 0 5px 15px rgba(13, 110, 253, 0.2);
        }
        .candidate-photo {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #dee2e6;
        }
        .candidate-card.selected .candidate-photo {
            border-color: #0d6efd;
        }
        .progress {
            height: 10px;
        }
        .voting-container {
            min-height: 80vh;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('student.dashboard') }}">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text">
                    Voting: <strong>{{ $activeElection->title }}</strong>
                </span>
            </div>
        </div>
    </nav>

    <div class="container voting-container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h4 class="mb-0 text-center text-primary" id="position-title">
                            <i class="fas fa-vote-yea"></i> Select Your Candidate
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        <div id="voting-interface">
                            <!-- Voting interface will be loaded here via JavaScript -->
                        </div>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-body">
                        <h6 class="card-title">
                            <i class="fas fa-info-circle text-info"></i> Voting Instructions
                        </h6>
                        <ul class="list-unstyled mb-0">
                            <li><small class="text-muted">• Select one candidate per position</small></li>
                            <li><small class="text-muted">• You must vote for all positions to proceed</small></li>
                            <li><small class="text-muted">• Click "Vote & Next" to save your selection and move to the next position</small></li>
                            <li><small class="text-muted">• You can go back to previous positions to change your vote</small></li>
                            <li><small class="text-muted">• Once submitted, votes cannot be changed</small></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const positions = @json($positions);
        let currentPositionIndex = 0;
        const votes = {};

        function loadPosition(index) {
            const position = positions[index];
            const isLast = index === positions.length - 1;
            const progress = ((index + 1) / positions.length) * 100;
            
            let html = `
                <div class="text-center mb-4">
                    <h5 class="text-dark">${position.title}</h5>
                    ${position.description ? `<p class="text-muted">${position.description}</p>` : ''}
                    
                    <div class="progress mb-3" style="height: 8px;">
                        <div class="progress-bar bg-primary" role="progressbar" 
                             style="width: ${progress}%"
                             aria-valuenow="${progress}" aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>
                    <small class="text-muted">Position ${index + 1} of ${positions.length}</small>
                </div>
                
                <div class="row g-4" id="candidates-container">
            `;

            if (position.candidates.length === 0) {
                html += `
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No Candidates Available</h5>
                        <p class="text-muted">There are no candidates for this position at the moment.</p>
                    </div>
                `;
            } else {
                position.candidates.forEach(candidate => {
                    const isSelected = votes[position.id] === candidate.id;
                    html += `
                        <div class="col-md-6 col-lg-4">
                            <div class="card candidate-card h-100 ${isSelected ? 'selected' : ''}" 
                                 onclick="selectCandidate(${position.id}, ${candidate.id})">
                                <div class="card-body text-center p-4">
                                    <div class="mb-3">
                                        ${candidate.photo ? 
                                            `<img src="/storage/${candidate.photo}" class="candidate-photo" alt="${candidate.name}">` :
                                            `<div class="candidate-photo bg-light d-flex align-items-center justify-content-center mx-auto">
                                                <i class="fas fa-user fa-2x text-muted"></i>
                                            </div>`
                                        }
                                    </div>
                                    <h6 class="card-title mb-2">${candidate.name}</h6>
                                    ${candidate.bio ? `<p class="card-text small text-muted">${candidate.bio}</p>` : ''}
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="radio" 
                                               name="candidate_${position.id}" 
                                               value="${candidate.id}" 
                                               ${isSelected ? 'checked' : ''}
                                               onchange="selectCandidate(${position.id}, ${candidate.id})">
                                        <label class="form-check-label small">Select Candidate</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
            }

            html += `</div>`;

            // Navigation buttons
            html += `
                <div class="row mt-5">
                    <div class="col-4">
                        ${index > 0 ? `
                            <button class="btn btn-outline-secondary" onclick="navigateTo(${index - 1})">
                                <i class="fas fa-arrow-left"></i> Previous
                            </button>
                        ` : ''}
                    </div>
                    <div class="col-4 text-center">
                        <button class="btn btn-success px-4" onclick="recordVote()" id="vote-btn" 
                                ${!votes[position.id] || position.candidates.length === 0 ? 'disabled' : ''}>
                            <i class="fas fa-check-circle"></i> 
                            ${isLast ? 'Finalize Votes' : 'Vote & Next'}
                        </button>
                    </div>
                    <div class="col-4 text-end">
                        ${!isLast ? `
                            <button class="btn btn-primary" onclick="navigateTo(${index + 1})" id="next-btn" disabled>
                                Next <i class="fas fa-arrow-right"></i>
                            </button>
                        ` : ''}
                    </div>
                </div>
            `;

            document.getElementById('voting-interface').innerHTML = html;
            document.getElementById('position-title').innerHTML = 
                `<i class="fas fa-vote-yea"></i> ${position.title}`;
        }

        function selectCandidate(positionId, candidateId) {
            votes[positionId] = candidateId;
            loadPosition(currentPositionIndex); // Reload to update UI
            
            // Enable vote button if there are candidates
            const position = positions[currentPositionIndex];
            if (position.candidates.length > 0) {
                document.getElementById('vote-btn').disabled = false;
            }
        }

        function recordVote() {
            const position = positions[currentPositionIndex];
            const candidateId = votes[position.id];

            if (!candidateId) {
                alert('Please select a candidate before voting.');
                return;
            }

            // Show loading state
            const voteBtn = document.getElementById('vote-btn');
            const originalText = voteBtn.innerHTML;
            voteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            voteBtn.disabled = true;

            // Send vote to server
            fetch('{{ route("student.vote") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    position_id: position.id,
                    candidate_id: candidateId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                    voteBtn.innerHTML = originalText;
                    voteBtn.disabled = false;
                    return;
                }

                // Enable next button or proceed to submission
                if (currentPositionIndex < positions.length - 1) {
                    document.getElementById('next-btn').disabled = false;
                    voteBtn.innerHTML = originalText;
                    voteBtn.disabled = false;
                } else {
                    // All positions voted, proceed to submission
                    window.location.href = '{{ route("student.voting.submit") }}';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while recording your vote. Please try again.');
                voteBtn.innerHTML = originalText;
                voteBtn.disabled = false;
            });
        }

        function navigateTo(index) {
            if (index >= 0 && index < positions.length) {
                currentPositionIndex = index;
                loadPosition(currentPositionIndex);
            }
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            loadPosition(0);
        });
    </script>
</body>
</html>