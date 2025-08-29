<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Agent;

class AgentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing users to assign as agents
        $users = User::take(5)->get();
        
        if ($users->isEmpty()) {
            // Create a sample user if none exist
            $user = User::create([
                'name' => 'John Smith',
                'email' => 'john.smith@example.com',
                'password' => bcrypt('password'),
            ]);
            $users = collect([$user]);
        }

        $agents = [
            [
                'agent_code' => 'AG001',
                'company_name' => 'London Property Experts',
                'license_number' => 'LPE123456',
                'phone' => '+44 20 7123 4567',
                'mobile' => '+44 7700 900123',
                'website' => 'https://londonpropertyexperts.co.uk',
                'bio' => 'Experienced property agent specializing in residential properties across London. Over 10 years of experience in the London property market.',
                'specialization' => 'Residential, Flats, Houses',
                'experience_years' => '10',
                'languages' => 'English, Spanish',
                'office_address' => '123 Oxford Street',
                'office_city' => 'London',
                'office_postcode' => 'W1D 1BS',
                'office_phone' => '+44 20 7123 4568',
                'office_email' => 'office@londonpropertyexperts.co.uk',
                'working_hours' => 'Mon-Fri 9AM-6PM, Sat 10AM-4PM',
                'is_verified' => true,
                'is_featured' => true,
                'properties_count' => 25,
                'rating' => 4.8,
                'reviews_count' => 47,
            ],
            [
                'agent_code' => 'AG002',
                'company_name' => 'East London Properties',
                'license_number' => 'ELP789012',
                'phone' => '+44 20 7987 6543',
                'mobile' => '+44 7700 900456',
                'website' => 'https://eastlondonproperties.co.uk',
                'bio' => 'Specializing in East London properties including Shoreditch, Hackney, and Stratford. Great knowledge of the area and local market trends.',
                'specialization' => 'East London, New Developments',
                'experience_years' => '7',
                'languages' => 'English, Polish',
                'office_address' => '456 Brick Lane',
                'office_city' => 'London',
                'office_postcode' => 'E1 6RU',
                'office_phone' => '+44 20 7987 6544',
                'office_email' => 'info@eastlondonproperties.co.uk',
                'working_hours' => 'Mon-Fri 8AM-7PM, Sat 9AM-5PM',
                'is_verified' => true,
                'is_featured' => false,
                'properties_count' => 18,
                'rating' => 4.6,
                'reviews_count' => 32,
            ],
            [
                'agent_code' => 'AG003',
                'company_name' => 'Central London Estates',
                'license_number' => 'CLE345678',
                'phone' => '+44 20 7580 1234',
                'mobile' => '+44 7700 900789',
                'website' => 'https://centrallondonestates.co.uk',
                'bio' => 'Luxury property specialist in Central London. Expert in high-end residential properties in Mayfair, Knightsbridge, and Belgravia.',
                'specialization' => 'Luxury, Central London, Penthouses',
                'experience_years' => '15',
                'languages' => 'English, French, Italian',
                'office_address' => '789 Park Lane',
                'office_city' => 'London',
                'office_postcode' => 'W1K 7TH',
                'office_phone' => '+44 20 7580 1235',
                'office_email' => 'contact@centrallondonestates.co.uk',
                'working_hours' => 'Mon-Fri 9AM-6PM, Sat 10AM-3PM',
                'is_verified' => true,
                'is_featured' => true,
                'properties_count' => 42,
                'rating' => 4.9,
                'reviews_count' => 89,
            ],
            [
                'agent_code' => 'AG004',
                'company_name' => 'Student Accommodation Specialists',
                'license_number' => 'SAS901234',
                'phone' => '+44 20 8234 5678',
                'mobile' => '+44 7700 900012',
                'website' => 'https://studentaccommodation.co.uk',
                'bio' => 'Dedicated to helping students find the perfect accommodation in London. Specializing in student housing near universities and colleges.',
                'specialization' => 'Student Housing, Shared Accommodation',
                'experience_years' => '5',
                'languages' => 'English, Mandarin, Arabic',
                'office_address' => '321 Bloomsbury Street',
                'office_city' => 'London',
                'office_postcode' => 'WC1B 3QE',
                'office_phone' => '+44 20 8234 5679',
                'office_email' => 'hello@studentaccommodation.co.uk',
                'working_hours' => 'Mon-Fri 9AM-6PM, Sat 10AM-4PM',
                'is_verified' => true,
                'is_featured' => false,
                'properties_count' => 31,
                'rating' => 4.7,
                'reviews_count' => 156,
            ],
            [
                'agent_code' => 'AG005',
                'company_name' => 'South London Homes',
                'license_number' => 'SLH567890',
                'phone' => '+44 20 8765 4321',
                'mobile' => '+44 7700 900345',
                'website' => 'https://southlondonhomes.co.uk',
                'bio' => 'Family homes specialist in South London. Expert in houses, gardens, and family-friendly neighborhoods from Clapham to Croydon.',
                'specialization' => 'Family Homes, Houses with Gardens',
                'experience_years' => '12',
                'languages' => 'English, Portuguese',
                'office_address' => '654 Clapham High Street',
                'office_city' => 'London',
                'office_postcode' => 'SW4 7UN',
                'office_phone' => '+44 20 8765 4322',
                'office_email' => 'info@southlondonhomes.co.uk',
                'working_hours' => 'Mon-Fri 8AM-6PM, Sat 9AM-5PM',
                'is_verified' => true,
                'is_featured' => false,
                'properties_count' => 28,
                'rating' => 4.5,
                'reviews_count' => 41,
            ],
        ];

        foreach ($users as $index => $user) {
            if (isset($agents[$index])) {
                $agentData = $agents[$index];
                $agentData['user_id'] = $user->id;
                $agentData['social_media'] = [
                    'linkedin' => 'https://linkedin.com/in/' . strtolower(str_replace(' ', '-', $user->name)),
                    'twitter' => 'https://twitter.com/' . strtolower(str_replace(' ', '', $user->name)),
                ];
                $agentData['certifications'] = [
                    'NAEA Member',
                    'Propertymark Accredited',
                    'London Property Professional'
                ];
                $agentData['awards'] = [
                    'Best Agent 2023 - London Property Awards',
                    'Customer Service Excellence'
                ];
                $agentData['last_active'] = now();

                Agent::create($agentData);
            }
        }
    }
}
