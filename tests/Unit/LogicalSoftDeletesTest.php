<?php

namespace Tests\Unit;

use App\Models\Concerns\LogicalSoftDeletes;
use MongoDB\Laravel\Eloquent\Model;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use Tests\MongoTestCase;

class LogicalSoftDeletesTest extends MongoTestCase
{
    #[Test]
    public function delete_fisico_esta_prohibido(): void
    {
        $model = LogicalStub::query()->create(['name' => 'TRD']);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('CRITICAL_POLICY_VIOLATION');

        $model->delete();
    }

    #[Test]
    public function soft_delete_marca_is_deleted_y_oculta_el_registro(): void
    {
        $model = LogicalStub::query()->create(['name' => 'Expediente']);
        $model->softDelete();

        $this->assertTrue($model->fresh()?->trashed() ?? $model->trashed());
        $this->assertTrue((bool) LogicalStub::withTrashed()->find($model->getKey())?->is_deleted);
        $this->assertNull(LogicalStub::query()->find($model->getKey()));
        $this->assertSame(1, LogicalStub::onlyTrashed()->count());
    }
}

class LogicalStub extends Model
{
    use LogicalSoftDeletes;

    protected $collection = 'logical_stubs';

    protected $guarded = [];
}
