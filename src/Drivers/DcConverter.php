<?php


namespace Colombo\Converters\Drivers;

use Colombo\Converters\ConvertedResult;
use Colombo\Converters\Exceptions\ConvertException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;

class DcConverter extends JodConverter
{
    protected $options = [
        'base_uri' => 'http://127.0.0.1:3000',
        'timeout' => 300,
        'verify' => false,
    ];

    protected $client;

    /**
     * @throws ConvertException
     * @throws GuzzleException
     */
    public function convert($path, $outputFormat, $inputFormat = ''): ConvertedResult
    {
        $client = $this->getClient();
        $result = new ConvertedResult();
        try {
            $response = $client->post('convert', [
                'multipart' => [
                    [
                        'name' => 'format',
                        'contents' => 'pdf',
                    ],
                    [
                        'name' => 'file',
                        'contents' => fopen($path, 'r'),
                        'filename' => basename($path).'.'.$inputFormat,
                    ],
                ],
                'verify' => false,
            ]);
            $result->setContent($response->getBody()->getContents());
        } catch (RequestException $ex) {
            $msg = 'Can not convert by dc converter '.$this->options('base_uri').' :: '.$ex->getMessage();
            $result->addErrors($msg, $ex->getCode());
        }

        return $result;
    }

    /**
     * @throws ConvertException
     * @throws GuzzleException
     */
    protected function getClient(): Client
    {
        $client = new Client([
            'base_uri' => $this->options('base_uri'),
            'timeout' => $this->options('timeout'),
            'verify' => $this->options('verify'),
        ]);
        try {
            $client->get('/', [
                'timeout' => 5,
                'verify' => false,
            ]);
        } catch (RequestException $ex) {
            throw new ConvertException('DcConverter can not connect to '.$this->options('base_uri'));
        }

        return $client;
    }
}
