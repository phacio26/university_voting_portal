<?php

namespace App\Providers;

use App\Models\Admin;
use App\Models\Student;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        Schema::defaultStringLength(191);
        
        // Set timezone for Malawi
        config(['app.timezone' => 'Africa/Blantyre']);

        // Build password-reset links using the correct route per guard/user type.
        ResetPassword::createUrlUsing(function ($notifiable, string $token) {
            if ($notifiable instanceof Admin) {
                return route('admin.password.reset', [
                    'token' => $token,
                    'email' => $notifiable->getEmailForPasswordReset(),
                ]);
            }

            if ($notifiable instanceof Student) {
                return route('student.password.reset', [
                    'token' => $token,
                    'email' => $notifiable->getEmailForPasswordReset(),
                ]);
            }

            return route('student.password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ]);
        });
    }
}
