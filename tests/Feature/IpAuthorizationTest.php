<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserLoginLog;
use App\Support\RoleCatalog;
use PHPUnit\Framework\Attributes\Test;
use Tests\MongoTestCase;

class IpAuthorizationTest extends MongoTestCase
{
    #[Test]
    public function login_desde_ip_fuera_de_rango_queda_bloqueado(): void
    {
        $user = User::factory()->create([
            'email' => 'ip@trd.gob',
            'password' => 'secret123',
            'role' => RoleCatalog::APRENDIZ,
            'roles' => [RoleCatalog::APRENDIZ],
            'permissions' => RoleCatalog::forRole(RoleCatalog::APRENDIZ),
            'allowed_ip_range' => '10.0.0.*',
            'is_active' => true,
            'force_logout' => false,
        ]);

        $this->from('/login')
            ->withSession(['simulated_ip' => '201.55.99.1'])
            ->post('/login', [
                'email' => $user->email,
                'password' => 'secret123',
            ])
            ->assertRedirect()
            ->assertSessionHasErrors('email');

        $this->assertGuest();
        $this->assertTrue(
            UserLoginLog::query()
                ->where('user_email', $user->email)
                ->where('status', 'BLOCKED')
                ->where('ip_address', '201.55.99.1')
                ->exists()
        );
    }

    #[Test]
    public function sesion_activa_con_ip_no_autorizada_activa_force_logout(): void
    {
        $user = User::factory()->superAdmin()->create([
            'allowed_ip_range' => '127.0.0.1',
            'current_ip' => '127.0.0.1',
            'is_active' => true,
            'force_logout' => false,
        ]);

        $this->actingAs($user)
            ->withSession(['simulated_ip' => '8.8.8.8'])
            ->get('/')
            ->assertRedirect('/login');

        $user->refresh();
        $this->assertTrue($user->force_logout);
        $this->assertFalse($user->is_active);
        $this->assertTrue(
            UserLoginLog::query()
                ->where('status', 'BLOCKED')
                ->where('ip_address', '8.8.8.8')
                ->exists()
        );
    }
}
