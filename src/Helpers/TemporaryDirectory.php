<?php

namespace Colombo\Converters\Helpers;

use Colombo\Converters\Exceptions\ConvertException;
use Exception;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;


class TemporaryDirectory
{
    /** @var string */
    protected $location;

    /** @var string */
    protected $name;

    /** @var bool */
    protected $forceCreate = false;

    /** @var bool */
    protected $autoDestroyed = true;

    /** @var array */
    protected $createdPaths = [];

    public function __construct(string $location = '')
    {
        $this->location = $this->sanitizePath($location);
    }

    public function create($autoDestroyed = true): self
    {
        if (empty($this->location)) {
            $this->location = $this->getSystemTemporaryDirectory();
        }
        if (empty($this->name)) {
            $this->name = str_replace([' ', '.'], '', microtime());
        }
        if ($this->forceCreate && file_exists($this->getFullPath())) {
            $this->deleteDirectory($this->getFullPath());
        }
        if (file_exists($this->getFullPath())) {
            throw new InvalidArgumentException("Path `{$this->getFullPath()}` already exists.");
        }
        mkdir($this->getFullPath(), 0777, true);
        $this->pathWasCreated($this->getFullPath());

        return $this;
    }

    public function force(): self
    {
        $this->forceCreate = true;

        return $this;
    }

    public function name(string $name): self
    {
        $this->name = $this->sanitizeName($name);

        return $this;
    }

    public function location(string $location): self
    {
        $this->location = $this->sanitizePath($location);

        return $this;
    }

    public function autoDestroyed($isAuto = null): bool
    {
        if ($isAuto !== null) {
            $this->autoDestroyed = (bool) $isAuto;
        }

        return $this->autoDestroyed;
    }

    public function tmpPath($subfix = '', $prefix = 'tmp', $ext_dot = '.')
    {
        $subfix = $subfix ? $ext_dot.$subfix : '';
        $fileName = $prefix.time().Str::random(4).$subfix;

        return $this->path($fileName);
    }

    /**
     * @throws ConvertException
     */
    public function path(string $pathOrFilename = ''): string
    {
        if (empty($pathOrFilename)) {
            return $this->getFullPath();
        }
        $path = $this->getFullPath().DIRECTORY_SEPARATOR.trim($pathOrFilename, '/');
        $directoryPath = $this->removeFilenameFromPath($path);
        try {
            if (! file_exists($directoryPath) && ! mkdir($directoryPath, 0777, true) && ! file_exists($directoryPath)) {
                throw new ConvertException(sprintf('Impossible to create the root directory "%s".', $directoryPath));
            }
        } catch (\ErrorException $exception) {
        }

        return $path;
    }

    /**
     * @throws ConvertException
     */
    public function empty(): self
    {
        $this->deleteDirectory($this->getFullPath());
        $umask = umask(0);
        @mkdir($this->getFullPath());
        umask($umask);
        if (! is_dir($this->getFullPath())) {
            throw new ConvertException(sprintf('Impossible to create the root directory "%s".', $this->getFullPath()));
        }

        return $this;
    }

    public function delete(): bool
    {
        return $this->deleteDirectory($this->getFullPath());
    }

    protected function getFullPath(): string
    {
        return $this->location.($this->name ? DIRECTORY_SEPARATOR.$this->name : '');
    }

    protected function isValidDirectoryName(string $directoryName): bool
    {
        return strpbrk($directoryName, '\\/?%*:|"<>') === false;
    }

    protected function getSystemTemporaryDirectory(): string
    {
        return rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR);
    }

    protected function sanitizePath(string $path): string
    {
        $path = rtrim($path);

        return rtrim($path, DIRECTORY_SEPARATOR);
    }

    /**
     * @throws Exception
     */
    protected function sanitizeName(string $name): string
    {
        if (! $this->isValidDirectoryName($name)) {
            throw new Exception("The directory name `$name` contains invalid characters.");
        }

        return trim($name);
    }

    protected function removeFilenameFromPath(string $path): string
    {
        if (! $this->isFilePath($path)) {
            return $path;
        }

        return substr($path, 0, strrpos($path, DIRECTORY_SEPARATOR));
    }

    protected function isFilePath(string $path): bool
    {
        $info = pathinfo($path);

        return \Arr::get($info, 'extension', false) !== false;
    }

    public function deleteDirectory(string $path): bool
    {
        return false;

        if (! file_exists($path)) {
            return true;
        }
        if (! is_dir($path)) {
            try {
                return unlink($path);
            } catch (\Exception $exception) {
                return false;
            }
        }
        if (! file_exists($path)) {
            return true;
        }
        foreach (scandir($path) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }
            if (! $this->deleteDirectory($path.DIRECTORY_SEPARATOR.$item)) {
                return false;
            }
        }
        try {
            return rmdir($path);
        } catch (Exception $exception) {
            return false;
        }
    }

    protected function pathWasCreated($path)
    {
        $this->createdPaths[] = $path;
    }

    public function __destruct()
    {
        if ($this->autoDestroyed) {
            $this->deleteDirectory($this->getFullPath());
        }
    }

    public function clean($minutes = 10)
    {
        $finder = new Finder();
        $finder->files()->in($this->location)->date('< now - '.$minutes.' minutes');
        $count = 0;
        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            $count += @unlink($file) ? 1 : 0;
        }

        return $count;
    }
}
