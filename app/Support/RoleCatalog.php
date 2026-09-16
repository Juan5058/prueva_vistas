<?php

namespace App\Support;

class RoleCatalog
{
    public const SUPER_ADMIN = 'super_admin';

    public const LIDER_AMBIENTAL = 'lider_ambiental';

    public const APRENDIZ = 'aprendiz';

    public const PERMISSIONS = [
        'users.view',
        'users.create',
        'users.edit',
        'users.force_logout',
        'users.delete',
        'trd.view',
        'trd.create',
        'trd.edit',
        'trd.import',
        'trd.delete',
        'proceedings.view',
        'proceedings.create',
        'proceedings.edit',
        'proceedings.delete',
        'documents.view',
        'documents.upload',
        'documents.download',
        'documents.delete',
        'reports.view',
        'reports.download-pdf',
        'security.view_sessions',
        'security.force_logout',
    ];

    /**
     * @return array<string, string>
     */
    public static function labels(): array
    {
        return [
            self::SUPER_ADMIN => 'Super Admin',
            self::LIDER_AMBIENTAL => 'Líder Ambiental',
            self::APRENDIZ => 'Aprendiz',
        ];
    }

    public static function label(string $role): string
    {
        return self::labels()[$role] ?? $role;
    }

    /**
     * @return array<string, list<string>>
     */
    public static function defaults(): array
    {
        return [
            self::SUPER_ADMIN => self::PERMISSIONS,
            self::LIDER_AMBIENTAL => [
                'trd.view', 'trd.create', 'trd.edit', 'trd.import',
                'proceedings.view', 'proceedings.create', 'proceedings.edit',
                'documents.view', 'documents.upload', 'documents.download',
                'reports.view', 'reports.download-pdf',
            ],
            self::APRENDIZ => [
                'trd.view', 'proceedings.view', 'documents.view', 'documents.download',
                'reports.view', 'reports.download-pdf', 'security.view_sessions',
            ],
        ];
    }

    /**
     * @return list<string>
     */
    public static function forRole(string $role): array
    {
        return self::defaults()[$role] ?? self::defaults()[self::APRENDIZ];
    }

    /**
     * @return array<string, string>
     */
    public static function dispositionLabels(): array
    {
        return [
            'CT' => 'Conservación Total',
            'E' => 'Eliminación',
            'M' => 'Digitalización / Medio',
            'S' => 'Selección',
        ];
    }
}
