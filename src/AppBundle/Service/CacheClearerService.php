<?php

namespace AppBundle\Service;

use Symfony\Component\Filesystem\Filesystem;

/**
 * Clear the HTTP Cache.
 */
class CacheClearerService
{
    /**
     * The filesystem class.
     *
     * @var Filesystem
     */
    private $filesystem;

    /**
     * The cache directory path.
     *
     * @var string
     */
    private $realCacheDir;

    /**
     * Constructor.
     *
     * @param Filesystem $filesystem
     * @param string     $realCacheDir
     */
    public function __construct(Filesystem $filesystem, string $realCacheDir)
    {
        $this->filesystem = $filesystem;
        $this->realCacheDir = $realCacheDir.'/http_cache';
    }

    /**
     * Clear the cache.
     */
    public function clear()
    {
        $this->filesystem->remove($this->realCacheDir);
    }
}
