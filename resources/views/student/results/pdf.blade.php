<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Election Results - {{ $election->title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #28a745;
            padding-bottom: 20px;
        }
        .position-section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .table th,
        .table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .winner-row {
            background-color: #d4edda;
        }
        .candidate-photo {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
        }
        .progress-bar {
            background-color: #007bff;
            height: 20px;
            border-radius: 3px;
        }
        .footer {
            text-align: center;
            margin-top: 50px;
            font-size: 12px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="color: #28a745; margin-bottom: 10px;">
            <i class="fas fa-trophy"></i> Election Results
        </h1>
        <h2 style="margin-bottom: 5px;">{{ $election->title }}</h2>
        <p style="margin-bottom: 5px;">
            <strong>Election Period:</strong> 
            {{ $election->start_time->format('M j, Y g:i A') }} - 
            {{ $election->end_time->format('M j, Y g:i A') }}
        </p>
        <p style="margin-bottom: 0;">
            <strong>Total Votes Cast:</strong> {{ $election->getTotalVotesCount() }}
        </p>
        <p style="margin-bottom: 0;">
            <strong>Generated:</strong> {{ now()->format('M j, Y g:i A') }}
        </p>
    </div>

    @foreach($positions as $position)
        <div class="position-section">
            <h3 style="color: #007bff; border-bottom: 1px solid #dee2e6; padding-bottom: 10px;">
                {{ $position->title }}
            </h3>
            
            @if($position->description)
                <p style="color: #6c757d; margin-bottom: 15px;">{{ $position->description }}</p>
            @endif

            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 40px;">Rank</th>
                        <th style="width: 60px;">Photo</th>
                        <th>Candidate Name</th>
                        <th style="width: 80px; text-align: center;">Votes</th>
                        <th style="width: 100px; text-align: center;">Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($position->candidates as $index => $candidate)
                        <tr class="{{ $index === 0 ? 'winner-row' : '' }}">
                            <td style="text-align: center;">{{ $index + 1 }}</td>
                            <td style="text-align: center;">
                                @if($candidate->photo)
                                    <img src="{{ storage_path('app/public/' . $candidate->photo) }}" 
                                         class="candidate-photo" alt="{{ $candidate->name }}">
                                @else
                                    <div style="width: 50px; height: 50px; background: #f8f9fa; border-radius: 50%; 
                                                display: flex; align-items: center; justify-content: center;">
                                        <span style="color: #6c757d; font-size: 12px;">No Photo</span>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $candidate->name }}</strong>
                                @if($candidate->bio)
                                    <br><small style="color: #6c757d;">{{ $candidate->bio }}</small>
                                @endif
                            </td>
                            <td style="text-align: center;">{{ $candidate->vote_count }}</td>
                            <td style="text-align: center;">{{ number_format($candidate->vote_percentage, 1) }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if($position->candidates->isEmpty())
                <p style="color: #6c757d; text-align: center; font-style: italic;">
                    No candidates for this position
                </p>
            @endif
        </div>
    @endforeach

    <div class="footer">
        <p>Generated by University Voting Portal</p>
        <p>Page <span class="pageNumber"></span> of <span class="totalPages"></span></p>
    </div>
</body>
</html>