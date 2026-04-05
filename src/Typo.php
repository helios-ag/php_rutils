<?php

namespace php_rutils;

/**
 * Russian typography
 * Class Typo
 */
class Typo
{
    //CLEAN SPACES RULE
    // arguments for preg_replace: pattern and replacement
    private static array $_CLEAN_SPACES_TABLE = [
        //remove spaces before punctuation marks
        ['#\s+([\.,?!:;\)]+)#u', '$1'],
        //add spaces after punctuation marks
        ['#([^\.][\.,?!:;\)]+)([^\.!,?\)]+)#u', '$1 $2'],
        //remove spaces after opening bracket
        ['#(\S+)\s*(\()\s+(\S+)#u', '$1 ($3'],
        //remove heading spaces
        ['#^\s+#um', ''],
        //remove trailing spaces
        ['#\s+$#um', ''],
        //remove double spaces
        ['#[ ]+#um', ' '],
    ];

    private static array $_CLEAN_SPACES_PATTERN = [];
    private static array $_CLEAN_SPACES_REPLACEMENT = [];

    //ELLIPSIS RULE
    private static array $_ELLIPSIS_PATTERN = [
        '#([^\.]|^)\.\.\.([^\.]|$)#u',
        '#(^|"|“|«)\s*…\s*([[:alpha:]])#ui'
    ];
    private static string $_ELLIPSIS_REPLACEMENT = '$1…$2';

    //DASHES RULE
    private static array $_DASHES_PATTERN = [
        //dash in the beginning of the sentence
        '#(^|(?:[\.\?!…]\s*))--?\s*(.|$)#u',
        //dash between words
        '#([[:alpha:]])(?:\s+--?\s+)|(?:--)(.|$)#u',
        //dash in range of numbers
        '#(\d)\s*--?\s*(\d)#u',
        '#([+-]?\d)\s*--?\s*([+-]?\d)#u'
    ];
    private static array $_DASHES_REPLACEMENT = [
        "$1—\xE2\x80\x89$2",
        "$1\xE2\x80\x89— $2",
        '$1—$2',
        '$1…$2',
    ];

    //WORD GLUE RULE
    private static array $_GLUE_PATTERN = [
        //particles
        '#(\S)\s+(же|ли|ль|бы|б|ж|ка)([\s\.,!\?:;…]*)#u',
        //short words
        '#([[:^alpha:]][[:alpha:]]{1,3})\s+(\S)#u',
        '#^([[:alpha:]]{1,3})\s+(\S)#u',
        //dashes
        '#(\s+)([—-]+)(\s+)#u',
    ];
    private static array $_GLUE_REPLACEMENT = [
        "$1\xC2\xA0$2$3",
        "$1\xC2\xA0$2",
        "$1\xC2\xA0$2",
        "\xE2\x80\x89$2$3",
    ];

    //MARKS RULE
    private static array $_MARKS_TABLE = [
        ['#((?:-|\+)?\d+)\s*([fc]\W)#ui', "$1\xE2\x80\x89°$2"],
        ['#\(c\)#ui', '©'],
        ['#\(r\)#ui', '®'],
        ['#\(p\)#ui', '§'],
        ['#\(tm\)#ui', '™'],
        ['#(©)\s*(\d+)#u', "$1\xE2\x80\x89$2"],
        ['#([^+])((?:\+-)|(?:-\+))#u', '$1±'],
        ['#(\w)\s+(®|™)#u', '$1$2'],
        ['#\s(no|№)\s*(\d+)#ui', "\xC2\xA0№\xE2\x80\x89$2"],
    ];

    private static array $_MARKS_PATTERN = [];
    private static array $_MARKS_REPLACEMENT = [];

    //QUOTES RULE
    private static array $_QUOTES_PATTERN = [
        '#(^|\s)(")(\w)#u',
        '#(\w)(")([\s,;:?!\.]|$)#u',
        '#(^|\s)(\')(\w)#u',
        '#(\w)(\')([\s,;:?!\.]|$)#u',
    ];
    private static array $_QUOTES_REPLACEMENT = ['$1«$3', '$1»$3', '$1“$3', '$1”$3'];

    /**
     * "Constructor" for class variables
     */
    public static function staticConstructor(): void
    {
        self::$_CLEAN_SPACES_PATTERN = [];
        self::$_CLEAN_SPACES_REPLACEMENT = [];

        foreach (self::$_CLEAN_SPACES_TABLE as $pair) {
            self::$_CLEAN_SPACES_PATTERN[] = $pair[0];
            self::$_CLEAN_SPACES_REPLACEMENT[] = $pair[1];
        }

        self::$_MARKS_PATTERN = [];
        self::$_MARKS_REPLACEMENT = [];

        foreach (self::$_MARKS_TABLE as $pair) {
            self::$_MARKS_PATTERN[] = $pair[0];
            self::$_MARKS_REPLACEMENT[] = $pair[1];
        }
    }

    /** Clean double spaces, trailing spaces, heading spaces, spaces before punctuations */
    public function rlCleanSpaces(string $text): string
    {
        return preg_replace(self::$_CLEAN_SPACES_PATTERN, self::$_CLEAN_SPACES_REPLACEMENT, $text) ?? $text;
    }

    /** Replace three dots to ellipsis */
    public function rlEllipsis(string $text): string
    {
        return preg_replace(self::$_ELLIPSIS_PATTERN, self::$_ELLIPSIS_REPLACEMENT, $text) ?? $text;
    }

    /** Replace space between initials and surname by thin space */
    public function rlInitials(string $text): string
    {
        return preg_replace(
            '#([А-Я])\.\s*([А-Я])\.\s*([А-Я][а-я]+)#u',
            "$1.\xE2\x80\x89$2.\xE2\x80\x89$3",
            $text
        ) ?? $text;
    }

    /** Replace dash to long/medium dashes */
    public function rlDashes(string $text): string
    {
        return preg_replace(self::$_DASHES_PATTERN, self::$_DASHES_REPLACEMENT, $text) ?? $text;
    }

    /** Glue (set nonbreakable space) short words with word before/after */
    public function rlWordGlue(string $text): string
    {
        return preg_replace(self::$_GLUE_PATTERN, self::$_GLUE_REPLACEMENT, $text) ?? $text;
    }

    /** Replace +-, (c), (tm), (r), (p), etc by its typographic equivalents */
    public function rlMarks(string $text): string
    {
        return preg_replace(self::$_MARKS_PATTERN, self::$_MARKS_REPLACEMENT, $text) ?? $text;
    }

    /** Replace quotes by typographic quotes */
    public static function rlQuotes(string $text): string
    {
        return preg_replace(self::$_QUOTES_PATTERN, self::$_QUOTES_REPLACEMENT, $text) ?? $text;
    }

    /** Typography applier */
    public function typography(string $text, ?array $rules = null): string
    {
        if ($rules === null) {
            $rules = TypoRules::$STANDARD_RULES;
        }
        if (array_diff($rules, TypoRules::$EXTENDED_RULES)) {
            throw new \InvalidArgumentException('Invalid typo rules');
        }

        foreach ($rules as $rule) {
            $funcName = 'rl' . $rule;
            $text = call_user_func([$this, $funcName], $text);
        }
        return $text;
    }
}

Typo::staticConstructor();
