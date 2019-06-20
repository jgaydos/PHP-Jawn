<?php

namespace Jawn\Io;

/**
 * You put it in and take it out
 */
class Xml
{
    /**
     * Extract or read from source and store as an array
     *
     * @access  public
     * @param   string  $path   Source
     * @param   string  $options   Extract options
     * @return  array
     */
    public static function extract(string $path, array $options = []): array
    {
		$xmlObject = simplexml_load_file($path);
		$json = json_encode($xmlObject);
        $array = json_decode($json, true);
        return array_values($array);
    }

    /**
     * Load or write array to destination
     *
     * @access  public
     * @param   string  $path   Destination
     * @param   array   $data   Data to save
     * @param   string  $options   Loas options
     * @return  array
     */
    public static function load(string $path, array $data, array $options = []): void
    {
        $xml = new \XMLWriter();

        $xml->openURI($path);
        $xml->startDocument();
        $xml->setIndent(true);

        $xml->startElement('rows');
        foreach ($data as $key => $row) {
            $xml->startElement("row{$key}");
            foreach ($row as $column => $value) {
                $xml->startElement($column);
                $xml->writeRaw($value);
                $xml->endElement();
            }
            $xml->endElement();
        }
        $xml->endElement();
        $xml->flush();
    }
}
