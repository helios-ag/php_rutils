<?php

namespace php_rutils;

/**
 * Simple transliteration
 * Class Translit
 */
class Translit
{
    private static array $_TRANSLATION_TABLE = [
        //Non-alphabet symbols
        ['‘', "'"],
        ['’', "'"],
        ['«', '"'],
        ['»', '"'],
        ['“', '"'],
        ['”', '"'],
        ['№', '#'],
        //Alphabet (ISO9 [ГОСТ 7.79—2000], Scheme B)
        //3-symbolic
        ['Щ', 'Shh'],
        ['щ', 'shh'],
        //2-symbolic
        ['Ё', 'Yo'],
        ['ё', 'yo'],
        ['Ж', 'Zh'],
        ['ж', 'zh'],
        ['Ц', 'Cz'],
        ['ц', 'cz'],
        ['Ч', 'Ch'],
        ['ч', 'ch'],
        ['Ш', 'Sh'],
        ['ш', 'sh'],
        ['ъ', '``'],
        ['Ъ', '``'],
        ['Ы', 'Y`'],
        ['ы', 'y`'],
        ['Э', 'E`'],
        ['э', 'e`'],
        ['Ю', 'Yu'],
        ['ю', 'yu'],
        ['Я', 'Ya'],
        ['я', 'ya'],
        //1-symbolic
        ['А', 'A'],
        ['а', 'a'],
        ['Б', 'B'],
        ['б', 'b'],
        ['В', 'V'],
        ['в', 'v'],
        ['Г', 'G'],
        ['г', 'g'],
        ['Д', 'D'],
        ['д', 'd'],
        ['Е', 'E'],
        ['е', 'e'],
        ['З', 'Z'],
        ['з', 'z'],
        ['И', 'I'],
        ['и', 'i'],
        ['Й', 'J'],
        ['й', 'j'],
        ['К', 'K'],
        ['к', 'k'],
        ['Л', 'L'],
        ['л', 'l'],
        ['М', 'M'],
        ['м', 'm'],
        ['Н', 'N'],
        ['н', 'n'],
        ['О', 'O'],
        ['о', 'o'],
        ['П', 'P'],
        ['п', 'p'],
        ['Р', 'R'],
        ['р', 'r'],
        ['С', 'S'],
        ['с', 's'],
        ['Т', 'T'],
        ['т', 't'],
        ['У', 'U'],
        ['у', 'u'],
        ['Ф', 'F'],
        ['ф', 'f'],
        ['Х', 'X'],
        ['х', 'x'],
        ['ь', '`'],
        ['Ь', '`'],
    ];  //Translation table

    private static array $_RU_ALPHABET = [];
    private static array $_EN_ALPHABET = [];

    private static array $_CORRECTION_PATTERN = ['#(\w)«#u', '#(\w)“#u', '#(\w)‘#u'];
    private static array $_CORRECTION_REPLACEMENT = ['$1»', '$1”', '$1’'];

    /**
     * "Constructor" for class variables
     */
    public static function staticConstructor(): void
    {
        self::$_RU_ALPHABET = [];
        self::$_EN_ALPHABET = [];

        foreach (self::$_TRANSLATION_TABLE as $pair) {
            self::$_RU_ALPHABET[] = $pair[0];
            self::$_EN_ALPHABET[] = $pair[1];
        }
    }

    /** Translify russian text */
    public function translify(string $inString): string
    {
        return str_replace(self::$_RU_ALPHABET, self::$_EN_ALPHABET, $inString);
    }

    /** Detranslify */
    public function detranslify(string $inString): string
    {
        $dirtyResult = str_replace(self::$_EN_ALPHABET, self::$_RU_ALPHABET, $inString);
        return preg_replace(self::$_CORRECTION_PATTERN, self::$_CORRECTION_REPLACEMENT, $dirtyResult) ?? $dirtyResult;
    }

    /** Prepare string for slug (i.e. URL or file/dir name) */
    public function slugify(string $inString): string
    {
        //convert & to "and"
        $inString = preg_replace('/(?:&amp;)|&/u', ' and ', $inString);

        //replace spaces
        $inString = preg_replace('/[—−\-\s\t]+/u', '-', $inString);

        $translitString = strtolower($this->translify($inString));
        return preg_replace('/[^a-z0-9_-]+/i', '', $translitString) ?? '';
    }
}

Translit::staticConstructor();
