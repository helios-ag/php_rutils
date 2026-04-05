<?php

use php_rutils\RUtils;

require_once __DIR__ . '/../../vendor/autoload.php';

define('CLI', php_sapi_name() === 'cli');
mb_internal_encoding('UTF-8');

if (!CLI) {
    header('Content-type: text/plain; charset=UTF-8');
}

//Word forms
$variants = [
    'гвоздь', //1
    'гвоздя', //2
    'гвоздей' //5
];

//Choose plural (variant only)
$amount = 15;
echo $amount, ' ', RUtils::numeral()->choosePlural($amount, $variants), PHP_EOL;
//Result: 15 гвоздей


//Get plural (amount and variant):
$amount = 2;
echo RUtils::numeral()->getPlural($amount, $variants), PHP_EOL;
//Result: 2 гвоздя


//Sum string in words
$amount = 1234;
$gender = RUtils::MALE;
echo RUtils::numeral()->sumString($amount, $gender, $variants), PHP_EOL;
//Result: одна тысяча двести тридцать четыре гвоздя


//Numeral in words
$numeral = RUtils::numeral();
echo $numeral->getInWordsInt(100), PHP_EOL;
//Result: сто
echo $numeral->getInWordsFloat(100.025), PHP_EOL;
//Result: сто целых двадцать пять тысячных
echo $numeral->getInWords(100.0), PHP_EOL;
//Result: сто


//Get string for money (RUB)
echo RUtils::numeral()->getRubles(100.25), PHP_EOL;
//Result: сто рублей двадцать пять копеек
