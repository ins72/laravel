<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of products
     */
    public function index(Request $request)
    {
        $query = Product::with(['user', 'site']);

        // Filter by user if not admin
        if ($request->user()->role !== 1) {
            $query->where('user_id', $request->user()->id);
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

        // Filter by price type
        if ($request->has('price_type')) {
            $query->where('price_type', $request->price_type);
        }

        // Filter by price range
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Sort functionality
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $products = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $products->items(),
            'meta' => [
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                    'last_page' => $products->lastPage(),
                    'from' => $products->firstItem(),
                    'to' => $products->lastItem(),
                    'has_more_pages' => $products->hasMorePages(),
                ]
            ]
        ]);
    }

    /**
     * Store a newly created product
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'price_type' => 'required|integer|in:1,2,3', // 1=fixed, 2=pwyw, 3=free
            'description' => 'nullable|string',
            'status' => 'required|integer|in:0,1',
            'stock' => 'nullable|integer|min:0',
            'sku' => 'nullable|string|max:255|unique:products,sku',
            'featured_img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner' => 'nullable|array',
            'media' => 'nullable|array',
            'seo' => 'nullable|array',
            'site_id' => 'nullable|exists:sites,id',
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

        // Handle featured image upload
        if ($request->hasFile('featured_img')) {
            $path = $request->file('featured_img')->store('products/featured', 'public');
            $data['featured_img'] = [
                'url' => Storage::url($path),
                'path' => $path,
                'filename' => $request->file('featured_img')->getClientOriginalName(),
                'size' => $request->file('featured_img')->getSize(),
            ];
        }

        $product = Product::create($data);

        return response()->json([
            'success' => true,
            'data' => $product->load(['user', 'site']),
            'message' => 'Product created successfully'
        ], 201);
    }

    /**
     * Display the specified product
     */
    public function show(Request $request, $id)
    {
        $query = Product::with(['user', 'site']);
        
        // Filter by user if not admin
        $user = $request->user();
        if ($user && $user->role !== 1) {
            $query->where('user_id', $user->id);
        }

        $product = $query->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }

    /**
     * Update the specified product
     */
    public function update(Request $request, $id)
    {
        $query = Product::query();
        
        // Filter by user if not admin
        $user = $request->user();
        if ($user && $user->role !== 1) {
            $query->where('user_id', $user->id);
        }

        $product = $query->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'price_type' => 'sometimes|required|integer|in:1,2,3',
            'description' => 'nullable|string',
            'status' => 'sometimes|required|integer|in:0,1',
            'stock' => 'nullable|integer|min:0',
            'sku' => 'nullable|string|max:255|unique:products,sku,' . $id,
            'featured_img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner' => 'nullable|array',
            'media' => 'nullable|array',
            'seo' => 'nullable|array',
            'site_id' => 'nullable|exists:sites,id',
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
            if ($product->featured_img && isset($product->featured_img['path'])) {
                Storage::disk('public')->delete($product->featured_img['path']);
            }

            $path = $request->file('featured_img')->store('products/featured', 'public');
            $data['featured_img'] = [
                'url' => Storage::url($path),
                'path' => $path,
                'filename' => $request->file('featured_img')->getClientOriginalName(),
                'size' => $request->file('featured_img')->getSize(),
            ];
        }

        $product->update($data);

        return response()->json([
            'success' => true,
            'data' => $product->fresh()->load(['user', 'site']),
            'message' => 'Product updated successfully'
        ]);
    }

    /**
     * Remove the specified product
     */
    public function destroy(Request $request, $id)
    {
        $query = Product::query();
        
        // Filter by user if not admin
        $user = $request->user();
        if ($user && $user->role !== 1) {
            $query->where('user_id', $user->id);
        }

        $product = $query->findOrFail($id);

        // Delete associated files
        if ($product->featured_img && isset($product->featured_img['path'])) {
            Storage::disk('public')->delete($product->featured_img['path']);
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully'
        ]);
    }

    /**
     * Search products
     */
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'q' => 'required|string|min:2',
            'category' => 'nullable|string',
            'price_min' => 'nullable|numeric|min:0',
            'price_max' => 'nullable|numeric|min:0',
            'status' => 'nullable|integer|in:0,1',
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

        $query = Product::with(['user', 'site'])->where('status', 1);

        // Search in name and description
        $searchTerm = $request->q;
        $query->where(function($q) use ($searchTerm) {
            $q->where('name', 'like', "%{$searchTerm}%")
              ->orWhere('description', 'like', "%{$searchTerm}%")
              ->orWhere('sku', 'like', "%{$searchTerm}%");
        });

        // Price range filter
        if ($request->has('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }
        if ($request->has('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        // Status filter
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $perPage = $request->get('per_page', 15);
        $products = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $products->items(),
            'meta' => [
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                    'last_page' => $products->lastPage(),
                ],
                'search' => [
                    'query' => $request->q,
                    'results_count' => $products->total(),
                ]
            ]
        ]);
    }

    /**
     * Get featured products
     */
    public function featured(Request $request)
    {
        $query = Product::with(['user', 'site'])
            ->where('status', 1)
            ->whereNotNull('featured_img');

        $perPage = $request->get('per_page', 10);
        $products = $query->paginate($perPage);

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
     * Bulk operations
     */
    public function bulkOperations(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|string|in:delete,update,status',
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'integer|exists:products,id',
            'data' => 'required_if:action,update|array',
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

        $query = Product::query();
        
        // Filter by user if not admin
        if ($request->user()->role !== 1) {
            $query->where('user_id', $request->user()->id);
        }

        $products = $query->whereIn('id', $request->product_ids)->get();

        switch ($request->action) {
            case 'delete':
                foreach ($products as $product) {
                    if ($product->featured_img && isset($product->featured_img['path'])) {
                        Storage::disk('public')->delete($product->featured_img['path']);
                    }
                    $product->delete();
                }
                $message = 'Products deleted successfully';
                break;

            case 'update':
                foreach ($products as $product) {
                    $product->update($request->data);
                }
                $message = 'Products updated successfully';
                break;

            case 'status':
                $status = $request->data['status'] ?? 1;
                foreach ($products as $product) {
                    $product->update(['status' => $status]);
                }
                $message = 'Product status updated successfully';
                break;
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'affected_count' => $products->count()
            ]
        ]);
    }
} 