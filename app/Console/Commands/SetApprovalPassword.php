<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\AproveSaveSetting;

class SetApprovalPassword extends Command
{
    protected $signature = 'app:approval-password:set {password?}';
    protected $description = 'Set or update the approval password (aprove_save_settings table)';

    public function handle(): int
    {
        $password = $this->argument('password') ?? $this->secret('New approval password');
        if (!$password || strlen($password) < 4) {
            $this->error('Password must be at least 4 characters.');
            return self::INVALID;
        }

        try {
            DB::beginTransaction();

            $hashed = Hash::make($password);
            $record = AproveSaveSetting::query()->first();
            if (!$record) {
                $record = new AproveSaveSetting();
            }
            $record->approval_password_save = $hashed;
            $record->last_request_at = now();
            $record->save();

            DB::commit();
            $this->info('Approval password saved successfully.');
            return self::SUCCESS;
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Failed to save approval password: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}

