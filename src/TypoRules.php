<?php

namespace php_rutils;

/** Rules for Typo::typography */
class TypoRules
{
    //Clean double spaces, trailing spaces, heading spaces,
    public const CLEAN_SPACES = 'CleanSpaces';

    //Replace three dots to ellipsis
    public const ELLIPSIS = 'Ellipsis';

    //Replace space between initials and surname by thin space
    public const INITIALS = 'Initials';

    //Replace dash to long/medium dashes
    public const DASHES = 'Dashes';

    //Glue (set nonbreakable space) short words with word before/after
    public const WORD_GLUE = 'WordGlue';

    //Replace +-, (c), (tm), (r), (p), etc by its typographic equivalents
    public const MARKS = 'Marks';

    //Replace quotes by typographic quotes
    public const QUOTES = 'Quotes';

    //Standard rules: quotes, marks, dashes, clean spaces
    public static array $STANDARD_RULES = [self::QUOTES, self::MARKS, self::DASHES, self::CLEAN_SPACES];

    //Standard rules: quotes, marks, word glue, dashes, initials, ellipsis, clean spaces
    public static array $EXTENDED_RULES = [
        self::DASHES,
        self::QUOTES,
        self::MARKS,
        self::ELLIPSIS,
        self::CLEAN_SPACES,
        self::INITIALS,
        self::WORD_GLUE,
    ];
}
