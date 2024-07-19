<?php


namespace Colombo\Converters\Drivers;

use Colombo\Converters\ConvertedResult;
use Colombo\Converters\Exceptions\ConvertException;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;

class OnlyOffice implements ConverterInterface
{
    protected $options = [
        'host' => 'http://localhost:8080',
    ];

    /** @var Client */
    protected $client;

    protected $error_messages = [
        1 => 'Unknown error',
        2 => 'Conversion timeout error',
        3 => 'Conversion error',
        4 => 'Error while downloading the document file to be converted',
        5 => 'Incorrect password',
        6 => 'Error while accessing the conversion result database',
        8 => 'Invalid token',
    ];

    public function __construct($host = 'http://localhost:8080', $tmp = '')
    {
        $this->setHost($host);
    }

    public function setHost($host)
    {
        $host = rtrim($host, '/');
        $this->client = new Client([
            'base_uri' => $host,
        ]);
        $this->options('host', $host);
    }

    public function convert($path, $outputFormat, $inputFormat = ''): ConvertedResult
    {
        $result = new ConvertedResult();

        $response = $this->client->post('converter', [
            'json' => [
                'async' => false,
                'key' => $this->hashUrl($path),
                'outputtype' => $outputFormat,
                'url' => $path,
            ],
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);

        $response = json_decode($response->getBody()->getContents(), true);

        if (isset($response['error'])) {
            $message = $response['error'].' : '.Arr::get($this->error_messages, abs($response['error']), 'Unknown error');
            throw new ConvertException($message);
        }

        if ($response['endConvert'] && $response['percent']) {
            $response['fileUrl'] = preg_replace("/^(https?\:\/\/[^\/]+)/", $this->options('host'), $response['fileUrl']);
            $result->setContent(file_get_contents($response['fileUrl']));
        }

        return $result;
    }

    /**
     * Custom options
     *
     * @param  null  $key
     * @param  null  $value
     * @return mixed
     */
    public function options($key = null, $value = null)
    {
        if (is_array($key)) {
            if ($value === true) {// overwrite all option
                $this->options = $key;
            } else {
                foreach ($key as $k => $v) {
                    $this->options($k, $v);
                }
            }
        } elseif ($value !== null) {
            $this->options[$key] = $value;
        } else {
            return $this->options[$key];
        }
    }

    public function hashUrl($url)
    {
        return md5($url);
    }

    public function startPage(int $page)
    {
        // Not supported
    }

    public function endPage(int $page)
    {
        // Not supported
    }
}
