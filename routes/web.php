<?php

use App\Http\Controllers\Admin\Auth\LoginController as AdminLoginController;
use App\Http\Controllers\Admin\Auth\ForgotPasswordController as AdminForgotPasswordController;
use App\Http\Controllers\Admin\Auth\ResetPasswordController as AdminResetPasswordController;
use App\Http\Controllers\Admin\CandidateController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ElectionPeriodController;
use App\Http\Controllers\Admin\PositionController;
use App\Http\Controllers\Admin\ResultsController as AdminResultsController;
use App\Http\Controllers\Student\Auth\ForgotPasswordController as StudentForgotPasswordController;
use App\Http\Controllers\Student\Auth\LoginController as StudentLoginController;
use App\Http\Controllers\Student\Auth\ResetPasswordController as StudentResetPasswordController;
use App\Http\Controllers\Student\Auth\RegisterController as StudentRegisterController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\ResultsController;
use App\Http\Controllers\Student\VotingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('student.login');
});

Route::prefix('student')->name('student.')->group(function () {
    Route::middleware('guest:student')->group(function () {
        Route::get('/login', [StudentLoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [StudentLoginController::class, 'login']);
        Route::get('/register', [StudentRegisterController::class, 'showRegistrationForm'])->name('register');
        Route::post('/register', [StudentRegisterController::class, 'register']);
        Route::get('/forgot-password', [StudentForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
        Route::post('/forgot-password', [StudentForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
        Route::get('/reset-password/{token}', [StudentResetPasswordController::class, 'showResetForm'])->name('password.reset');
        Route::post('/reset-password', [StudentResetPasswordController::class, 'reset'])->name('password.update');
    });

    Route::middleware('student.auth')->group(function () {
        Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
        Route::get('/voting/status', [StudentDashboardController::class, 'votingStatus'])->name('voting.status');

        Route::post('/logout', [StudentLoginController::class, 'logout'])->name('logout');
        Route::get('/logout', [StudentLoginController::class, 'logout']);

        Route::prefix('voting')->name('voting.')->group(function () {
            Route::get('/', [VotingController::class, 'index'])->name('index');
            Route::get('/check', [VotingController::class, 'checkVotingStatus'])->name('check');
            Route::get('/start', [VotingController::class, 'start'])->name('start');
            Route::get('/position/{position}', [VotingController::class, 'showPosition'])->name('position');
            Route::post('/position/{position}/vote', [VotingController::class, 'voteForPosition'])->name('vote.position');
            Route::get('/review', [VotingController::class, 'review'])->name('review');
            Route::post('/submit', [VotingController::class, 'submitAllVotes'])->name('submit');
            Route::post('/finalize', [VotingController::class, 'finalizeVotes'])->name('finalize');
            Route::post('/cancel', [VotingController::class, 'cancelVotingSession'])->name('cancel');
        });

        Route::post('/vote', [VotingController::class, 'vote'])->name('vote');

        Route::prefix('results')->name('results.')->group(function () {
            Route::get('/', [ResultsController::class, 'index'])->name('index');
            Route::get('/history', [ResultsController::class, 'history'])->name('history');
            Route::delete('/history/{election}', [ResultsController::class, 'destroyHistory'])->name('history.destroy');
            Route::get('/{election}', [ResultsController::class, 'show'])->name('show');
            Route::get('/position/{position}', [ResultsController::class, 'showPosition'])->name('position');
            Route::get('/download/pdf', [ResultsController::class, 'downloadPdf'])->name('download.pdf');
        });
    });
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminLoginController::class, 'login']);
        Route::get('/forgot-password', [AdminForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
        Route::post('/forgot-password', [AdminForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
        Route::get('/reset-password/{token}', [AdminResetPasswordController::class, 'showResetForm'])->name('password.reset');
        Route::post('/reset-password', [AdminResetPasswordController::class, 'reset'])->name('password.update');
    });

    Route::middleware('admin.auth')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/stats', [AdminDashboardController::class, 'getVotingStats'])->name('stats');
        Route::get('/live-status', [AdminDashboardController::class, 'getLiveStatus'])->name('live-status');

        Route::post('/logout', [AdminLoginController::class, 'logout'])->name('logout');
        Route::get('/logout', [AdminLoginController::class, 'logout']);

        Route::resource('election-periods', ElectionPeriodController::class)->except(['show']);
        Route::post('/election-periods/{electionPeriod}/activate', [ElectionPeriodController::class, 'activate'])
            ->name('election-periods.activate');
        Route::post('/election-periods/{electionPeriod}/deactivate', [ElectionPeriodController::class, 'deactivate'])
            ->name('election-periods.deactivate');
        Route::post('/election-periods/{electionPeriod}/results-available', [ElectionPeriodController::class, 'makeResultsAvailable'])
            ->name('election-periods.results-available');

        Route::resource('positions', PositionController::class)->except(['show']);
        Route::post('/positions/{position}/toggle-status', [PositionController::class, 'toggleStatus'])
            ->name('positions.toggle-status');

        Route::resource('candidates', CandidateController::class)->except(['show']);
        Route::post('/candidates/{candidate}/disqualify', [CandidateController::class, 'disqualify'])
            ->name('candidates.disqualify');
        Route::post('/candidates/{candidate}/reinstate', [CandidateController::class, 'reinstate'])
            ->name('candidates.reinstate');

        Route::prefix('results')->name('results.')->group(function () {
            Route::get('/', [AdminResultsController::class, 'index'])->name('index');
            Route::get('/{election}', [AdminResultsController::class, 'show'])->name('show');
        });
    });
});
