<?php


namespace Colombo\Converters\Drivers;

use Colombo\Converters\ConvertedResult;
use Colombo\Converters\Drivers\GS\ProfileInterface;
use Colombo\Converters\Drivers\GS\SimpleProfile;
use Colombo\Converters\Exceptions\ConvertException;
use Colombo\Converters\Process\CanRunCommand;
use Illuminate\Support\Facades\Log;

class GS extends CanRunCommand implements ConverterInterface
{
    protected $bin = 'gs';

    protected $docker_bin = '';

    protected $process_options = [

    ];

    use HasTmp;

    /**
     * Pdf2HtmlEx constructor.
     *
     * @param  string  $bin
     * @param  string  $tmp
     */
    public function __construct($bin = '', $tmp = '')
    {
        parent::__construct($bin, $tmp);
        $this->applyProfile(new SimpleProfile());
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
            throw new ConvertException($outputFormat.' was not supported by ghostscript converter');
        }

        $output_file = $this->tmpFolder->tmpPath('pdf');

        $this->options('-o', $output_file);

        $command = $this->buildCommand([$path]);
        try {
            $this->run($command);
            $result->setContent(file_get_contents($output_file));
            @unlink($output_file);
        } catch (\RuntimeException $ex) {
            $result->addErrors($ex->getMessage(), $ex->getCode());
        } finally {
            $this->process->stop(0, SIGKILL);
        }

        return $result;
    }

    /**
     * Custom options
     *
     * @param  null  $key
     * @param  null  $value
     *
     * @return mixed
     */
    public function options($key = null, $value = null, $append = false)
    {
        if ($key == 'tmp') {
            $this->setTmp($value);
        } elseif ($key == 'bin') { // custom bin path
            $this->bin($value);
        } else {
            return $this->custom_options($key, $value, $append);
        }
    }

    private function custom_options($key = null, $value = null, $append = false)
    {
        if (is_array($key)) {
            if ($value === true) {// overwrite all option
                $this->process_options = $key;
            } else {
                foreach ($key as $k => $v) {// merge
                    $this->custom_options($k, $v, $append);
                }
            }
        } elseif ($key != null) {
            if ($value !== null) {
                if ($append && isset($this->process_options[$key]) && is_array($this->process_options[$key])) {
                    $this->process_options[$key][] = $value;
                }
                $this->process_options[$key] = $value;
            }

            return $this->process_options[$key];
        }

        return $this->process_options;
    }

    public function applyProfile(ProfileInterface $profile, $append = false, $append_recursive = false)
    {
        $options = $profile->getOptions();
        if ($append) {
            foreach ($options as $k => $v) {
                $this->options($k, $v, $append_recursive);
            }
        } else {
            $this->options($options, true);
        }
    }

    public function startPage(int $page)
    {
        $this->options('-dFirstPage='.$page, true);
    }

    public function endPage(int $page)
    {
        $this->options('-dLastPage='.$page, true);
    }

    /**
     * Get blank page
     *
     * @param  string  $input
     *
     * @return array
     */
    public function getBlankPage(string $input): array
    {
        $this->resetOptions();
        $prepend = [
            '-o -',
            '-sDEVICE=inkcov',
        ];

        $append = [
            $input,
            '| grep -B 1 "^ 0.000[01][[:digit:]]  0.000[01][[:digit:]]  0.000[01][[:digit:]]  0.000[01]"',
            "| grep 'Page'",
            "| awk '{print $2}'",
        ];

        $command = $this->buildCommand($append, $prepend);
        $command = implode(' ', $command);

        try {
            $this->timeout(600);
            $this->run($command);
            return array_filter(explode("\n", $this->output()));
        } catch (\RuntimeException $ex) {
            dump($ex);
            Log::error($ex);
        } finally {
            $this->process->stop(0, SIGKILL);
        }
        return [];
    }
}
