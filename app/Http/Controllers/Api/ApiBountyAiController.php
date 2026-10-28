<?php

namespace AppHttpControllersApi;

use AppHttpControllersController;
use AppModelsBountyAiCategory;
use AppModelsBountyAiProfile;
use AppServicesGoogleBusinessService;
use CarbonCarbon;
use AppModelsUser;
use IlluminateHttpJsonResponse;
use IlluminateHttpRequest;

class ApiBountyAiController extends Controller
{
    /**
     * Resolves or dynamically initializes a BountyAiProfile for the user.
     * Guaranteed to never return null, eliminating 404s.
     */
    protected function resolveProfile(?int $userId, ?string $email = null): BountyAiProfile
    {
        $profile = null;
        if ($userId && $userId > 0) {
            $profile = BountyAiProfile::where('user_id', $userId)->first();
        }

        if (!$profile && !empty($email)) {
            $profile = BountyAiProfile::where('google_account_email', $email)
                ->orWhere('email', $email)
                ->first();
        }

        if (!$profile && $userId && $userId > 0) {
            $user = User::find($userId);
            if ($user) {
                if (!empty($user->email)) {
                    $profile = BountyAiProfile::where('email', $user->email)
                        ->orWhere('google_account_email', $user->email)
                        ->first();
                }
                if (!$profile) {
                    $profile = BountyAiProfile::create([
                        'user_id'             => $user->id,
                        'business_name'       => !empty($user->name) ? $user->name : 'My Business',
                        'email'               => $user->email,
                        'phone_number'        => '',
                        'city'                => '',
                        'state'               => '',
                        'address'             => '',
                        'is_google_connected' => false,
                        'onboarding_status'   => 'in_progress',
                        'current_step'        => 1,
                    ]);
                }
            }
        }

        if (!$profile) {
            $profile = BountyAiProfile::latest('updated_at')->first();
        }

        if (!$profile) {
            $firstUser = User::first();
            $profile = BountyAiProfile::create([
                'user_id'             => $firstUser ? $firstUser->id : 1,
                'business_name'       => 'My Business',
                'is_google_connected' => false,
                'onboarding_status'   => 'in_progress',
                'current_step'        => 1,
            ]);
        }

        return $profile;
    }

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
        $profile = $this->resolveProfile($userId ? (int)$userId : null);

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
        $profile = $this->resolveProfile($rawUserId ? (int)$rawUserId : null);

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
        $profile = $this->resolveProfile($rawUserId ? (int)$rawUserId : null, $validated['email'] ?? null);

        if (!empty($validated['business_name'])) {
            $profile->business_name = $validated['business_name'];
        }

        if (isset($validated['selected_goals']) && !empty($validated['selected_goals'])) {
            $profile->selected_goals = $validated['selected_goals'];
        }

        $profile->phone_number = $validated['phone_number'];
        if (!empty($validated['email'])) {
            $profile->email = $validated['email'];
        }
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
        $profile = $this->resolveProfile($rawUserId ? (int)$rawUserId : null);

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
        $profile = $this->resolveProfile($rawUserId ? (int)$rawUserId : null);

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

