<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Site;
use App\Models\Product;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of users (Admin only)
     */
    public function index(Request $request)
    {
        // Check if user is admin
        if ($request->user()->role !== 1) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UNAUTHORIZED',
                    'message' => 'Access denied. Admin privileges required.'
                ]
            ], 403);
        }

        $query = User::with(['sites', 'products']);

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Sort functionality
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $users = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $users->items(),
            'meta' => [
                'pagination' => [
                    'current_page' => $users->currentPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total(),
                    'last_page' => $users->lastPage(),
                    'from' => $users->firstItem(),
                    'to' => $users->lastItem(),
                    'has_more_pages' => $users->hasMorePages(),
                ]
            ]
        ]);
    }

    /**
     * Store a newly created user (Admin only)
     */
    public function store(Request $request)
    {
        // Check if user is admin
        if ($request->user()->role !== 1) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UNAUTHORIZED',
                    'message' => 'Access denied. Admin privileges required.'
                ]
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'sometimes|integer|in:0,1',
            'status' => 'sometimes|integer|in:0,1',
            'title' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'VALIDATION_ERROR',
                    'message' => 'The given data was invalid.',
                    'details' => $validator->errors()
                ]
            ], 422);
        }

        $data = $request->except(['avatar']);
        $data['password'] = Hash::make($request->password);
        $data['email_verified_at'] = now();

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('users/avatars', 'public');
            $data['avatar'] = [
                'url' => Storage::url($path),
                'path' => $path,
                'filename' => $request->file('avatar')->getClientOriginalName(),
                'size' => $request->file('avatar')->getSize(),
            ];
        }

        $user = User::create($data);

        return response()->json([
            'success' => true,
            'data' => $user->load(['sites', 'products']),
            'message' => 'User created successfully'
        ], 201);
    }

    /**
     * Display the specified user
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        
        // Users can only view their own profile unless they're admin
        if ($user->id != $id && $user->role !== 1) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UNAUTHORIZED',
                    'message' => 'Access denied. You can only view your own profile.'
                ]
            ], 403);
        }

        $targetUser = User::with(['sites', 'products'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $targetUser
        ]);
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();
        
        // Users can only update their own profile unless they're admin
        if ($user->id != $id && $user->role !== 1) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UNAUTHORIZED',
                    'message' => 'Access denied. You can only update your own profile.'
                ]
            ], 403);
        }

        $targetUser = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $id,
            'role' => 'sometimes|integer|in:0,1',
            'status' => 'sometimes|integer|in:0,1',
            'title' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'VALIDATION_ERROR',
                    'message' => 'The given data was invalid.',
                    'details' => $validator->errors()
                ]
            ], 422);
        }

        $data = $request->except(['avatar']);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($targetUser->avatar && is_array($targetUser->avatar) && isset($targetUser->avatar['path'])) {
                Storage::disk('public')->delete($targetUser->avatar['path']);
            }

            $path = $request->file('avatar')->store('users/avatars', 'public');
            $data['avatar'] = [
                'url' => Storage::url($path),
                'path' => $path,
                'filename' => $request->file('avatar')->getClientOriginalName(),
                'size' => $request->file('avatar')->getSize(),
            ];
        }

        $targetUser->update($data);

        return response()->json([
            'success' => true,
            'data' => $targetUser->fresh()->load(['sites', 'products']),
            'message' => 'User updated successfully'
        ]);
    }

    /**
     * Remove the specified user (Admin only)
     */
    public function destroy(Request $request, $id)
    {
        // Check if user is admin
        if ($request->user()->role !== 1) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UNAUTHORIZED',
                    'message' => 'Access denied. Admin privileges required.'
                ]
            ], 403);
        }

        $user = User::findOrFail($id);

        // Prevent admin from deleting themselves
        if ($user->id === $request->user()->id) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'INVALID_OPERATION',
                    'message' => 'You cannot delete your own account.'
                ]
            ], 400);
        }

        // Delete associated files
        if ($user->avatar && is_array($user->avatar) && isset($user->avatar['path'])) {
            Storage::disk('public')->delete($user->avatar['path']);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }

    /**
     * Get user's sites
     */
    public function sites(Request $request, $id)
    {
        $user = $request->user();
        
        // Users can only view their own sites unless they're admin
        if ($user->id != $id && $user->role !== 1) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UNAUTHORIZED',
                    'message' => 'Access denied.'
                ]
            ], 403);
        }

        $sites = Site::where('user_id', $id)
            ->with(['products', 'courses'])
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $sites->items(),
            'meta' => [
                'pagination' => [
                    'current_page' => $sites->currentPage(),
                    'per_page' => $sites->perPage(),
                    'total' => $sites->total(),
                    'last_page' => $sites->lastPage(),
                ]
            ]
        ]);
    }

    /**
     * Get user's products
     */
    public function products(Request $request, $id)
    {
        $user = $request->user();
        
        // Users can only view their own products unless they're admin
        if ($user->id != $id && $user->role !== 1) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UNAUTHORIZED',
                    'message' => 'Access denied.'
                ]
            ], 403);
        }

        $products = Product::where('user_id', $id)
            ->with(['site'])
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $products->items(),
            'meta' => [
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                    'last_page' => $products->lastPage(),
                ]
            ]
        ]);
    }

    /**
     * Get user's courses
     */
    public function courses(Request $request, $id)
    {
        $user = $request->user();
        
        // Users can only view their own courses unless they're admin
        if ($user->id != $id && $user->role !== 1) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UNAUTHORIZED',
                    'message' => 'Access denied.'
                ]
            ], 403);
        }

        $courses = Course::where('user_id', $id)
            ->with(['site'])
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $courses->items(),
            'meta' => [
                'pagination' => [
                    'current_page' => $courses->currentPage(),
                    'per_page' => $courses->perPage(),
                    'total' => $courses->total(),
                    'last_page' => $courses->lastPage(),
                ]
            ]
        ]);
    }
} 