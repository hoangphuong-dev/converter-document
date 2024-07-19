<?php
/**
 * Created by PhpStorm
 * Filename: PdfToTextPyMuPDF.php
 * User: MinhHN
 * 
 * 
 */

namespace Colombo\Converters\Drivers;

use Colombo\Converters\ConvertedResult;
use Colombo\Converters\Process\CanRunCommand;

class PdfToTextPyMuPDF extends CanRunCommand implements ConverterInterface
{
    protected $bin = 'python3';

    protected $process_options = [
    ];

    protected int $start_page = 1;

    protected ?int $end_page = -1;

    public function convert($path, $outputFormat, $inputFormat = '') : ConvertedResult
    {
        $pyPath  = base_path().'/packages/pdf_to_text/pdf-to-text.py';
        $result  = new ConvertedResult();
        $command = array_merge($this->buildCommand([$pyPath, $path, $this->start_page, $this->end_page]));
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
        $this->start_page = $page;
    }

    public function endPage(int $page)
    {
        $this->end_page = $page;
    }
}
