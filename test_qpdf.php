<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__.'/vendor/autoload.php';

$input = __DIR__.'/files/long.pdf';

$tmp = __DIR__.'/tmp';
$tmpFolder = new \Colombo\Converters\Helpers\TemporaryDirectory($tmp);
//$tmpFolder->autoDestroyed(false);
//dd($tmpFolder->clean(1));

$converter = new \Colombo\Converters\Helpers\Converter(null);

$converter->setInput($input);

// force custom converter
//$converter->setForceConverter( new \Colombo\Converters\Drivers\GS('', $tmpFolder) );
$gs = new \Colombo\Converters\Drivers\Qpdf('', $tmpFolder);
//$gs->timeout(2);
$converter->setMappingConverter('pdf', 'pdf', $gs);

$converter->setOutputFormat('pdf');

$converter->setStartPage(3);

$result = $converter->run();

if ($result->isSuccess()) {
    $result->saveTo(__DIR__.'/output_qpdf.pdf');
} else {
    dump('Error', $result->getErrors());
}

//echo $result->getContent();
