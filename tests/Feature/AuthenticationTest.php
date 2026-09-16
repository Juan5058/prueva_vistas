<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\RoleCatalog;
use PHPUnit\Framework\Attributes\Test;
use Tests\MongoTestCase;

class AuthenticationTest extends MongoTestCase
{
    #[Test]
    public function la_pagina_de_login_se_muestra(): void
    {
        $this->get('/login')->assertOk();
    }

    #[Test]
    public function los_invitados_van_al_login(): void
    {
        $this->get('/')->assertRedirect('/login');
    }

    #[Test]
    public function super_admin_puede_autenticarse(): void
    {
        $this->seed();

        $this->from('/login')->post('/login', [
            'email' => 'admin@trd.gob',
            'password' => 'admin123',
        ])->assertRedirect('/');

        $this->assertAuthenticated();
    }

    #[Test]
    public function aprendiz_no_puede_crear_trd(): void
    {
        $this->seed();
        $aprendiz = User::query()->where('email', 'aprendiz@trd.gob')->first();

        $this->actingAs($aprendiz)
            ->get('/trd-nueva')
            ->assertForbidden();
    }

    #[Test]
    public function roles_oficiales_quedan_sembrados(): void
    {
        $this->seed();

        $this->assertTrue(User::query()->where('role', RoleCatalog::SUPER_ADMIN)->exists());
        $this->assertTrue(User::query()->where('role', RoleCatalog::LIDER_AMBIENTAL)->exists());
        $this->assertTrue(User::query()->where('role', RoleCatalog::APRENDIZ)->exists());
    }
}
