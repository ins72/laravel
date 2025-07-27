<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    /**
     * Display a listing of media files
     */
    public function index(Request $request)
    {
        $query = Media::with(['user']);

        // Filter by user if not admin
        $user = $request->user();
        if ($user && $user->role !== 1) {
            $query->where('user_id', $user->id);
        }

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('filename', 'like', "%{$search}%")
                  ->orWhere('original_name', 'like', "%{$search}%");
            });
        }

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filter by mime type
        if ($request->has('mime_type')) {
            $query->where('mime_type', 'like', $request->mime_type . '%');
        }

        // Sort functionality
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $media = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $media->items(),
            'meta' => [
                'pagination' => [
                    'current_page' => $media->currentPage(),
                    'per_page' => $media->perPage(),
                    'total' => $media->total(),
                    'last_page' => $media->lastPage(),
                    'from' => $media->firstItem(),
                    'to' => $media->lastItem(),
                    'has_more_pages' => $media->hasMorePages(),
                ]
            ]
        ]);
    }

    /**
     * Upload a new media file
     */
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|max:10240', // 10MB max
            'type' => 'nullable|string|max:50',
            'alt_text' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'tags' => 'nullable|array',
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

        $file = $request->file('file');
        $user = $request->user();

        // Generate unique filename
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        
        // Determine storage path based on file type
        $type = $request->get('type', 'general');
        $path = $file->storeAs("media/{$type}", $filename, 'public');

        // Get file info
        $fileInfo = [
            'url' => Storage::url($path),
            'path' => $path,
            'filename' => $filename,
            'original_name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'extension' => $file->getClientOriginalExtension(),
        ];

        // Create media record
        $media = Media::create([
            'user_id' => $user->id,
            'filename' => $filename,
            'original_name' => $file->getClientOriginalName(),
            'path' => $path,
            'url' => Storage::url($path),
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'type' => $type,
            'alt_text' => $request->get('alt_text'),
            'description' => $request->get('description'),
            'tags' => $request->get('tags', []),
            'file_info' => $fileInfo,
        ]);

        return response()->json([
            'success' => true,
            'data' => $media->load(['user']),
            'message' => 'File uploaded successfully'
        ], 201);
    }

    /**
     * Bulk upload media files
     */
    public function bulkUpload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'files' => 'required|array|min:1|max:10',
            'files.*' => 'required|file|max:10240', // 10MB max per file
            'type' => 'nullable|string|max:50',
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

        $user = $request->user();
        $type = $request->get('type', 'general');
        $uploadedFiles = [];

        foreach ($request->file('files') as $file) {
            // Generate unique filename
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            // Store file
            $path = $file->storeAs("media/{$type}", $filename, 'public');

            // Get file info
            $fileInfo = [
                'url' => Storage::url($path),
                'path' => $path,
                'filename' => $filename,
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'extension' => $file->getClientOriginalExtension(),
            ];

            // Create media record
            $media = Media::create([
                'user_id' => $user->id,
                'filename' => $filename,
                'original_name' => $file->getClientOriginalName(),
                'path' => $path,
                'url' => Storage::url($path),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'type' => $type,
                'file_info' => $fileInfo,
            ]);

            $uploadedFiles[] = $media->load(['user']);
        }

        return response()->json([
            'success' => true,
            'data' => $uploadedFiles,
            'message' => count($uploadedFiles) . ' files uploaded successfully'
        ], 201);
    }

    /**
     * Display the specified media file
     */
    public function show(Request $request, $id)
    {
        $query = Media::with(['user']);
        
        // Filter by user if not admin
        $user = $request->user();
        if ($user && $user->role !== 1) {
            $query->where('user_id', $user->id);
        }

        $media = $query->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $media
        ]);
    }

    /**
     * Remove the specified media file
     */
    public function destroy(Request $request, $id)
    {
        $query = Media::query();
        
        // Filter by user if not admin
        $user = $request->user();
        if ($user && $user->role !== 1) {
            $query->where('user_id', $user->id);
        }

        $media = $query->findOrFail($id);

        // Delete file from storage
        if (Storage::disk('public')->exists($media->path)) {
            Storage::disk('public')->delete($media->path);
        }

        $media->delete();

        return response()->json([
            'success' => true,
            'message' => 'Media file deleted successfully'
        ]);
    }

    /**
     * Get user's media files
     */
    public function userFiles(Request $request, $userId)
    {
        $user = $request->user();
        
        // Users can only view their own files unless they're admin
        if ($user->id != $userId && $user->role !== 1) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UNAUTHORIZED',
                    'message' => 'Access denied.'
                ]
            ], 403);
        }

        $query = Media::where('user_id', $userId);

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('filename', 'like', "%{$search}%")
                  ->orWhere('original_name', 'like', "%{$search}%");
            });
        }

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $media = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $media->items(),
            'meta' => [
                'pagination' => [
                    'current_page' => $media->currentPage(),
                    'per_page' => $media->perPage(),
                    'total' => $media->total(),
                    'last_page' => $media->lastPage(),
                ]
            ]
        ]);
    }
} 