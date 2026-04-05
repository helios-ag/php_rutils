<?php

namespace php_rutils;

class RUtils
{
    //gender constants
    public const MALE = 1;
    public const FEMALE = 2;
    public const NEUTER = 3;

    //accuracy for Dt::distanceOfTimeInWords function
    public const ACCURACY_YEAR = 1;
    public const ACCURACY_MONTH = 2;
    public const ACCURACY_DAY = 3;
    public const ACCURACY_HOUR = 4;
    public const ACCURACY_MINUTE = 5;

    private static ?Numeral $_numeral = null;
    private static ?Dt $_dt = null;
    private static ?Translit $_translit = null;
    private static ?Typo $_typo = null;

    /** Plural forms and in-word representation for numerals */
    public static function numeral(): Numeral
    {
        if (self::$_numeral === null) {
            self::$_numeral = new Numeral();
        }
        return self::$_numeral;
    }

    /** Russian dates without locales */
    public static function dt(): Dt
    {
        if (self::$_dt === null) {
            self::$_dt = new Dt();
        }
        return self::$_dt;
    }

    /** Simple transliteration */
    public static function translit(): Translit
    {
        if (self::$_translit === null) {
            self::$_translit = new Translit();
        }
        return self::$_translit;
    }

    /** Russian typography */
    public static function typo(): Typo
    {
        if (self::$_typo === null) {
            self::$_typo = new Typo();
        }
        return self::$_typo;
    }

    /** Format number with russian locale */
    public static function formatNumber(int|float $number, int $decimals = 0): string
    {
        $number = number_format($number, $decimals, ',', ' ');
        return str_replace(' ', "\xE2\x80\x89", $number);
    }
}
