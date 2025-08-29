@extends('layouts.app')

@section('title', 'About Us')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">About Us</h1>
        <p class="text-xl text-gray-600">Learn more about our mission and values</p>
    </div>

    <!-- Mission Section -->
    <div class="bg-white rounded-lg shadow-md p-8 mb-8">
        <h2 class="text-2xl font-semibold text-gray-900 mb-4">Our Mission</h2>
        <p class="text-gray-600 leading-relaxed">
            At {{ config('app.name', 'Laravel') }}, we're passionate about connecting customers with high-quality products 
            from trusted sellers around the world. Our mission is to provide a seamless, secure, and enjoyable 
            shopping experience while supporting both buyers and sellers in our growing community.
        </p>
    </div>

    <!-- Values Section -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6 text-center">
            <div class="bg-indigo-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Quality</h3>
            <p class="text-gray-600">We carefully curate our product selection to ensure only the best items reach our customers.</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 text-center">
            <div class="bg-indigo-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Security</h3>
            <p class="text-gray-600">Your security and privacy are our top priorities. We use industry-standard encryption and security measures.</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 text-center">
            <div class="bg-indigo-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 2.25a9.75 9.75 0 100 19.5 9.75 9.75 0 000-19.5z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Support</h3>
            <p class="text-gray-600">Our dedicated customer support team is here to help you with any questions or concerns.</p>
        </div>
    </div>

    <!-- Story Section -->
    <div class="bg-white rounded-lg shadow-md p-8 mb-8">
        <h2 class="text-2xl font-semibold text-gray-900 mb-4">Our Story</h2>
        <div class="prose text-gray-600 max-w-none">
            <p class="mb-4">
                Founded with a vision to revolutionize online shopping, {{ config('app.name', 'Laravel') }} began as a small 
                startup with big dreams. We recognized that the ecommerce landscape needed a platform that 
                prioritized user experience, security, and community.
            </p>
            <p class="mb-4">
                Today, we're proud to serve thousands of customers and sellers, offering a diverse range of 
                products from electronics and fashion to home goods and beyond. Our platform continues to 
                evolve with the latest technology and user feedback, ensuring we remain at the forefront of 
                online retail innovation.
            </p>
            <p>
                We believe that great shopping experiences are built on trust, transparency, and exceptional 
                service. That's why we're committed to maintaining the highest standards in everything we do, 
                from product quality to customer support.
            </p>
        </div>
    </div>

    <!-- Team Section -->
    <div class="bg-white rounded-lg shadow-md p-8 mb-8">
        <h2 class="text-2xl font-semibold text-gray-900 mb-6">Our Team</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="text-center">
                <div class="w-24 h-24 bg-gray-300 rounded-full mx-auto mb-4 flex items-center justify-center">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">John Doe</h3>
                <p class="text-gray-600">CEO & Founder</p>
            </div>

            <div class="text-center">
                <div class="w-24 h-24 bg-gray-300 rounded-full mx-auto mb-4 flex items-center justify-center">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Jane Smith</h3>
                <p class="text-gray-600">CTO</p>
            </div>

            <div class="text-center">
                <div class="w-24 h-24 bg-gray-300 rounded-full mx-auto mb-4 flex items-center justify-center">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Mike Johnson</h3>
                <p class="text-gray-600">Head of Operations</p>
            </div>
        </div>
    </div>

    <!-- Contact CTA -->
    <div class="bg-indigo-600 rounded-lg shadow-md p-8 text-center">
        <h2 class="text-2xl font-semibold text-white mb-4">Get in Touch</h2>
        <p class="text-indigo-100 mb-6">
            Have questions or want to learn more? We'd love to hear from you!
        </p>
        <a href="{{ route('contact') }}" class="bg-white text-indigo-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition duration-300">
            Contact Us
        </a>
    </div>
</div>
@endsection