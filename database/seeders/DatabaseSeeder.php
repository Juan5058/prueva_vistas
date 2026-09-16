<?php

namespace Database\Seeders;

use App\Models\ArchiveDocument;
use App\Models\Proceeding;
use App\Models\TrdStructure;
use App\Models\User;
use App\Models\UserLoginLog;
use App\Support\RoleCatalog;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = $this->user(
            'Administrador General TRD',
            'admin@trd.gob',
            'admin123',
            RoleCatalog::SUPER_ADMIN,
            '127.0.0.1',
            '*',
        );

        $lider = $this->user(
            'Lic. Elena Morales - Líder Ambiental',
            'lider@trd.gob',
            'lider123',
            RoleCatalog::LIDER_AMBIENTAL,
            '192.168.1.45',
            '*',
        );

        $this->user(
            'Aprendiz SENA - Gestión Documental',
            'aprendiz@trd.gob',
            'aprendiz123',
            RoleCatalog::APRENDIZ,
            '192.168.1.80',
            '*',
        );

        UserLoginLog::query()->create([
            'user_id' => $superAdmin->getKey(),
            'user_email' => $superAdmin->email,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 Chrome/120',
            'status' => 'SUCCESS',
            'reason' => 'Inicio de sesión exitoso por Super Admin',
        ]);

        UserLoginLog::query()->create([
            'user_id' => $lider->getKey(),
            'user_email' => $lider->email,
            'ip_address' => '192.168.1.45',
            'user_agent' => 'Mozilla/5.0 Edge/122',
            'status' => 'SUCCESS',
            'reason' => 'Inicio de sesión exitoso de Líder Ambiental',
        ]);

        UserLoginLog::query()->create([
            'user_id' => $lider->getKey(),
            'user_email' => $lider->email,
            'ip_address' => '190.85.12.99',
            'user_agent' => 'Mozilla/5.0 iPhone',
            'status' => 'BLOCKED',
            'reason' => 'Acceso bloqueado: Intento de conexión desde IP externa no autorizada (190.85.12.99)',
        ]);

        $trd1 = TrdStructure::query()->updateOrCreate(
            ['section_code' => '100'],
            [
                'section_name' => 'Despacho de la Dirección General',
                'version' => 'TRD-V3-2025',
                'is_active' => true,
                'is_deleted' => false,
                'sub_sections' => [
                    ['sub_section_id' => 'sec-110', 'sub_section_code' => '110', 'sub_section_name' => 'Oficina Asesora Jurídica'],
                    ['sub_section_id' => 'sec-120', 'sub_section_code' => '120', 'sub_section_name' => 'Oficina de Planeación y Control'],
                ],
                'series' => [
                    ['serie_id' => 'ser-actas', 'serie_code' => '110.10', 'serie_name' => 'Actas de Comité Directivo', 'retencion_gestion' => 3, 'retencion_central' => 15, 'disposicion_final' => 'CT'],
                    ['serie_id' => 'ser-contratos', 'serie_code' => '110.20', 'serie_name' => 'Contratos y Convenios', 'retencion_gestion' => 5, 'retencion_central' => 20, 'disposicion_final' => 'M'],
                    ['serie_id' => 'ser-conceptos', 'serie_code' => '110.30', 'serie_name' => 'Conceptos Jurídicos Especializados', 'retencion_gestion' => 2, 'retencion_central' => 10, 'disposicion_final' => 'S'],
                ],
                'sub_series' => [
                    ['sub_serie_id' => 'ss-actas', 'sub_serie_code' => '110.10.01', 'sub_serie_name' => 'Actas Ordinarias de Consejo Superior'],
                    ['sub_serie_id' => 'ss-obra', 'sub_serie_code' => '110.20.02', 'sub_serie_name' => 'Contratos de Obra Pública e Infraestructura'],
                    ['sub_serie_id' => 'ss-tutelas', 'sub_serie_code' => '110.30.01', 'sub_serie_name' => 'Conceptos de Tutelas y Demandas'],
                ],
            ],
        );

        $trd2 = TrdStructure::query()->updateOrCreate(
            ['section_code' => '200'],
            [
                'section_name' => 'Secretaría General y Administrativa',
                'version' => 'TRD-V3-2025',
                'is_active' => true,
                'is_deleted' => false,
                'sub_sections' => [
                    ['sub_section_id' => 'sec-210', 'sub_section_code' => '210', 'sub_section_name' => 'Subdirección de Talento Humano'],
                    ['sub_section_id' => 'sec-220', 'sub_section_code' => '220', 'sub_section_name' => 'Gestión Financiera y Contable'],
                ],
                'series' => [
                    ['serie_id' => 'ser-hl', 'serie_code' => '210.05', 'serie_name' => 'Historias Laborales', 'retencion_gestion' => 5, 'retencion_central' => 75, 'disposicion_final' => 'CT'],
                    ['serie_id' => 'ser-egresos', 'serie_code' => '220.15', 'serie_name' => 'Comprobantes de Egreso y Balances', 'retencion_gestion' => 3, 'retencion_central' => 10, 'disposicion_final' => 'E'],
                ],
                'sub_series' => [
                    ['sub_serie_id' => 'ss-hl', 'sub_serie_code' => '210.05.01', 'sub_serie_name' => 'Historias Laborales Personal Planta'],
                    ['sub_serie_id' => 'ss-dian', 'sub_serie_code' => '220.15.02', 'sub_serie_name' => 'Declaraciones Tributarias y Retenciones'],
                ],
            ],
        );

        $exp1 = Proceeding::query()->updateOrCreate(
            ['file_number' => 'EXP-2025-ACT-001'],
            [
                'trd_structure_id' => $trd1->getKey(),
                'section_code' => '100',
                'serie_id' => 'ser-actas',
                'sub_serie_id' => 'ss-actas',
                'serie_name' => 'Actas de Comité Directivo',
                'sub_serie_name' => 'Actas Ordinarias de Consejo Superior',
                'name' => 'Sesiones Ordinarias Consejo Superior 2025 - Primer Trimestre',
                'description' => 'Expediente integral con actas de deliberación, soportes normativos y acuerdos de rectoría.',
                'opening_date' => '2025-01-15',
                'deadline' => '2028-01-15',
                'state' => 'Público',
                'is_deleted' => false,
                'physical_location' => [
                    'deposit' => 'Depósito Central B',
                    'shelf' => 'Estante 04',
                    'module' => 'Módulo 2',
                    'box' => 'Caja 14',
                    'folder' => 'Carpeta 01-03',
                ],
                'sub_proceedings' => [
                    ['sub_proceeding_id' => 'sub-01', 'code' => 'SUB-01', 'name' => 'Sesión Inaugural Enero', 'creation_date' => '2025-01-15T09:00:00.000Z'],
                    ['sub_proceeding_id' => 'sub-02', 'code' => 'SUB-02', 'name' => 'Sesión Extraordinaria Presupuesto', 'creation_date' => '2025-02-10T14:30:00.000Z'],
                ],
            ],
        );

        Proceeding::query()->updateOrCreate(
            ['file_number' => 'EXP-2025-OBR-0042'],
            [
                'trd_structure_id' => $trd1->getKey(),
                'section_code' => '100',
                'serie_id' => 'ser-contratos',
                'sub_serie_id' => 'ss-obra',
                'serie_name' => 'Contratos y Convenios',
                'sub_serie_name' => 'Contratos de Obra Pública e Infraestructura',
                'name' => 'Licitación y Contrato LP-003-2025 Remodelación Centro de Cómputo',
                'description' => 'Expediente contractual que incluye pliegos, pólizas, actas de inicio y pagos de anticipo.',
                'opening_date' => '2025-02-01',
                'deadline' => '2030-02-01',
                'state' => 'Público',
                'is_deleted' => false,
                'physical_location' => [
                    'deposit' => 'Depósito Central A',
                    'shelf' => 'Estante 12',
                    'module' => 'Módulo 1',
                    'box' => 'Caja 55',
                    'folder' => 'Carpeta 08',
                ],
                'sub_proceedings' => [],
            ],
        );

        Proceeding::query()->updateOrCreate(
            ['file_number' => 'EXP-HL-1098-4421'],
            [
                'trd_structure_id' => $trd2->getKey(),
                'section_code' => '200',
                'serie_id' => 'ser-hl',
                'sub_serie_id' => 'ss-hl',
                'serie_name' => 'Historias Laborales',
                'sub_serie_name' => 'Historias Laborales Personal Planta',
                'name' => 'Historia Laboral Funcionario: Ing. Carlos Alberto Sarmiento',
                'description' => 'Documentación reservada: hoja de vida, exámenes médicos ocupacionales y resoluciones de nombramiento.',
                'opening_date' => '2024-06-01',
                'deadline' => '2099-06-01',
                'state' => 'Reservado',
                'is_deleted' => false,
                'physical_location' => [
                    'deposit' => 'Depósito Reservado TH',
                    'shelf' => 'Estante 01',
                    'module' => 'Módulo R',
                    'box' => 'Caja HL-09',
                    'folder' => 'Carpeta 4421',
                ],
                'sub_proceedings' => [],
            ],
        );

        ArchiveDocument::query()->updateOrCreate(
            ['name' => 'Acta 001 - Sesión Inaugural 2025'],
            [
                'proceedings_id' => $exp1->getKey(),
                'sub_proceeding_id' => 'sub-01',
                'document_type' => 'Acta',
                'description' => 'Acta de instalación del Consejo Superior para la vigencia 2025.',
                'document_creation_date' => '2025-01-15',
                'support' => 'Electrónico',
                'file_path' => null,
                'file_metadata' => [
                    'size_bytes' => 245760,
                    'mime_type' => 'application/pdf',
                    'original_name' => 'acta-001-2025.pdf',
                    'hash_sha256' => hash('sha256', 'acta-001-demo'),
                    'uuid' => '11111111-1111-4111-8111-111111111111',
                ],
                'state' => 'Cerrado',
                'is_deleted' => false,
            ],
        );
    }

    private function user(string $name, string $email, string $password, string $role, string $ip, string $range): User
    {
        return User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => $password,
                'role' => $role,
                'roles' => [$role],
                'permissions' => RoleCatalog::forRole($role),
                'current_ip' => $ip,
                'allowed_ip_range' => $range,
                'last_login_at' => now(),
                'is_active' => true,
                'force_logout' => false,
                'is_deleted' => false,
            ],
        );
    }
}
