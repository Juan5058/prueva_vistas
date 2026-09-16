<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use MongoDB\Laravel\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('mongodb')->table('users', function (Blueprint $collection) {
            $collection->unique('email');
            $collection->index('is_deleted');
            $collection->index('force_logout');
            $collection->index('role');
        });

        Schema::connection('mongodb')->table('user_login_logs', function (Blueprint $collection) {
            $collection->index('user_email');
            $collection->index('status');
            $collection->index('ip_address');
            $collection->index('created_at');
        });

        Schema::connection('mongodb')->table('trd_structures', function (Blueprint $collection) {
            $collection->index('section_code');
            $collection->index('is_deleted');
        });

        Schema::connection('mongodb')->table('proceedings', function (Blueprint $collection) {
            $collection->unique('file_number');
            $collection->index('is_deleted');
        });

        Schema::connection('mongodb')->table('documents', function (Blueprint $collection) {
            $collection->index('proceedings_id');
            $collection->index('is_deleted');
        });

        Schema::connection('mongodb')->table('document_audit_logs', function (Blueprint $collection) {
            $collection->index('document_id');
            $collection->index('user_id');
            $collection->index('action');
        });
    }

    public function down(): void
    {
        // Índices Mongo: se recrean al migrar de nuevo.
    }
};
