@extends('layouts.app')

@section('title', 'Privacy Policy')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Privacy Policy</h1>
        <p class="text-lg text-gray-600 mt-2">Last updated: {{ date('F j, Y') }}</p>
    </div>

    <div class="bg-white rounded-lg shadow-md p-8">
        <div class="prose max-w-none">
            <h2>1. Information We Collect</h2>
            <p>
                We collect information you provide directly to us, such as when you create an account, make a purchase, or contact us for support. This may include:
            </p>
            <ul>
                <li>Name, email address, and contact information</li>
                <li>Billing and shipping addresses</li>
                <li>Payment information (processed securely through our payment partners)</li>
                <li>Order history and preferences</li>
                <li>Communications with our support team</li>
            </ul>

            <h2>2. How We Use Your Information</h2>
            <p>
                We use the information we collect to:
            </p>
            <ul>
                <li>Process and fulfill your orders</li>
                <li>Communicate with you about your account and orders</li>
                <li>Provide customer support</li>
                <li>Send you marketing communications (with your consent)</li>
                <li>Improve our services and user experience</li>
                <li>Comply with legal obligations</li>
            </ul>

            <h2>3. Information Sharing</h2>
            <p>
                We do not sell, trade, or otherwise transfer your personal information to third parties except in the following circumstances:
            </p>
            <ul>
                <li>With your explicit consent</li>
                <li>To process payments (through secure payment processors)</li>
                <li>To fulfill orders (shipping partners)</li>
                <li>To comply with legal requirements</li>
                <li>To protect our rights and safety</li>
            </ul>

            <h2>4. Data Security</h2>
            <p>
                We implement appropriate security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction. These measures include:
            </p>
            <ul>
                <li>Encryption of sensitive data</li>
                <li>Regular security assessments</li>
                <li>Access controls and authentication</li>
                <li>Secure data transmission</li>
            </ul>

            <h2>5. Cookies and Tracking</h2>
            <p>
                We use cookies and similar technologies to:
            </p>
            <ul>
                <li>Remember your preferences and settings</li>
                <li>Analyze website traffic and usage</li>
                <li>Provide personalized content and recommendations</li>
                <li>Improve our website functionality</li>
            </ul>
            <p>
                You can control cookie settings through your browser preferences.
            </p>

            <h2>6. Your Rights</h2>
            <p>
                You have the right to:
            </p>
            <ul>
                <li>Access your personal information</li>
                <li>Correct inaccurate information</li>
                <li>Request deletion of your data</li>
                <li>Opt-out of marketing communications</li>
                <li>Withdraw consent for data processing</li>
            </ul>

            <h2>7. Data Retention</h2>
            <p>
                We retain your personal information for as long as necessary to:
            </p>
            <ul>
                <li>Provide our services</li>
                <li>Comply with legal obligations</li>
                <li>Resolve disputes</li>
                <li>Enforce our agreements</li>
            </ul>

            <h2>8. Children's Privacy</h2>
            <p>
                Our services are not intended for children under 13 years of age. We do not knowingly collect personal information from children under 13. If you believe we have collected such information, please contact us immediately.
            </p>

            <h2>9. International Transfers</h2>
            <p>
                Your information may be transferred to and processed in countries other than your own. We ensure appropriate safeguards are in place to protect your data in accordance with this privacy policy.
            </p>

            <h2>10. Changes to This Policy</h2>
            <p>
                We may update this privacy policy from time to time. We will notify you of any material changes by posting the new policy on our website and updating the "Last updated" date.
            </p>

            <h2>11. Contact Us</h2>
            <p>
                If you have any questions about this privacy policy or our data practices, please contact us at:
            </p>
            <ul>
                <li>Email: privacy@{{ config('app.name', 'laravel') }}.com</li>
                <li>Phone: +1 (555) 123-4567</li>
                <li>Address: 123 Commerce Street, Business District, City, State 12345</li>
            </ul>

            <h2>12. Legal Basis for Processing</h2>
            <p>
                We process your personal information based on:
            </p>
            <ul>
                <li>Contract performance (fulfilling your orders)</li>
                <li>Legitimate interests (improving our services)</li>
                <li>Consent (marketing communications)</li>
                <li>Legal obligations (tax and accounting requirements)</li>
            </ul>
        </div>
    </div>
</div>
@endsection