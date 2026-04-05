PHP RUtils
----------

## Summary

[![License](https://poser.pugx.org/helios-ag/php_rutils/license)](https://packagist.org/packages/helios-ag/php_rutils)
[![Latest Stable Version](https://poser.pugx.org/helios-ag/php_rutils/v/stable)](https://packagist.org/packages/helios-ag/php_rutils)

[![Build Status](https://github.com/helios-ag/php_rutils/actions/workflows/tests.yml/badge.svg)](https://github.com/helios-ag/php_rutils/actions/workflows/tests.yml)

RUtils is a Russian-specific string utils (transliteration, numeral is words, russian dates, typography) for PHP.
This is a port of the Python [Pytils](https://github.com/j2a/pytils) to PHP.

See additional docs and examples in [doc subdir](https://github.com/helios-ag/php_rutils/tree/main/doc).

Library is published on the Composer: https://packagist.org/packages/helios-ag/php_rutils

Requires PHP 8.3+.

----------

RUtils — утилиты для работы c текстом на русском языке (транслитерация, числительные словами, русские даты,
простая типографика) для языка PHP.
RUtils — порт утилит [Pytils](https://github.com/j2a/pytils) на PHP.

Документацию и примеры смотрите в [каталоге doc](https://github.com/helios-ag/php_rutils/tree/main/doc).

Библиотека доступна через Composer: https://packagist.org/packages/helios-ag/php_rutils

[![Daily Downloads](https://poser.pugx.org/helios-ag/php_rutils/d/daily)](https://packagist.org/packages/helios-ag/php_rutils)
[![Monthly Downloads](https://poser.pugx.org/helios-ag/php_rutils/d/monthly)](https://packagist.org/packages/helios-ag/php_rutils)
[![Total Downloads](https://poser.pugx.org/helios-ag/php_rutils/downloads)](https://packagist.org/packages/helios-ag/php_rutils)

----------

Буду рад принять помощь по проекту в виде советов, баг-репортов и pull-реквестов.

Проект использует PSR-4 autoloading, [PHP CS Fixer](https://github.com/PHP-CS-Fixer/PHP-CS-Fixer) и [Psalm](https://psalm.dev/).
Проверка стиля и статический анализ запускаются в CI и доступны локально через `Makefile`.

### Development

- Local dev environment: `docker compose up -d` or `make up`
- Shell in container: `make shell`
- Tests: `make test`
- Fix code style: `make cs-fix`
- Static analysis: `make psalm`
- VS Code / OpenCode devcontainer: `.devcontainer/devcontainer.json`
- Doc examples in `doc/examples/` run standalone.

Хочу сказать большое спасибо [всем людям](https://github.com/helios-ag/php_rutils/graphs/contributors),
внесшим свой вклад в улучшение проекта.

----------

Basic usage
-----------

Modules of PHP RUtils:
 - Numeral - Plural forms and in-word representation for numerals
 - Dt - Russian dates without locales and other dates handling
 - Translit - Simple transliteration
 - Typo - Basic russian typography


**Numeral**

Choosing the word form depending of a number:
```php
$variants = [
    'гвоздь', //1
    'гвоздя', //2
    'гвоздей' //5
];
echo $amount, ' ', RUtils::numeral()->choosePlural(15, $variants);
//Result: 15 гвоздей

echo RUtils::numeral()->getPlural(2, $variants);
//Result: 2 гвоздя
```

Choosing the word form and print number in words:
```php
echo RUtils::numeral()->sumString(1234, RUtils::MALE, $variants);
//Result: одна тысяча двести тридцать четыре гвоздя
```

Print number in words:
```php
$numeral = RUtils::numeral();
echo $numeral->getInWordsInt(100);
//Result: сто

echo $numeral->getInWordsFloat(100.025);
//Result: сто целых двадцать пять тысячных

echo $numeral->getInWords(100.0);
//Result: сто
```

Print money (RUB):
```php
echo RUtils::numeral()->getRubles(100.25);
//Result: сто рублей двадцать пять копеек
```


**Dt**

Today date:
`\php_rutils\struct\TimeParams` - params structure, may be passed as array
```php
$params = new TimeParams();
$params->date = null; //default value, 'now'
$params->format = 'сегодня d F Y года';
$params->monthInflected = true;
echo RUtils::dt()->ruStrFTime($params);
//Result: сегодня 22 октября 2013 года
```


Historical date:
```php
$params = [
    'date' => '09-05-1945',
    'format' => 'l d F Y была одержана победа над немецко-фашистскими захватчиками',
    'monthInflected' => true,
    'preposition' => true,
];
echo RUtils::dt()->ruStrFTime($params);
//Result: в среду 9 мая 1945 была одержана победа над немецко-фашистскими захватчиками
```

Time interval to fixed date:
```php
$toTime = new \DateTime('05-06-1945'); //Unix timestamp and string also available
echo RUtils::dt()->distanceOfTimeInWords($toTime), PHP_EOL;
//Result: 68 лет назад

$toTime = strtotime('05-06-1945');
$fromTime = null; //now
$accuracy = RUtils::ACCURACY_MINUTE; //years, months, days, hours, minutes
echo RUtils::dt()->distanceOfTimeInWords($toTime, $fromTime, $accuracy), PHP_EOL;
//Result: 68 лет, 4 месяца, 21 день, 19 часов, 12 минут назад
```


Time interval from fixed date to fixed date:
```php
$fromTime = '1988-01-01 11:40';
$toTime = '2088-01-01 12:35';
$accuracy = RUtils::ACCURACY_MINUTE; //years, months, days, hours, minutes
echo RUtils::dt()->distanceOfTimeInWords($toTime, $fromTime, $accuracy), PHP_EOL;
//Result: через 100 лет, 55 минут
```

Age:
```php
$birthDate = strtotime('today - 25 years');
echo RUtils::dt()->getAge($birthDate);
//Result: 25
```


**Translit**
```php
//Translify
echo RUtils::translit()->translify('Муха - это маленькая птичка');
//Result: Muxa - e`to malen`kaya ptichka

//Detranslify
echo RUtils::translit()->detranslify("Muxa - e`to malen`kaya ptichka");
//Result: Муха - это маленькая птичка

//Prepare to use in URLs or file paths
echo RUtils::translit()->slugify('Муха — это маленькая птичка');
//Result: muha-eto-malenkaya-ptichka
```


**Typo**
```php
$text = <<<TEXT
...Когда В. И. Пупкин увидел в газете ( это была "Сермяжная правда" № 45) рубрику Weather Forecast (r),
он не поверил своим глазам - температуру обещали +-451F.
TEXT;

//Standard rules
echo RUtils::typo()->typography($text);
/**
 * Result:
 * ...Когда В. И. Пупкин увидел в газете (это была «Сермяжная правда» №45) рубрику Weather Forecast®,
 * он не поверил своим глазам — температуру обещали ±451°F.
 */


//Extended rules
echo RUtils::typo()->typography($text, TypoRules::$EXTENDED_RULES);
/**
 * Result:
 * …Когда В. И. Пупкин увидел в газете (это была «Сермяжная правда» №45) рубрику Weather Forecast®,
 * он не поверил своим глазам — температуру обещали ±451 °F.
 */

//Custom rules
echo RUtils::typo()->typography($text, [TypoRules::DASHES, TypoRules::CLEAN_SPACES]);
/**
 * Result:
 * ...Когда В. И. Пупкин увидел в газете (это была "Сермяжная правда" № 45) рубрику Weather Forecast (r),
 * он не поверил своим глазам — температуру обещали +-451F.
 */
```
