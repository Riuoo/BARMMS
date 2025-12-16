@extends('layouts.public')

@section('title', 'Terms of Service - BARMMS')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Terms of Service</h1>
            <p class="text-gray-600 mb-6">Last updated: {{ date('F d, Y') }}</p>

            <div class="prose prose-lg max-w-none">
                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4">1. Acceptance of Terms</h2>
                    <p class="text-gray-700 mb-4">
                        By accessing and using the Barangay Administrative Records Management and Monitoring System (BARMMS), you accept and agree to be bound by the terms and provision of this agreement. If you do not agree to abide by the above, please do not use this service.
                    </p>
                    <p class="text-gray-700 mb-4">
                        These Terms of Service govern your use of BARMMS and any related services provided by the barangay administration.
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4">2. Description of Service</h2>
                    <p class="text-gray-700 mb-4">BARMMS provides the following services:</p>
                    <ul class="list-disc list-inside text-gray-700 mb-4 ml-4">
                        <li>Resident profile management and record keeping</li>
                        <li>Document request processing and certification</li>
                        <li>Blotter report management and tracking</li>
                        <li>Community concern reporting and resolution</li>
                        <li>Health record management and vaccination tracking</li>
                        <li>Project accomplishment monitoring and reporting</li>
                        <li>Analytics and data visualization for barangay planning</li>
                        <li>Communication and notification services</li>
                    </ul>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4">3. User Accounts and Registration</h2>
                    
                    <h3 class="text-xl font-medium text-gray-800 mb-3">3.1 Account Creation</h3>
                    <p class="text-gray-700 mb-4">
                        To access certain features of BARMMS, you must create an account. You agree to provide accurate, current, and complete information during registration and to update such information to keep it accurate, current, and complete.
                    </p>
                    <p class="text-gray-700 mb-4">
                        <strong>Consent Requirement:</strong> During account registration, you must provide explicit consent to our Privacy Policy by checking the consent checkbox. Without providing this consent, you cannot complete the registration process.
                    </p>

                    <h3 class="text-xl font-medium text-gray-800 mb-3">3.2 Account Security</h3>
                    <p class="text-gray-700 mb-4">
                        You are responsible for safeguarding the password and for all activities that occur under your account. You agree to notify us immediately of any unauthorized use of your account.
                    </p>

                    <h3 class="text-xl font-medium text-gray-800 mb-3">3.3 Account Termination</h3>
                    <p class="text-gray-700 mb-4">
                        We reserve the right to terminate or suspend your account at any time for violations of these terms or for any other reason at our sole discretion.
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4">4. Acceptable Use Policy</h2>
                    <p class="text-gray-700 mb-4">You agree not to use BARMMS to:</p>
                    <ul class="list-disc list-inside text-gray-700 mb-4 ml-4">
                        <li>Violate any applicable laws or regulations</li>
                        <li>Infringe upon the rights of others</li>
                        <li>Submit false or misleading information</li>
                        <li>Submit forms without providing required privacy consent</li>
                        <li>Attempt to bypass or circumvent privacy consent requirements</li>
                        <li>Attempt to gain unauthorized access to the system</li>
                        <li>Interfere with or disrupt the service</li>
                        <li>Use automated tools to access the system</li>
                        <li>Harass, abuse, or harm other users</li>
                        <li>Upload malicious code or files</li>
                        <li>Attempt to reverse engineer the system</li>
                        <li>Use the service for commercial purposes without authorization</li>
                    </ul>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4">5. Data and Privacy</h2>
                    <p class="text-gray-700 mb-4">
                        Your privacy is important to us. Please review our Privacy Policy, which also governs your use of the service, to understand our practices regarding the collection and use of your information.
                    </p>
                    <p class="text-gray-700 mb-4">
                        By using BARMMS, you consent to the collection and use of your information as described in our Privacy Policy.
                    </p>
                    
                    <h3 class="text-xl font-medium text-gray-800 mb-3">5.1 Explicit Consent Requirement</h3>
                    <p class="text-gray-700 mb-4">
                        When submitting forms that collect personal, health, or sensitive information, you must provide explicit consent by checking the privacy consent checkbox. This consent:
                    </p>
                    <ul class="list-disc list-inside text-gray-700 mb-4 ml-4">
                        <li>Is required before you can submit any form that collects personal information</li>
                        <li>Indicates that you have read and understood the Privacy Policy</li>
                        <li>Confirms your agreement to the collection, use, and storage of your data as described in the Privacy Policy</li>
                        <li>Can be withdrawn at any time by contacting our Data Protection Officer</li>
                    </ul>
                    <p class="text-gray-700 mb-4">
                        <strong>Important:</strong> Failure to provide consent will prevent you from submitting forms and accessing certain services that require personal information. Withdrawal of consent may also limit your ability to use certain features of the system.
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4">6. Intellectual Property Rights</h2>
                    <p class="text-gray-700 mb-4">
                        The BARMMS system, including its original content, features, and functionality, is owned by the barangay administration and is protected by copyright, trademark, and other intellectual property laws.
                    </p>
                    <p class="text-gray-700 mb-4">
                        You may not copy, modify, distribute, sell, or lease any part of our service or included software, nor may you reverse engineer or attempt to extract the source code of that software.
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4">7. User Content and Submissions</h2>
                    
                    <h3 class="text-xl font-medium text-gray-800 mb-3">7.1 Content Ownership</h3>
                    <p class="text-gray-700 mb-4">
                        You retain ownership of any content you submit to BARMMS. However, by submitting content, you grant us a license to use, store, and display that content in connection with the service.
                    </p>

                    <h3 class="text-xl font-medium text-gray-800 mb-3">7.2 Content Standards</h3>
                    <p class="text-gray-700 mb-4">
                        All content submitted must be accurate, lawful, and not infringe on the rights of others. We reserve the right to remove any content that violates these standards.
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4">8. Service Availability and Modifications</h2>
                    
                    <h3 class="text-xl font-medium text-gray-800 mb-3">8.1 Service Availability</h3>
                    <p class="text-gray-700 mb-4">
                        We strive to maintain high availability of BARMMS, but we do not guarantee that the service will be available at all times. The service may be temporarily unavailable due to maintenance, updates, or technical issues.
                    </p>

                    <h3 class="text-xl font-medium text-gray-800 mb-3">8.2 Service Modifications</h3>
                    <p class="text-gray-700 mb-4">
                        We reserve the right to modify, suspend, or discontinue any part of the service at any time. We will provide reasonable notice of any material changes when possible.
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4">9. Limitation of Liability</h2>
                    <p class="text-gray-700 mb-4">
                        To the maximum extent permitted by law, the barangay administration shall not be liable for any indirect, incidental, special, consequential, or punitive damages, including but not limited to loss of profits, data, or use.
                    </p>
                    <p class="text-gray-700 mb-4">
                        Our total liability to you for any claims arising from the use of BARMMS shall not exceed the amount you paid, if any, for accessing the service.
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4">10. Indemnification</h2>
                    <p class="text-gray-700 mb-4">
                        You agree to indemnify and hold harmless the barangay administration, its officers, employees, and agents from any claims, damages, or expenses arising from your use of BARMMS or violation of these terms.
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4">11. Dispute Resolution</h2>
                    <p class="text-gray-700 mb-4">
                        Any disputes arising from the use of BARMMS shall be resolved through amicable settlement. If such settlement is not possible, disputes shall be resolved in accordance with Philippine law and the jurisdiction of the appropriate courts.
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4">12. Governing Law</h2>
                    <p class="text-gray-700 mb-4">
                        These Terms of Service are governed by and construed in accordance with the laws of the Philippines. Any legal actions shall be brought in the appropriate courts of the Philippines.
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4">13. Severability</h2>
                    <p class="text-gray-700 mb-4">
                        If any provision of these Terms of Service is found to be unenforceable or invalid, that provision will be limited or eliminated to the minimum extent necessary so that the Terms of Service will otherwise remain in full force and effect.
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4">14. Changes to Terms</h2>
                    <p class="text-gray-700 mb-4">
                        We reserve the right to modify these Terms of Service at any time. We will notify users of any material changes through the system interface or by email. Your continued use of BARMMS after such changes constitutes acceptance of the updated terms.
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4">15. Contact Information</h2>
                    <p class="text-gray-700 mb-4">If you have questions about these Terms of Service, please contact us:</p>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-gray-700 mb-2"><strong>Barangay Administration:</strong></p>
                        <p class="text-gray-700 mb-2">Barangay Lower Malinao</p>
                        <p class="text-gray-700 mb-2">Email: admin@barmms.gov.ph</p>
                        <p class="text-gray-700 mb-2">Phone: +63 XXX XXX XXXX</p>
                        <p class="text-gray-700">Address: [Barangay Address]</p>
                    </div>
                </section>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('public.privacy') }}" class="text-blue-600 hover:text-blue-800 underline">Privacy Policy</a>
                <span class="mx-2 text-gray-400">|</span>
                <a href="{{ route('landing') }}" class="text-blue-600 hover:text-blue-800 underline">Back to Home</a>
            </div>
        </div>
    </div>
</div>
@endsection
