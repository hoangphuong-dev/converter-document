<?php


namespace Colombo\Converters\Drivers;

use Colombo\Converters\ConvertedResult;
use Colombo\Converters\Exceptions\ConvertException;
use Colombo\Converters\Process\CanRunCommand;

class PdfToHtml extends CanRunCommand implements ConverterInterface
{
    protected $bin = 'pdftohtml';

    protected $process_options = [
        '-i' => true,
        '-stdout' => true,
        '-nodrm' => true,
        '-fontfullname' => true,
    ];

    /**
     * @param  string  $outputFormat
     * @param  string  $inputFormat
     *
     * @throws ConvertException
     */
    public function convert($path, $outputFormat, $inputFormat = ''): ConvertedResult
    {
        $result = new ConvertedResult();
        $command = [];
        switch ($outputFormat) {
            case 'html':

                break;
            case 'xml':
                $this->options('-xml', true);
                $command = [
                    '-enc',
                    'UTF-8',
                ];
                break;
            default:
                throw new ConvertException($outputFormat.' was not supported by pdftohtml converter');
        }

        $command = array_merge($this->buildCommand([], [$path]), $command);

        try {
            $this->run($command);
            $result->setContent($this->output());
        } catch (\RuntimeException $ex) {
            $result->addErrors($ex->getMessage(), $ex->getCode());
        }finally{
            $this->process->stop(0, SIGKILL);
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
