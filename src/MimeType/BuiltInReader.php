<?php


namespace Colombo\Converters\MimeType;

class BuiltInReader implements ReaderInterface
{
    public function fromFile($path)
    {
        if (! file_exists($path)) {
            throw new \Exception('File not found at '.$path);
        }

        return mime_content_type($path);
    }

    public function fromResource($resource)
    {
        throw new \Exception('Not supported');
    }
}
