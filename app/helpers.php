<?php

use Ramsey\Uuid\Uuid;

if (!function_exists('uuid7')) {
    function uuid7()
    {
        return Uuid::uuid7()->toString();
    }
    if (!function_exists('active_role')) {
    function active_role()
    {
        return session('active_role');
    }
}

}
