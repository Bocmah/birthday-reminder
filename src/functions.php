<?php

declare(strict_types=1);

if (!function_exists('mb_ucfirst')) {
    function mb_ucfirst(string $string, ?string $encoding = null): string
    {
        $firstChar = mb_substr($string, 0, 1, $encoding);
        $then = mb_substr($string, 1, null, $encoding);

        return mb_strtoupper($firstChar, $encoding) . $then;
    }
}
