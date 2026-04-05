<?php

use php_rutils\RUtils;
use php_rutils\TypoRules;

require_once __DIR__ . '/../../vendor/autoload.php';

define('CLI', php_sapi_name() === 'cli');
mb_internal_encoding('UTF-8');

if (!CLI) {
    header('Content-type: text/plain; charset=UTF-8');
}

$text = <<<TEXT
...Когда В. И. Пупкин увидел в газете ( это была "Сермяжная правда" № 45) рубрику Weather Forecast (r),
он не поверил своим глазам - температуру обещали +-451F.
TEXT;

//Standard rules
echo RUtils::typo()->typography($text), PHP_EOL;
/**
 * Result:
 * ...Когда В. И. Пупкин увидел в газете (это была «Сермяжная правда» №45) рубрику Weather Forecast®,
 * он не поверил своим глазам — температуру обещали ±451°F.
 */


//Extended rules
echo RUtils::typo()->typography($text, TypoRules::$EXTENDED_RULES), PHP_EOL;
/**
 * Result:
 * …Когда В. И. Пупкин увидел в газете (это была «Сермяжная правда» №45) рубрику Weather Forecast®,
 * он не поверил своим глазам — температуру обещали ±451 °F.
 */

//Custom rules
echo RUtils::typo()->typography($text, [TypoRules::DASHES, TypoRules::CLEAN_SPACES]), PHP_EOL;
/**
 * Result:
 * ...Когда В. И. Пупкин увидел в газете (это была "Сермяжная правда" № 45) рубрику Weather Forecast (r),
 * он не поверил своим глазам — температуру обещали +-451F.
 */
