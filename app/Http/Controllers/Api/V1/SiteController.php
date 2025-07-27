<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class SiteController extends Controller
{
    /**
     * Display a listing of sites
     */
    public function index(Request $request)
    {
        $query = Site::with(['user', 'products']);

        // Filter by user if not admin
        $user = $request->user();
        if ($user && $user->role !== 1) {
            $query->where('user_id', $user->id);
        }

        // Search functionality
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        // Filter by published status
        if ($request->has('published')) {
            $query->where('published', $request->get('published'));
        }

        $sites = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $sites
        ]);
    }

    /**
     * Store a newly created site
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255|unique:sites,address',
            'status' => 'required|integer|in:0,1',
            'published' => 'sometimes|integer|in:0,1',
            'settings' => 'nullable|array',
            'seo' => 'nullable|array',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,ico|max:1024',
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

        $data = $request->except(['logo', 'favicon']);
        $data['user_id'] = $request->user()->id;
        $data['_slug'] = \Str::slug($request->name);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('sites/logos', 'public');
            $data['logo'] = [
                'url' => Storage::url($path),
                'path' => $path,
                'filename' => $request->file('logo')->getClientOriginalName(),
                'size' => $request->file('logo')->getSize(),
            ];
        }

        // Handle favicon upload
        if ($request->hasFile('favicon')) {
            $path = $request->file('favicon')->store('sites/favicons', 'public');
            $data['favicon'] = [
                'url' => Storage::url($path),
                'path' => $path,
                'filename' => $request->file('favicon')->getClientOriginalName(),
                'size' => $request->file('favicon')->getSize(),
            ];
        }

        $site = Site::create($data);

        return response()->json([
            'success' => true,
            'data' => $site->load(['user', 'products']),
            'message' => 'Site created successfully'
        ], 201);
    }

    /**
     * Display the specified site
     */
    public function show(Request $request, $id)
    {
        $query = Site::with(['user', 'products']);
        
        // Filter by user if not admin
        $user = $request->user();
        if ($user && $user->role !== 1) {
            $query->where('user_id', $user->id);
        }

        $site = $query->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $site
        ]);
    }

    /**
     * Update the specified site
     */
    public function update(Request $request, $id)
    {
        $query = Site::query();
        
        // Filter by user if not admin
        $user = $request->user();
        if ($user && $user->role !== 1) {
            $query->where('user_id', $user->id);
        }

        $site = $query->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'address' => 'sometimes|required|string|max:255|unique:sites,address,' . $id,
            'status' => 'sometimes|required|integer|in:0,1',
            'published' => 'sometimes|integer|in:0,1',
            'settings' => 'nullable|array',
            'seo' => 'nullable|array',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,ico|max:1024',
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

        $data = $request->except(['logo', 'favicon']);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($site->logo && is_array($site->logo) && isset($site->logo['path'])) {
                Storage::disk('public')->delete($site->logo['path']);
            }

            $path = $request->file('logo')->store('sites/logos', 'public');
            $data['logo'] = [
                'url' => Storage::url($path),
                'path' => $path,
                'filename' => $request->file('logo')->getClientOriginalName(),
                'size' => $request->file('logo')->getSize(),
            ];
        }

        // Handle favicon upload
        if ($request->hasFile('favicon')) {
            // Delete old favicon if exists
            if ($site->favicon && is_array($site->favicon) && isset($site->favicon['path'])) {
                Storage::disk('public')->delete($site->favicon['path']);
            }

            $path = $request->file('favicon')->store('sites/favicons', 'public');
            $data['favicon'] = [
                'url' => Storage::url($path),
                'path' => $path,
                'filename' => $request->file('favicon')->getClientOriginalName(),
                'size' => $request->file('favicon')->getSize(),
            ];
        }

        $site->update($data);

        return response()->json([
            'success' => true,
            'data' => $site->fresh()->load(['user', 'products']),
            'message' => 'Site updated successfully'
        ]);
    }

    /**
     * Remove the specified site
     */
    public function destroy(Request $request, $id)
    {
        $query = Site::query();
        
        // Filter by user if not admin
        $user = $request->user();
        if ($user && $user->role !== 1) {
            $query->where('user_id', $user->id);
        }

        $site = $query->findOrFail($id);

        // Delete associated files
        if ($site->logo && is_array($site->logo) && isset($site->logo['path'])) {
            Storage::disk('public')->delete($site->logo['path']);
        }

        if ($site->favicon && is_array($site->favicon) && isset($site->favicon['path'])) {
            Storage::disk('public')->delete($site->favicon['path']);
        }

        $site->delete();

        return response()->json([
            'success' => true,
            'message' => 'Site deleted successfully'
        ]);
    }

    /**
     * Search sites
     */
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'q' => 'required|string|min:2',
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

        $query = Site::with(['user', 'products']);

        // Filter by user if not admin
        $user = $request->user();
        if ($user && $user->role !== 1) {
            $query->where('user_id', $user->id);
        }

        $search = $request->get('q');
        $sites = $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('address', 'like', "%{$search}%")
              ->orWhere('_slug', 'like', "%{$search}%");
        })->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $sites
        ]);
    }

    /**
     * Get published sites
     */
    public function published(Request $request)
    {
        $sites = Site::with(['user', 'products'])
            ->where('published', 1)
            ->where('status', 1)
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $sites
        ]);
    }
}
