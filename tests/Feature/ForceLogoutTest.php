<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserLoginLog;
use App\Support\RoleCatalog;
use PHPUnit\Framework\Attributes\Test;
use Tests\MongoTestCase;

class ForceLogoutTest extends MongoTestCase
{
    #[Test]
    public function force_logout_cierra_la_sesion_en_el_siguiente_request(): void
    {
        $user = User::factory()->superAdmin()->create([
            'force_logout' => true,
            'is_active' => true,
            'allowed_ip_range' => '*',
        ]);

        $this->actingAs($user)
            ->get('/')
            ->assertRedirect('/login');

        $this->assertGuest();
        $this->assertTrue(
            UserLoginLog::query()
                ->where('user_email', $user->email)
                ->where('status', 'BLOCKED')
                ->where('reason', 'regex', '/force_logout/i')
                ->exists()
        );
    }

    #[Test]
    public function super_admin_puede_activar_force_logout_en_otro_usuario(): void
    {
        $admin = User::factory()->superAdmin()->create([
            'email' => 'admin-force@trd.gob',
            'allowed_ip_range' => '*',
        ]);
        $target = User::factory()->aprendiz()->create([
            'email' => 'aprendiz-force@trd.gob',
            'allowed_ip_range' => '*',
            'force_logout' => false,
        ]);

        $this->actingAs($admin)
            ->get('/seguridad/sesiones')
            ->assertOk();

        app(\App\Services\UserService::class)->forceLogout(
            $target,
            '127.0.0.1',
            'PHPUnit',
        );

        $this->assertTrue($target->fresh()->force_logout);
        $this->assertTrue(
            UserLoginLog::query()
                ->where('user_email', $target->email)
                ->where('reason', 'regex', '/cierre forzado/i')
                ->exists()
        );
        $this->assertNotSame(RoleCatalog::SUPER_ADMIN, $target->role);
    }
}
