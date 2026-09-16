<?php

namespace Tests;

use App\Models\Document;
use App\Models\DocumentAuditLog;
use App\Models\Proceeding;
use App\Models\TrdImport;
use App\Models\TrdStructure;
use App\Models\User;
use App\Models\UserLoginLog;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Support\Facades\DB;

abstract class MongoTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(PreventRequestForgery::class);

        if (! extension_loaded('mongodb')) {
            $this->markTestSkipped('Requiere la extensión PHP mongodb (use docker compose).');
        }

        config([
            'database.default' => 'mongodb',
            'database.connections.mongodb.database' => 'trd_documental_test',
            'queue.default' => 'sync',
            'cache.default' => 'array',
            'session.driver' => 'array',
        ]);

        DB::purge('mongodb');

        $this->flushMongo();
    }

    protected function flushMongo(): void
    {
        foreach ([
            (new User)->getTable(),
            (new UserLoginLog)->getTable(),
            (new TrdStructure)->getTable(),
            (new Proceeding)->getTable(),
            (new Document)->getTable(),
            (new DocumentAuditLog)->getTable(),
            (new TrdImport)->getTable(),
            'logical_stubs',
        ] as $collection) {
            try {
                DB::connection('mongodb')->getCollection($collection)->drop();
            } catch (\Throwable) {
                // Colección aún no existe.
            }
        }
    }
}
