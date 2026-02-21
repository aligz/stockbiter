<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

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

        if (! $token) {
            $token = $this->ask('Please enter your Stockbit token');
        }

        if (empty($token)) {
            $this->error('Token cannot be empty.');

            return 1;
        }

        // Save to Storage
        Storage::put('stockbit_token', $token);

        $this->info('Stockbit token saved successfully to storage!');

        // Verify it reads back
        try {
            $check = Storage::get('stockbit_token');
            if ($check === $token) {
                $this->info('Token verification successful (storage write/read works).');
            }
        } catch (\Exception $e) {
            $this->error('Token verification failed: '.$e->getMessage());
        }

        return 0;
    }
}
