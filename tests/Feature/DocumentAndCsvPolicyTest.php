<?php

namespace Tests\Feature;

use App\Jobs\ImportTrdJob;
use App\Models\ArchiveDocument;
use App\Models\DocumentAuditLog;
use App\Models\Proceeding;
use App\Models\TrdImport;
use App\Models\TrdStructure;
use App\Models\User;
use App\Services\DocumentService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\MongoTestCase;

class DocumentAndCsvPolicyTest extends MongoTestCase
{
    #[Test]
    public function pdf_se_guarda_con_uuid_y_mime_application_pdf(): void
    {
        Storage::fake();

        $user = User::factory()->superAdmin()->create();
        $trd = TrdStructure::query()->create([
            'section_code' => '300',
            'section_name' => 'Sección PDF',
            'version' => 'TRD-PDF',
            'is_active' => true,
            'is_deleted' => false,
            'series' => [['serie_id' => 's1', 'serie_code' => '1', 'serie_name' => 'Serie', 'retencion_gestion' => 1, 'retencion_central' => 1, 'disposicion_final' => 'CT']],
            'sub_sections' => [],
            'sub_series' => [['sub_serie_id' => 'ss1', 'sub_serie_code' => '1.1', 'sub_serie_name' => 'Sub']],
        ]);
        $proceeding = Proceeding::query()->create([
            'trd_structure_id' => (string) $trd->getKey(),
            'serie_id' => 's1',
            'sub_serie_id' => 'ss1',
            'file_number' => 'EXP-PDF-001',
            'name' => 'Expediente PDF',
            'state' => 'Público',
            'is_deleted' => false,
        ]);

        $this->actingAs($user);

        $file = UploadedFile::fake()->create('informe.pdf', 512, 'application/pdf');
        $document = app(DocumentService::class)->create([
            'proceedings_id' => (string) $proceeding->getKey(),
            'document_type' => 'Informe',
            'name' => 'Informe ambiental',
            'support' => 'Electrónico',
            'state' => 'Cerrado',
            'document_creation_date' => now()->toDateString(),
        ], $file);

        $this->assertTrue(Str::isUuid(data_get($document->file_metadata, 'uuid')));
        $this->assertSame('application/pdf', data_get($document->file_metadata, 'mime_type'));
        $this->assertNotNull($document->file_path);
        $this->assertMatchesRegularExpression('/^[0-9a-f-]{36}\.pdf$/i', basename((string) $document->file_path));
        $this->assertTrue(
            ArchiveDocument::query()->where('_id', $document->getKey())->exists()
        );
        $this->assertLessThanOrEqual(10 * 1024 * 1024, (int) data_get($document->file_metadata, 'size_bytes'));
    }

    #[Test]
    public function ver_documento_registra_auditoria_view(): void
    {
        $user = User::factory()->superAdmin()->create();
        $trd = TrdStructure::query()->create([
            'section_code' => '400',
            'section_name' => 'Sección VIEW',
            'version' => 'TRD-VIEW',
            'is_active' => true,
            'is_deleted' => false,
            'series' => [['serie_id' => 's1', 'serie_code' => '1', 'serie_name' => 'Serie', 'retencion_gestion' => 1, 'retencion_central' => 1, 'disposicion_final' => 'CT']],
            'sub_sections' => [],
            'sub_series' => [['sub_serie_id' => 'ss1', 'sub_serie_code' => '1.1', 'sub_serie_name' => 'Sub']],
        ]);
        $proceeding = Proceeding::query()->create([
            'trd_structure_id' => (string) $trd->getKey(),
            'serie_id' => 's1',
            'sub_serie_id' => 'ss1',
            'file_number' => 'EXP-VIEW-001',
            'name' => 'Expediente VIEW',
            'state' => 'Público',
            'is_deleted' => false,
        ]);
        $document = ArchiveDocument::query()->create([
            'proceedings_id' => (string) $proceeding->getKey(),
            'document_type' => 'Acta',
            'name' => 'Acta de auditoría VIEW',
            'support' => 'Electrónico',
            'state' => 'Cerrado',
            'is_deleted' => false,
            'file_metadata' => [
                'mime_type' => 'application/pdf',
                'original_name' => 'acta-view.pdf',
                'size_bytes' => 1024,
            ],
        ]);

        $this->actingAs($user)
            ->get(route('documents.show', $document->getKey()))
            ->assertOk()
            ->assertSee('Acta de auditoría VIEW', false)
            ->assertSee($user->name, false)
            ->assertSee('Consulta', false)
            ->assertDontSee($user->getKey(), false);

        $this->assertTrue(
            DocumentAuditLog::query()
                ->where('document_id', $document->getKey())
                ->where('action', 'VIEW')
                ->exists()
        );
    }

    #[Test]
    public function csv_exige_encabezados_del_pliego(): void
    {
        $import = TrdImport::query()->create([
            'user_id' => 'tester',
            'file_name' => 'malo.csv',
            'status' => 'PENDING',
            'total_rows' => 0,
            'processed_rows' => 0,
            'error_log' => [],
        ]);

        (new ImportTrdJob((string) $import->getKey(), "foo,bar\n1,2"))->handle();

        $import->refresh();
        $this->assertSame('FAILED', $import->status);
        $this->assertNotEmpty($import->error_log);
        $this->assertStringContainsString('codigo_seccion', implode(' ', $import->error_log));
    }
}
