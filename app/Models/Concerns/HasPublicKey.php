<?php

namespace App\Models\Concerns;

trait HasPublicKey
{
    public function getRouteKey(): mixed
    {
        return $this->getKey();
    }
}
