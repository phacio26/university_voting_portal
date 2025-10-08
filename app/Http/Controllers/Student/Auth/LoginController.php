<?php

namespace App\Http\Controllers\Student\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('student.auth.login');
    }

    public function login(Request $request)
    {
        Log::info('Login attempt started', [
            'registration_number' => $request->registration_number,
            'ip' => $request->ip()
        ]);

        $validator = Validator::make($request->all(), [
            'registration_number' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            Log::warning('Login validation failed', ['errors' => $validator->errors()]);
            return back()->withErrors($validator)->withInput();
        }

        // METHOD 1: Try direct Auth::attempt first
        $credentials = $request->only('registration_number', 'password');
        
        Log::info('Attempting authentication with credentials', [
            'registration_number' => $credentials['registration_number'],
            'password_length' => strlen($credentials['password'])
        ]);

        if (Auth::guard('student')->attempt($credentials, $request->boolean('remember'))) {
            Log::info('Auth::attempt SUCCESS', [
                'student_id' => Auth::guard('student')->id(),
                'registration_number' => $credentials['registration_number']
            ]);
            
            $request->session()->regenerate();
            return redirect()->intended(route('student.dashboard'));
        }

        // METHOD 2: If Auth::attempt fails, do detailed debugging
        Log::warning('Auth::attempt failed, starting manual authentication check');
        
        $student = \App\Models\Student::where('registration_number', $request->registration_number)->first();

        if (!$student) {
            Log::error('Student not found in database', [
                'registration_number' => $request->registration_number,
                'all_students_count' => \App\Models\Student::count()
            ]);
            
            return back()->withErrors([
                'registration_number' => 'No student found with this registration number.',
            ])->withInput();
        }

        Log::info('Student found in database', [
            'student_id' => $student->id,
            'name' => $student->name,
            'is_active' => $student->is_active
        ]);

        if (!$student->is_active) {
            Log::warning('Student account is inactive', ['student_id' => $student->id]);
            return back()->withErrors([
                'registration_number' => 'Your account has been deactivated.',
            ])->withInput();
        }

        // Manual password verification
        $passwordValid = \Illuminate\Support\Facades\Hash::check($request->password, $student->password);
        
        Log::info('Manual password check', [
            'student_id' => $student->id,
            'password_valid' => $passwordValid
        ]);

        if (!$passwordValid) {
            Log::warning('Password mismatch', [
                'student_id' => $student->id,
                'provided_password' => $request->password,
                'stored_hash' => $student->password
            ]);
            
            return back()->withErrors([
                'registration_number' => 'The provided credentials do not match our records.',
            ])->withInput();
        }

        // METHOD 3: Manual login since password is correct
        Log::info('Manual login starting', ['student_id' => $student->id]);
        
        try {
            Auth::guard('student')->login($student, $request->boolean('remember'));
            
            Log::info('Manual login completed', [
                'is_authenticated' => Auth::guard('student')->check(),
                'user_id' => Auth::guard('student')->id()
            ]);

            if (Auth::guard('student')->check()) {
                $request->session()->regenerate();
                Log::info('Login SUCCESSFUL - Redirecting to dashboard');
                return redirect()->intended(route('student.dashboard'));
            } else {
                Log::error('Manual login failed - user not authenticated after login');
                return back()->withErrors([
                    'registration_number' => 'Authentication failed. Please try again.',
                ])->withInput();
            }
            
        } catch (\Exception $e) {
            Log::error('Login exception', [
                'error' => $e->getMessage(),
                'student_id' => $student->id
            ]);
            
            return back()->withErrors([
                'registration_number' => 'Login error: ' . $e->getMessage(),
            ])->withInput();
        }
    }

    public function logout(Request $request)
    {
        $studentId = Auth::guard('student')->id();
        
        Auth::guard('student')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Log::info('Student logged out', ['student_id' => $studentId]);

        return redirect()->route('student.login');
    }
}