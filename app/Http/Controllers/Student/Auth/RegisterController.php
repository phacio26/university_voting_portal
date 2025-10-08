<?php

namespace App\Http\Controllers\Student\Auth;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('student.auth.register');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'registration_number' => 'required|string|unique:students,registration_number',
            'email' => 'required|string|email|unique:students,email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'registration_number.unique' => 'This registration number is already registered.',
            'email.unique' => 'This email address is already registered.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please fix the errors below.');
        }

        try {
            // Create student with hashed password
            $student = Student::create([
                'name' => $request->name,
                'registration_number' => $request->registration_number,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'is_active' => true,
            ]);

            return redirect()->route('student.login')
                ->with('success', 'Registration successful! Please login with your credentials.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Registration failed. Please try again.')
                ->withInput();
        }
    }
}