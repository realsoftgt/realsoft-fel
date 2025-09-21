<?php

namespace RealSoft\FEL\Facades;

use Illuminate\Support\Facades\Facade;

class FEL extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \RealSoft\FEL\FELManager::class;
    }
}
