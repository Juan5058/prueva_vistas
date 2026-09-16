<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Jobs fallidos: Redis + driver de cola. Sin tablas SQL.
    }

    public function down(): void
    {
        //
    }
};
