<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;

class CreateAdminUser extends Command
{
    protected $signature = 'app:create-admin {--name=} {--email=} {--password=}';
    protected $description = 'Create an admin user safely (uses Spatie roles)';

    public function handle(): int
    {
        $name = $this->option('name') ?? $this->ask('Admin name');
        $email = $this->option('email') ?? $this->ask('Admin email');
        $password = $this->option('password') ?? $this->secret('Admin password');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Invalid email.');
            return self::INVALID;
        }

        if (strlen((string) $password) < 6) {
            $this->error('Password must be at least 6 characters.');
            return self::INVALID;
        }

        // Check if an admin already exists
        $existingAdmin = User::whereHas('roles', function ($q) {
            $q->where('name', 'admin');
        })->first();
        if ($existingAdmin) {
            $this->warn('An admin already exists: ' . $existingAdmin->email);
        }

        if (User::where('email', $email)->exists()) {
            $this->error('The email is already in use.');
            return self::FAILURE;
        }

        // Ensure role exists
        $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        // Create user
        $user = new User();
        $user->name = $name;
        $user->email = $email;
        $user->password = Hash::make($password);
        if ($user->isFillable('status')) {
            $user->status = 'active';
        }
        if ($user->isFillable('role_id')) {
            $user->role_id = 1; // keep backward compatibility
        }
        $user->email_verified_at = now();
        $user->save();

        $user->assignRole($role);

        $this->info('Admin created successfully: ' . $user->email);
        $this->line('ID: ' . $user->id);
        return self::SUCCESS;
    }
}

