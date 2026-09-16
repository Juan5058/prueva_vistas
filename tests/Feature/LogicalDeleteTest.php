<?php

namespace Tests\Feature;

use App\Models\ArchiveDocument;
use App\Models\Proceeding;
use App\Models\TrdStructure;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use Tests\MongoTestCase;

class LogicalDeleteTest extends MongoTestCase
{
    #[Test]
    public function delete_en_documento_lanza_politica_critica(): void
    {
        $document = ArchiveDocument::query()->create([
            'name' => 'Acta PDF',
            'document_type' => 'Acta',
            'support' => 'Electrónico',
            'state' => 'Cerrado',
            'is_deleted' => false,
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('CRITICAL_POLICY_VIOLATION');

        $document->delete();
    }

    #[Test]
    public function soft_delete_oculta_usuario_expediente_y_documento(): void
    {
        $user = User::factory()->superAdmin()->create(['email' => 'baja@trd.gob']);
        $trd = TrdStructure::query()->create([
            'section_code' => '900',
            'section_name' => 'Sección de prueba',
            'version' => 'TRD-TEST',
            'is_active' => true,
            'is_deleted' => false,
            'series' => [],
            'sub_sections' => [],
            'sub_series' => [],
        ]);
        $proceeding = Proceeding::query()->create([
            'trd_structure_id' => (string) $trd->getKey(),
            'file_number' => 'EXP-DEL-001',
            'name' => 'Expediente lógico',
            'state' => 'Público',
            'is_deleted' => false,
        ]);
        $document = ArchiveDocument::query()->create([
            'proceedings_id' => (string) $proceeding->getKey(),
            'name' => 'Documento lógico',
            'document_type' => 'Acta',
            'support' => 'Físico',
            'state' => 'Abierto',
            'is_deleted' => false,
        ]);

        $user->softDelete();
        $proceeding->softDelete();
        $document->softDelete();
        $trd->softDelete();

        $this->assertNull(User::query()->where('email', 'baja@trd.gob')->first());
        $this->assertNull(Proceeding::query()->where('file_number', 'EXP-DEL-001')->first());
        $this->assertNull(ArchiveDocument::query()->where('name', 'Documento lógico')->first());
        $this->assertNull(TrdStructure::query()->where('section_code', '900')->first());

        $this->assertTrue(User::onlyTrashed()->where('email', 'baja@trd.gob')->first()?->is_deleted);
        $this->assertTrue(Proceeding::onlyTrashed()->where('file_number', 'EXP-DEL-001')->first()?->is_deleted);
        $this->assertTrue(ArchiveDocument::onlyTrashed()->where('name', 'Documento lógico')->first()?->is_deleted);
        $this->assertTrue(TrdStructure::onlyTrashed()->where('section_code', '900')->first()?->is_deleted);
    }

    #[Test]
    public function login_rechaza_cuenta_con_is_deleted(): void
    {
        $user = User::factory()->create([
            'email' => 'deleted@trd.gob',
            'password' => 'secret123',
            'is_active' => true,
        ]);
        $user->softDelete();

        $this->post('/login', [
            'email' => 'deleted@trd.gob',
            'password' => 'secret123',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }
}
