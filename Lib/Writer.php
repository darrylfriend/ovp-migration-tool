<?php

abstract class Writer {

    protected $fileName = '';
    protected $items = array();
    protected $formatXml = false;

    /**
     * Set the output file name
     *
     * @param $name
     */
    public function setFileName($name) {
        $this->fileName = $name;
    }

    /**
     * Sets formatXml output for easy file reading
     *
     * @param $bool
     * @return bool
     */
    public function setFormatXml($bool) {
        if (!(is_bool($bool))) {
            echo 'Could not set formatOutput';
            return false;
        }
        $this->formatXml = $bool;
    }

    /**
     * Set items to write to file
     *
     * @param $items
     * @return bool
     */
    public function setItems($items) {
        if(!is_array($items)) {
            return false;
        }
        $this->items = $items;
    }

    /**
     * Write file contents
     *
     * @return mixed
     */
    abstract public function write();

}