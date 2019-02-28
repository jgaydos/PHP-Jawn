<?php

namespace Jawn\Remote;

/**
 * Get files from and Put files on remote server
 * @author Jon Gaydos
 */
class Sftp
{
    private $_conn;

    public function __construct(object $connection)
    {
        //Establish the connection
        $this->_conn = new \phpseclib\Net\SFTP($connection->host);

        if (isset($connection->key)) {
            $privatekey = new \phpseclib\Crypt\RSA();
            $privatekey->loadKey(file_get_contents($connection->key));
            $key = $privatekey;
        } else {
            $key = $connection->password;
        }

        if (!$this->_conn->login($connection->username, $key))
            throw new SftpConnectionException('Login Failed');
    }

    /**
     * Get file from remote server
     * @param $remote	string	Remote file path
     * @param $local	string	Local file path
     * @return bool	uploaded (true); not uploaded (false)
     */
    public function get(string $source, string $target): bool
    {
        return $this->_conn->get($source, $target);

        /* // this also works
        $conn = ssh2_connect($host, $port);
        ssh2_auth_password($conn, $username, $password);
        return ssh2_scp_recv($conn, $source, $target);
        */
    }

    /**
     * Put file on remote server
     * @param $local	string	Local file path
     * @param $remote	string	Remote file path
     * @return bool	downloaded (true); not downloaded (false)
     */
    public function put(string $source, string $target): bool
    {
        return $this->_conn->put($target, $source, 1 /** 1 makes it file */);
    }

    /**
     * Writes a string to a file on remote server
     * @param $local	string	Local file path
     * @param $remote	string	Remote file path
     * @return bool
     */
    public function write(string $source, string $target): bool
    {
        return $this->_conn->put($target, $source);
    }

}
