<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateSuperUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create-super';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new super user account';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Creating a new Super User for TRUEHOLD');
        $this->newLine();

        // Get user input
        $name = $this->ask('Enter full name');
        $email = $this->ask('Enter email address');
        $password = $this->secret('Enter password (min 8 characters)');
        $confirmPassword = $this->secret('Confirm password');

        // Validate input
        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $confirmPassword,
        ], [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return 1;
        }

        // Create the user
        try {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
            ]);

            $this->newLine();
            $this->info('✅ Super User created successfully!');
            $this->table(
                ['Field', 'Value'],
                [
                    ['Name', $user->name],
                    ['Email', $user->email],
                    ['ID', $user->id],
                    ['Created', $user->created_at->format('Y-m-d H:i:s')],
                ]
            );

            $this->newLine();
            $this->warn('🔐 You can now login with these credentials at:');
            $this->line('   Login URL: ' . url('/login'));
            $this->line('   Admin URL: ' . url('/admin'));

        } catch (\Exception $e) {
            $this->error('❌ Failed to create user: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
