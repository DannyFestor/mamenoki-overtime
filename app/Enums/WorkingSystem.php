<?php

namespace App\Enums;

enum WorkingSystem: int
{
    case MONTHLY = 1;
    case HOURLY = 2;

    public static function toArray(): array
    {
        $cases = [];
        foreach (self::cases() as $case) {
            $cases[$case->value] = $case->toString();
        }

        return $cases;
    }

    public function toString(): string
    {
        return match ($this) {
            self::MONTHLY => '月給',
            self::HOURLY => '時給',
        };
    }
}
