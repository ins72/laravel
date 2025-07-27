<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Site;
use App\Models\Product;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $sites = $user->sites()->latest()->take(5)->get();
        $products = $user->products()->latest()->take(5)->get();
        $courses = $user->courses()->latest()->take(5)->get();
        
        return view('user.profile.index', compact('user', 'sites', 'products', 'courses'));
    }

    public function edit()
    {
        $user = Auth::user();
        return view('user.profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'title' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only(['name', 'email', 'title']);
        
        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::exists($user->avatar)) {
                Storage::delete($user->avatar);
            }
            
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $avatarPath;
        }

        $user->update($data);
        
        return redirect()->route('user.profile.index')->with('success', 'Profile updated successfully');
    }

    public function changePassword()
    {
        return view('user.profile.change-password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()->route('user.profile.index')->with('success', 'Password changed successfully');
    }

    // Site Management
    public function sites()
    {
        $sites = Auth::user()->sites()->latest()->paginate(10);
        return view('user.sites.index', compact('sites'));
    }

    public function siteCreate()
    {
        return view('user.sites.create');
    }

    public function siteStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'domain' => 'nullable|string|max:255|unique:sites,domain',
            'settings' => 'nullable|array',
        ]);

        $site = Auth::user()->sites()->create([
            'name' => $request->name,
            'domain' => $request->domain,
            'settings' => $request->settings ?? [],
            'status' => 1,
        ]);

        return redirect()->route('user.sites.index')->with('success', 'Site created successfully');
    }

    public function siteEdit($id)
    {
        $site = Auth::user()->sites()->findOrFail($id);
        return view('user.sites.edit', compact('site'));
    }

    public function siteUpdate(Request $request, $id)
    {
        $site = Auth::user()->sites()->findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'domain' => ['nullable', 'string', 'max:255', Rule::unique('sites')->ignore($site->id)],
            'settings' => 'nullable|array',
        ]);

        $site->update([
            'name' => $request->name,
            'domain' => $request->domain,
            'settings' => $request->settings ?? $site->settings,
        ]);

        return redirect()->route('user.sites.index')->with('success', 'Site updated successfully');
    }

    public function siteDelete($id)
    {
        $site = Auth::user()->sites()->findOrFail($id);
        $site->delete();

        return redirect()->route('user.sites.index')->with('success', 'Site deleted successfully');
    }

    // Product Management
    public function products()
    {
        $products = Auth::user()->products()->latest()->paginate(10);
        return view('user.products.index', compact('products'));
    }

    public function productCreate()
    {
        return view('user.products.create');
    }

    public function productStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'required|integer|in:0,1',
        ]);

        $product = Auth::user()->products()->create([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'status' => $request->status,
            'price_type' => $request->price_type ?? 1,
        ]);

        return redirect()->route('user.products.index')->with('success', 'Product created successfully');
    }

    public function productEdit($id)
    {
        $product = Auth::user()->products()->findOrFail($id);
        return view('user.products.edit', compact('product'));
    }

    public function productUpdate(Request $request, $id)
    {
        $product = Auth::user()->products()->findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'required|integer|in:0,1',
        ]);

        $product->update([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        return redirect()->route('user.products.index')->with('success', 'Product updated successfully');
    }

    public function productDelete($id)
    {
        $product = Auth::user()->products()->findOrFail($id);
        $product->delete();

        return redirect()->route('user.products.index')->with('success', 'Product deleted successfully');
    }

    // Course Management
    public function courses()
    {
        $courses = Auth::user()->courses()->latest()->paginate(10);
        return view('user.courses.index', compact('courses'));
    }

    public function courseCreate()
    {
        return view('user.courses.create');
    }

    public function courseStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'required|integer|in:0,1',
        ]);

        $course = Auth::user()->courses()->create([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'status' => $request->status,
            'price_type' => $request->price_type ?? 1,
        ]);

        return redirect()->route('user.courses.index')->with('success', 'Course created successfully');
    }

    public function courseEdit($id)
    {
        $course = Auth::user()->courses()->findOrFail($id);
        return view('user.courses.edit', compact('course'));
    }

    public function courseUpdate(Request $request, $id)
    {
        $course = Auth::user()->courses()->findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'required|integer|in:0,1',
        ]);

        $course->update([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        return redirect()->route('user.courses.index')->with('success', 'Course updated successfully');
    }

    public function courseDelete($id)
    {
        $course = Auth::user()->courses()->findOrFail($id);
        $course->delete();

        return redirect()->route('user.courses.index')->with('success', 'Course deleted successfully');
    }

    // Settings
    public function settings()
    {
        $user = Auth::user();
        return view('user.profile.settings', compact('user'));
    }

    public function updateSettings(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'settings' => 'nullable|array',
            'store' => 'nullable|array',
            'wallet_settings' => 'nullable|array',
        ]);

        $user->update([
            'settings' => $request->settings ?? $user->settings,
            'store' => $request->store ?? $user->store,
            'wallet_settings' => $request->wallet_settings ?? $user->wallet_settings,
        ]);

        return redirect()->route('user.profile.settings')->with('success', 'Settings updated successfully');
    }
} 