<?php


namespace Colombo\Converters\MimeType;

use Mimey\MimeTypes;

class MimeyMapper implements MapperInterface
{
    protected $mimey;

    /**
     * MimeyMapper constructor.
     *
     * @param $mimey
     */
    public function __construct()
    {
        $this->mimey = new MimeTypes();
    }

    /**
     * Get mime type from extension
     *
     * @param  string  $ext
     * @return string
     */
    public function mimeType($ext)
    {
        return $this->mimey->getMimeType($ext);
    }

    /**
     * Get all possible mime types from an extension
     *
     * @param  string  $ext
     * @return array
     */
    public function allMimeTypes($ext)
    {
        return $this->mimey->getAllMimeTypes($ext);
    }

    /**
     * Get extension from a mime type
     *
     * @param  string  $mime_type
     * @return string
     */
    public function extension($mime_type)
    {
        return $this->mimey->getExtension($mime_type);
    }

    /**
     * Get all possible extensions from a mime type
     *
     * @param  string  $mime_type
     * @return array
     */
    public function allExtensions($mime_type)
    {
        return $this->mimey->getAllExtensions($mime_type);
    }
}
