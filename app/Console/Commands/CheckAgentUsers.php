<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CheckAgentUsers extends Command
{
    protected $signature = 'check:agent-users';
    protected $description = 'Check agent users in the system';

    public function handle()
    {
        $this->info('Agent Users:');
        $users = User::where('role', 'agent')->get(['id', 'name']);
        
        foreach ($users as $user) {
            $this->line("ID: {$user->id} - Name: '{$user->name}'");
        }
        
        $this->newLine();
        $this->info("Total agent users: {$users->count()}");
    }
}