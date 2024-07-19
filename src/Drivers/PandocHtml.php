<?php
/**
 * Created by PhpStorm
 * Filename: PandocHtml.php
 * User: MinhHN
 * 
 * 
 */

namespace Colombo\Converters\Drivers;

use Colombo\Converters\ConvertedResult;
use Colombo\Converters\Process\CanRunCommand;

class PandocHtml extends CanRunCommand implements ConverterInterface
{
    protected $bin = 'pandoc';

    protected $process_options
        = [
            '--no-highlight'    => true,
            '--embed-resources' => true,
//            '--section-divs'    => true, // Nhóm các phần tử thành 1 section
            //            '--number-sections' => true, // Đánh số cho các thẻ heading
            //            '--strip-comments' => true, // Bỏ comment trong html
            //            '--fail-if-warnings'   => true,
            //            '--verbose'            => true,
            //            '--quiet'              => true,
            //            '--list-input-formats' => true,
            //            '--list-output-formats' => true,
            //            '--list-highlight-styles' => 'pygments'
        ];

    /**
     * @param  string  $path
     * @param  string  $outputFormat
     * @param  string  $inputFormat
     *
     * @return ConvertedResult
     */
    public function convert($path, $outputFormat, $inputFormat = '') : ConvertedResult
    {
        $result  = new ConvertedResult();
        $command = [];
        $command = array_merge($this->buildCommand([], [$path]), $command);
        try {
            $this->run($command);
            $result->setContent($this->output());
        } catch (\RuntimeException $ex) {
            $result->addErrors($ex->getMessage(), $ex->getCode());
        } finally {
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
