<?php

use App\Http\Controllers\Student\Auth\LoginController as StudentLoginController;
use App\Http\Controllers\Student\Auth\RegisterController as StudentRegisterController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\VotingController as StudentVotingController;
use App\Http\Controllers\Student\ResultsController as StudentResultsController;

use App\Http\Controllers\Admin\Auth\LoginController as AdminLoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ElectionPeriodController as AdminElectionPeriodController;
use App\Http\Controllers\Admin\PositionController as AdminPositionController;
use App\Http\Controllers\Admin\CandidateController as AdminCandidateController;

// Student Routes
Route::prefix('student')->name('student.')->group(function () {
    // Authentication
    Route::get('login', [StudentLoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [StudentLoginController::class, 'login']);
    Route::get('register', [StudentRegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [StudentRegisterController::class, 'register']);
    
    // Add GET route for logout
    Route::get('logout', [StudentLoginController::class, 'logout'])->name('logout');
    Route::post('logout', [StudentLoginController::class, 'logout'])->name('logout.post');
});

// Student Protected Routes
Route::prefix('student')->name('student.')->middleware(['auth:student'])->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
    
    // Voting
    Route::middleware(['voting.period'])->group(function () {
        Route::get('/voting', [StudentVotingController::class, 'index'])->name('voting.index');
        Route::post('/vote', [StudentVotingController::class, 'vote'])->name('vote');
        Route::get('/voting/submit', [StudentVotingController::class, 'submitAllVotes'])->name('voting.submit');
        Route::post('/voting/finalize', [StudentVotingController::class, 'finalizeVotes'])->name('voting.finalize');
    });

    // Results
    Route::get('/results', [StudentResultsController::class, 'index'])->name('results.index');
    Route::get('/results/download', [StudentResultsController::class, 'downloadPdf'])->name('results.download');
});

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Authentication
    Route::get('login', [AdminLoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AdminLoginController::class, 'login']);
    
    // Add GET route for admin logout
    Route::get('logout', [AdminLoginController::class, 'logout'])->name('logout');
    Route::post('logout', [AdminLoginController::class, 'logout'])->name('logout.post');
});

// Admin Protected Routes
Route::prefix('admin')->name('admin.')->middleware(['auth:admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/stats', [AdminDashboardController::class, 'getVotingStats'])->name('stats');

    // Election Periods
    Route::resource('election-periods', AdminElectionPeriodController::class);
    Route::post('election-periods/{electionPeriod}/activate', [AdminElectionPeriodController::class, 'activate'])->name('election-periods.activate');
    Route::post('election-periods/{electionPeriod}/deactivate', [AdminElectionPeriodController::class, 'deactivate'])->name('election-periods.deactivate');
    Route::post('election-periods/{electionPeriod}/results-available', [AdminElectionPeriodController::class, 'makeResultsAvailable'])->name('election-periods.results-available');

    // Positions
    Route::resource('positions', AdminPositionController::class);
    Route::post('positions/{position}/toggle-status', [AdminPositionController::class, 'toggleStatus'])->name('positions.toggle-status');

    // Candidates
    Route::resource('candidates', AdminCandidateController::class);
    Route::post('candidates/{candidate}/disqualify', [AdminCandidateController::class, 'disqualify'])->name('candidates.disqualify');
    Route::post('candidates/{candidate}/reinstate', [AdminCandidateController::class, 'reinstate'])->name('candidates.reinstate');
});

// Home Route
Route::get('/', function () {
    return redirect()->route('student.login');
});