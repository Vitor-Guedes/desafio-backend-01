<?php

if (! function_exists('converToCents')) {
    function convertToCents(float $value): int {
        return $value * 100;
    }
}