<?php
namespace App;

class NumberHelper
{
    public static function price(float $number, string $symbol = "€"): string
    {
        return number_format($number, 0, '', ' ') . ' ' . $symbol;
    }
}
