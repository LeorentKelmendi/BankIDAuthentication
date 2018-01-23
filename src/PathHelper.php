<?php

namespace Leo\BankIDAuthenticaton;


class PathHelper
{
    /**
     * @param string $path
     */
    public static function createIntermediateFolders($path)
    {
        $folderPath = static::removeFileName($path);
        if (! is_dir($folderPath)) {
            mkdir($folderPath, 0777, true);
        }
    }
    /**
     * @param string|null $fileName
     *
     * @throws \Sven\ArtisanView\Exceptions\UnsupportedException
     *
     * @return string
     */
    public static function getPath($fileName = null)
    {
        /** @var \Illuminate\View\FileViewFinder $viewFinder */
        $viewFinder = app('view.finder');
        $paths = $viewFinder->getPaths();
        // If we have more than one path configured, throw an
        // exception as this is currently not supported by
        // the package. It might be supported later on.
        if (count($paths) !== 1) {
            throw new \Exception('More than a path configured atm')
        }
        $path = reset($paths);
        return self::normalizePath($path.DIRECTORY_SEPARATOR.$fileName);
    }
    /**
     * @param string $path
     *
     * @return string
     */
    public static function normalizePath($path)
    {
        $withoutBackslashes = str_replace('\\', DIRECTORY_SEPARATOR, $path);
        return str_replace('/', DIRECTORY_SEPARATOR, $withoutBackslashes);
    }
    /**
     * @param string $path
     *
     * @return string
     */
    public static function removeFileName($path)
    {
        $parts = explode(DIRECTORY_SEPARATOR, static::normalizePath($path));
        array_pop($parts);
        return implode(DIRECTORY_SEPARATOR, $parts);
    }

 ?>