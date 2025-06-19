<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE personal_access_tokens ALTER COLUMN created_at DATETIME2');
        DB::statement('ALTER TABLE personal_access_tokens ALTER COLUMN updated_at DATETIME2');
        DB::statement('ALTER TABLE personal_access_tokens ALTER COLUMN last_used_at DATETIME2 NULL');
        DB::statement('ALTER TABLE personal_access_tokens ALTER COLUMN expires_at DATETIME2 NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE personal_access_tokens ALTER COLUMN created_at DATETIME');
        DB::statement('ALTER TABLE personal_access_tokens ALTER COLUMN updated_at DATETIME');
        DB::statement('ALTER TABLE personal_access_tokens ALTER COLUMN last_used_at DATETIME NULL');
        DB::statement('ALTER TABLE personal_access_tokens ALTER COLUMN expires_at DATETIME NULL');
    }
};