<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\DocumentAuditLog;
use App\Models\Proceeding;
use App\Models\TrdImport;
use App\Models\TrdStructure;
use App\Models\User;
use App\Models\UserLoginLog;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use Tests\MongoTestCase;

class LogicalDeleteTest extends MongoTestCase
{
    #[Test]
    public function delete_en_documento_lanza_politica_critica(): void
    {
        $document = Document::query()->create([
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
        $document = Document::query()->create([
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
        $this->assertNull(Document::query()->where('name', 'Documento lógico')->first());
        $this->assertNull(TrdStructure::query()->where('section_code', '900')->first());

        $this->assertTrue(User::onlyTrashed()->where('email', 'baja@trd.gob')->first()?->is_deleted);
        $this->assertTrue(Proceeding::onlyTrashed()->where('file_number', 'EXP-DEL-001')->first()?->is_deleted);
        $this->assertTrue(Document::onlyTrashed()->where('name', 'Documento lógico')->first()?->is_deleted);
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

        $this->from('/login')->post('/login', [
            'email' => 'deleted@trd.gob',
            'password' => 'secret123',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    #[Test]
    public function soft_delete_oculta_import_log_y_auditoria(): void
    {
        $import = TrdImport::query()->create([
            'user_id' => 'tester',
            'file_name' => 'trd.csv',
            'status' => 'PENDING',
            'error_log' => [],
        ]);
        $log = UserLoginLog::query()->create([
            'user_email' => 'log@trd.gob',
            'ip_address' => '127.0.0.1',
            'status' => 'SUCCESS',
            'reason' => 'ok',
        ]);
        $audit = DocumentAuditLog::query()->create([
            'user_id' => 'tester',
            'document_id' => 'doc-1',
            'action' => 'VIEW',
            'ip_address' => '127.0.0.1',
        ]);

        $import->softDelete();
        $log->softDelete();
        $audit->softDelete();

        $this->assertNull(TrdImport::query()->find($import->getKey()));
        $this->assertNull(UserLoginLog::query()->find($log->getKey()));
        $this->assertNull(DocumentAuditLog::query()->find($audit->getKey()));
        $this->assertTrue(TrdImport::onlyTrashed()->find($import->getKey())?->is_deleted);
        $this->assertTrue(UserLoginLog::onlyTrashed()->find($log->getKey())?->is_deleted);
        $this->assertTrue(DocumentAuditLog::onlyTrashed()->find($audit->getKey())?->is_deleted);
    }
}
