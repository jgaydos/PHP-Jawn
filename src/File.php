<?php

namespace Jawn;

/**
* File class
*/
class File
{
    /**
     * Checks whether a file or directory exists
     *
     * @param   string  $target
     * @return  bool
     */
    public function exists(string $target): bool
    {
        return file_exists($target);
    }

    /**
     * List dir contents
     * @param   string  $target
     * @param   array   $exclude
     * @return  array|bool
     */
    public function ls(string $target, array $exclude = ['..', '.'])
    {
        if (self::exists($target)) {
            $ofTheKing = array_values(array_diff(scandir($target), $exclude));
            array_walk($ofTheKing, function (&$path) use ($target) {
                $path = rtrim($target, '/\\').'/'.$path;
            });
            return $ofTheKing;
        }
        return false;
    }

    /**
     * List dir contents: files
     * @param   string  $target
     * @return  array
     */
    public function lf(string $target)
    {
        $ls = self::ls($target);
        if ($ls === false) {
            return false;
        }
        $ofTheKing = [];
        foreach ($ls as $name) {
            if (is_file($name)) {
                $ofTheKing[] = $name;
            }
        }
        return $ofTheKing;
    }

    /**
     * List dir contents: directories
     * @param   string  $target
     * @return  array
     */
    public function ld(string $target)
    {
        $ls = self::ls($target);
        if ($ls === false) {
            return false;
        }
        $ofTheKing = [];
        foreach ($ls as $name) {
            if (is_dir($name)) {
                $ofTheKing[] = $name;
            }
        }
        return $ofTheKing;
    }

    /**
     * Tree listing of all files and directories
     * @param   string  $target
     * @param   bool    $dirFirst
     * @return  array
     */
    public function tree(string $target, bool $dirFirst = false)
    {
        $ofTheKing = [];
        if (
            ($ld = self::ld($target)) === false ||
            ($lf = self::lf($target)) === false
        ) {
            return false;
        }
        foreach ($ld as $dir) {
            if ($dirFirst) {
                $ofTheKing[] = $dir;
            }
            $ofTheKing = array_merge($ofTheKing, self::tree($dir));
            if (!$dirFirst) {
                $ofTheKing[] = $dir;
            }
        }
        foreach ($lf as $file) {
            $ofTheKing[] = $file;
        }
        return $ofTheKing;
    }

