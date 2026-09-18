<?php

namespace Tests\Feature;

use App\Livewire\QuickSearch;
use App\Models\Document;
use App\Models\Proceeding;
use App\Models\TrdStructure;
use App\Models\User;
use App\Support\RoleCatalog;
use App\Support\SearchQuery;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\MongoTestCase;

class QuickSearchTest extends MongoTestCase
{
    #[Test]
    public function el_home_muestra_el_buscador_rapido(): void
    {
        $user = User::factory()->superAdmin()->create(['allowed_ip_range' => '*']);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Buscador rápido', false)
            ->assertSee('Código', false)
            ->assertSee('Persona', false);
    }

    #[Test]
    public function resultados_enlazan_al_modulo_y_respetan_el_tope(): void
    {
        $user = User::factory()->superAdmin()->create([
            'name' => 'Ana Ambiental',
            'email' => 'ana.ambiental@trd.gob',
            'allowed_ip_range' => '*',
        ]);

        $trd = TrdStructure::query()->create([
            'section_code' => 'ACT-100',
            'section_name' => 'Actas de comité',
            'version' => 'TRD-QS',
            'is_active' => true,
            'is_deleted' => false,
            'series' => [],
            'sub_sections' => [],
            'sub_series' => [],
        ]);

        $proceeding = Proceeding::query()->create([
            'trd_structure_id' => (string) $trd->getKey(),
            'file_number' => 'ACT-EXP-001',
            'name' => 'Acta de apertura',
            'state' => 'Público',
            'is_deleted' => false,
        ]);

        Document::query()->create([
            'proceedings_id' => (string) $proceeding->getKey(),
            'document_type' => 'Acta',
            'name' => 'Acta ambiental firmada',
            'support' => 'Físico',
            'state' => 'Abierto',
            'is_deleted' => false,
        ]);

        for ($i = 1; $i <= SearchQuery::MAX_RESULTS + 4; $i++) {
            Proceeding::query()->create([
                'trd_structure_id' => (string) $trd->getKey(),
                'file_number' => sprintf('ACT-LOT-%03d', $i),
                'name' => 'Lote acta '.$i,
                'state' => 'Público',
                'is_deleted' => false,
            ]);
        }

        $this->actingAs($user);

        $lote = app(\App\Services\QuickSearchService::class)->search($user, 'ACT-LOT', 'expediente');
        $this->assertNotEmpty($lote['items']);
        $this->assertLessThanOrEqual(SearchQuery::MAX_RESULTS, count($lote['items']));

        Livewire::test(QuickSearch::class)
            ->set('q', 'act')
            ->assertSee('Actas de comité', false)
            ->assertSee(route('trd.show', $trd->getKey()), false)
            ->assertSee('Acta de apertura', false)
            ->assertSee(route('proceedings.show', $proceeding->getKey()), false)
            ->assertSee('Acta ambiental firmada', false)
            ->assertSee('Ana Ambiental', false)
            ->set('filter', 'expediente')
            ->assertDontSee('Acta ambiental firmada', false)
            ->assertDontSee('Ana Ambiental', false)
            ->assertSee('Acta de apertura', false);
    }

    #[Test]
    public function persona_queda_oculta_sin_permiso_de_usuarios(): void
    {
        $aprendiz = User::factory()->aprendiz()->create([
            'name' => 'Pedro Aprendiz',
            'permissions' => ['documents.view', 'proceedings.view', 'trd.view'],
            'allowed_ip_range' => '*',
        ]);
        User::factory()->create([
            'name' => 'Pedro Persona',
            'email' => 'pedro.persona@trd.gob',
            'role' => RoleCatalog::LIDER_AMBIENTAL,
            'roles' => [RoleCatalog::LIDER_AMBIENTAL],
            'allowed_ip_range' => '*',
        ]);

        $this->actingAs($aprendiz);

        Livewire::test(QuickSearch::class)
            ->set('q', 'Pedro')
            ->set('filter', 'persona')
            ->assertDontSee('Pedro Persona', false);
    }
}
