<?php


namespace Colombo\Converters\Drivers;

use Colombo\Converters\ConvertedResult;
use Colombo\Converters\Exceptions\ConvertException;
use Colombo\Converters\Helpers\Converter;
use Colombo\Converters\Helpers\TemporaryDirectory;
use Colombo\Converters\Process\CanRunCommand;

class OcrMyPdf extends CanRunCommand implements ConverterInterface
{
    protected $bin = 'ocrmypdf';

    protected $process_options = [
        '-l' => 'vie+eng',
        '--force-ocr' => true,
    ];

    protected $tmpFolder;

    protected $output = '';

    /**
     * OcrMyPdf constructor.
     *
     * @param  string  $bin
     */
    public function __construct($bin = '', $tmp = '')
    {
        parent::__construct($bin);
    }

    /**
     * @throws ConvertException
     */
    public function setTmp()
    {
        $this->tmpFolder = new TemporaryDirectory(config('converters.tmp'));
        $this->output = $this->tmpFolder->path(uniqid('ocrmypdf_', true).'.pdf');
    }

    public function options($key = null, $value = null)
    {
        if ($key == 'tmp') {
            $this->setTmp($value);
        } else {
            return parent::options($key, $value);
        }
    }

    /**
     * @param  string  $inputFormat
     *
     * @throws ConvertException
     */
    public function convert($path, $outputFormat, $inputFormat = ''): ConvertedResult
    {
        $result = new ConvertedResult();

        if ($outputFormat != 'pdf') {
            throw new ConvertException($outputFormat.' was not supported by ocrmypdf converter');
        }
        $command = $this->buildCommand([], [$path, $this->output]);
        try {
            $this->run($command);
            $result->setContent(file_get_contents($this->output));
            $result->addMessages($this->output(), Converter::MSG_OUTPUT);
            $result->addMessages('Tmp file: '.$this->output);
        } catch (\RuntimeException $ex) {
            $result->addErrors($ex->getMessage(), $ex->getCode());
        }finally{
            $this->process->stop(0, SIGKILL);
        }

        return $result;
    }

    public function startPage(int $page)
    {
        // can not set
    }

    public function endPage(int $page)
    {
        // can not set
    }
}
