<?php

namespace Jawn\Remote;

/**
 * Run commands on remote server over SSH
 */
class Ssh
{
    private $_conn;

    public function __construct(object $connection)
    {
        //Establish the connection
        $this->_conn = new \phpseclib\Net\SSH2($connection->host);

        if (isset($connection->key)) {
            $privatekey = new \phpseclib\Crypt\RSA();
            $privatekey->loadKey(file_get_contents($connection->key));
            $key = $privatekey;
        } else {
            $key = $connection->password;
        }

        if (!$this->_conn->login($connection->username, $key)) {
            exit('Login Failed');
        }
    }

    /**
     * Run single command
     */
    public function command(string $cmd): string
    {
        return $this->_conn->exec($cmd);
    }
}
