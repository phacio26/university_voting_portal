<?php

namespace App\Http\Controllers\Student\Auth;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('student.auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $student = Student::where('email', $request->email)->first();

        if (!$student) {
            return back()->withErrors([
                'email' => 'No student found with this email address.',
            ]);
        }

        try {
            $status = Password::broker('students')->sendResetLink(
                $request->only('email')
            );

            if ($status === Password::RESET_LINK_SENT) {
                // Log successful email send for debugging
                Log::info('Password reset link sent successfully', [
                    'email' => $request->email,
                    'student_id' => $student->id,
                    'mail_driver' => config('mail.default'),
                    'mail_host' => config('mail.mailers.smtp.host'),
                    'from_address' => config('mail.from.address'),
                ]);

                return back()->with('success', 'Password reset link has been sent to your email address. Please check your inbox and spam folder.');
            }

            return back()->withErrors([
                'email' => 'Unable to send password reset link. Please try again.',
            ]);

        } catch (\Throwable $e) {
            Log::error('Failed to send student password reset email.', [
                'email' => $request->email,
                'student_id' => $student->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'mail_config' => [
                    'driver' => config('mail.default'),
                    'host' => config('mail.mailers.smtp.host'),
                    'port' => config('mail.mailers.smtp.port'),
                    'username' => config('mail.mailers.smtp.username'),
                    'from' => config('mail.from.address'),
                ]
            ]);

            // Local fallback for development when SMTP is not available
            if (app()->environment('local')) {
                $token = Password::broker('students')->createToken($student);
                $resetUrl = route('student.password.reset', [
                    'token' => $token,
                    'email' => $student->email,
                ]);

                return back()
                    ->with('status', 'Email service is currently unavailable. Use the secure reset link below.')
                    ->with('reset_url', $resetUrl)
                    ->with('success', 'Development mode: Password reset link generated (check below).');
            }

            return back()->withErrors([
                'email' => 'Unable to send reset link right now. Please try again shortly.',
            ]);
        }
    }
}
