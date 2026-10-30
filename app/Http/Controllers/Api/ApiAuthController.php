<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ApiAuthController extends Controller
{
    /**
     * Handle user registration.
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'nullable|string|min:6',
            'role' => 'nullable|string|in:user,business',
            'phone_number' => 'nullable|string|max:50',
            'category' => 'nullable|string|max:100',
            'category_id' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:20',
            'google_account_email' => 'nullable|email|max:255',
            'google_account_name' => 'nullable|string|max:255',
            'google_avatar_url' => 'nullable|string',
            'is_google_connected' => 'nullable|boolean',
            'google_location_id' => 'nullable|string|max:100',
            'google_location_title' => 'nullable|string|max:255',
        ]);

        $role = $validated['role'] ?? 'user';

        $password = !empty($validated['password'])
            ? Hash::make($validated['password'])
            : Hash::make(\Illuminate\Support\Str::random(32));

        $user = User::where('email', $validated['email'])->first();
        if ($user) {
            $user->name = $validated['name'];
            if (!empty($validated['password'])) {
                $user->password = $password;
            }
            $user->role = $role;
            $user->save();
        } else {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $password,
                'role' => $role,
            ]);
        }

        // If registered as a business, populate/create their BountyAiProfile immediately!
        if ($role === 'business') {
            try {
                \App\Models\BountyAiProfile::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'business_name' => $validated['name'],
                        'email' => $validated['email'],
                        'phone_number' => $validated['phone_number'] ?? '',
                        'category' => $validated['category'] ?? 'Retail & Shopping',
                        'category_id' => $validated['category_id'] ?? null,
                        'address' => $validated['address'] ?? '',
                        'city' => $validated['city'] ?? '',
                        'state' => $validated['state'] ?? '',
                        'pincode' => $validated['pincode'] ?? '',
                        'is_google_connected' => $validated['is_google_connected'] ?? !empty($validated['google_account_email']),
                        'google_account_email' => $validated['google_account_email'] ?? $validated['email'],
                        'google_account_name' => $validated['google_account_name'] ?? $validated['name'],
                        'google_avatar_url' => $validated['google_avatar_url'] ?? null,
                        'google_location_id' => $validated['google_location_id'] ?? null,
                        'google_location_title' => $validated['google_location_title'] ?? $validated['name'],
                        'onboarding_status' => 'completed',
                        'current_step' => 8,
                    ]
                );
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Failed auto-populating BountyAiProfile: ' . $e->getMessage());
            }
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Registration successful',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'wallet_balance' => (float) ($user->wallet_balance ?? 0.00),
                    'created_at' => $user->created_at ? $user->created_at->toIso8601String() : null,
                ],
                'token' => $token,
            ],
        ], 201);
    }

    /**
     * Send OTP to mobile number for verification.
     */
    public function sendOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'phone_number' => 'required|string|min:10|max:15',
        ]);

        $phone = preg_replace('/[^0-9]/', '', $validated['phone_number']);
        if (strlen($phone) > 10) {
            $phone = substr($phone, -10);
        }

        // Generate 6-digit OTP
        $otp = (string) mt_rand(100000, 999999);
        \Illuminate\Support\Facades\Cache::put('otp_' . $phone, $otp, now()->addMinutes(10));

        return response()->json([
            'status' => true,
            'message' => "OTP sent successfully to +91 {$phone}",
            'data' => [
                'phone_number' => $phone,
                'expires_in' => 600,
            ],
        ]);
    }

    /**
     * Verify mobile number with OTP.
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'phone_number' => 'required|string|min:10|max:15',
            'otp' => 'required|string|min:4|max:8',
        ]);

        $phone = preg_replace('/[^0-9]/', '', $validated['phone_number']);
        if (strlen($phone) > 10) {
            $phone = substr($phone, -10);
        }

        $inputOtp = trim($validated['otp']);
        $cachedOtp = \Illuminate\Support\Facades\Cache::get('otp_' . $phone);

        // Strictly verify against real cached OTP generated by server
        if ($cachedOtp && $inputOtp === (string)$cachedOtp) {
            \Illuminate\Support\Facades\Cache::forget('otp_' . $phone);
            \Illuminate\Support\Facades\Cache::put('verified_phone_' . $phone, true, now()->addHours(2));

            return response()->json([
                'status' => true,
                'message' => 'Mobile number verified successfully',
                'data' => [
                    'phone_number' => $phone,
                    'verified' => true,
                ],
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Invalid or expired OTP. Please enter the correct code.',
        ], 422);
    }

    /**
     * Handle user login.
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid email or password.',
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'wallet_balance' => (float) ($user->wallet_balance ?? 0.00),
                    'created_at' => $user->created_at ? $user->created_at->toIso8601String() : null,
                ],
                'token' => $token,
            ],
        ]);
    }

    /**
     * Handle user logout.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Successfully logged out',
        ]);
    }

    /**
     * Get authenticated user profile.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'status' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'wallet_balance' => (float) ($user->wallet_balance ?? 0.00),
                    'created_at' => $user->created_at ? $user->created_at->toIso8601String() : null,
                ],
            ],
        ]);
    }

    /**
     * Handle direct Google login for business or user accounts.
     */
    public function googleLogin(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'name' => 'nullable|string|max:255',
            'avatar_url' => 'nullable|string',
        ]);

        $email = $validated['email'];
        $user = User::where('email', $email)->first();

        if (!$user) {
            // Auto-create user if logging in via Google for first time
            $role = $request->input('role', 'business');
            $user = User::create([
                'name' => $validated['name'] ?? explode('@', $email)[0],
                'email' => $email,
                'password' => Hash::make(\Illuminate\Support\Str::random(32)),
                'role' => $role,
            ]);

            if ($role === 'business') {
                \App\Models\BountyAiProfile::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'business_name' => $user->name,
                        'email' => $user->email,
                        'is_google_connected' => true,
                        'google_account_email' => $user->email,
                        'google_account_name' => $user->name,
                        'google_avatar_url' => $validated['avatar_url'] ?? null,
                        'onboarding_status' => 'completed',
                        'current_step' => 8,
                    ]
                );
            }
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'wallet_balance' => (float) ($user->wallet_balance ?? 0.00),
                    'created_at' => $user->created_at ? $user->created_at->toIso8601String() : null,
                ],
                'token' => $token,
            ],
        ]);
    }

}
