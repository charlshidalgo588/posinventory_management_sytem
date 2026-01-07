<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    /**
     * ----------------------------------------------------------
     * SPA LOGIN (Laravel + Sanctum + Vue)
     * ----------------------------------------------------------
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => ['required', 'email'],
            'password' => ['required', 'string', 'min:8'],
            'remember' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $remember = $request->remember ? true : false;

        if (!Auth::attempt($request->only('email', 'password'), $remember)) {
            return response()->json([
                'message' => 'Invalid email or password.',
            ], 401);
        }

        // Important for Sanctum SPA
        $request->session()->regenerate();

        return response()->json([
            'message' => 'Login successful',
            'user'    => Auth::user(),
        ]);
    }

    /**
     * ----------------------------------------------------------
     * GET AUTHENTICATED USER
     * Used by Vue Layout / Settings / Restore Session
     * ----------------------------------------------------------
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * ----------------------------------------------------------
     * UPDATE USER PROFILE (NAME & EMAIL)
     * Route: PUT /api/user
     * ----------------------------------------------------------
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name'  => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
        ]);

        return response()->json([
            'message' => 'Profile updated successfully.',
            'user'    => $user,
        ]);
    }

    /**
     * ----------------------------------------------------------
     * UPDATE USER PASSWORD
     * Route: PUT /api/user/password
     * ----------------------------------------------------------
     */
    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'current_password' => ['required'],
            'new_password'     => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Check current password
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect.',
            ], 403);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json([
            'message' => 'Password updated successfully.',
        ]);
    }

    /**
     * ----------------------------------------------------------
     * LOGOUT (Sanctum SPA)
     * ----------------------------------------------------------
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }
}
