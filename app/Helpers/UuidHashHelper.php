<?php

namespace App\Helpers;

use Hashids\Hashids;

class UuidHashHelper
{
    public static function encodeUuid(string $uuid): string
    {
        $hashids = new Hashids(config('app.key'), 8);
        return $hashids->encodeHex(str_replace('-', '', $uuid));
    }

    public static function decodeUuid(string $hash): string
    {
        $hashids = new Hashids(config('app.key'), 8);
        return substr_replace($hashids->decodeHex($hash), '-', 8, 0);
    }
}
