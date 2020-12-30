<?php

namespace Skaffold\Cache;

use League\Flysystem\Filesystem;
use League\Flysystem\StorageAttributes;
use Throwable;

class FileCacheRepository implements CacheRepositoryInterface
{
    protected Filesystem $filesystem;
    protected string $cacheDirectory;

    /**
     * Constructs the file cache repository.
     * 
     * @param  Filesystem  $filesystem  PHP League filesystem object for interacting with the files.
     * @param  string      $cacheDirectory  The directory cache files should be created in.
     */
    public function __construct(Filesystem $filesystem, string $cacheDirectory = '.cache')
    {
        $this->filesystem     = $filesystem;
        $this->cacheDirectory = rtrim($cacheDirectory, '\/');
    }

    /**
     * {@inheritdoc}
     */
    public function delete(string $key): void
    {
        $this->filesystem->delete($this->path($key));
    }

    /**
     * {@inheritdoc}
     */
    public function fill(array $items): void
    {
        foreach ($items as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function flush(): void
    {
        $files = $this->filesystem->listContents($this->cacheDirectory)
            ->filter(function (StorageAttributes $file) {
                if (! $file->isFile()) {
                    return false;
                }

                // Removes any files that do not end in ".cache"
                if (substr_compare($file->path(), '.cache', -6, 6) !== 0) {
                    return false;
                }

                return true;
            })
            ->getIterator();

        foreach ($files as $file) {
            $this->filesystem->delete($file->path());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $key, $default = null)
    {
        try {
            return unserialize($this->filesystem->read($this->path($key)));
        } catch (Throwable $error) {
            return $default;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $key): bool
    {
        return $this->filesystem->fileExists($this->path($key));
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $key, $value): void
    {
        $this->filesystem->write($this->path($key), serialize($value));
    }

    /**
     * Returns the filepath for the passed key.
     * 
     * @param  string  $key  The key to build a path for.
     * @return string
     */
    protected function path(string $key): string
    {
        $key = md5(strtolower($key));
        return "{$this->cacheDirectory}/$key.cache";
    }
}
