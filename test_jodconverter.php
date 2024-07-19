<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__.'/vendor/autoload.php';

$input = __DIR__.'/files/test.docx';

$tmp = __DIR__.'/tmp';
$tmpFolder = new \Colombo\Converters\Helpers\TemporaryDirectory($tmp);
//$tmpFolder->autoDestroyed(false);
//dd($tmpFolder->clean(1));

$converter = new \Colombo\Converters\Helpers\Converter(null);

$converter->setInput($input);

// force custom converter
$driver = new \Colombo\Converters\Drivers\JodConverter('http://118.70.13.36:8999');
//$gs->timeout(2);
$converter->setForceConverter($driver);

$outputFormat = 'pdf';
$converter->setOutputFormat($outputFormat);

$result = $converter->run();

if ($result->isSuccess()) {
    dump($result->saveTo(__DIR__.'/output_jodconverter.'.$outputFormat));
} else {
    dump('Error', $result->getErrors());
}

//echo $result->getContent();
