<?php

/**
* Hash class
*/
class Hash
{
    /**
     * Returns the MD5 (128-bit) checksum of string
     * @param   $plaintext   String to hash
     * @return  string
     */
    public function md5($plaintext = '', string $salt = ''): string
    {
        if (!is_string($plaintext)) {
            $plaintext = json_encode($plaintext);
        }
        return md5($plaintext.$salt).$salt;
    }

    /**
     * Returns the SHA1 (160-bit) checksum of string
     * @param   $plaintext   String to hash
     * @return  string
     */
    public function sha1($plaintext = '', string $salt = ''): string
    {
        if (!is_string($plaintext)) {
            $plaintext = json_encode($plaintext);
        }
        return sha1($plaintext.$salt).$salt;
    }

    /**
     *  Blowfish hashing with a salt as follows: "$2a$", "$2x$" or "$2y$", a
     * two digit cost parameter, "$", and 22 characters from the alphabet
     * "./0-9A-Za-z". Using characters outside of this range in the salt will
     * cause crypt() to return a zero-length string. The two digit cost
     * parameter is the base-2 logarithm of the iteration count for the
     * underlying Blowfish-based hashing algorithmeter and must be in range
     * 04-31, values outside this range will cause crypt() to fail.
     */
    public function blowfish($plaintext = '', $cost = 7, $salt = ''): string
    {
        if (!is_string($plaintext)) {
            $plaintext = json_encode($plaintext);
        }
        if ($salt === '') {
            $salt_chars = array_merge(
                range('A', 'Z'),
                range('a', 'z'),
                range(0, 9)
            );
            for ($i = 0; $i < 22; $i++) {
                $salt .= $salt_chars[array_rand($salt_chars)];
            }
        }
        return crypt($plaintext, sprintf('$2y$%02d$', $cost) . $salt);
    }

    /**
     * Returns the MD5 (128-bit) checksum of file
     * @param   $location   Path of file
     * @return  string
     */
    public function md5File(string $location = ''): string
    {
        return md5_file($location);
    }

    /**
     * Returns the SHA1 (160-bit) checksum of file
     * @param   $location   Path of file
     * @return  string
     */
    public function sha1File(string $location = ''): string
    {
        return sha1_file($location);
    }
}
