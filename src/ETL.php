<?php

namespace Jawn;

/**
 * Rick but with the master array replaced with a SQLite database.
 */
class ETL
{
    use Traits\OverloadTrait;

    /**
     * ETL constructor.
     */
    public function __construct()
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
    public function extract(): ETL
    {
        $args = func_get_args();

        if ($this->overload($args, ['string', 'string|array', 'string'])) {
            $type = $args[0];
            $source = $args[1];
            $options = [];
            $handle = $args[2] ?? 'morty';
        } elseif ($this->overload($args, ['string', 'string|array', 'array', 'string'])) {
            $type = $args[0];
            $source = $args[1];
            $options = $args[2] ?? [];
            $handle = $args[3] ?? 'morty';
        }

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
    public function transform(): ETL
    {
        $args = func_get_args();

        if ($this->overload($args, ['string', 'string'])) {
            $query = $args[0];
            $params = [];
            $handle = $args[1] ?? 'morty';
        } elseif ($this->overload($args, ['string', 'array', 'string'])) {
            $query = $args[0];
            $params = $args[1] ?? [];
            $handle = $args[2] ?? 'morty';
        }

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
    public function load()
    {
        $args = func_get_args();

        if ($this->overload($args, ['string', 'string|array', 'string'])) {
            $type = $args[0];
            $destination = $args[1];
            $options = [];
            $handle = $args[2] ?? 'morty';
        } elseif ($this->overload($args, ['string', 'string|array', 'array', 'string'])) {
            $type = $args[0];
            $destination = $args[1];
            $options = $args[2] ?? [];
            $handle = $args[3] ?? 'morty';
        }

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
