<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Faq;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = [
            [
                'question' => 'How do I register as a resident?',
                'answer' => 'To register as a resident, visit the barangay hall during office hours and bring a valid ID, proof of residence, and other required documents. Our staff will assist you with the registration process.',
                'category' => 'Resident Registration',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'question' => 'What documents do I need for barangay clearance?',
                'answer' => 'You need to bring a valid ID, proof of residence, and pay the required fee. The clearance is usually processed within 1-2 business days.',
                'category' => 'Document Services',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'question' => 'How can I report a community concern?',
                'answer' => 'You can report community concerns through our online portal, visit the barangay hall, or call our hotline. All reports are treated confidentially and will be addressed promptly.',
                'category' => 'Community Services',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'question' => 'What are the office hours?',
                'answer' => 'Our office is open Monday to Friday from 8:00 AM to 5:00 PM, and Saturday from 8:00 AM to 12:00 PM. We are closed on Sundays and holidays.',
                'category' => 'General Information',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'question' => 'How do I request a medical certificate?',
                'answer' => 'Visit the health center during office hours and bring a valid ID. The medical certificate will be issued after a medical examination by our health personnel.',
                'category' => 'Health Services',
                'order' => 5,
                'is_active' => true,
            ],
            [
                'question' => 'Can I pay barangay fees online?',
                'answer' => 'Yes, we accept online payments for most barangay services. You can pay through our online portal using various payment methods such as GCash, PayMaya, or bank transfer.',
                'category' => 'Payment Services',
                'order' => 6,
                'is_active' => true,
            ],
            [
                'question' => 'How do I schedule an appointment?',
                'answer' => 'You can schedule an appointment through our online booking system, call our office, or visit the barangay hall. We recommend booking in advance to avoid long waiting times.',
                'category' => 'Appointments',
                'order' => 7,
                'is_active' => true,
            ],
            [
                'question' => 'What services are available for senior citizens?',
                'answer' => 'Senior citizens can avail of various services including medical assistance, social pension, senior citizen ID, and priority lanes for government transactions. Please bring your senior citizen ID for verification.',
                'category' => 'Senior Citizens',
                'order' => 8,
                'is_active' => true,
            ],
            [
                'question' => 'How do I get a barangay ID?',
                'answer' => 'To get a barangay ID, bring a valid government-issued ID, proof of residence, and 2x2 ID picture. The ID is usually processed within 3-5 business days.',
                'category' => 'Document Services',
                'order' => 9,
                'is_active' => true,
            ],
            [
                'question' => 'What should I do in case of emergency?',
                'answer' => 'In case of emergency, call 911 or our barangay emergency hotline. For non-life threatening emergencies, you can also contact our barangay hall for assistance.',
                'category' => 'Emergency Services',
                'order' => 10,
                'is_active' => true,
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::create($faq);
        }
    }
}
