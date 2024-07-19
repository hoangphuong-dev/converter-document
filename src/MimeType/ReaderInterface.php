<?php


namespace Colombo\Converters\MimeType;

interface ReaderInterface
{
    /**
     * Read mime type from a path
     *
     * @return mixed
     */
    public function fromFile($path);

    /**
     * Read mime type from a resource
     *
     * @return mixed
     */
    public function fromResource($resource);
}
