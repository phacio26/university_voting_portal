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

        try {
            $status = Password::broker('students')->sendResetLink(
                $request->only('email')
            );
        } catch (\Throwable $e) {
            Log::error('Failed to send student password reset email.', [
                'email' => $request->email,
                'error' => $e->getMessage(),
            ]);

            $student = Student::where('email', $request->email)->first();

            // Local fallback so recovery is still possible when SMTP is unavailable.
            if (app()->environment('local') && $student) {
                $token = Password::broker('students')->createToken($student);
                $resetUrl = route('student.password.reset', [
                    'token' => $token,
                    'email' => $student->email,
                ]);

                return back()
                    ->with('status', 'Email service is currently unavailable. Use the secure reset link below.')
                    ->with('reset_url', $resetUrl);
            }

            return back()->withErrors([
                'email' => 'Unable to send reset link right now. Please try again shortly.',
            ]);
        }

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', __($status));
        }

        return back()->withErrors(['email' => __($status)]);
    }
}
