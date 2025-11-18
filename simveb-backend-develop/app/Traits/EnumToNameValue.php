<?php

namespace App\Traits;

trait EnumToNameValue
{
    public static function toNameValue(): array
    {
        $map = [];
        foreach (self::cases() as $case) {
            $map[$case->name] = $case->value;
        }
        return $map;
    }

    public static function toNameValueWithKey(): array
    {
        $map = [];
        foreach (self::cases() as $case) {
            $map[] = ['value' => $case->value, 'key' => $case->name];
        }
        return $map;
    }
}
