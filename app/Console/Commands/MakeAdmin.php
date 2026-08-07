<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:make-admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    // app/Console/Commands/MakeAdmin.php
public function handle()
{
    $name = $this->ask('Name');
    $email = $this->ask('Email');
    $password = $this->secret('Password');

    User::create([
        'name' => $name,
        'email' => $email,
        'password' => Hash::make($password),
        'role' => 'admin',
    ]);

    $this->info("Admin created: {$email}");
}
}
