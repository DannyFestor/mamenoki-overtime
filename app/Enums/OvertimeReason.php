<?php

namespace App\Enums;

enum OvertimeReason: int
{
    case EXTENDED_CARE = 1;
    case TRAINING = 2;
    case MEETING = 3;
    case PARENTAL_SUPPORT = 4;
    case OFFICE_WORK = 5;
    case ELSE = 6;
    case HOLIDAY_EVENT = 7;
    case HOLIDAY_TRAINING = 8;
    case HOLIDAY_PARENTAL_SUPPORT = 9;
    case HOLIDAY_OFFICE_WORK = 10;
    case HOLIDAY_ELSE = 11;

    public static function toArray(): array
    {
        $cases = [];
        foreach(self::cases() as $case) {
            $cases[$case->value] = $case->toString();
        }

        return $cases;
    }

    public function toString(): string
    {
        return match($this) {
            self::EXTENDED_CARE => '時間外勤務（延長保育のため）',
            self::TRAINING => '時間外勤務（研修参加のため）',
            self::MEETING => '時間外勤務（会議等参加のため）',
            self::PARENTAL_SUPPORT => '時間外勤務（保護者対応のため）',
            self::OFFICE_WORK => '時間外勤務（事務のため）',
            self::ELSE => '時間外勤務（その他）',
            self::HOLIDAY_EVENT => '休日出勤（行事参加のため）',
            self::HOLIDAY_TRAINING => '休日出勤（研修のため）',
            self::HOLIDAY_PARENTAL_SUPPORT => '休日出勤（保護者対応のため）',
            self::HOLIDAY_OFFICE_WORK => '休日出勤（事務のため）',
            self::HOLIDAY_ELSE => '休日出勤（その他）',
        };
    }
}
