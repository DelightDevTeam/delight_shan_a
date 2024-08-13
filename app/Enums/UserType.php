<?php

namespace App\Enums;

enum UserType: int
{
    case Admin = 10;
    case Senior = 20;
    case Master = 30;
    case Agent = 40;
    case Player = 50;

    public static function usernameLength(UserType $type)
    {
        return match ($type) {
            self::Admin => 1,
            self::Senior => 2,
            self::Master => 3,
            self::Agent => 4,
            self::Player => 5
        };
    }

    public static function childUserType(UserType $type)
    {
        return match ($type) {
            self::Admin => self::Senior,
            self::Senior => self::Master,
            self::Master => self::Agent,
            self::Agent => self::Player,
            self::Player => self::Player
        };
    }
}
