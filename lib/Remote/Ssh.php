<?php
namespace Remote;

/**
 * Run commands on remote server over SSH
 */
class Ssh
{
    private $conn;

    public function __construct(object $connection)
    {
        //Establish the connection
        $this->conn = new \phpseclib\Net\SSH2($connection->host);

        if (isset($connection->key)) {
            $privatekey = new \phpseclib\Crypt\RSA();
            $privatekey->loadKey(file_get_contents($connection->key));
            $key = $privatekey;
        } else {
            $key = $connection->password;
        }

        if (!$this->conn->login($connection->username, $key)) {
            exit('Login Failed');
        }
    }

    /**
     * Run single command
     */
    public function command(string $cmd): string
    {
        return $this->conn->exec($cmd);
    }
}
