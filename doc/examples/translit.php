<?php

use php_rutils\RUtils;

require_once __DIR__ . '/../../vendor/autoload.php';

define('CLI', php_sapi_name() === 'cli');
mb_internal_encoding('UTF-8');

if (!CLI) {
    header('Content-type: text/plain; charset=UTF-8');
}

//Translify
echo RUtils::translit()->translify('Муха - это маленькая птичка'), PHP_EOL;
//Result: Muxa - e`to malen`kaya ptichka

//Detranslify
echo RUtils::translit()->detranslify('Muxa - e`to malen`kaya ptichka'), PHP_EOL;
//Result: Муха - это маленькая птичка

//Prepare to use in URLs or file/dir name
echo RUtils::translit()->slugify('Муха — это маленькая птичка'), PHP_EOL;
//Result: muha-eto-malenkaya-ptichka
