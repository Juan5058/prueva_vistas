<?php

namespace Tests\Feature;

use App\Jobs\ImportTrdJob;
use App\Models\Document;
use App\Models\DocumentAuditLog;
use App\Models\Proceeding;
use App\Models\TrdImport;
use App\Models\TrdStructure;
use App\Models\User;
use App\Services\DocumentService;
use App\Support\RoleCatalog;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\MongoTestCase;
use ZipArchive;

class PromptGapsTest extends MongoTestCase
{
    #[Test]
    public function excel_xlsx_importa_encabezados_del_pliego(): void
    {
        $import = TrdImport::query()->create([
            'user_id' => 'tester',
            'file_name' => 'trd.xlsx',
            'status' => 'PENDING',
            'total_rows' => 0,
            'processed_rows' => 0,
            'error_log' => [],
        ]);

        $xlsx = $this->xlsx([
            ImportTrdJob::REQUIRED_HEADERS,
            ['300', 'Ambiente', '310', 'Gestión', '300.10', 'Informes', '300.10.01', 'Informes anuales', '3', '10', 'CT'],
        ]);

        (new ImportTrdJob((string) $import->getKey(), $xlsx, 'trd.xlsx'))->handle();

        $import->refresh();
        $this->assertSame('COMPLETED', $import->status, implode(' | ', $import->error_log ?? []));
        $this->assertSame(1, $import->processed_rows);
        $this->assertNotNull(TrdStructure::query()->where('section_code', '300')->first());
    }

    #[Test]
    public function reporte_pdf_requiere_permiso_y_descarga_application_pdf(): void
    {
        $denied = User::factory()->create([
            'role' => RoleCatalog::APRENDIZ,
            'roles' => [RoleCatalog::APRENDIZ],
            'permissions' => ['documents.view'],
            'allowed_ip_range' => '*',
        ]);
        $admin = User::factory()->superAdmin()->create(['allowed_ip_range' => '*']);

        $this->actingAs($denied)->get(route('reports.index'))->assertForbidden();
        $this->actingAs($denied)->get(route('reports.download-pdf'))->assertForbidden();

        $this->actingAs($admin)->get(route('reports.index'))->assertOk()->assertSee('Reportes de inventario', false);

        $download = $this->actingAs($admin)->get(route('reports.download-pdf'));
        $download->assertOk();
        $download->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', $download->getContent());
    }

    #[Test]
    public function documento_tiene_pantalla_de_edicion(): void
    {
        Storage::fake('private');
        $user = User::factory()->superAdmin()->create(['allowed_ip_range' => '*']);
        $trd = TrdStructure::query()->create([
            'section_code' => '500',
            'section_name' => 'Edición',
            'version' => 'TRD-EDIT',
            'is_active' => true,
            'is_deleted' => false,
            'series' => [],
            'sub_sections' => [],
            'sub_series' => [],
        ]);
        $proceeding = Proceeding::query()->create([
            'trd_structure_id' => (string) $trd->getKey(),
            'file_number' => 'EXP-EDIT-001',
            'name' => 'Expediente edición',
            'state' => 'Público',
            'is_deleted' => false,
        ]);
        $document = Document::query()->create([
            'proceedings_id' => (string) $proceeding->getKey(),
            'document_type' => 'Acta',
            'name' => 'Documento editable',
            'support' => 'Físico',
            'state' => 'Abierto',
            'is_deleted' => false,
        ]);

        $this->actingAs($user)
            ->get(route('documents.edit', $document->getKey()))
            ->assertOk()
            ->assertSee('Editar documento', false);

        app(DocumentService::class)->update((string) $document->getKey(), [
            'proceedings_id' => (string) $proceeding->getKey(),
            'document_type' => 'Informe',
            'name' => 'Documento actualizado',
            'description' => 'Cambio',
            'support' => 'Físico',
            'state' => 'Cerrado',
            'document_creation_date' => now()->toDateString(),
        ]);

        $this->assertSame('Documento actualizado', $document->fresh()->name);
        $this->assertTrue(
            DocumentAuditLog::query()
                ->where('document_id', $document->getKey())
                ->where('action', 'UPDATE')
                ->exists()
        );
    }

    /**
     * @param  list<list<string>>  $rows
     */
    private function xlsx(array $rows): string
    {
        $letters = range('A', 'Z');
        $sheetRows = '';

        foreach ($rows as $r => $cols) {
            $cells = '';
            foreach ($cols as $c => $value) {
                $ref = $letters[$c].($r + 1);
                $cells .= '<c r="'.$ref.'" t="inlineStr"><is><t>'.htmlspecialchars((string) $value, ENT_XML1).'</t></is></c>';
            }
            $sheetRows .= '<row r="'.($r + 1).'">'.$cells.'</row>';
        }

        $tmp = tempnam(sys_get_temp_dir(), 'xlsx');
        unlink($tmp);
        $zip = new ZipArchive;
        $zip->open($tmp, ZipArchive::CREATE);
        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/></Types>');
        $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>');
        $zip->addFromString('xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="TRD" sheetId="1" r:id="rId1"/></sheets></workbook>');
        $zip->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/></Relationships>');
        $zip->addFromString('xl/worksheets/sheet1.xml', '<?xml version="1.0" encoding="UTF-8"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>'.$sheetRows.'</sheetData></worksheet>');
        $zip->close();

        $binary = file_get_contents($tmp);
        unlink($tmp);

        return $binary;
    }
}
