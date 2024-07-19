<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__.'/vendor/autoload.php';

$input = __DIR__.'/files/cv.pdf';

$tmp = __DIR__.'/tmp';
$tmpFolder = new \Colombo\Converters\Helpers\TemporaryDirectory($tmp);
//$tmpFolder->autoDestroyed(false);
//dd($tmpFolder->clean(1));

$converter = new \Colombo\Converters\Helpers\Converter($input);

// force custom converter
$pdf2htmlex = new \Colombo\Converters\Drivers\Pdf2HtmlEx();

//$converter->setForceConverter( new \Colombo\Converters\Drivers\Pdf2HtmlEx('', $tmpFolder) );
$converter->setMappingConverter('pdf', 'html', new \Colombo\Converters\Drivers\Pdf2HtmlEx('', $tmpFolder));

$converter->setStartPage(1);
$converter->setEndPage(2);
$converter->setOutputFormat('html');

$result = $converter->run(['--process-outline' => 0]);

if ($result->isSuccess()) {
    $result->saveAsZip(__DIR__.'/a.zip');
    dump('Success', $result->getMessages());
} else {
    dump('Error', $result->getErrors());
}
