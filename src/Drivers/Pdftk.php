<?php


namespace Colombo\Converters\Drivers;

use Colombo\Converters\ConvertedResult;
use Colombo\Converters\Exceptions\ConvertException;
use Colombo\Converters\Helpers\Converter;
use Colombo\Converters\Helpers\TemporaryDirectory;
use Colombo\Converters\Process\CanRunCommand;

class Pdftk extends CanRunCommand
{
    protected $bin = 'pdftk';

    protected $process_options = [];

    protected TemporaryDirectory $tmpFolder;

    protected string $output = '';

    /**
     * @return void
     * @throws ConvertException
     */
    public function setTmp(): void
    {
        $this->tmpFolder = new TemporaryDirectory(config('converters.tmp'));
        $this->output    = $this->tmpFolder->path(uniqid('pdftk_', true).'.pdf');
    }

    public function copy(string $input, array $pages): ConvertedResult
    {
        $result = new ConvertedResult();

        $prepend = [
            'cat',
            implode(' ', $pages),
            'output',
            $this->output
        ];

        $command = $this->buildCommand($prepend, [$input]);
        $command = implode(' ', $command);

        try {
            $this->timeout(900);
            $this->run($command);

            $result->setContent(file_get_contents($this->output));
            $result->addMessages($this->output, Converter::MSG_OUTPUT);
        } catch (\RuntimeException $ex) {
            $result->addErrors($ex->getMessage(), $ex->getCode());
        } finally {
            $this->process->stop(0, SIGKILL);
        }

        return $result;
    }
}
