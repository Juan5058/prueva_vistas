<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Colecciones MongoDB: users, password_reset_tokens y sessions se crean al insertar.
    }

    public function down(): void
    {
        //
    }
};
