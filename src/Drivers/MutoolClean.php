<?php


namespace Colombo\Converters\Drivers;

use Colombo\Converters\ConvertedResult;
use Colombo\Converters\Exceptions\ConvertException;
use Colombo\Converters\Process\CanRunCommand;

class MutoolClean extends CanRunCommand implements ConverterInterface
{
    use HasTmp;

    protected $bin = 'mutool';

    protected $process_options = [
        'clean' => true, // clean tool
        '-l' => true, // linearize PDF
        '-gggg' => true, //
        '-f' => true,
        '-i' => true,
        '-c' => true,
        '-s' => true,
    ];

    protected $start_page = 0;

    protected $end_page = 2000;

    /**
     * @throws ConvertException
     */
    public function convert($path, $outputFormat, $inputFormat = ''): ConvertedResult
    {
        $result = new ConvertedResult();

        if ($outputFormat != 'html') {
            throw new ConvertException($outputFormat.' was not supported by mutool converter');
        }

        $output = $this->tmpFolder->name('output.pdf');

        echo $output;

        return;

        $command = $this->buildCommand($path.' -o '.$output.' '.$this->start_page.'-'.$this->end_page);
        try {
            $this->run($command);
            $files = glob($output_dir.'/*');
            foreach ($files as $file) {
                $result->addContent(file_get_contents($file), basename($file));
            }
        } catch (\RuntimeException $ex) {
            $result->addErrors($ex->getMessage(), $ex->getCode());
        }finally{
            $this->process->stop(0, SIGKILL);
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
