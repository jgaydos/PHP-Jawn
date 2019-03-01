<?php

namespace Jawn;

/**
 * Simple way to thread NTS PHP
 * Found on php.net I think
 */
class Thread
{
    private $_threads = [];
    private $_size = 1048576;
    private $_callback;

    public function __construct()
    {
        $this->_callback = function () {};
    }

    /**
     * Add a thread to queue
     * @param   object  $function   Anonymous function
     * @return  void
     */
    public function add(object $function): void
    {
        $this->_threads[] = $function;
    }

    /**
     * Set a callback
     * @param   object  $function   Anonymous function
     * @return  void
     */
    public function callback(object $function): void
    {
        $this->_callback = $function;
    }

    /**
     * Set size of shared memory
     * @param   int    $mb  Size of shared memory in MB
     * @return  void
     */
    public function size(int $mb): void
    {
        $this->_size = (pow(1024,2) * $mb);
    }

    /**
     * Run all threads in queue
     * @return  void
     */
    public function run(): void
    {
        $shared_memory_monitor = shmop_open(
            ftok(__FILE__, chr(0)),
            "c",
            0644,
            count($this->_threads)
        );
        $shared_memory_ids = (object) array();

        for ($i = 1; $i <= count($this->_threads); ++$i) {
            $shared_memory_ids->$i = shmop_open(
                ftok(__FILE__, chr($i)),
                "c",
                0644,
                $this->_size
            );
        }

        for ($i = 1; $i <= count($this->_threads); ++$i) {
            $pid = pcntl_fork();
            if (!$pid) {
                if ($i === 1)
                    usleep(100000);
                $shared_memory_data = $this->_threads[$i - 1]();
                shmop_write($shared_memory_ids->$i, $shared_memory_data, 0);
                shmop_write($shared_memory_monitor, "1", $i-1);
                exit($i);
            }
        }

        while (pcntl_waitpid(0, $status) != -1) {
            if (
                shmop_read(
                    $shared_memory_monitor,
                    0,
                    count($this->_threads)) == str_repeat("1", count($this->_threads))
            ) {
                $result = array();
                foreach ($shared_memory_ids as $key => $value) {
                    $result[$key-1] = shmop_read($shared_memory_ids->$key, 0, $this->_size);
                    shmop_delete($shared_memory_ids->$key);
                }
                shmop_delete($shared_memory_monitor);
                $callback = $this->_callback;
                $callback($result);
            }
        }
    }
}
