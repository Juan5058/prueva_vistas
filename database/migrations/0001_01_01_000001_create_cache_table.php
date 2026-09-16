<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Caché y colas van por Redis, no por tablas SQL.
    }

    public function down(): void
    {
        //
    }
};
