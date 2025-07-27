<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    /**
     * Display a listing of courses
     */
    public function index(Request $request)
    {
        $query = Course::with(['user', 'site', 'lessons']);

        // Filter by user if not admin
        $user = $request->user();
        if ($user && $user->role !== 1) {
            $query->where('user_id', $user->id);
        }

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by published status
        if ($request->has('published')) {
            $query->where('published', $request->published);
        }

        // Filter by price type
        if ($request->has('price_type')) {
            $query->where('price_type', $request->price_type);
        }

        // Sort functionality
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $courses = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $courses->items(),
            'meta' => [
                'pagination' => [
                    'current_page' => $courses->currentPage(),
                    'per_page' => $courses->perPage(),
                    'total' => $courses->total(),
                    'last_page' => $courses->lastPage(),
                    'from' => $courses->firstItem(),
                    'to' => $courses->lastItem(),
                    'has_more_pages' => $courses->hasMorePages(),
                ]
            ]
        ]);
    }

    /**
     * Store a newly created course
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'price_type' => 'required|integer|in:1,2,3', // 1=fixed, 2=pwyw, 3=free
            'status' => 'required|integer|in:0,1',
            'published' => 'sometimes|integer|in:0,1',
            'featured_img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner' => 'nullable|array',
            'media' => 'nullable|array',
            'seo' => 'nullable|array',
            'site_id' => 'nullable|exists:sites,id',
            'duration' => 'nullable|string',
            'difficulty' => 'nullable|string|in:beginner,intermediate,advanced',
            'category' => 'nullable|string',
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

        $data = $request->except(['featured_img']);
        $data['user_id'] = $request->user()->id;
        $data['slug'] = \Str::slug($request->name);

        // Handle featured image upload
        if ($request->hasFile('featured_img')) {
            $path = $request->file('featured_img')->store('courses/featured', 'public');
            $data['featured_img'] = [
                'url' => Storage::url($path),
                'path' => $path,
                'filename' => $request->file('featured_img')->getClientOriginalName(),
                'size' => $request->file('featured_img')->getSize(),
            ];
        }

        $course = Course::create($data);

        return response()->json([
            'success' => true,
            'data' => $course->load(['user', 'site', 'lessons']),
            'message' => 'Course created successfully'
        ], 201);
    }

    /**
     * Display the specified course
     */
    public function show(Request $request, $id)
    {
        $query = Course::with(['user', 'site', 'lessons']);
        
        // Filter by user if not admin
        $user = $request->user();
        if ($user && $user->role !== 1) {
            $query->where('user_id', $user->id);
        }

        $course = $query->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $course
        ]);
    }

    /**
     * Update the specified course
     */
    public function update(Request $request, $id)
    {
        $query = Course::query();
        
        // Filter by user if not admin
        $user = $request->user();
        if ($user && $user->role !== 1) {
            $query->where('user_id', $user->id);
        }

        $course = $query->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'price_type' => 'sometimes|required|integer|in:1,2,3',
            'status' => 'sometimes|required|integer|in:0,1',
            'published' => 'sometimes|integer|in:0,1',
            'featured_img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner' => 'nullable|array',
            'media' => 'nullable|array',
            'seo' => 'nullable|array',
            'site_id' => 'nullable|exists:sites,id',
            'duration' => 'nullable|string',
            'difficulty' => 'nullable|string|in:beginner,intermediate,advanced',
            'category' => 'nullable|string',
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

        $data = $request->except(['featured_img']);

        // Handle featured image upload
        if ($request->hasFile('featured_img')) {
            // Delete old image if exists
            if ($course->featured_img && is_array($course->featured_img) && isset($course->featured_img['path'])) {
                Storage::disk('public')->delete($course->featured_img['path']);
            }

            $path = $request->file('featured_img')->store('courses/featured', 'public');
            $data['featured_img'] = [
                'url' => Storage::url($path),
                'path' => $path,
                'filename' => $request->file('featured_img')->getClientOriginalName(),
                'size' => $request->file('featured_img')->getSize(),
            ];
        }

        $course->update($data);

        return response()->json([
            'success' => true,
            'data' => $course->fresh()->load(['user', 'site', 'lessons']),
            'message' => 'Course updated successfully'
        ]);
    }

    /**
     * Remove the specified course
     */
    public function destroy(Request $request, $id)
    {
        $query = Course::query();
        
        // Filter by user if not admin
        $user = $request->user();
        if ($user && $user->role !== 1) {
            $query->where('user_id', $user->id);
        }

        $course = $query->findOrFail($id);

        // Delete associated files
        if ($course->featured_img && is_array($course->featured_img) && isset($course->featured_img['path'])) {
            Storage::disk('public')->delete($course->featured_img['path']);
        }

        $course->delete();

        return response()->json([
            'success' => true,
            'message' => 'Course deleted successfully'
        ]);
    }

    /**
     * Get course lessons
     */
    public function lessons(Request $request, $id)
    {
        $query = Course::query();
        
        // Filter by user if not admin
        $user = $request->user();
        if ($user && $user->role !== 1) {
            $query->where('user_id', $user->id);
        }

        $course = $query->findOrFail($id);
        $lessons = $course->lessons()->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $lessons->items(),
            'meta' => [
                'pagination' => [
                    'current_page' => $lessons->currentPage(),
                    'per_page' => $lessons->perPage(),
                    'total' => $lessons->total(),
                    'last_page' => $lessons->lastPage(),
                ]
            ]
        ]);
    }

    /**
     * Store a new lesson for the course
     */
    public function storeLesson(Request $request, $id)
    {
        $query = Course::query();
        
        // Filter by user if not admin
        $user = $request->user();
        if ($user && $user->role !== 1) {
            $query->where('user_id', $user->id);
        }

        $course = $query->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'video_url' => 'nullable|url',
            'duration' => 'nullable|string',
            'order' => 'nullable|integer|min:1',
            'status' => 'sometimes|integer|in:0,1',
            'media' => 'nullable|array',
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
        $data['course_id'] = $course->id;

        $lesson = Lesson::create($data);

        return response()->json([
            'success' => true,
            'data' => $lesson,
            'message' => 'Lesson created successfully'
        ], 201);
    }

    /**
     * Update a lesson
     */
    public function updateLesson(Request $request, $id, $lessonId)
    {
        $query = Course::query();
        
        // Filter by user if not admin
        $user = $request->user();
        if ($user && $user->role !== 1) {
            $query->where('user_id', $user->id);
        }

        $course = $query->findOrFail($id);
        $lesson = $course->lessons()->findOrFail($lessonId);

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'content' => 'nullable|string',
            'video_url' => 'nullable|url',
            'duration' => 'nullable|string',
            'order' => 'nullable|integer|min:1',
            'status' => 'sometimes|integer|in:0,1',
            'media' => 'nullable|array',
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

        $lesson->update($request->all());

        return response()->json([
            'success' => true,
            'data' => $lesson->fresh(),
            'message' => 'Lesson updated successfully'
        ]);
    }

    /**
     * Delete a lesson
     */
    public function destroyLesson(Request $request, $id, $lessonId)
    {
        $query = Course::query();
        
        // Filter by user if not admin
        $user = $request->user();
        if ($user && $user->role !== 1) {
            $query->where('user_id', $user->id);
        }

        $course = $query->findOrFail($id);
        $lesson = $course->lessons()->findOrFail($lessonId);

        $lesson->delete();

        return response()->json([
            'success' => true,
            'message' => 'Lesson deleted successfully'
        ]);
    }

    /**
     * Enroll in a course
     */
    public function enroll(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        // Check if user is already enrolled
        $enrollment = $course->enrollments()->where('user_id', $request->user()->id)->first();
        
        if ($enrollment) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'ALREADY_ENROLLED',
                    'message' => 'You are already enrolled in this course.'
                ]
            ], 400);
        }

        // Create enrollment
        $enrollment = $course->enrollments()->create([
            'user_id' => $request->user()->id,
            'enrolled_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'data' => $enrollment,
            'message' => 'Successfully enrolled in course'
        ], 201);
    }

    /**
     * Get enrollment progress
     */
    public function progress(Request $request, $id)
    {
        $course = Course::findOrFail($id);
        $enrollment = $course->enrollments()->where('user_id', $request->user()->id)->first();

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'NOT_ENROLLED',
                    'message' => 'You are not enrolled in this course.'
                ]
            ], 404);
        }

        $totalLessons = $course->lessons()->count();
        $completedLessons = $enrollment->completedLessons()->count();
        $progress = $totalLessons > 0 ? ($completedLessons / $totalLessons) * 100 : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'enrollment' => $enrollment,
                'progress' => [
                    'total_lessons' => $totalLessons,
                    'completed_lessons' => $completedLessons,
                    'percentage' => round($progress, 2),
                ]
            ]
        ]);
    }
} 