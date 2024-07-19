<?php


namespace Colombo\Converters\MimeType;

use Vaites\ApacheTika\Client;

class TikaReader implements ReaderInterface
{
    public static $tika_server = 'http://127.0.0.1:9998';

    protected $client;

    /**
     * TikaReader constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->client = Client::make(self::$tika_server);
    }

    /**
     * Read mime type from a path
     *
     * @return mixed
     */
    public function fromFile($path)
    {
        $mimetype = $this->client->getMime($path);

        return $mimetype;
    }

    /**
     * Read mime type from a resource
     *
     * @return mixed
     */
    public function fromResource($resource)
    {
        // TODO: Implement fromResource() method.
    }
}
