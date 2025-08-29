<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\Agent;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing agents to assign clients to
        $agents = Agent::take(5)->get();
        
        if ($agents->isEmpty()) {
            // If no agents exist, create clients without agent assignment
            $agents = collect([null]);
        }

        $clients = [
            [
                'full_name' => 'Sarah Johnson',
                'date_of_birth' => '1995-03-15',
                'phone_number' => '+44 7700 900123',
                'email' => 'sarah.johnson@email.com',
                'nationality' => 'British',
                'current_address' => '123 High Street, London, W1D 1BS',
                'company_university_name' => 'University College London',
                'company_university_address' => 'Gower Street, London, WC1E 6BT',
                'position_role' => 'MSc Student - Computer Science',
            ],
            [
                'full_name' => 'Michael Chen',
                'date_of_birth' => '1988-07-22',
                'phone_number' => '+44 7700 900456',
                'email' => 'michael.chen@techcorp.com',
                'nationality' => 'Chinese',
                'current_address' => '456 Oxford Street, London, W1C 1AP',
                'company_university_name' => 'TechCorp Solutions Ltd',
                'company_university_address' => 'Canary Wharf, London, E14 5AB',
                'position_role' => 'Senior Software Engineer',
            ],
            [
                'full_name' => 'Emma Rodriguez',
                'date_of_birth' => '1992-11-08',
                'phone_number' => '+44 7700 900789',
                'email' => 'emma.rodriguez@email.com',
                'nationality' => 'Spanish',
                'current_address' => '789 Camden High Street, London, NW1 7NL',
                'company_university_name' => 'King\'s College London',
                'company_university_address' => 'Strand, London, WC2R 2LS',
                'position_role' => 'PhD Student - Economics',
            ],
            [
                'full_name' => 'James Wilson',
                'date_of_birth' => '1985-04-30',
                'phone_number' => '+44 7700 900012',
                'email' => 'james.wilson@finance.com',
                'nationality' => 'British',
                'current_address' => '321 Mayfair, London, W1J 8LB',
                'company_university_name' => 'Goldman Sachs',
                'company_university_address' => 'Plumtree Court, London, EC4A 3AB',
                'position_role' => 'Investment Analyst',
            ],
            [
                'full_name' => 'Aisha Patel',
                'date_of_birth' => '1990-09-12',
                'phone_number' => '+44 7700 900345',
                'email' => 'aisha.patel@email.com',
                'nationality' => 'Indian',
                'current_address' => '654 Brick Lane, London, E1 6RU',
                'company_university_name' => 'Imperial College London',
                'company_university_address' => 'South Kensington, London, SW7 2AZ',
                'position_role' => 'Research Assistant - Engineering',
            ],
            [
                'full_name' => 'David Thompson',
                'date_of_birth' => '1987-12-03',
                'phone_number' => '+44 7700 900678',
                'email' => 'david.thompson@consulting.com',
                'nationality' => 'American',
                'current_address' => '987 Soho Square, London, W1D 3QB',
                'company_university_name' => 'McKinsey & Company',
                'company_university_address' => '1 Jermyn Street, London, SW1Y 4UH',
                'position_role' => 'Management Consultant',
            ],
            [
                'full_name' => 'Maria Garcia',
                'date_of_birth' => '1993-06-18',
                'phone_number' => '+44 7700 900901',
                'email' => 'maria.garcia@email.com',
                'nationality' => 'Mexican',
                'current_address' => '147 Shoreditch High Street, London, E1 6JE',
                'company_university_name' => 'London School of Economics',
                'company_university_address' => 'Houghton Street, London, WC2A 2AE',
                'position_role' => 'MSc Student - International Relations',
            ],
            [
                'full_name' => 'Robert Kim',
                'date_of_birth' => '1986-01-25',
                'phone_number' => '+44 7700 900234',
                'email' => 'robert.kim@startup.com',
                'nationality' => 'South Korean',
                'current_address' => '258 Old Street, London, EC1V 9BP',
                'company_university_name' => 'TechStartup Ltd',
                'company_university_address' => 'Silicon Roundabout, London, EC1V 9BP',
                'position_role' => 'Founder & CEO',
            ],
            [
                'full_name' => 'Lisa Anderson',
                'date_of_birth' => '1991-08-14',
                'phone_number' => '+44 7700 900567',
                'email' => 'lisa.anderson@email.com',
                'nationality' => 'Canadian',
                'current_address' => '369 Notting Hill Gate, London, W11 3JQ',
                'company_university_name' => 'Royal College of Art',
                'company_university_address' => 'Kensington Gore, London, SW7 2EU',
                'position_role' => 'MA Student - Fine Art',
            ],
            [
                'full_name' => 'Ahmed Hassan',
                'date_of_birth' => '1989-05-07',
                'phone_number' => '+44 7700 900890',
                'email' => 'ahmed.hassan@healthcare.com',
                'nationality' => 'Egyptian',
                'current_address' => '741 Harley Street, London, W1G 9QN',
                'company_university_name' => 'NHS Foundation Trust',
                'company_university_address' => 'University College Hospital, London, NW1 2BU',
                'position_role' => 'Junior Doctor',
            ],
        ];

        foreach ($clients as $index => $clientData) {
            // Assign to agents in round-robin fashion
            $agentIndex = $index % $agents->count();
            $agent = $agents[$agentIndex];
            
            if ($agent) {
                $clientData['agent_id'] = $agent->id;
            }
            
            Client::create($clientData);
        }
    }
}
