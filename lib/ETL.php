<?php

/**
 * Rick but with the master array replaced with a SQLite database.
 */
class ETL
{
    /**
     * ETL constructor.
     *
     * @param string $conf
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        return $this;
    }

    /**
     * Extract data
     *
     * @access  public
     * @param string $table
     * @param string $type
     * @param string $source
     * @param array $options
     * @return ETL
     */
    public function extract(string $type, $source, string $handle = 'morty', array $options = []): ETL
    {
        Console::info("E -> $type as $handle", '');

        if ($type === 'array') {
            Coffer::set($source, $handle);
            Console::success('...Wubbalubbadubdub!');
            return $this;
        }

        $class = 'Io\\'.$type;
        if (!class_exists($class)) {
            Console::danger("...I am in great pain, Please help me: $type does not exist!");
        }
        Coffer::set($class::extract($source, $options), $handle);

        //Console::danger('...I am in great pain, please help me: No Data');
        Console::success('...Wubbalubbadubdub!');
        return $this;
    }

    /**
     * Transform data that has been extracted before it is loaded anywhere.
     *
     * @access  public
     * @param string $query
     * @param array $params
     * @param string $handle
     * @return ETL
     */
    public function transform(string $query, array $params = [], string $handle = 'morty'): ETL
    {
        Console::info("T -> ".($handle !== '' ? "$handle = " : '')."(".substr(str_replace(["\r", "\n", ' '], ' ', $query), 0, 30)."...)", '');
        Coffer::query($query, $params, $handle);
        Console::success('...Wubbalubbadubdub!');
        return $this;
    }

    /**
     * Load data into a variety of different formatts and places.
     *
     * @access  public
     * @param string $type
     * @param string $destination
     * @param array $options
     * @param string $handle
     * @return ETL
     */
    public function load(string $type, $destination, string $handle = 'morty', array $options = [])
    {
        Console::info("L -> $type $destination".($handle !== '' ? " with $handle" : ''), '');

        // array
        if ($type === 'array') {
            return Coffer::get($handle);
        }

        // everything else
        $class = 'Io\\'.ucfirst($type);
        if (!class_exists($class)) {
            Console::danger("...I am in great pain. Please help me, $type does not exist!");
        }
        $class::load($destination, Coffer::get($handle), $options);

        Console::success('...Wubbalubbadubdub!');
        return $this;
    }
}
