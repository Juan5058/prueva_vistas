<?php

namespace Tests\Unit;

use App\Support\IpRange;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class IpRangeTest extends TestCase
{
    #[Test]
    public function permite_cualquier_ip_con_asterisco(): void
    {
        $this->assertTrue(IpRange::allows('201.55.99.1', '*'));
        $this->assertTrue(IpRange::allows('127.0.0.1', null));
    }

    #[Test]
    public function autoriza_ip_exacta_y_rango_con_wildcard(): void
    {
        $this->assertTrue(IpRange::allows('192.168.1.45', '192.168.1.45'));
        $this->assertTrue(IpRange::allows('192.168.1.80', '192.168.1.*'));
        $this->assertFalse(IpRange::allows('10.0.0.8', '192.168.1.*'));
    }

    #[Test]
    public function normaliza_localhost_ipv6(): void
    {
        $this->assertSame('127.0.0.1', IpRange::normalize('::1'));
        $this->assertSame('127.0.0.1', IpRange::normalize('::ffff:127.0.0.1'));
    }
}