    /**
     * Removes a file or directory or array of
     * @param   string  $target
     * @return  bool
     */
    public function rm(string $target)
    {
        if (is_array($target)) {
            foreach ($target as $trail) {
                if (!self::rm($trail)) {
                    return false;
                }
            }
        } else {
            if (is_dir($target)) {
                if (!rmdir($target)) {
                    return false;
                } elseif (!unlink($target)) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Make difectory
     * @param   string  $target
     * @param   int     $mode
     * @param   bool    $recursive
     * @return  bool
     */
    public function mkdir(
        string $target,
        int $mode = 0777,
        bool $recursive = false
    ): bool {
        if (!mkdir($target, $mode, $recursive)) {
            return false;
        }
        return true;
    }

    /**
     * Remove directory
     * @param   string  $target
     * @return  bool
     */
    public function rmdir(string $target): bool
    {
        if (!rmdir($target)) {
            return false;
        }
        return true;
    }

    /**
     * Make hard link
     * @param   string  $target
     * @param   string  $link
     * @return  bool
     */
    public function link(string $target, string $link): bool
    {
        if (!link($target, $link)) {
            return false;
        }
        return true;
    }

    /**
     * Permission check & create file if not exist
     * @param   string  $target
     * @return  bool
     */
    public function touch(string $target): bool
    {
        if (!touch($target)) {
            return false;
        }
        return true;
    }

    /**
     * Copy file
     * @param   string  $source
     * @param   string  $target
     * @return  bool
     */
    public function cp(string $source, string $target): bool
    {
        if (!file_exists($source)) {
            return false;
        }
        if (!copy($source, $target)) {
            return false;
        }
        return true;
    }

    /**
     * Move/rename file
     * @param   string  $oldname
     * @param   string  $newname
     * @return  bool
     */
    public function mv(string $oldname, string $newname): bool
    {
        if (!rename($oldname, $newname)) {
            return false;
        }
        return true;
    }

    /**
     * Reads an entire file into a string
     * @param   string  $target
     * @return  string
     */
    public function contents(string $target): string
    {
        if (!file_exists($target)) {
            return false;
        }
        return file_get_contents($target);
    }

    /**
     * Reads an entire file into an array
     * @param   string  $target
     * @return  array
     */
    public function read(string $target): array
    {
        if (!file_exists($target)) {
            return false;
        }
        return file($target);
    }

    /**
     * Replace all occurances of a string with another within a file
     * @param   string  $target
     * @param   string  $old
     * @param   string  $new
     * @return  void
     */
    public function replace(string $target, string $old, string $new): void
    {
        //read the entire string
        $str = file_get_contents($target);
        //replace something in the file string - this is a VERY simple example
        $str = str_replace($old, $new, $str);
        //write the entire string
        file_put_contents($target, $str);
    }

    /**
     * Adds a file to a ZIP archive from the given path.
     * Overrides/Creates archives
     * @param   string|array  $source
     * @param   string  $destination
     * @return  bool
     */
    public function zip($source, string $destination): bool
    {
        if (is_file($source)) {
            $zip = new ZipArchive;
            if ($zip->open($destination, ZIPARCHIVE::CREATE | ZipArchive::OVERWRITE) === true) {
                if (is_array($files)) {
                    foreach ($files as $key => $value) {
                        if (is_int($key)) {
                            $zip->addFile($value, basename($value));
                        } else {
                            $zip->addFile($key, $value);
                        }
                    }
                } else {
                    $zip->addFile($files, basename($files));
                }
                $zip->close();
                return true;
            } else {
                return false;
            }
        } elseif (is_dir($source)) {
            if (extension_loaded('zip')) {
                if (file_exists($source)) {
                    $zip = new ZipArchive();
                    if ($zip->open($destination, ZIPARCHIVE::CREATE)) {
                        $source = realpath($source);
                        if (is_dir($source)) {
                            $iterator = new RecursiveDirectoryIterator($source);
                            // skip dot files while iterating
                            $iterator->setFlags(RecursiveDirectoryIterator::SKIP_DOTS);
                            $files = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::SELF_FIRST);
                            foreach ($files as $file) {
                                $file = realpath($file);
                                if (is_dir($file)) {
                                    $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
                                } elseif (is_file($file)) {
                                    $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
                                }
                            }
                        } elseif (is_file($source)) {
                            $zip->addFromString(basename($source), file_get_contents($source));
                        }
                    }
                    return $zip->close();
                }
            }
            return false;
        }
    }

    /**
     * Extract the archive contents
     * @param   string  $source
     * @param   string  $target
     * @return  bool
     */
    public function unzip(string $source, string $target): bool
    {
        $zip = new ZipArchive;
        if ($zip->open($source) === true) {
            $zip->extractTo($target);
            $zip->close();
            return true;
        }
        return false;
    }

    /**
     * Write data to a file, if exists override
     * @param   string          $target     Path to the file where to write the data
     * @param   string|array    $contents   The data to write (string, an array or a stream resource)
     * @return  bool
     */
    public function write(string $target, $contents)
    {
        if (file_put_contents($target, $contents) === false) {
            return false;
        }
        return true;
    }

    /**
     * Write data to a file, if exists append
     * @param   string          $target     Path to the file where to write the data
     * @param   string|array    $contents   The data to write (string, an array or a stream resource)
     * @return  bool
     */
    public function append(string $target, $contents)
    {
        if (file_put_contents($target, $contents, FILE_APPEND) === false) {
            return false;
        }
        return true;
    }
}
