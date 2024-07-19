<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__.'/vendor/autoload.php';

$input = __DIR__.'/files/cv.pdf';

$tmp = __DIR__.'/tmp';
$tmpFolder = new \Colombo\Converters\Helpers\TemporaryDirectory($tmp);
//$tmpFolder->autoDestroyed(false);
//dd($tmpFolder->clean(1));

$converter = new \Colombo\Converters\Helpers\Converter(null);

$converter->setInput($input);

// force custom converter
//$converter->setForceConverter( new \Colombo\Converters\Drivers\GS('', $tmpFolder) );
$gs = new \Colombo\Converters\Drivers\GS('', $tmpFolder);
//$gs->timeout(2);
$converter->setMappingConverter('pdf', 'pdf', $gs);

$converter->setOutputFormat('pdf');

$result = $converter->run();

if ($result->isSuccess()) {
    $result->saveTo(__DIR__.'/output_gs.pdf');
} else {
    dump('Error', $result->getErrors());
}

//echo $result->getContent();
