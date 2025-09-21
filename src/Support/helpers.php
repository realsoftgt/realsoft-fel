<?php
namespace RealSoft\FEL\Support;

function array_get(array $array, string $key, $default=null) {
    return \Illuminate\Support\Arr::get($array, $key, $default);
}
