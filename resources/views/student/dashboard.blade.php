@extends('layouts.student-portal')

@section('title', 'Student Dashboard')

@push('styles')
<style>
    .kpi {
        position: relative;
        overflow: hidden;
        border: 1px solid var(--portal-border);
        background: #f8fbff;
        border-radius: 14px;
        padding: 1rem;
        transition: transform 0.22s ease, box-shadow 0.22s ease, border-color 0.22s ease, background-color 0.22s ease;
        cursor: default;
    }
    .kpi::after {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(10, 77, 145, 0.1), rgba(15, 118, 110, 0.08));
        opacity: 0;
        transition: opacity 0.22s ease;
        pointer-events: none;
    }
    .kpi:hover {
        transform: translateY(-4px) scale(1.01);
        border-color: #9fc3e8;
        background: linear-gradient(180deg, #ffffff 0%, #eef6ff 100%);
        box-shadow: 0 16px 30px rgba(10, 77, 145, 0.16);
    }
    .kpi:hover::after {
        opacity: 1;
    }
    .kpi h2 {
        font-size: 1.55rem;
        font-weight: 800;
        margin: 0;
        line-height: 1.2;
        word-break: break-word;
        color: #071426;
        letter-spacing: -0.01em;
    }
    .kpi small {
        color: #14293f !important;
        font-weight: 800;
        font-size: 0.84rem;
        letter-spacing: 0.01em;
        line-height: 1.25;
    }
    .status-flip-wrap {
        margin: 0.05rem 0 0;
        perspective: 700px;
    }
    .flip-card-inner {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 7.1rem;
        min-height: 2.05rem;
        padding: 0.28rem 0.58rem;
        border-radius: 10px;
        font-size: 1.02rem;
        font-weight: 900;
        letter-spacing: 0.01em;
        line-height: 1.1;
        border: 1px solid transparent;
        backface-visibility: hidden;
        transform-origin: center center;
        transition: transform 0.32s ease, opacity 0.2s ease, background-color 0.22s ease, color 0.22s ease, border-color 0.22s ease, box-shadow 0.22s ease;
        box-shadow: 0 6px 14px rgba(10, 77, 145, 0.12);
    }
    .flip-card-inner.is-flipping {
        transform: rotateX(90deg);
        opacity: 0.08;
    }
    .status-tone-completed {
        background: #e9fbf3;
        border-color: #9fdfc0;
        color: #0f5132;
    }
    .status-tone-open {
        background: #e8f2ff;
        border-color: #96bbea;
        color: #0a3f78;
    }
    .status-tone-ended {
        background: #fff1e8;
        border-color: #efc2a8;
        color: #8a310f;
    }
    .status-tone-upcoming {
        background: #f1ecff;
        border-color: #c6b5f4;
        color: #4d2b95;
    }
    .status-tone-no-election,
    .status-tone-pending {
        background: #eef3f8;
        border-color: #becddd;
        color: #24384f;
    }
    .status-tone-available {
        background: #e9fbf3;
        border-color: #9fdfc0;
        color: #0f5132;
    }
    .status-tone-locked {
        background: #fff5e6;
        border-color: #efcf9e;
        color: #8c5a0a;
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
        font-weight: 800;
        line-height: 1.2;
    }
    .quick-links-row .btn i {
        color: inherit;
    }
    .quick-actions-title {
        letter-spacing: 0;
        color: #071426;
        font-weight: 800;
    }
    .mini-stat {
        position: relative;
        overflow: hidden;
        border: 1px solid var(--portal-border);
        border-radius: 12px;
        background: #fff;
        padding: 0.75rem;
        text-align: center;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease, background-color 0.2s ease;
        cursor: default;
    }
    .mini-stat:hover {
        transform: translateY(-3px) scale(1.01);
        border-color: #a9caeb;
        background: linear-gradient(180deg, #ffffff 0%, #f3f9ff 100%);
        box-shadow: 0 14px 24px rgba(10, 77, 145, 0.14);
    }
    .mini-stat strong {
        font-size: 1.1rem;
        font-weight: 800;
        display: block;
        color: #0a3f78;
        letter-spacing: -0.01em;
    }
    #turnoutCount {
        line-height: 1.25;
        white-space: normal;
    }
    .mini-stat small {
        color: #14293f !important;
        font-weight: 800;
        font-size: 0.84rem;
        letter-spacing: 0.01em;
        line-height: 1.2;
    }
    .vote-note-card {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        margin-top: 0.35rem;
        padding: 0.48rem 0.7rem;
        border-radius: 10px;
        background: linear-gradient(135deg, rgba(10, 77, 145, 0.12), rgba(15, 118, 110, 0.1));
        border: 1px solid #b7cee9;
        color: #102844;
        font-weight: 800;
        letter-spacing: 0.01em;
        box-shadow: 0 6px 16px rgba(10, 77, 145, 0.1);
    }
    .vote-note-card i {
        color: #0a4d91;
        font-size: 0.95rem;
    }
    .portal-heading {
        color: #061223;
    }
    .portal-muted {
        color: #2a3f5b !important;
        font-weight: 800;
    }
    .quick-links-row .btn,
    #castVoteBtn {
        color: #0c2139;
        text-shadow: 0 0 0.01px rgba(7, 20, 38, 0.35);
    }
    .upcoming-progress-wrap {
        margin-top: 0.6rem;
    }
    .upcoming-progress-wrap .progress {
        height: 10px;
        background: #e8edf3;
        border-radius: 999px;
        overflow: hidden;
    }
    .upcoming-progress-wrap .progress-bar {
        width: 86%;
        background: linear-gradient(90deg, #198754, #157347);
        border-radius: 999px;
    }
    @media (prefers-reduced-motion: reduce) {
        .kpi,
        .mini-stat {
            transition: none;
        }
        .kpi:hover,
        .mini-stat:hover {
            transform: none;
            box-shadow: none;
        }
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
        .vote-note-card {
            width: 100%;
            justify-content: center;
            text-align: center;
            font-size: 0.86rem;
        }
        .flip-card-inner {
            min-width: 5.75rem;
            min-height: 1.78rem;
            font-size: 0.88rem;
            padding: 0.22rem 0.46rem;
        }
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
    <div>
        <h1 class="portal-heading h3 mb-1">{{ $student->name }}</h1>
        <div class="vote-note-card">
            <i class="fas fa-bullhorn"></i>
            <span>Your voice shapes our future.</span>
        </div>
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
            <h2 class="status-flip-wrap">
                <span id="votingStatusText" class="flip-card-inner">
                    @if($hasVoted)
                        Completed
                    @elseif($canVote)
                        Open
                    @elseif($activeElection && $activeElection->hasEnded())
                        Ended
                    @elseif(isset($nextElection) && $nextElection)
                        Upcoming
                    @elseif(!$activeElection && !$nextElection)
                        No Election
                    @else
                        Pending
                    @endif
                </span>
            </h2>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="kpi">
            <small class="portal-muted d-block">Results Access</small>
            <h2 class="status-flip-wrap">
                <span id="resultsAccessText" class="flip-card-inner">{{ $showResults ? 'Available' : 'Locked' }}</span>
            </h2>
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
                            <strong>Tie-break voting:</strong>
                            A tie was recorded in
                            <strong>{{ $activeElection->positions->pluck('title')->implode(', ') ?: 'selected positions' }}</strong>,
                            so a new voting round is open for these positions only.
                            <br>
                            <span class="small">
                                Closes: {{ $activeElection->end_time->format('M j, Y g:i A') }}.
                            </span>
                            Cast your vote before the deadline.
                        </div>
                    @else
                        <div class="alert alert-success mb-0 js-revote-thanks">
                            <strong>Tie-break vote received.</strong>
                            Your vote has been recorded.
                        </div>
                    @endif
                @elseif($hasVoted && !$activeElection->hasEnded() && !$activeElection->results_available)
                    <div class="alert alert-success mb-0 js-vote-submitted" data-election-id="{{ $activeElection->id }}">
                        <strong>Vote received.</strong> Your selection has been saved.
                    </div>
                @elseif($canVote)
                    <a class="btn btn-primary" href="{{ route('student.voting.index') }}">
                        <i class="fas fa-vote-yea me-2"></i> Cast Vote
                    </a>
                @elseif(isset($revoteElection) && $revoteElection)
                    <div class="alert alert-warning mb-0">
                        <strong>Tie-break update:</strong>
                        Positions with equal votes:
                        <strong>{{ $revoteElection->positions->pluck('title')->implode(', ') ?: 'selected positions' }}</strong>.
                        @if(now()->lt($revoteElection->start_time))
                            Tie-break voting starts on {{ $revoteElection->start_time->format('M j, Y g:i A') }}.
                        @else
                            Tie-break voting has ended. Final results will be published after verification.
                        @endif
                    </div>
                @elseif($activeElection->hasEnded())
                    <div class="alert alert-warning mb-0">Voting period has ended.</div>
                @else
                    <div>
                        <div class="alert alert-info mb-0">Voting opens {{ $activeElection->start_time->format('M j, Y g:i A') }}.</div>
                        <div class="upcoming-progress-wrap">
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" aria-label="Upcoming election progress"></div>
                            </div>
                        </div>
                    </div>
                @endif
                </div>
            @else
                <h3 id="activeElectionTitle" class="h6 mb-1">No Active Election</h3>
                <p id="activeElectionDescription" class="portal-muted mb-3">There is no active election right now.</p>
                <div id="activeElectionRange" class="small text-muted mb-3 d-none"></div>

                <div id="currentElectionState">
                    @if(isset($nextElection) && $nextElection)
                        <div id="nextElectionAlert">
                            <div class="alert alert-info mb-0">
                                Voting opens {{ $nextElection->start_time->format('M j, Y g:i A') }}.
                                <span class="ms-2">Begins in <strong id="voteCountdown">--:--:--</strong></span>
                            </div>
                            <div class="upcoming-progress-wrap">
                                <div class="progress">
                                    <div class="progress-bar" role="progressbar" aria-label="Upcoming election progress"></div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div id="noActiveElectionAlert" class="alert alert-warning mb-0">No elections scheduled at this time.</div>
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
                    <i class="fas fa-vote-yea me-2"></i> Cast Vote
                </a>
                <div class="quick-links-row">
                    <a class="btn btn-outline-primary btn-sm" href="{{ route('student.results.index') }}">
                        <i class="fas fa-chart-simple me-1"></i>View Results
                    </a>
                    <a class="btn btn-outline-secondary btn-sm" href="{{ route('student.results.history') }}">
                        <i class="fas fa-history me-1"></i>Election History
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
        const voteSubmittedDismissPrefix = 'student_vote_submitted_dismissed_';

        let countdownInterval = null;

        function voteSubmittedKey(electionId) {
            return `${voteSubmittedDismissPrefix}${String(electionId || '0')}`;
        }

        function isVoteSubmittedDismissed(electionId) {
            if (!electionId) return false;
            try {
                return localStorage.getItem(voteSubmittedKey(electionId)) === '1';
            } catch (e) {
                return false;
            }
        }

        function markVoteSubmittedDismissed(electionId) {
            if (!electionId) return;
            try {
                localStorage.setItem(voteSubmittedKey(electionId), '1');
            } catch (e) {
                // ignore storage issues
            }
        }

        function scheduleVoteSubmittedDismiss(electionId) {
            const alert = document.querySelector('.js-vote-submitted');
            if (!alert) return;
            if (isVoteSubmittedDismissed(electionId)) {
                alert.remove();
                return;
            }
            if (alert.dataset.dismissBound === '1') return;

            alert.dataset.dismissBound = '1';
            setTimeout(() => {
                markVoteSubmittedDismissed(electionId);
                if (alert && alert.parentNode) {
                    alert.classList.add('fade');
                    alert.style.opacity = '0';
                    setTimeout(() => {
                        if (alert.parentNode) alert.remove();
                    }, 400);
                }
            }, 10000);
        }

        function statusToneClass(kind, text) {
            const value = String(text || '').toLowerCase().trim();
            if (kind === 'voting') {
                if (value === 'completed') return 'status-tone-completed';
                if (value === 'open') return 'status-tone-open';
                if (value === 'ended') return 'status-tone-ended';
                if (value === 'upcoming') return 'status-tone-upcoming';
                if (value === 'pending') return 'status-tone-pending';
                return 'status-tone-no-election';
            }
            if (value === 'available') return 'status-tone-available';
            return 'status-tone-locked';
        }

        function setStatusWithFlip(el, nextText, kind) {
            if (!el) return;
            const next = String(nextText || '').trim();
            const previous = String(el.dataset.current || el.textContent || '').trim();
            const allToneClasses = [
                'status-tone-completed',
                'status-tone-open',
                'status-tone-ended',
                'status-tone-upcoming',
                'status-tone-pending',
                'status-tone-no-election',
                'status-tone-available',
                'status-tone-locked'
            ];
            const applyTone = () => {
                el.classList.remove(...allToneClasses);
                el.classList.add(statusToneClass(kind, next));
            };

            if (!previous) {
                el.textContent = next;
                el.dataset.current = next;
                applyTone();
                return;
            }

            if (previous === next) {
                el.dataset.current = next;
                applyTone();
                return;
            }

            el.classList.add('is-flipping');
            setTimeout(() => {
                el.textContent = next;
                el.dataset.current = next;
                applyTone();
                el.classList.remove('is-flipping');
            }, 165);
        }

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
                            <strong>Thank you for voting in the tie-break round.</strong>
                            Your vote has been recorded.
                        </div>
                        `;
                        scheduleRevoteThanksDismiss();
                        return;
                    }

                    currentElectionState.innerHTML = `
                        <div class="alert alert-warning mb-0">
                            <strong>Tie-break round in progress:</strong>
                            A tie was recorded in
                            <strong>${escapeHtml(payload.active_revote_positions_text || 'selected positions')}</strong>,
                            so this round is only for the tied position(s).
                            <br>
                            <span class="small">
                                Tie-break window: ${escapeHtml(payload.active_start_display || '--')} to ${escapeHtml(payload.active_end_display || '--')}.
                            </span>
                            Tie-break voting is open. Please submit your vote before the closing time above.
                        </div>
                    `;
                    return;
                }

                if (payload.has_voted && !payload.active_has_ended && !payload.active_results_available) {
                    if (isVoteSubmittedDismissed(payload.active_election_id)) {
                        currentElectionState.innerHTML = '';
                        return;
                    }

                    if (currentElectionState.querySelector('.js-vote-submitted')) {
                        return;
                    }

                    currentElectionState.innerHTML = `
                    <div class="alert alert-success mb-0 js-vote-submitted" data-election-id="${escapeHtml(payload.active_election_id || '')}">
                        <strong>Vote submitted.</strong> Your vote has been recorded.
                    </div>
                    `;
                    scheduleVoteSubmittedDismiss(payload.active_election_id);
                    return;
                }

                if (payload.can_vote) {
                    currentElectionState.innerHTML = `
                        <a class="btn btn-primary" href="${voteUrl}">
                            <i class="fas fa-check-to-slot me-2"></i>Cast Vote
                        </a>
                    `;
                    return;
                }

                if (payload.revote_election_id) {
                    const isUpcomingRevote = payload.revote_start_display && !payload.is_revote;
                    currentElectionState.innerHTML = `
                        <div class="alert alert-warning mb-0">
                            <strong>Tie-break update:</strong>
                            Positions with equal votes:
                            <strong>${escapeHtml(payload.revote_positions_text || 'selected positions')}</strong>.
                            ${isUpcomingRevote
                                ? ` Tie-break voting starts on ${escapeHtml(payload.revote_start_display)}.`
                                : ' Tie-break voting has ended. Final results will be published after verification.'}
                        </div>
                    `;
                    return;
                }

                if (payload.active_has_ended) {
                    currentElectionState.innerHTML = '<div class="alert alert-warning mb-0">Voting has ended for this election.</div>';
                    return;
                }

                currentElectionState.innerHTML = `
                    <div>
                        <div class="alert alert-info mb-0">Voting opens on ${escapeHtml(payload.active_start_display || '--')}.</div>
                        <div class="upcoming-progress-wrap">
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" aria-label="Upcoming election progress"></div>
                            </div>
                        </div>
                    </div>
                `;
                return;
            }

            if (payload && payload.next_election_id) {
                currentElectionState.innerHTML = `
                    <div id="nextElectionAlert">
                        <div class="alert alert-info mb-0">
                            Voting opens on ${escapeHtml(payload.next_election_start_display || '--')}.
                            <span class="ms-2">Starting in <strong id="voteCountdown">--:--:--</strong></span>
                        </div>
                        <div class="upcoming-progress-wrap">
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" aria-label="Upcoming election progress"></div>
                            </div>
                        </div>
                    </div>
                `;
                startCountdown(payload.next_election_start_iso);
                return;
            }

            clearCountdown();
            currentElectionState.innerHTML = '<div id="noActiveElectionAlert" class="alert alert-warning mb-0">No election is scheduled right now.</div>';
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
                    activeElectionDescription.textContent = payload && payload.active_election_id ? '' : 'There is no active election right now.';
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
                    setStatusWithFlip(votingStatusText, 'Completed', 'voting');
                } else if (payload && (payload.can_vote || payload.revote_can_vote)) {
                    setStatusWithFlip(votingStatusText, 'Open', 'voting');
                } else if (payload && payload.active_has_ended) {
                    setStatusWithFlip(votingStatusText, 'Ended', 'voting');
                } else if (payload && payload.active_election_id) {
                    setStatusWithFlip(votingStatusText, 'Pending', 'voting');
                } else if (payload && payload.next_election_id) {
                    setStatusWithFlip(votingStatusText, 'Upcoming', 'voting');
                } else {
                    setStatusWithFlip(votingStatusText, 'No Election', 'voting');
                }
            }

            if (resultsAccessText) {
                setStatusWithFlip(resultsAccessText, (payload && payload.show_results) ? 'Available' : 'Locked', 'results');
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
        const initialVoteSubmittedAlert = document.querySelector('.js-vote-submitted');
        if (initialVoteSubmittedAlert) {
            const initialElectionId = initialVoteSubmittedAlert.getAttribute('data-election-id');
            if (isVoteSubmittedDismissed(initialElectionId)) {
                initialVoteSubmittedAlert.remove();
            } else {
                scheduleVoteSubmittedDismiss(initialElectionId);
            }
        }
        setInterval(pollStatus, 5000);
        setStatusWithFlip(votingStatusText, votingStatusText ? votingStatusText.textContent : 'No Election', 'voting');
        setStatusWithFlip(resultsAccessText, resultsAccessText ? resultsAccessText.textContent : 'Locked', 'results');
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