    /**
     * Save Step 5: Google Business Profile OAuth Account Connection.
     */
    public function saveStep5GoogleConnect(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'nullable|integer',
            'google_account_email' => 'required|email|max:255',
            'google_account_name' => 'nullable|string|max:255',
            'google_avatar_url' => 'nullable|string',
            'server_auth_code' => 'nullable|string',
            'is_google_connected' => 'nullable|boolean',
        ]);

        $rawUserId = $validated['user_id'] ?? ($request->user() ? $request->user()->id : null);
        $profile = $this->resolveProfile($rawUserId ? (int)$rawUserId : null, $validated['google_account_email']);

        $profile->is_google_connected = $validated['is_google_connected'] ?? true;
        $profile->google_account_email = $validated['google_account_email'];
        if (!empty($validated['google_account_name'])) {
            $profile->google_account_name = $validated['google_account_name'];
            if (empty($profile->business_name) || $profile->business_name === 'My Business') {
                $profile->business_name = $validated['google_account_name'];
            }
        }
        if (!empty($validated['google_avatar_url'])) {
            $profile->google_avatar_url = $validated['google_avatar_url'];
        }

        $gbpService = new GoogleBusinessService();

        // 1. Exchange server_auth_code for permanent OAuth tokens
        if (!empty($validated['server_auth_code'])) {
            $tokenRes = $gbpService->exchangeAuthCode($validated['server_auth_code']);
            if (!empty($tokenRes['status'])) {
                $profile->google_access_token = $tokenRes['access_token'] ?? null;
                $profile->google_refresh_token = $tokenRes['refresh_token'] ?? null;
                $profile->google_scope = $tokenRes['scope'] ?? null;
                if (!empty($tokenRes['expires_in'])) {
                    $profile->google_token_expires_at = Carbon::now()->addSeconds((int) $tokenRes['expires_in']);
                }
            }
        }

        $profile->current_step = max(5, (int) $profile->current_step);
        $profile->save();

        // 2. Fetch live Google Business Profile Locations
        $locations = $gbpService->fetchLocations($profile);

        // 3. Auto-populate profile details from the first location if available
        if (!empty($locations) && is_array($locations)) {
            $first = $locations[0];
            if (!empty($first['id'])) {
                $profile->google_location_id = $first['id'];
            }
            if (!empty($first['title'])) {
                $profile->business_name = $first['title'];
            }
            if (!empty($first['address'])) {
                $profile->google_location_address = $first['address'];
                $profile->address = $first['address'];
            }
            if (!empty($first['phone'])) {
                $profile->phone_number = $first['phone'];
            }
            if (!empty($first['city'])) {
                $profile->city = $first['city'];
            }
            if (!empty($first['state'])) {
                $profile->state = $first['state'];
            }
            if (isset($first['rating'])) {
                $profile->google_rating = $first['rating'];
            }
            if (isset($first['totalReviews'])) {
                $profile->google_reviews_count = $first['totalReviews'];
            }
            if (!empty($first['category'])) {
                $profile->category = $first['category'];
            }
            $profile->save();
        }

        return response()->json([
            'status' => true,
            'message' => 'Google Business Profile connected successfully!',
            'data' => [
                'profile' => $profile,
                'listings' => $locations,
            ],
        ]);
    }

    /**
     * Save Step 6: Selected Google Business Location Listing.
     */
    public function saveStep6GoogleLocation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'nullable|integer',
            'google_location_id' => 'required|string|max:100',
            'google_location_title' => 'required|string|max:255',
            'google_location_address' => 'nullable|string',
            'google_rating' => 'nullable|numeric',
            'google_reviews_count' => 'nullable|integer',
            'is_verified' => 'nullable|boolean',
        ]);

        $rawUserId = $validated['user_id'] ?? ($request->user() ? $request->user()->id : null);
        $profile = $this->resolveProfile($rawUserId ? (int)$rawUserId : null);

        $profile->google_location_id = $validated['google_location_id'];
        $profile->google_location_title = $validated['google_location_title'];
        $profile->business_name = $validated['google_location_title'];
        if (!empty($validated['google_location_address'])) {
            $profile->google_location_address = $validated['google_location_address'];
            $profile->address = $validated['google_location_address'];
            $parts = explode(',', $validated['google_location_address']);
            if (count($parts) > 1 && empty($profile->city)) {
                $profile->city = trim($parts[count($parts) - 2]);
            }
        }
        if (isset($validated['google_rating'])) {
            $profile->google_rating = $validated['google_rating'];
        }
        if (isset($validated['google_reviews_count'])) {
            $profile->google_reviews_count = $validated['google_reviews_count'];
        }

        $profile->current_step = max(6, (int) $profile->current_step);
        $profile->onboarding_status = 'completed';
        $profile->save();

        return response()->json([
            'status' => true,
            'message' => 'Google location selected successfully!',
            'data' => [
                'profile' => $profile,
            ],
        ]);
    }

    /**
     * Get Google Business Profile locations for a business profile.
     */
    public function getGoogleLocations(Request $request): JsonResponse
    {
        $userId = $request->query('user_id') ?? ($request->user() ? $request->user()->id : null);
        $profile = $this->resolveProfile($userId ? (int)$userId : null);

        $gbpService = new GoogleBusinessService();
        $locations = $gbpService->fetchLocations($profile);

        return response()->json([
            'status' => true,
            'data'   => [
                'listings' => $locations,
            ],
        ]);
    }

    /**
     * Fetch customer reviews from Google Business Profile.
     */
    public function getGoogleReviews(Request $request): JsonResponse
    {
        $userId = $request->query('user_id') ?? ($request->user() ? $request->user()->id : null);
        $profile = $this->resolveProfile($userId ? (int)$userId : null);

        $gbpService = new GoogleBusinessService();
        $reviews = $gbpService->fetchReviews($profile);

        return response()->json([
            'status' => true,
            'data'   => [
                'reviews' => $reviews,
            ],
        ]);
    }

    /**
     * Reply to a customer review on Google Business Profile with AI/Custom text.
     */
    public function replyGoogleReview(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id'   => 'nullable|integer',
            'review_id' => 'required|string',
            'reply'     => 'required|string',
        ]);

        $userId = $validated['user_id'] ?? ($request->user() ? $request->user()->id : null);
        $profile = $this->resolveProfile($userId ? (int)$userId : null);

        $gbpService = new GoogleBusinessService();
        $res = $gbpService->replyToReview($profile, $validated['review_id'], $validated['reply']);

        return response()->json($res);
    }

    /**
     * Publish an AI or custom marketing post to Google Business Profile.
     */
    public function createGooglePost(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id'     => 'nullable|integer',
            'summary'     => 'required|string',
            'action_type' => 'nullable|string',
            'url'         => 'nullable|string',
            'media_url'   => 'nullable|string',
        ]);

        $userId = $validated['user_id'] ?? ($request->user() ? $request->user()->id : null);
        $profile = $this->resolveProfile($userId ? (int)$userId : null);

        $gbpService = new GoogleBusinessService();
        $res = $gbpService->createLocalPost($profile, $validated);

        return response()->json($res);
    }

    /**
     * Fetch published Google Business Profile posts.
     */
    public function getGooglePosts(Request $request): JsonResponse
    {
        $userId = $request->query('user_id') ?? ($request->user() ? $request->user()->id : null);
        $profile = $this->resolveProfile($userId ? (int)$userId : null);

        $gbpService = new GoogleBusinessService();
        $posts = $gbpService->fetchPosts($profile);

        return response()->json([
            'status' => true,
            'data'   => [
                'posts' => $posts,
            ],
        ]);
    }
}
