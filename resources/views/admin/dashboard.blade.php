@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page-title', 'Admin Dashboard')
@section('page-icon', 'fa-tachometer-alt')

@section('styles')
<style>
    .monitor-kpi .kpi-value {
        font-size: 1.1rem;
        font-weight: 700;
        line-height: 1.2;
    }

    .race-mobile-card {
        border: 1px solid var(--neutral-border);
        border-radius: var(--radius-md);
        background: #fff;
    }

    @media (max-width: 767.98px) {
        .monitor-kpi {
            padding: 0.75rem !important;
        }

        .monitor-kpi .kpi-value {
            font-size: 1rem;
        }

        .card-header h5 {
            font-size: 1rem;
        }
    }
</style>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card stat-card border-primary h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="card-title text-muted mb-2">Total Students</h6>
                    <h3 class="text-primary mb-0">{{ $stats['total_students'] }}</h3>
                </div>
                <i class="fas fa-users fa-2x text-primary"></i>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card stat-card border-success h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="card-title text-muted mb-2">Active Positions</h6>
                    <h3 class="text-success mb-0">{{ $stats['total_positions'] }}</h3>
                </div>
                <i class="fas fa-bullseye fa-2x text-success"></i>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card stat-card border-warning h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="card-title text-muted mb-2">Active Candidates</h6>
                    <h3 class="text-warning mb-0">{{ $stats['total_candidates'] }}</h3>
                </div>
                <i class="fas fa-user-friends fa-2x text-warning"></i>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card stat-card border-info h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="card-title text-muted mb-2">Active Elections</h6>
                    <h3 class="text-info mb-0">{{ $stats['active_elections'] }}</h3>
                </div>
                <i class="fas fa-vote-yea fa-2x text-info"></i>
            </div>
        </div>
    </div>
</div>

@if($activeElection)
<div class="row mb-4">
    <div class="col-xl-4 col-md-6 mb-3">
        <div class="card stat-card border-danger h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="card-title text-muted mb-2">Total Votes</h6>
                    <h3 id="adminTotalVotes" class="text-danger mb-0">{{ $stats['total_votes'] ?? 0 }}</h3>
                </div>
                <i class="fas fa-chart-bar fa-2x text-danger"></i>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-3">
        <div class="card stat-card border-success h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="card-title text-muted mb-2">Voter Turnout</h6>
                    <h3 id="adminVoterTurnout" class="text-success mb-0">{{ number_format($stats['voter_turnout'] ?? 0, 1) }}%</h3>
                </div>
                <i class="fas fa-percentage fa-2x text-success"></i>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-3">
        <div class="card stat-card border-warning h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="card-title text-muted mb-2">Remaining Students</h6>
                    <h3 id="adminRemainingStudents" class="text-warning mb-0">{{ $stats['total_students'] - ($stats['total_votes'] ?? 0) }}</h3>
                </div>
                <i class="fas fa-user-clock fa-2x text-warning"></i>
            </div>
        </div>
    </div>
</div>

@if(($stats['total_votes'] ?? 0) > 0)
<div class="row mb-4">
    <div class="col-lg-8 mb-3">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-line"></i> Voting Progress</h5>
            </div>
            <div class="card-body">
                <canvas id="votingProgressChart" height="250"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-3">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Vote Distribution</h5>
            </div>
            <div class="card-body">
                <canvas id="voteDistributionChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>
@endif
@endif

