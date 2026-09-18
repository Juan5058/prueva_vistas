<?php

namespace Tests\Unit;

use App\Support\SearchQuery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SearchQueryTest extends TestCase
{
    #[Test]
    public function ignora_terminos_cortos_y_recorta_los_largos(): void
    {
        $this->assertNull(SearchQuery::normalize('  ab  '));
        $this->assertSame('acta', SearchQuery::normalize('  acta  '));
        $this->assertSame(SearchQuery::MAX_LENGTH, mb_strlen((string) SearchQuery::normalize(str_repeat('a', 200))));
    }
}
