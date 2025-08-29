<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Ecommerce Store') - {{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('ecommerce.index') }}" class="flex-shrink-0 flex items-center">
                        <i class="fas fa-shopping-bag text-2xl text-indigo-600 mr-2"></i>
                        <span class="text-xl font-bold text-gray-900">Shop</span>
                    </a>
                </div>

                <!-- Search Bar -->
                <div class="flex-1 max-w-lg mx-8">
                    <form action="{{ route('ecommerce.index') }}" method="GET" class="relative">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Search products..." 
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <button type="submit" class="absolute left-3 top-2.5 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>

                <div class="flex items-center space-x-4">
                    <!-- Cart Icon -->
                    <a href="{{ route('ecommerce.cart') }}" class="relative p-2 text-gray-600 hover:text-indigo-600 transition-colors">
                        <i class="fas fa-shopping-cart text-xl"></i>
                        <span id="cart-count" class="absolute -top-1 -right-1 bg-indigo-600 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                            {{ Cart::getTotalQuantity() }}
                        </span>
                    </a>

                    @auth
                        <!-- User Menu -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <img class="h-8 w-8 rounded-full" src="https://ui-avatars.com/api/?name={{ auth()->user()->name }}&color=7C3AED&background=EBF4FF" alt="{{ auth()->user()->name }}">
                            </button>
                            
                            <div x-show="open" @click.away="open = false" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                                <div class="py-1">
                                    <a href="{{ route('user.profile.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-user mr-2"></i> Profile
                                    </a>
                                    <a href="{{ route('ecommerce.orders') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-box mr-2"></i> My Orders
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Auth Links -->
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-indigo-600 transition-colors">
                            <i class="fas fa-sign-in-alt mr-1"></i> Login
                        </a>
                        <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                            <i class="fas fa-user-plus mr-1"></i> Register
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Category Navigation -->
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex space-x-8 overflow-x-auto">
                <a href="{{ route('ecommerce.index') }}" class="py-4 px-2 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap">
                    All Products
                </a>
                <a href="{{ route('ecommerce.index', ['category' => 'electronics']) }}" class="py-4 px-2 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap">
                    Electronics
                </a>
                <a href="{{ route('ecommerce.index', ['category' => 'clothing']) }}" class="py-4 px-2 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap">
                    Clothing
                </a>
                <a href="{{ route('ecommerce.index', ['category' => 'books']) }}" class="py-4 px-2 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap">
                    Books
                </a>
                <a href="{{ route('ecommerce.index', ['category' => 'home']) }}" class="py-4 px-2 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap">
                    Home & Garden
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="py-8">
        @if(session('success'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-lg font-semibold mb-4">About Us</h3>
                    <p class="text-gray-300">Your trusted online marketplace for quality products and exceptional service.</p>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('ecommerce.index') }}" class="text-gray-300 hover:text-white">Shop</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white">About</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white">Contact</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white">Support</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Customer Service</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-300 hover:text-white">Shipping Info</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white">Returns</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white">Size Guide</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white">FAQ</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Connect With Us</h3>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-300 hover:text-white">
                            <i class="fab fa-facebook text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-300 hover:text-white">
                            <i class="fab fa-twitter text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-300 hover:text-white">
                            <i class="fab fa-instagram text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-300 hover:text-white">
                            <i class="fab fa-linkedin text-xl"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-300">
                <p>&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.</p>
            </div>
        </div>
    </footer>

    @stack('scripts')
    
    <script>
        // Update cart count when cart changes
        function updateCartCount(count) {
            document.getElementById('cart-count').textContent = count;
        }
        
        // Global error handler for AJAX requests
        function handleAjaxError(xhr) {
            if (xhr.responseJSON && xhr.responseJSON.message) {
                alert(xhr.responseJSON.message);
            } else {
                alert('An error occurred. Please try again.');
            }
        }
    </script>
</body>
</html>