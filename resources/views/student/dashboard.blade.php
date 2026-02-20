@extends('layouts.student-portal')

@section('title', 'Student Dashboard')

@push('styles')
<style>
    .kpi {
        border: 1px solid var(--portal-border);
        background: #f8fbff;
        border-radius: 14px;
        padding: 1rem;
    }
    .kpi h2 {
        font-size: 1.55rem;
        font-weight: 800;
        margin: 0;
        line-height: 1.2;
        word-break: break-word;
    }
    .quick-links-row {
        display: flex;
        gap: 0.5rem;
        flex-wrap: nowrap;
    }
    .quick-links-row .btn {
        flex: 1 1 0;
        min-width: 0;
        min-height: 2.35rem;
        padding: 0.42rem 0.65rem;
        font-size: 0.92rem;
        font-weight: 700;
        line-height: 1.2;
    }
    .quick-links-row .btn i {
        color: inherit;
    }
    .quick-actions-title {
        letter-spacing: 0;
        color: #0a1528;
    }
    .mini-stat {
        border: 1px solid var(--portal-border);
        border-radius: 12px;
        background: #fff;
        padding: 0.75rem;
        text-align: center;
    }
    .mini-stat strong {
        font-size: 1.1rem;
        font-weight: 800;
        display: block;
        color: var(--portal-brand);
    }
    @media (max-width: 576px) {
        .kpi {
            padding: 0.75rem;
        }
        .kpi h2 {
            font-size: 1.15rem;
        }
        .kpi small {
            font-size: 0.78rem;
        }
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
    <div>
        <h1 class="portal-heading h3 mb-1">Welcome, {{ $student->name }}</h1>
        <p class="portal-muted mb-0"><strong>Your vote is your voice.</strong></p>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-6 col-lg-3">
        <div class="kpi">
            <small class="portal-muted d-block">Registration Number</small>
            <h2>{{ $student->registration_number }}</h2>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="kpi">
            <small class="portal-muted d-block">Voting Status</small>
            <h2 id="votingStatusText">
                @if($hasVoted)
                    Completed
                @elseif($canVote)
                    Started
                @elseif($activeElection && $activeElection->hasEnded())
                    Has Ended
                @elseif(isset($nextElection) && $nextElection)
                    Upcoming
                @elseif(!$activeElection && !$nextElection)
                    No Election
                @else
                    Pending
                @endif
            </h2>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="kpi">
            <small class="portal-muted d-block">Results Access</small>
            <h2 id="resultsAccessText">{{ $showResults ? 'Available' : 'Locked' }}</h2>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="kpi">
            <small class="portal-muted d-block">Account</small>
            <h2>Active</h2>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-4">
        <div class="mini-stat">
            <small class="portal-muted d-block">Participants</small>
            <strong id="participantsCount" data-countup="{{ $ballotsCast }}" data-countup-decimals="0">{{ number_format($ballotsCast) }}</strong>
        </div>
    </div>
    <div class="col-4">
        <div class="mini-stat">
            <small class="portal-muted d-block">Registered</small>
            <strong id="registeredCount" data-countup="{{ $registeredStudents }}" data-countup-decimals="0">{{ number_format($registeredStudents) }}</strong>
        </div>
    </div>
    <div class="col-4">
        <div class="mini-stat">
            <small class="portal-muted d-block">Turnout</small>
            <strong id="turnoutCount" data-countup="{{ $turnoutPercent }}" data-countup-decimals="1">{{ number_format($turnoutPercent, 1) }}%</strong>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="portal-card p-4 h-100">
            <h2 class="h5 portal-heading mb-3"><i class="fas fa-calendar-days me-2"></i>Current Election</h2>
            @if($activeElection)
                <h3 id="activeElectionTitle" class="h6 mb-1">{{ $activeElection->title }}</h3>
                <p id="activeElectionDescription" class="portal-muted mb-3 {{ ($activeElection->description && strcasecmp(trim($activeElection->description), trim($activeElection->title)) === 0) || ($activeElection->is_revote && !$revoteCanVote) ? 'd-none' : '' }}">
                    {{ $activeElection->description ?: 'No description provided.' }}
                </p>
                <div id="activeElectionRange" class="small text-muted mb-3 {{ ($activeElection->is_revote && !$revoteCanVote) ? 'd-none' : '' }}">
                    {{ $activeElection->start_time->format('M j, Y g:i A') }} to {{ $activeElection->end_time->format('M j, Y g:i A') }}
                </div>

                <div id="currentElectionState">
                @if($activeElection->is_revote)
                    @if($revoteCanVote)
                        <div class="alert alert-warning mb-0">
                            <strong>Re-vote in progress:</strong>
                            A tie was recorded in
                            <strong>{{ $activeElection->positions->pluck('title')->implode(', ') ?: 'selected positions' }}</strong>,
                            so this re-vote is only for the tied position(s).
                            <br>
                            <span class="small">
                                Re-vote window: {{ $activeElection->start_time->format('M j, Y g:i A') }} to {{ $activeElection->end_time->format('M j, Y g:i A') }}.
                            </span>
                            Re-voting is currently open. Please submit your ballot before the closing time above.
                        </div>
                    @else
                        <div class="alert alert-success mb-0 js-revote-thanks">
                            <strong>Thank you for coming again to vote for the tied position.</strong>
                            Your results have been submitted successfully.
                        </div>
                    @endif
                @elseif($hasVoted && !$activeElection->hasEnded() && !$activeElection->results_available)
                    <div class="alert alert-success mb-0 js-auto-dismiss">
                        <strong>Vote submitted.</strong> Your ballot has been recorded successfully.
                    </div>
                @elseif($canVote)
                    <a class="btn btn-primary" href="{{ route('student.voting.index') }}">
                        <i class="fas fa-check-to-slot me-2"></i>Start Voting
                    </a>
                @elseif(isset($revoteElection) && $revoteElection)
                    <div class="alert alert-warning mb-0">
                        <strong>Re-vote update:</strong>
                        Positions with equal votes:
                        <strong>{{ $revoteElection->positions->pluck('title')->implode(', ') ?: 'selected positions' }}</strong>.
                        @if(now()->lt($revoteElection->start_time))
                            Re-voting starts on {{ $revoteElection->start_time->format('M j, Y g:i A') }}.
                        @else
                            Re-voting has ended. Final results will be published after verification.
                        @endif
                    </div>
                @elseif($activeElection->hasEnded())
                    <div class="alert alert-warning mb-0">Voting period has ended.</div>
                @else
                    <div class="alert alert-info mb-0">Voting opens on {{ $activeElection->start_time->format('M j, Y g:i A') }}.</div>
                @endif
                </div>
            @else
                <h3 id="activeElectionTitle" class="h6 mb-1">No Active Election</h3>
                <p id="activeElectionDescription" class="portal-muted mb-3">No election is active right now.</p>
                <div id="activeElectionRange" class="small text-muted mb-3 d-none"></div>

                <div id="currentElectionState">
                    @if(isset($nextElection) && $nextElection)
                        <div id="nextElectionAlert" class="alert alert-info mb-0">
                            Voting opens on {{ $nextElection->start_time->format('M j, Y g:i A') }}.
                            <span class="ms-2">Starting in <strong id="voteCountdown">--:--:--</strong></span>
                        </div>
                    @else
                        <div id="noActiveElectionAlert" class="alert alert-warning mb-0">No active election period is currently available.</div>
                    @endif
                </div>
            @endif
        </div>
    </div>
    <div class="col-lg-4">
        <div class="portal-card p-4 h-100">
            <h2 class="h6 portal-heading mb-3 quick-actions-title">Quick Actions</h2>
            <div class="d-grid gap-2">
                <a id="castVoteBtn" class="btn btn-primary {{ ($canVote || $revoteCanVote) ? '' : 'd-none' }}" href="{{ route('student.voting.index') }}">
                    <i class="fas fa-vote-yea me-2"></i>Cast Vote
                </a>
                <div class="quick-links-row">
                    <a class="btn btn-outline-primary btn-sm" href="{{ route('student.results.index') }}">
                        <i class="fas fa-chart-column me-1"></i>View Results
                    </a>
                    <a class="btn btn-outline-secondary btn-sm" href="{{ route('student.results.history') }}">
                        <i class="fas fa-clock-rotate-left me-1"></i>Election History
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        const statusUrl = "{{ route('student.voting.status') }}";
        const voteUrl = "{{ route('student.voting.index') }}";
        const castVoteBtn = document.getElementById('castVoteBtn');
        const votingStatusText = document.getElementById('votingStatusText');
        const resultsAccessText = document.getElementById('resultsAccessText');
        const participantsCount = document.getElementById('participantsCount');
        const registeredCount = document.getElementById('registeredCount');
        const turnoutCount = document.getElementById('turnoutCount');
        const activeElectionTitle = document.getElementById('activeElectionTitle');
        const activeElectionDescription = document.getElementById('activeElectionDescription');
        const activeElectionRange = document.getElementById('activeElectionRange');
        const currentElectionState = document.getElementById('currentElectionState');

        let countdownInterval = null;

        function escapeHtml(value) {
            return String(value == null ? '' : value).replace(/[&<>"']/g, function (char) {
                const map = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#39;'
                };
                return map[char];
            });
        }

        function clearCountdown() {
            if (countdownInterval) {
                clearInterval(countdownInterval);
                countdownInterval = null;
            }
        }

        function startCountdown(startIso) {
            const countdownEl = document.getElementById('voteCountdown');
            if (!countdownEl || !startIso) return;

            clearCountdown();
            const startTime = new Date(startIso).getTime();

            const tick = () => {
                const diff = startTime - Date.now();
                if (diff <= 0) {
                    countdownEl.textContent = '00:00:00';
                    clearCountdown();
                    pollStatus();
                    return;
                }
                const totalSeconds = Math.floor(diff / 1000);
                const hours = String(Math.floor(totalSeconds / 3600)).padStart(2, '0');
                const minutes = String(Math.floor((totalSeconds % 3600) / 60)).padStart(2, '0');
                const seconds = String(totalSeconds % 60).padStart(2, '0');
                countdownEl.textContent = `${hours}:${minutes}:${seconds}`;
            };

            tick();
            countdownInterval = setInterval(tick, 1000);
        }

        function renderCurrentElectionState(payload) {
            if (!currentElectionState) return;

            if (payload && payload.active_election_id) {
                if (payload.is_revote) {
                    if (!payload.revote_can_vote) {
                        currentElectionState.innerHTML = `
                            <div class="alert alert-success mb-0 js-revote-thanks">
                                <strong>Thank you for coming again to vote for the tied position.</strong>
                                Your results have been submitted successfully.
                            </div>
                        `;
                        scheduleRevoteThanksDismiss();
                        return;
                    }

                    currentElectionState.innerHTML = `
                        <div class="alert alert-warning mb-0">
                            <strong>Re-vote in progress:</strong>
                            A tie was recorded in
                            <strong>${escapeHtml(payload.active_revote_positions_text || 'selected positions')}</strong>,
                            so this re-vote is only for the tied position(s).
                            <br>
                            <span class="small">
                                Re-vote window: ${escapeHtml(payload.active_start_display || '--')} to ${escapeHtml(payload.active_end_display || '--')}.
                            </span>
                            Re-voting is currently open. Please submit your ballot before the closing time above.
                        </div>
                    `;
                    return;
                }

                if (payload.has_voted && !payload.active_has_ended && !payload.active_results_available) {
                    currentElectionState.innerHTML = `
                        <div class="alert alert-success mb-0 js-auto-dismiss">
                            <strong>Vote submitted.</strong> Your ballot has been recorded successfully.
                        </div>
                    `;
                    return;
                }

                if (payload.can_vote) {
                    currentElectionState.innerHTML = `
                        <a class="btn btn-primary" href="${voteUrl}">
                            <i class="fas fa-check-to-slot me-2"></i>Start Voting
                        </a>
                    `;
                    return;
                }

                if (payload.revote_election_id) {
                    const isUpcomingRevote = payload.revote_start_display && !payload.is_revote;
                    currentElectionState.innerHTML = `
                        <div class="alert alert-warning mb-0">
                            <strong>Re-vote update:</strong>
                            Positions with equal votes:
                            <strong>${escapeHtml(payload.revote_positions_text || 'selected positions')}</strong>.
                            ${isUpcomingRevote
                                ? ` Re-voting starts on ${escapeHtml(payload.revote_start_display)}.`
                                : ' Re-voting has ended. Final results will be published after verification.'}
                        </div>
                    `;
                    return;
                }

                if (payload.active_has_ended) {
                    currentElectionState.innerHTML = '<div class="alert alert-warning mb-0">Voting period has ended.</div>';
                    return;
                }

                currentElectionState.innerHTML = `<div class="alert alert-info mb-0">Voting opens on ${escapeHtml(payload.active_start_display || '--')}.</div>`;
                return;
            }

            if (payload && payload.next_election_id) {
                currentElectionState.innerHTML = `
                    <div id="nextElectionAlert" class="alert alert-info mb-0">
                        Voting opens on ${escapeHtml(payload.next_election_start_display || '--')}.
                        <span class="ms-2">Starting in <strong id="voteCountdown">--:--:--</strong></span>
                    </div>
                `;
                startCountdown(payload.next_election_start_iso);
                return;
            }

            clearCountdown();
            currentElectionState.innerHTML = '<div id="noActiveElectionAlert" class="alert alert-warning mb-0">No active election period is currently available.</div>';
        }

        function scheduleRevoteThanksDismiss() {
            const alert = document.querySelector('.js-revote-thanks');
            if (!alert || alert.dataset.dismissBound === '1') return;
            alert.dataset.dismissBound = '1';
            setTimeout(() => {
                if (alert && alert.parentNode) {
                    alert.classList.add('fade');
                    alert.style.opacity = '0';
                    setTimeout(() => {
                        if (alert.parentNode) alert.remove();
                    }, 400);
                }
            }, 50000);
        }

        function applyStatus(payload) {
            const isCompletedRevote = !!(payload && payload.active_election_id && payload.is_revote && payload.has_voted && !payload.revote_can_vote);

            if (activeElectionTitle) {
                activeElectionTitle.textContent = (payload && payload.active_election_title) ? payload.active_election_title : 'No Active Election';
            }

            if (activeElectionDescription) {
                const title = payload && payload.active_election_title ? String(payload.active_election_title).trim() : '';
                const description = payload && payload.active_election_description ? String(payload.active_election_description).trim() : '';
                if (!isCompletedRevote && description && title && description.toLowerCase() !== title.toLowerCase()) {
                    activeElectionDescription.textContent = description;
                    activeElectionDescription.classList.remove('d-none');
                } else {
                    activeElectionDescription.textContent = payload && payload.active_election_id ? '' : 'No election is active right now.';
                    if (payload && payload.active_election_id) {
                        activeElectionDescription.classList.add('d-none');
                    } else {
                        activeElectionDescription.classList.remove('d-none');
                    }
                }
            }

            if (activeElectionRange) {
                if (!isCompletedRevote && payload && payload.active_start_display && payload.active_end_display) {
                    activeElectionRange.textContent = `${payload.active_start_display} to ${payload.active_end_display}`;
                    activeElectionRange.classList.remove('d-none');
                } else {
                    activeElectionRange.textContent = '';
                    activeElectionRange.classList.add('d-none');
                }
            }

            if (votingStatusText) {
                if (payload && payload.has_voted) {
                    votingStatusText.textContent = 'Completed';
                } else if (payload && (payload.can_vote || payload.revote_can_vote)) {
                    votingStatusText.textContent = 'Started';
                } else if (payload && payload.active_has_ended) {
                    votingStatusText.textContent = 'Has Ended';
                } else if (payload && payload.active_election_id) {
                    votingStatusText.textContent = 'Pending';
                } else if (payload && payload.next_election_id) {
                    votingStatusText.textContent = 'Upcoming';
                } else {
                    votingStatusText.textContent = 'No Election';
                }
            }

            if (resultsAccessText) {
                resultsAccessText.textContent = (payload && payload.show_results) ? 'Available' : 'Locked';
            }

            if (castVoteBtn) {
                if (payload && (payload.can_vote || payload.revote_can_vote)) {
                    castVoteBtn.classList.remove('d-none');
                } else {
                    castVoteBtn.classList.add('d-none');
                }
            }

            if (participantsCount && payload) {
                participantsCount.textContent = Number(payload.ballots_cast ?? 0).toLocaleString();
            }

            if (registeredCount && payload) {
                registeredCount.textContent = Number(payload.registered_students ?? 0).toLocaleString();
            }

            if (turnoutCount && payload) {
                const turnout = Number(payload.turnout_percent ?? 0);
                turnoutCount.textContent = `${turnout.toFixed(1)}%`;
            }

            renderCurrentElectionState(payload);
            scheduleRevoteThanksDismiss();
        }

        async function pollStatus() {
            try {
                const res = await fetch(`${statusUrl}?_=${Date.now()}`, {
                    headers: { 'Accept': 'application/json' },
                    cache: 'no-store'
                });
                if (!res.ok) return;
                const data = await res.json();
                applyStatus(data);
            } catch (e) {
                // ignore
            }
        }

        pollStatus();
        setInterval(pollStatus, 5000);
    })();

    (function () {
        const counters = document.querySelectorAll('[data-countup]');
        if (!counters.length) return;

        const duration = 900;
        const easeOut = (t) => 1 - Math.pow(1 - t, 3);

        counters.forEach((el) => {
            const target = parseFloat(el.getAttribute('data-countup') || '0');
            const decimals = parseInt(el.getAttribute('data-countup-decimals') || '0', 10);
            const suffix = (el.textContent || '').trim().endsWith('%') ? '%' : '';
            const startTime = performance.now();

            const tick = (now) => {
                const progress = Math.min((now - startTime) / duration, 1);
                const value = target * easeOut(progress);
                el.textContent = `${value.toFixed(decimals)}${suffix}`;
                if (progress < 1) {
                    requestAnimationFrame(tick);
                } else {
                    el.textContent = `${target.toFixed(decimals)}${suffix}`;
                }
            };

            requestAnimationFrame(tick);
        });
    })();
</script>
@endpush
