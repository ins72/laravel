@extends('layouts.app')

@section('title', 'Contact Us')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">Contact Us</h1>
        <p class="text-xl text-gray-600">Get in touch with our team</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Contact Form -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-semibold text-gray-900 mb-6">Send us a Message</h2>
            
            <form action="#" method="POST" class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                    <input type="text" name="name" id="name" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                    <input type="email" name="email" id="email" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                
                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">Subject *</label>
                    <select name="subject" id="subject" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="">Select a subject</option>
                        <option value="general">General Inquiry</option>
                        <option value="support">Customer Support</option>
                        <option value="sales">Sales Question</option>
                        <option value="technical">Technical Issue</option>
                        <option value="partnership">Partnership</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                
                <div>
                    <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Message *</label>
                    <textarea name="message" id="message" rows="5" required
                              placeholder="Please describe your inquiry in detail..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"></textarea>
                </div>
                
                <button type="submit" class="w-full bg-indigo-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-indigo-700 transition duration-300">
                    Send Message
                </button>
            </form>
        </div>
        
        <!-- Contact Information -->
        <div class="space-y-6">
            <!-- Company Info -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-2xl font-semibold text-gray-900 mb-4">Get in Touch</h2>
                <p class="text-gray-600 mb-6">
                    We're here to help and answer any questions you might have. We look forward to hearing from you.
                </p>
                
                <div class="space-y-4">
                    <div class="flex items-center space-x-3">
                        <div class="bg-indigo-100 w-10 h-10 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Phone</p>
                            <p class="text-gray-600">+1 (555) 123-4567</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        <div class="bg-indigo-100 w-10 h-10 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Email</p>
                            <p class="text-gray-600">info@{{ config('app.name', 'laravel') }}.com</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        <div class="bg-indigo-100 w-10 h-10 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Address</p>
                            <p class="text-gray-600">
                                123 Commerce Street<br>
                                Business District<br>
                                City, State 12345
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Business Hours -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Business Hours</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Monday - Friday</span>
                        <span class="font-medium">9:00 AM - 6:00 PM</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Saturday</span>
                        <span class="font-medium">10:00 AM - 4:00 PM</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Sunday</span>
                        <span class="font-medium">Closed</span>
                    </div>
                </div>
            </div>
            
            <!-- FAQ Link -->
            <div class="bg-indigo-50 rounded-lg p-6 text-center">
                <h3 class="text-lg font-semibold text-indigo-900 mb-2">Need Quick Help?</h3>
                <p class="text-indigo-700 mb-4">Check out our frequently asked questions for instant answers.</p>
                <a href="#" class="text-indigo-600 hover:text-indigo-800 font-medium">
                    View FAQ →
                </a>
            </div>
        </div>
    </div>
    
    <!-- Map Section -->
    <div class="mt-12 bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-semibold text-gray-900 mb-4">Find Us</h2>
        <div class="bg-gray-200 h-64 rounded-lg flex items-center justify-center">
            <div class="text-center text-gray-500">
                <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <p class="text-lg font-medium">Interactive Map</p>
                <p class="text-sm">Map integration would go here</p>
            </div>
        </div>
    </div>
</div>
@endsection