<?php


namespace Colombo\Converters\Drivers;

class PdfToXml extends PdfToHtml
{
    protected $process_options = [
        '-i' => true,
        '-xml' => true,
        '-stdout' => true,
        '-hidden' => true,
        '-nodrm' => true,
        '-fontfullname' => true,
    ];
}
