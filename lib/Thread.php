<?php

/**
 * Simple way to thread NTS PHP
 */
class Thread
{
    private $threads = [];
    private $size = 1048576;
    private $callback;

    /**
     * Add a thread to queue
     */
    public function add(object $function): void
    {
        $this->threads[] = $function;
    }

    /**
     * Set a callback
     */
    public function callback(object $function): void
    {
        $this->callback = $function;
    }

    /**
     * Set size of shared memory
     */
    public function size(int $mb): void
    {
        $this->size = (pow(1024,2) * $mb);
    }

    /**
     * Run all threads in queue
     */
    public function run(): void
    {
        $shared_memory_monitor = shmop_open(
            ftok(__FILE__, chr(0)),
            "c",
            0644,
            count($this->threads)
        );
        $shared_memory_ids = (object) array();

        for ($i = 1; $i <= count($this->threads); ++$i) {
            $shared_memory_ids->$i = shmop_open(
                ftok(__FILE__, chr($i)),
                "c",
                0644,
                $this->size
            );
        }

        for ($i = 1; $i <= count($this->threads); ++$i) {
            $pid = pcntl_fork();
            if (!$pid) {
                if ($i === 1)
                    usleep(100000);
                $shared_memory_data = $this->threads[$i - 1]();
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
                    count($this->threads)) == str_repeat("1", count($this->threads))
            ) {
                $result = array();
                foreach ($shared_memory_ids as $key => $value) {
                    $result[$key-1] = shmop_read($shared_memory_ids->$key, 0, $this->size);
                    shmop_delete($shared_memory_ids->$key);
                }
                shmop_delete($shared_memory_monitor);
                $callback = $this->callback;
                $callback($result);
            }
        }
    }

}
