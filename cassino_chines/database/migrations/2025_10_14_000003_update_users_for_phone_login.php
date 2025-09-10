<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY phone VARCHAR(30) NOT NULL");
        DB::statement("CREATE UNIQUE INDEX users_phone_unique ON users(phone)");
        DB::statement("ALTER TABLE users MODIFY email VARCHAR(191) NULL");
        DB::statement("ALTER TABLE users MODIFY name VARCHAR(191) NULL");
    }

    public function down(): void
    {
        DB::statement("DROP INDEX users_phone_unique ON users");
        DB::statement("ALTER TABLE users MODIFY phone VARCHAR(30) NULL");
        DB::statement("ALTER TABLE users MODIFY email VARCHAR(191) NOT NULL");
        DB::statement("ALTER TABLE users MODIFY name VARCHAR(191) NOT NULL");
    }
};

