<?php

namespace App\Http\Controllers\Student\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        // Clear any existing session data when showing login form
        if (Auth::guard('student')->check()) {
            Auth::guard('student')->logout();
            Session::flush();
        }
        
        $response = Response::make(view('student.auth.login'));
        
        // Set headers to prevent caching
        $response->header('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0');
        $response->header('Pragma', 'no-cache');
        $response->header('Expires', 'Fri, 01 Jan 1990 00:00:00 GMT');
        $response->header('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT');
        
        return $response;
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
            
            // Regenerate session and mark as authenticated
            $request->session()->regenerate();
            $request->session()->put('authenticated', true);
            $request->session()->put('last_activity', time());
            
            Log::info('Login successful, redirecting to dashboard');
            
            // Clear any previous session data
            $request->session()->forget('logout_timestamp');
            
            // Redirect with cache control headers
            return redirect()->intended(route('student.dashboard'))
                ->withHeaders([
                    'Cache-Control' => 'no-cache, no-store, must-revalidate, max-age=0',
                    'Pragma' => 'no-cache',
                    'Expires' => 'Fri, 01 Jan 1990 00:00:00 GMT',
                    'Last-Modified' => gmdate('D, d M Y H:i:s') . ' GMT',
                ]);
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
                $request->session()->put('authenticated', true);
                $request->session()->put('last_activity', time());
                $request->session()->forget('logout_timestamp');
                
                Log::info('Login SUCCESSFUL - Redirecting to dashboard');
                
                return redirect()->intended(route('student.dashboard'))
                    ->withHeaders([
                        'Cache-Control' => 'no-cache, no-store, must-revalidate, max-age=0',
                        'Pragma' => 'no-cache',
                        'Expires' => 'Fri, 01 Jan 1990 00:00:00 GMT',
                        'Last-Modified' => gmdate('D, d M Y H:i:s') . ' GMT',
                    ]);
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
        
        Log::info('Student logout initiated', ['student_id' => $studentId]);
        
        // Store logout timestamp
        $request->session()->put('logout_timestamp', time());
        
        // Clear all authentication data
        Auth::guard('student')->logout();
        
        // Clear all session data
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Clear any session cookies
        $request->session()->flush();
        Session::flush();
        
        // Clear session cookie
        $cookie = \Illuminate\Support\Facades\Cookie::forget('university_voting_portal_session');
        $xsrfCookie = \Illuminate\Support\Facades\Cookie::forget('XSRF-TOKEN');
        
        Log::info('Student logged out successfully', ['student_id' => $studentId]);

        // Create a response with strong cache control headers
        $response = redirect()->route('student.login')
            ->withHeaders([
                'Cache-Control' => 'no-cache, no-store, must-revalidate, max-age=0',
                'Pragma' => 'no-cache',
                'Expires' => 'Fri, 01 Jan 1990 00:00:00 GMT',
                'Last-Modified' => gmdate('D, d M Y H:i:s') . ' GMT',
            ]);
            
        // Clear cookies
        $response->withCookie($cookie)->withCookie($xsrfCookie);
        
        return $response;
    }
}