@extends('layouts.public')

@section('title', 'Privacy Policy - BARMMS')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Privacy Policy</h1>
            <p class="text-gray-600 mb-6">Last updated: {{ date('F d, Y') }}</p>

            <div class="prose prose-lg max-w-none">
                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4">1. Introduction</h2>
                    <p class="text-gray-700 mb-4">
                        The Barangay Administrative Records Management and Monitoring System (BARMMS) is committed to protecting your privacy and ensuring the security of your personal information. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our system.
                    </p>
                    <p class="text-gray-700 mb-4">
                        By using BARMMS, you consent to the data practices described in this policy. If you do not agree with our policies and practices, please do not use our system.
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4">2. Information We Collect</h2>
                    
                    <h3 class="text-xl font-medium text-gray-800 mb-3">2.1 Personal Information</h3>
                    <p class="text-gray-700 mb-4">We may collect the following types of personal information:</p>
                    <ul class="list-disc list-inside text-gray-700 mb-4 ml-4">
                        <li>Name, address, and contact information</li>
                        <li>Date of birth and place of birth</li>
                        <li>Civil status and nationality</li>
                        <li>Emergency contact information</li>
                        <li>Health-related information (for health services)</li>
                        <li>Government-issued identification numbers</li>
                        <li>Employment and educational background</li>
                    </ul>

                    <h3 class="text-xl font-medium text-gray-800 mb-3">2.2 System Usage Information</h3>
                    <p class="text-gray-700 mb-4">We automatically collect certain information about your use of the system:</p>
                    <ul class="list-disc list-inside text-gray-700 mb-4 ml-4">
                        <li>IP address and device information</li>
                        <li>Browser type and version</li>
                        <li>Pages visited and time spent</li>
                        <li>System access logs and timestamps</li>
                        <li>Error logs and system performance data</li>
                    </ul>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4">3. How We Use Your Information</h2>
                    <p class="text-gray-700 mb-4">We use the collected information for the following purposes:</p>
                    <ul class="list-disc list-inside text-gray-700 mb-4 ml-4">
                        <li>Providing barangay services and processing requests</li>
                        <li>Managing resident records and profiles</li>
                        <li>Processing document requests and certifications</li>
                        <li>Managing health records and vaccination schedules</li>
                        <li>Handling blotter reports and community concerns</li>
                        <li>Generating reports and analytics for barangay planning</li>
                        <li>Improving system functionality and user experience</li>
                        <li>Ensuring system security and preventing fraud</li>
                        <li>Complying with legal and regulatory requirements</li>
                    </ul>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4">4. Information Sharing and Disclosure</h2>
                    <p class="text-gray-700 mb-4">We do not sell, trade, or otherwise transfer your personal information to third parties, except in the following circumstances:</p>
                    <ul class="list-disc list-inside text-gray-700 mb-4 ml-4">
                        <li>With your explicit consent</li>
                        <li>To comply with legal obligations or court orders</li>
                        <li>To protect the rights, property, or safety of the barangay or others</li>
                        <li>To authorized government agencies as required by law</li>
                        <li>To service providers who assist in system operations (under strict confidentiality agreements)</li>
                    </ul>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4">5. Data Security</h2>
                    <p class="text-gray-700 mb-4">We implement appropriate technical and organizational security measures to protect your personal information:</p>
                    <ul class="list-disc list-inside text-gray-700 mb-4 ml-4">
                        <li>Encryption of sensitive data in transit and at rest</li>
                        <li>Secure authentication and access controls</li>
                        <li>Regular security audits and vulnerability assessments</li>
                        <li>Employee training on data protection practices</li>
                        <li>Incident response and breach notification procedures</li>
                        <li>Regular backups and disaster recovery planning</li>
                    </ul>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4">6. Data Retention</h2>
                    <p class="text-gray-700 mb-4">We retain your personal information only for as long as necessary to fulfill the purposes outlined in this policy:</p>
                    <ul class="list-disc list-inside text-gray-700 mb-4 ml-4">
                        <li>Active resident records: Retained while resident is active</li>
                        <li>Document requests: Retained for 7 years as per government requirements</li>
                        <li>Health records: Retained according to health regulations</li>
                        <li>System logs: Retained for 2 years for security and audit purposes</li>
                        <li>Inactive accounts: Deleted after 5 years of inactivity</li>
                    </ul>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4">7. Your Rights</h2>
                    <p class="text-gray-700 mb-4">You have the following rights regarding your personal information:</p>
                    <ul class="list-disc list-inside text-gray-700 mb-4 ml-4">
                        <li>Right to access and review your personal information</li>
                        <li>Right to request correction of inaccurate information</li>
                        <li>Right to request deletion of your information (subject to legal requirements)</li>
                        <li>Right to withdraw consent for data processing</li>
                        <li>Right to file a complaint with the Data Protection Commission</li>
                        <li>Right to request information about data sharing practices</li>
                    </ul>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4">8. Cookies and Tracking</h2>
                    <p class="text-gray-700 mb-4">
                        Our system uses essential cookies to maintain your session and ensure proper functionality. We do not use tracking cookies or third-party analytics that collect personal information.
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4">9. Children's Privacy</h2>
                    <p class="text-gray-700 mb-4">
                        We collect information about children only with parental consent and in accordance with applicable laws. Parents have the right to review, correct, or delete their children's information.
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4">10. Changes to This Policy</h2>
                    <p class="text-gray-700 mb-4">
                        We may update this Privacy Policy from time to time. We will notify users of any material changes through the system interface or by email. Your continued use of the system after such changes constitutes acceptance of the updated policy.
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4">11. Contact Information</h2>
                    <p class="text-gray-700 mb-4">If you have questions about this Privacy Policy or our data practices, please contact us:</p>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-gray-700 mb-2"><strong>Data Protection Officer:</strong></p>
                        <p class="text-gray-700 mb-2">Barangay Lower Malinao</p>
                        <p class="text-gray-700 mb-2">Email: privacy@barmms.gov.ph</p>
                        <p class="text-gray-700 mb-2">Phone: +63 XXX XXX XXXX</p>
                        <p class="text-gray-700">Address: [Barangay Address]</p>
                    </div>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4">12. Governing Law</h2>
                    <p class="text-gray-700 mb-4">
                        This Privacy Policy is governed by the laws of the Philippines, including the Data Privacy Act of 2012 (Republic Act No. 10173) and its implementing rules and regulations.
                    </p>
                </section>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('public.terms') }}" class="text-blue-600 hover:text-blue-800 underline">Terms of Service</a>
                <span class="mx-2 text-gray-400">|</span>
                <a href="{{ route('landing') }}" class="text-blue-600 hover:text-blue-800 underline">Back to Home</a>
            </div>
        </div>
    </div>
</div>
@endsection