<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-binoculars"></i> Live Voting Monitor</h5>
    </div>
    <div class="card-body">
        @if($activeElection)
            @php
                $tiedPositions = $raceMonitor->where('status', 'tie')->pluck('position')->values();
            @endphp

            @if($activeElection->is_revote)
                <div class="alert alert-warning mb-4">
                    <strong>Re-vote election is active.</strong>
                    This election was created because the previous round ended in a tie for
                    <strong>{{ $activeElection->positions->pluck('title')->implode(', ') ?: 'selected positions' }}</strong>.
                    Therefore, the system now allows students to re-vote for the tied position(s) only.
                    <div class="small mt-1">
                        Re-vote window: {{ $activeElection->start_time->format('M j, Y g:i A') }} to {{ $activeElection->end_time->format('M j, Y g:i A') }}.
                    </div>
                </div>
            @elseif($tiedPositions->count() > 0)
                <div class="alert alert-warning mb-4">
                    <strong>Tie detected:</strong>
                    {{ $tiedPositions->implode(', ') }}.
                    If this tie remains when voting closes, the system will allow students to re-vote for the tied position(s).
                </div>
            @endif

            <div class="row g-3 mb-4">
                <div class="col-md-3 col-6">
                    <div class="border rounded p-3 h-100 monitor-kpi">
                        <div class="text-muted small">Voters Participated</div>
                        <div id="pulseVotersParticipated" class="kpi-value mb-0">{{ $votingPulse['voters_participated'] }} / {{ $stats['total_students'] }}</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="border rounded p-3 h-100 monitor-kpi">
                        <div class="text-muted small">Participation Rate</div>
                        <div id="pulseParticipationRate" class="kpi-value mb-0">{{ number_format($votingPulse['participation_rate'], 1) }}%</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="border rounded p-3 h-100 monitor-kpi">
                        <div class="text-muted small">Ballots Cast</div>
                        <div id="pulseBallotsCast" class="kpi-value mb-0">{{ $votingPulse['ballots_cast'] }} / {{ $votingPulse['ballots_expected'] }}</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="border rounded p-3 h-100 monitor-kpi">
                        <div class="text-muted small">Ballot Progress</div>
                        <div id="pulseBallotProgress" class="kpi-value mb-0">{{ number_format($votingPulse['ballot_progress'], 1) }}%</div>
                    </div>
                </div>
            </div>

            @if($raceMonitor->count() > 0)
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Position</th>
                                <th>Status</th>
                                <th>Leading Candidate</th>
                                <th>Votes</th>
                                <th>Runner-up</th>
                                <th>Margin</th>
                            </tr>
                        </thead>
                        <tbody id="raceMonitorTableBody">
                            @foreach($raceMonitor as $race)
                                <tr>
                                    <td class="fw-semibold">{{ $race['position'] }}</td>
                                    <td>
                                        @if($race['status'] === 'tie')
                                            <span class="badge bg-warning text-dark">Tie</span>
                                        @elseif($race['status'] === 'leading')
                                            <span class="badge bg-success">Leading</span>
                                        @elseif($race['status'] === 'no_votes')
                                            <span class="badge bg-secondary">No Votes Yet</span>
                                        @else
                                            <span class="badge bg-secondary">No Candidates</span>
                                        @endif
                                    </td>
                                    <td>{{ $race['leader_name'] ?? '-' }}</td>
                                    <td>{{ $race['leader_votes'] }}</td>
                                    <td>{{ $race['runner_up_name'] ?? '-' }} @if($race['runner_up_name']) ({{ $race['runner_up_votes'] }}) @endif</td>
                                    <td>{{ $race['margin'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-md-none">
                    <div id="raceMonitorMobileList" class="d-grid gap-2">
                        @foreach($raceMonitor as $race)
                            <div class="race-mobile-card p-3">
                                <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                    <div class="fw-semibold">{{ $race['position'] }}</div>
                                    <div>
                                        @if($race['status'] === 'tie')
                                            <span class="badge bg-warning text-dark">Tie</span>
                                        @elseif($race['status'] === 'leading')
                                            <span class="badge bg-success">Leading</span>
                                        @elseif($race['status'] === 'no_votes')
                                            <span class="badge bg-secondary">No Votes Yet</span>
                                        @else
                                            <span class="badge bg-secondary">No Candidates</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="small text-muted mb-1">Leader: <span class="text-dark">{{ $race['leader_name'] ?? '-' }}</span> ({{ $race['leader_votes'] }})</div>
                                <div class="small text-muted mb-1">Runner-up: <span class="text-dark">{{ $race['runner_up_name'] ?? '-' }}</span> @if($race['runner_up_name']) ({{ $race['runner_up_votes'] }}) @endif</div>
                                <div class="small text-muted">Margin: <span class="text-dark">{{ $race['margin'] }}</span></div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-info-circle fa-2x text-muted mb-3"></i>
                    <p class="text-muted mb-0">No active race data available yet.</p>
                </div>
            @endif
        @else
            <div class="text-center py-4">
                <i class="fas fa-info-circle fa-2x text-muted mb-3"></i>
                <p class="text-muted mb-0">Start an election period to monitor live leaders and voting progress.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
@if($activeElection && ($stats['total_votes'] ?? 0) > 0)
<script>
    fetch('{{ route("admin.stats") }}')
        .then(response => response.json())
        .then(data => {
            if (data.error) return;

            const progressCanvas = document.getElementById('votingProgressChart');
            if (progressCanvas) {
                const progressCtx = progressCanvas.getContext('2d');
                new Chart(progressCtx, {
                    type: 'bar',
                    data: {
                        labels: data.positions.map(p => p.title),
                        datasets: [{
                            label: 'Votes per Position',
                            data: data.positions.map(p => p.candidates.reduce((sum, c) => sum + c.votes, 0)),
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
                                title: { display: true, text: 'Number of Votes' }
                            }
                        }
                    }
                });
            }

            const distributionCanvas = document.getElementById('voteDistributionChart');
            const position = data.positions[0];
            if (distributionCanvas && position && position.candidates.length > 0) {
                const distributionCtx = distributionCanvas.getContext('2d');
                new Chart(distributionCtx, {
                    type: 'pie',
                    data: {
                        labels: position.candidates.map(c => c.name),
                        datasets: [{
                            data: position.candidates.map(c => c.votes),
                            backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#6f42c1', '#fd7e14', '#20c997', '#e83e8c']
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { position: 'bottom' },
                            title: { display: true, text: position.title }
                        }
                    }
                });
            }
        })
        .catch(() => {});
</script>
@endif

@if($activeElection)
<script>
    (function () {
        const liveStatusUrl = "{{ route('admin.live-status') }}";
        const totalVotesEl = document.getElementById('adminTotalVotes');
        const turnoutEl = document.getElementById('adminVoterTurnout');
        const remainingEl = document.getElementById('adminRemainingStudents');
        const pulseVotersEl = document.getElementById('pulseVotersParticipated');
        const pulseRateEl = document.getElementById('pulseParticipationRate');
        const pulseCastEl = document.getElementById('pulseBallotsCast');
        const pulseProgressEl = document.getElementById('pulseBallotProgress');
        const tableBodyEl = document.getElementById('raceMonitorTableBody');
        const mobileListEl = document.getElementById('raceMonitorMobileList');

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

        function statusBadge(status) {
            if (status === 'tie') return '<span class="badge bg-warning text-dark">Tie</span>';
            if (status === 'leading') return '<span class="badge bg-success">Leading</span>';
            if (status === 'no_votes') return '<span class="badge bg-secondary">No Votes Yet</span>';
            return '<span class="badge bg-secondary">No Candidates</span>';
        }

        function renderRaceMonitor(raceMonitor) {
            if (tableBodyEl) {
                tableBodyEl.innerHTML = (raceMonitor || []).map((race) => `
                    <tr>
                        <td class="fw-semibold">${escapeHtml(race.position)}</td>
                        <td>${statusBadge(race.status)}</td>
                        <td>${escapeHtml(race.leader_name || '-')}</td>
                        <td>${Number(race.leader_votes || 0)}</td>
                        <td>${escapeHtml(race.runner_up_name || '-')}${race.runner_up_name ? ` (${Number(race.runner_up_votes || 0)})` : ''}</td>
                        <td>${Number(race.margin || 0)}</td>
                    </tr>
                `).join('');
            }

            if (mobileListEl) {
                mobileListEl.innerHTML = (raceMonitor || []).map((race) => `
                    <div class="race-mobile-card p-3">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                            <div class="fw-semibold">${escapeHtml(race.position)}</div>
                            <div>${statusBadge(race.status)}</div>
                        </div>
                        <div class="small text-muted mb-1">Leader: <span class="text-dark">${escapeHtml(race.leader_name || '-')}</span> (${Number(race.leader_votes || 0)})</div>
                        <div class="small text-muted mb-1">Runner-up: <span class="text-dark">${escapeHtml(race.runner_up_name || '-')}</span>${race.runner_up_name ? ` (${Number(race.runner_up_votes || 0)})` : ''}</div>
                        <div class="small text-muted">Margin: <span class="text-dark">${Number(race.margin || 0)}</span></div>
                    </div>
                `).join('');
            }
        }

        function applyLiveStatus(payload) {
            if (!payload || !payload.active_election) return;

            const stats = payload.stats || {};
            const pulse = payload.voting_pulse || {};

            if (totalVotesEl) totalVotesEl.textContent = Number(stats.total_votes || 0);
            if (turnoutEl) turnoutEl.textContent = `${Number(stats.voter_turnout || 0).toFixed(1)}%`;
            if (remainingEl) remainingEl.textContent = Number(stats.remaining_students || 0);

            if (pulseVotersEl) pulseVotersEl.textContent = `${Number(pulse.voters_participated || 0)} / ${Number(stats.total_students || 0)}`;
            if (pulseRateEl) pulseRateEl.textContent = `${Number(pulse.participation_rate || 0).toFixed(1)}%`;
            if (pulseCastEl) pulseCastEl.textContent = `${Number(pulse.ballots_cast || 0)} / ${Number(pulse.ballots_expected || 0)}`;
            if (pulseProgressEl) pulseProgressEl.textContent = `${Number(pulse.ballot_progress || 0).toFixed(1)}%`;

            renderRaceMonitor(payload.race_monitor || []);
        }

        async function pollLiveStatus() {
            try {
                const res = await fetch(`${liveStatusUrl}?_=${Date.now()}`, {
                    headers: { 'Accept': 'application/json' },
                    cache: 'no-store'
                });
                if (!res.ok) return;
                const data = await res.json();
                applyLiveStatus(data);
            } catch (e) {
                // ignore
            }
        }

        pollLiveStatus();
        setInterval(pollLiveStatus, 5000);
    })();
</script>
@endif
@endsection
