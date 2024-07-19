<?php
/**
 * @Created by : PhpStorm
 *
 * @Author : Hiệp Nguyễn
 *
 * @At : 02/04/2021, Friday
 *
 * @Filename : Pdf2pdf.php
 **/

namespace Colombo\Converters\Drivers;

use Colombo\Converters\ConvertedResult;

class Pdf2pdf extends \Colombo\Converters\Process\CanRunCommand implements ConverterInterface
{
    protected $bin = 'split_pdf';

    protected $start;

    protected $end;

    protected $tmp_folder;

    public function __construct($bin = '', $tmp = '')
    {
        parent::__construct($bin, $tmp);
        $this->tmp_folder = $tmp->path(time());
    }

    /**
     * {@inheritDoc}
     */
    public function convert($path, $outputFormat, $inputFormat = ''): ConvertedResult
    {
        $result = new ConvertedResult();
        $command = $this->buildCommand([], [
            $path,
            "{$this->start},{$this->end}",
            "{$this->tmp_folder}/output.pdf",
        ]);
        try {
            $this->run($command);
            $result->setContent(file_get_contents("{$this->tmp_folder}/output.pdf"));
        } catch (\RuntimeException $ex) {
            $result->addErrors($ex->getMessage(), $ex->getCode());
        }finally{
            $this->process->stop(0, SIGKILL);
        }

        return $result;
    }

    public function startPage(int $page)
    {
        $this->start = $page;
        $this->options('-f', $page);
    }

    public function endPage(int $page)
    {
        $this->end = $page;
        $this->options('-l', $page);
    }
}
