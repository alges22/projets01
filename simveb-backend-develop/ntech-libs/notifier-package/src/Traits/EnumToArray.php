<?php

namespace Ntech\NotifierPackage\Traits;

trait EnumToArray
{
    public static function toArray(): array {
        return array_map(
            fn(self $enum) => $enum->name,
            self::cases()
        );
    }
}
