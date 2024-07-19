<?php


namespace Colombo\Converters\MimeType;

interface MapperInterface
{
    /**
     * Get mime type from extension
     *
     * @param  string  $ext
     * @return string
     */
    public function mimeType($ext);

    /**
     * Get all possible mime types from an extension
     *
     * @param  string  $ext
     * @return array
     */
    public function allMimeTypes($ext);

    /**
     * Get extension from a mime type
     *
     * @param  string  $mime_type
     * @return string
     */
    public function extension($mime_type);

    /**
     * Get all possible extensions from a mime type
     *
     * @param  string  $mime_type
     * @return array
     */
    public function allExtensions($mime_type);
}
