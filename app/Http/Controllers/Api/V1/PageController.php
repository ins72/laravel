<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PageController extends Controller
{
    /**
     * Display a listing of pages
     */
    public function index(Request $request)
    {
        $query = Page::with(['site', 'sections']);

        // Filter by user if not admin
        $user = $request->user();
        if ($user && $user->role !== 1) {
            $query->whereHas('site', function($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        // Filter by site
        if ($request->has('site_id')) {
            $query->where('site_id', $request->site_id);
        }

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
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
        $pages = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $pages->items(),
            'meta' => [
                'pagination' => [
                    'current_page' => $pages->currentPage(),
                    'per_page' => $pages->perPage(),
                    'total' => $pages->total(),
                    'last_page' => $pages->lastPage(),
                    'from' => $pages->firstItem(),
                    'to' => $pages->lastItem(),
                    'has_more_pages' => $pages->hasMorePages(),
                ]
            ]
        ]);
    }

    /**
     * Store a newly created page
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'slug' => 'nullable|string|max:255|unique:pages,slug',
            'status' => 'required|integer|in:0,1',
            'site_id' => 'required|exists:sites,id',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'seo' => 'nullable|array',
            'settings' => 'nullable|array',
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

        // Check if user owns the site
        $user = $request->user();
        $site = \App\Models\Site::findOrFail($request->site_id);
        
        if ($user->role !== 1 && $site->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UNAUTHORIZED',
                    'message' => 'You can only create pages for your own sites.'
                ]
            ], 403);
        }

        $data = $request->all();
        if (!$request->has('slug')) {
            $data['slug'] = \Str::slug($request->title);
        }

        $page = Page::create($data);

        return response()->json([
            'success' => true,
            'data' => $page->load(['site', 'sections']),
            'message' => 'Page created successfully'
        ], 201);
    }

    /**
     * Display the specified page
     */
    public function show(Request $request, $id)
    {
        $query = Page::with(['site', 'sections']);
        
        // Filter by user if not admin
        $user = $request->user();
        if ($user && $user->role !== 1) {
            $query->whereHas('site', function($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        $page = $query->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $page
        ]);
    }

    /**
     * Update the specified page
     */
    public function update(Request $request, $id)
    {
        $query = Page::query();
        
        // Filter by user if not admin
        $user = $request->user();
        if ($user && $user->role !== 1) {
            $query->whereHas('site', function($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        $page = $query->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'content' => 'nullable|string',
            'slug' => 'sometimes|required|string|max:255|unique:pages,slug,' . $id,
            'status' => 'sometimes|required|integer|in:0,1',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'seo' => 'nullable|array',
            'settings' => 'nullable|array',
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

        $page->update($request->all());

        return response()->json([
            'success' => true,
            'data' => $page->fresh()->load(['site', 'sections']),
            'message' => 'Page updated successfully'
        ]);
    }

    /**
     * Remove the specified page
     */
    public function destroy(Request $request, $id)
    {
        $query = Page::query();
        
        // Filter by user if not admin
        $user = $request->user();
        if ($user && $user->role !== 1) {
            $query->whereHas('site', function($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        $page = $query->findOrFail($id);

        $page->delete();

        return response()->json([
            'success' => true,
            'message' => 'Page deleted successfully'
        ]);
    }

    /**
     * Store a new section for the page
     */
    public function storeSection(Request $request, $id)
    {
        $query = Page::query();
        
        // Filter by user if not admin
        $user = $request->user();
        if ($user && $user->role !== 1) {
            $query->whereHas('site', function($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        $page = $query->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'type' => 'required|string|max:100',
            'order' => 'nullable|integer|min:1',
            'status' => 'sometimes|integer|in:0,1',
            'settings' => 'nullable|array',
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

        $data = $request->all();
        $data['page_id'] = $page->id;

        $section = Section::create($data);

        return response()->json([
            'success' => true,
            'data' => $section,
            'message' => 'Section created successfully'
        ], 201);
    }

    /**
     * Update a section
     */
    public function updateSection(Request $request, $id, $sectionId)
    {
        $query = Page::query();
        
        // Filter by user if not admin
        $user = $request->user();
        if ($user && $user->role !== 1) {
            $query->whereHas('site', function($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        $page = $query->findOrFail($id);
        $section = $page->sections()->findOrFail($sectionId);

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'content' => 'nullable|string',
            'type' => 'sometimes|required|string|max:100',
            'order' => 'nullable|integer|min:1',
            'status' => 'sometimes|integer|in:0,1',
            'settings' => 'nullable|array',
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

        $section->update($request->all());

        return response()->json([
            'success' => true,
            'data' => $section->fresh(),
            'message' => 'Section updated successfully'
        ]);
    }

    /**
     * Delete a section
     */
    public function destroySection(Request $request, $id, $sectionId)
    {
        $query = Page::query();
        
        // Filter by user if not admin
        $user = $request->user();
        if ($user && $user->role !== 1) {
            $query->whereHas('site', function($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        $page = $query->findOrFail($id);
        $section = $page->sections()->findOrFail($sectionId);

        $section->delete();

        return response()->json([
            'success' => true,
            'message' => 'Section deleted successfully'
        ]);
    }
} 