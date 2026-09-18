<?php

namespace Tests\Feature;

use App\Livewire\Trd\Index;
use App\Models\Document;
use App\Models\Proceeding;
use App\Models\TrdStructure;
use App\Models\User;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\MongoTestCase;

class TrdHierarchyTest extends MongoTestCase
{
    #[Test]
    public function lider_inhabilita_trd_y_no_puede_reactivarla(): void
    {
        $lider = User::factory()->liderAmbiental()->create();
        $trd = $this->makeTrd();

        $this->actingAs($lider);

        Livewire::test(Index::class)
            ->call('toggleActive', (string) $trd->getKey())
            ->assertHasNoErrors();

        $this->assertFalse($trd->fresh()->is_active);

        Livewire::test(Index::class)
            ->call('toggleActive', (string) $trd->getKey())
            ->assertForbidden();

        $this->assertFalse($trd->fresh()->is_active);
    }

    #[Test]
    public function solo_superusuario_reactiva_la_trd(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $trd = $this->makeTrd(['is_active' => false]);

        $this->actingAs($admin);

        Livewire::test(Index::class)
            ->call('toggleActive', (string) $trd->getKey())
            ->assertHasNoErrors();

        $this->assertTrue($trd->fresh()->is_active);
    }

    #[Test]
    public function navegacion_por_capas_muestra_organo_series_expediente_y_pdf(): void
    {
        $user = User::factory()->superAdmin()->create();
        $trd = $this->makeTrd();
        $subId = $trd->sub_sections[0]['sub_section_id'];
        $serieId = $trd->series[0]['serie_id'];

        $proceeding = Proceeding::query()->create([
            'trd_structure_id' => (string) $trd->getKey(),
            'serie_id' => $serieId,
            'sub_serie_id' => $trd->sub_series[0]['sub_serie_id'],
            'file_number' => 'EXP-CAPA-001',
            'name' => 'Expediente jerárquico',
            'serie_name' => 'Actas',
            'sub_serie_name' => 'Ordinarias',
            'state' => 'Público',
            'is_deleted' => false,
        ]);
        Document::query()->create([
            'proceedings_id' => (string) $proceeding->getKey(),
            'name' => 'Acta de comité',
            'document_type' => 'Acta',
            'support' => 'Electrónico',
            'file_path' => 'docs/demo.pdf',
            'state' => 'Cerrado',
            'is_deleted' => false,
        ]);

        $this->actingAs($user)
            ->get(route('trd.index'))
            ->assertOk()
            ->assertSee('Capa 1 de 4', false)
            ->assertSee('Secretaría General', false);

        $this->get(route('trd.show', $trd->getKey()))
            ->assertOk()
            ->assertSee('Estructura orgánica', false)
            ->assertSee('Oficina Jurídica', false);

        $this->get(route('trd.series', [$trd->getKey(), $subId]))
            ->assertOk()
            ->assertSee('Series y subseries', false)
            ->assertSee('Actas', false)
            ->assertSee('Ordinarias', false);

        $this->get(route('trd.holdings', [$trd->getKey(), $subId, $serieId]))
            ->assertOk()
            ->assertSee('EXP-CAPA-001', false)
            ->assertSee('Expediente jerárquico', false);

        $this->get(route('trd.expediente', [$trd->getKey(), $subId, $serieId, $proceeding->getKey()]))
            ->assertOk()
            ->assertSee('Tipos documentales', false)
            ->assertSee('Acta de comité', false)
            ->assertSee('Abrir PDF', false);
    }

    #[Test]
    public function aprendiz_no_puede_cambiar_activacion(): void
    {
        $user = User::factory()->aprendiz()->create();
        $trd = $this->makeTrd();

        $this->actingAs($user);

        Livewire::test(Index::class)
            ->call('toggleActive', (string) $trd->getKey())
            ->assertForbidden();

        $this->assertTrue($trd->fresh()->is_active);
    }

    /**
     * @param  array<string, mixed>  $extra
     */
    private function makeTrd(array $extra = []): TrdStructure
    {
        return TrdStructure::query()->create(array_merge([
            'section_code' => '100',
            'section_name' => 'Secretaría General',
            'version' => 'TRD-V1-2026',
            'approved_at' => now()->subMonth(),
            'is_active' => true,
            'is_deleted' => false,
            'sub_sections' => [[
                'sub_section_id' => 'sub-juridica',
                'sub_section_code' => '110',
                'sub_section_name' => 'Oficina Jurídica',
            ]],
            'series' => [[
                'serie_id' => 'ser-actas',
                'serie_code' => '110.10',
                'serie_name' => 'Actas',
                'retencion_gestion' => 3,
                'retencion_central' => 15,
                'disposicion_final' => 'CT',
            ]],
            'sub_series' => [[
                'sub_serie_id' => 'ss-ord',
                'sub_serie_code' => '110.10.01',
                'sub_serie_name' => 'Ordinarias',
            ]],
        ], $extra));
    }
}
