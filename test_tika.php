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
$tika_host = null;
$tika_port = null;
$tika = new \Colombo\Converters\Drivers\Tika();

//$gs->timeout(2);
$converter->setForceConverter($tika);

$converter->setOutputFormat('html');

$result = $converter->run();

if ($result->isSuccess()) {
    dump($result->getContent());
} else {
    dump('Error', $result->getErrors());
}

//echo $result->getContent();
