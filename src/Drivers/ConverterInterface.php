<?php


namespace Colombo\Converters\Drivers;

use Colombo\Converters\ConvertedResult;

interface ConverterInterface
{
    public function __construct($bin = '', $tmp = '');

    public function convert($path, $outputFormat, $inputFormat = ''): ConvertedResult;

    /**
     * Custom options
     *
     * @param  null  $key
     * @param  null  $value
     * @return mixed
     */
    public function options($key = null, $value = null);

    public function startPage(int $page);

    public function endPage(int $page);
}
