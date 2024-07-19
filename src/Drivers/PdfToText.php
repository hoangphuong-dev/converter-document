<?php


namespace Colombo\Converters\Drivers;

use Colombo\Converters\ConvertedResult;
use Colombo\Converters\Process\CanRunCommand;

class PdfToText extends CanRunCommand implements ConverterInterface
{
    protected $bin = 'pdftotext';

    protected $process_options = [

    ];

    public function convert($path, $outputFormat, $inputFormat = ''): ConvertedResult
    {
        $result = new ConvertedResult();
        $this->options('-enc', 'UTF-8');
        $command = array_merge($this->buildCommand([$path, '-'], ['-layout', '-nodiag']));

        try {
            $this->run($command);
            $result->setContent($this->output());
        } catch (\RuntimeException $ex) {
            $result->addErrors($ex->getMessage(), $ex->getCode());
        }

        return $result;
    }

    public function startPage(int $page)
    {
        $this->options('-f', $page);
    }

    public function endPage(int $page)
    {
        $this->options('-l', $page);
    }
}
