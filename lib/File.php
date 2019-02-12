<?php

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
     * @return  array
     */
    public function ls(string $target, array $exclude = ['..', '.']): mixed
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
    public function lf(string $target): mixed
    {
        $ls = self::ls($target);
        if ($ls === false) {
            return false;
        }
        $ofTheKing = [];
        foreach ($ls as $name) {
            //$name = rtrim($target, '/\\').'/'.$name;
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
    public function ld(string $target): mixed
    {
        $ls = self::ls($target);
        if ($ls === false) {
            return false;
        }
        $ofTheKing = [];
        foreach ($ls as $name) {
            //$name = rtrim($target, '/\\').'/'.$name;
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
    public function tree(string $target, bool $dirFirst = false): mixed
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
    public function rm(string $target): mixed
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
    ): mixed {
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
     * @param   bool    $ignore_errors
     * @return  bool
     */
    public function cp(
        string $source,
        string $target,
        bool $ignore_errors = false
    ): bool {
        if (!file_exists($source) && $ignore_errors) {
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
     */
    public function contents(string $target): mixed
    {
        if (!file_exists($target)) {
            return false;
        }
        return file_get_contents($target);
    }

    /**
     * Reads an entire file into an array
     */
    public function read(string $target): mixed
    {
        if (!file_exists($target)) {
            return false;
        }
        return file($target);
    }

    /**
     * Replace all occurances of a string with another within a file
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
     * @param   string  $source
     * @param   string|array  $files
     * @return  bool
     */
    public function zip(string $source, $files): bool
    {
        Console::info('Zip: '.$source, '');
        $zip = new ZipArchive;
        if ($zip->open($source, ZIPARCHIVE::CREATE | ZipArchive::OVERWRITE) === true) {
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
            Console::success('...Wubbalubbadubdub!');
            return true;
        } else {
            Console::danger('...I am in great pain, please help me');
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
        } else {
            return false;
        }
    }
}
