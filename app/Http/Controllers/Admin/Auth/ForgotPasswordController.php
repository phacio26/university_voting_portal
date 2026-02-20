<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('admin.auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        try {
            $status = Password::broker('admins')->sendResetLink(
                $request->only('email')
            );
        } catch (\Throwable $e) {
            Log::error('Failed to send admin password reset email.', [
                'email' => $request->email,
                'error' => $e->getMessage(),
            ]);

            $admin = Admin::where('email', $request->email)->first();

            // Local fallback so recovery is still possible when SMTP is unavailable.
            if (app()->environment('local') && $admin) {
                $token = Password::broker('admins')->createToken($admin);
                $resetUrl = route('admin.password.reset', [
                    'token' => $token,
                    'email' => $admin->email,
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
