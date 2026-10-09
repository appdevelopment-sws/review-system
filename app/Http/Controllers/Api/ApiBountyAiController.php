<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BountyAiCategory;
use App\Models\BountyAiProfile;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiBountyAiController extends Controller
{
    /**
     * Get dynamic onboarding configuration (categories, states, default hours).
     */
    public function config(): JsonResponse
    {
        $categories = BountyAiCategory::active()->get();

        return response()->json([
            'status' => true,
            'data' => [
                'categories' => $categories,
                'business_types' => [
                    'Retail Store',
                    'Electronics & Appliances',
                    'Grocery & Supermarket',
                    'Restaurant / Cafe',
                    'Fashion & Apparel',
                    'Health & Medical',
                    'Beauty & Salon',
                    'Services & Consultancy',
                    'Wholesale & Distribution',
                    'Automotive & Hardware',
                    'Education & Coaching',
                    'Other Business',
                ],
                'states' => [
                    'Andhra Pradesh',
                    'Arunachal Pradesh',
                    'Assam',
                    'Bihar',
                    'Chhattisgarh',
                    'Goa',
                    'Gujarat',
                    'Haryana',
                    'Himachal Pradesh',
                    'Jharkhand',
                    'Karnataka',
                    'Kerala',
                    'Madhya Pradesh',
                    'Maharashtra',
                    'Manipur',
                    'Meghalaya',
                    'Mizoram',
                    'Nagaland',
                    'Odisha',
                    'Punjab',
                    'Rajasthan',
                    'Sikkim',
                    'Tamil Nadu',
                    'Telangana',
                    'Tripura',
                    'Uttar Pradesh',
                    'Uttarakhand',
                    'West Bengal',
                    'Delhi NCR',
                ],
                'default_opening_time' => '09:00 AM',
                'default_closing_time' => '09:00 PM',
                'default_working_days' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
            ],
        ]);
    }

    /**
     * Get list of active business categories for onboarding Step 3.
     */
    public function categories(): JsonResponse
    {
        $categories = BountyAiCategory::active()->get();

        return response()->json([
            'status' => true,
            'data' => [
                'categories' => $categories,
            ],
        ]);
    }

    /**
     * Get existing business profile by user ID or token.
     */
    public function getBusinessInfo(Request $request): JsonResponse
    {
        $userId = $request->input('user_id') ?? ($request->user() ? $request->user()->id : null);

        if (!$userId) {
            return response()->json([
                'status' => false,
                'message' => 'User ID is required.',
            ], 400);
        }

        $profile = BountyAiProfile::where('user_id', $userId)->first();

        return response()->json([
            'status' => true,
            'data' => [
                'profile' => $profile,
            ],
        ]);
    }

    /**
     * Save Step 1: Business Name & Selected Growth Goals.
     */
    public function saveStep1(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'nullable|integer',
            'business_name' => 'required|string|max:255',
            'selected_goals' => 'nullable|array',
        ]);

        $rawUserId = $validated['user_id'] ?? ($request->user() ? $request->user()->id : null);
        $userId = null;
        if (!empty($rawUserId) && (int) $rawUserId > 0) {
            if (User::where('id', (int) $rawUserId)->exists()) {
                $userId = (int) $rawUserId;
            }
        }

        $profile = null;
        if ($userId) {
            $profile = BountyAiProfile::where('user_id', $userId)->first();
        }

        if (!$profile) {
            $profile = new BountyAiProfile();
            $profile->user_id = $userId;
        }

        $profile->business_name = $validated['business_name'];
        if (isset($validated['selected_goals'])) {
            $profile->selected_goals = $validated['selected_goals'];
        }
        $profile->current_step = max(1, (int) $profile->current_step);
        if ($profile->onboarding_status !== 'completed') {
            $profile->onboarding_status = 'in_progress';
        }
        $profile->save();

        return response()->json([
            'status' => true,
            'message' => 'Step 1 saved successfully!',
            'data' => [
                'profile' => $profile,
            ],
        ]);
    }

    /**
     * Save Step 2: Full Detailed Business Information.
     */
    public function saveStep2(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'nullable|integer',
            'business_name' => 'nullable|string|max:255',
            'phone_number' => 'required|string|max:50',
            'email' => 'nullable|string|max:255',
            'website' => 'nullable|string|max:255',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'required|string|max:20',
            'business_type' => 'required|string|max:100',
            'category' => 'nullable|string|max:100',
            'category_id' => 'nullable|string|max:100',
            'working_days' => 'nullable|array',
            'opening_time' => 'nullable|string|max:30',
            'closing_time' => 'nullable|string|max:30',
            'is_24_hours' => 'nullable|boolean',
            'selected_goals' => 'nullable|array',
        ]);

        $rawUserId = $validated['user_id'] ?? ($request->user() ? $request->user()->id : null);
        $userId = null;
        if (!empty($rawUserId) && (int) $rawUserId > 0) {
            if (User::where('id', (int) $rawUserId)->exists()) {
                $userId = (int) $rawUserId;
            }
        }

        $profile = null;
        if ($userId) {
            $profile = BountyAiProfile::where('user_id', $userId)->first();
        }

        if (!$profile) {
            $profile = new BountyAiProfile();
            $profile->user_id = $userId;
        }

        if (!empty($validated['business_name'])) {
            $profile->business_name = $validated['business_name'];
        } elseif (empty($profile->business_name)) {
            $profile->business_name = 'My Business';
        }

        if (isset($validated['selected_goals']) && !empty($validated['selected_goals'])) {
            $profile->selected_goals = $validated['selected_goals'];
        }

        $profile->phone_number = $validated['phone_number'];
        $profile->email = $validated['email'] ?? null;
        $profile->website = $validated['website'] ?? null;
        $profile->address = $validated['address'];
        $profile->city = $validated['city'];
        $profile->state = $validated['state'];
        $profile->pincode = $validated['pincode'];
        $profile->business_type = $validated['business_type'];
        if (!empty($validated['category'])) {
            $profile->category = $validated['category'];
        }
        if (!empty($validated['category_id'])) {
            $profile->category_id = $validated['category_id'];
        }
        $profile->working_days = $validated['working_days'] ?? ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        $profile->opening_time = $validated['opening_time'] ?? '09:00 AM';
        $profile->closing_time = $validated['closing_time'] ?? '09:00 PM';
        $profile->is_24_hours = (bool) ($validated['is_24_hours'] ?? false);
        $profile->current_step = max(2, (int) $profile->current_step);
        $profile->onboarding_status = 'completed';
        $profile->save();

        return response()->json([
            'status' => true,
            'message' => 'Business profile submitted successfully!',
            'data' => [
                'profile' => $profile,
            ],
        ]);
    }

    /**
     * Save Step 3: Selected Business Category.
     */
    public function saveStep3Category(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'nullable|integer',
            'category' => 'required|string|max:100',
            'category_id' => 'nullable|string|max:100',
        ]);

        $rawUserId = $validated['user_id'] ?? ($request->user() ? $request->user()->id : null);
        $userId = null;
        if (!empty($rawUserId) && (int) $rawUserId > 0) {
            if (User::where('id', (int) $rawUserId)->exists()) {
                $userId = (int) $rawUserId;
            }
        }

        $profile = null;
        if ($userId) {
            $profile = BountyAiProfile::where('user_id', $userId)->first();
        }

        if (!$profile) {
            $profile = new BountyAiProfile();
            $profile->user_id = $userId;
            $profile->business_name = 'My Business';
        }

        $profile->category = $validated['category'];
        if (!empty($validated['category_id'])) {
            $profile->category_id = $validated['category_id'];
        }
        $profile->current_step = max(3, (int) $profile->current_step);
        $profile->save();

        return response()->json([
            'status' => true,
            'message' => 'Business category saved successfully!',
            'data' => [
                'profile' => $profile,
            ],
        ]);
    }
    /**
     * Save Step 4: Business Storefront Location & GPS Coordinates.
     */
    public function saveStep4Location(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'nullable|integer',
            'address' => 'required|string|max:500',
            'landmark' => 'nullable|string|max:255',
            'pincode' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_gps_detected' => 'nullable|boolean',
        ]);

        $rawUserId = $validated['user_id'] ?? ($request->user() ? $request->user()->id : null);
        $userId = null;
        if (!empty($rawUserId) && (int) $rawUserId > 0) {
            if (User::where('id', (int) $rawUserId)->exists()) {
                $userId = (int) $rawUserId;
            }
        }

        $profile = null;
        if ($userId) {
            $profile = BountyAiProfile::where('user_id', $userId)->first();
        }

        if (!$profile) {
            $profile = new BountyAiProfile();
            $profile->user_id = $userId;
            $profile->business_name = 'My Business';
        }

        $profile->address = $validated['address'];
        if (array_key_exists('landmark', $validated)) {
            $profile->landmark = $validated['landmark'];
        }
        if (!empty($validated['pincode'])) {
            $profile->pincode = $validated['pincode'];
        }
        if (!empty($validated['city'])) {
            $profile->city = $validated['city'];
        }
        if (!empty($validated['state'])) {
            $profile->state = $validated['state'];
        }
        if (isset($validated['latitude'])) {
            $profile->latitude = $validated['latitude'];
        }
        if (isset($validated['longitude'])) {
            $profile->longitude = $validated['longitude'];
        }
        if (isset($validated['is_gps_detected'])) {
            $profile->is_gps_detected = (bool) $validated['is_gps_detected'];
        }
        $profile->current_step = max(4, (int) $profile->current_step);
        $profile->save();

        return response()->json([
            'status' => true,
            'message' => 'Business location saved successfully!',
            'data' => [
                'profile' => $profile,
            ],
        ]);
    }
}
