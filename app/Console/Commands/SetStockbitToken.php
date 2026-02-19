<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class SetStockbitToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:token {token? : The Stockbit token}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set the Stockbit authentication token for the current user.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $token = $this->argument('token');

        if (!$token) {
            $token = $this->ask('Please enter your Stockbit token');
        }

        if (empty($token)) {
            $this->error('Token cannot be empty.');
            return 1;
        }

        // Get the first user (create one if doesn't exist just in case, but usually exists)
        $user = User::first();

        if (!$user) {
            // Check if we can create a default user
            if ($this->confirm('No user found. Create a default user?', true)) {
                $user = User::create([
                    'name' => 'Admin',
                    'email' => 'admin@stockbiter.test',
                    'password' => bcrypt('password'),
                ]);
                $this->info('Created default user: admin@stockbiter.test / password');
            } else {
                $this->error('Operation cancelled.');
                return 1;
            }
        }

        // Save trace
        $user->stockbit_token = $token;
        $user->save();

        $this->info('Stockbit token saved successfully!');

        // Verify it decrypts
        try {
            // Refresh model
            $user->refresh();
            // Access attribute to trigger decryption
            $decrypted = $user->stockbit_token;
            if ($decrypted === $token) {
                $this->info('Token verification successful (encryption works).');
            }
        } catch (\Exception $e) {
            $this->error('Token verification failed: ' . $e->getMessage());
        }

        return 0;
    }
}
