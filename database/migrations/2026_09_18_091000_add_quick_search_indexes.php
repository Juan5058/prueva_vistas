<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use MongoDB\Laravel\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('mongodb')->table('trd_structures', function (Blueprint $collection) {
            $collection->index('section_name');
        });

        Schema::connection('mongodb')->table('proceedings', function (Blueprint $collection) {
            $collection->index('name');
            $collection->index('section_code');
        });

        Schema::connection('mongodb')->table('documents', function (Blueprint $collection) {
            $collection->index('name');
        });

        Schema::connection('mongodb')->table('users', function (Blueprint $collection) {
            $collection->index('name');
        });
    }

    public function down(): void
    {
        // Índices Mongo: se recrean al migrar de nuevo.
    }
};
