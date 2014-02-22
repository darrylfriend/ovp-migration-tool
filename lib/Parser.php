<?php

/**
 * Class Parser
 */
abstract class Parser {

    /**
     * The location of the input file
     */
    protected $input = '';

    /**
     * Array of items read from the input document
     */
    protected $items = array();

    /**
     * If true, this will reverse the writable order.
     *  Most exported files give you a date ascending list.
     *
     *  By setting this to true, your import list will be date Desc, meaning the most recent videos are processed first.
     */
    protected $reverseOrder = true;

    /**
     * Mains a trace list of actions for your reference
     */
    protected $debug = array();

    /**
     * Set reverse order for writing the import file
     *
     * @param boolean $bool
     * @throws Exception if $status is not Boolean
     */
    public function setReverseOrder($bool) {
        if($bool !== true && $bool !== false){
            $this->debug[] = 'Could not set reverse order, param type was set to: '.$bool.', expected Bool';
            throw new Exception();
        }
        $this->reverseOrder = $bool;
    }

    /**
     * Returns the last logged error
     * @return boolean
     */
    public function getDebug() {
        return $this->debug;
    }

    /**
     * Sets the input file
     *
     * @param $input
     */
    public function setInput($input) {
        $this->input = $input;
    }

    /**
     * Returns the contents of a file
     * @param string $filename
     * @return boolean|string
     */
    public function getFileContents($filename) {

        if (!file_exists($filename)) {
            $this->debug[] = 'File does not exist: '.$filename;
            return false;
        }

        $input = file_get_contents($filename);
        if (empty($input)) {
            $this->debug[] = 'File is empty: '.$filename;
            return false;
        }

        return $input;
    }

    /**
     * Parse will prepare the items
     *
     * @param unknown $file
     * @return void|boolean
     */
    abstract public function parse($file);

    /**
     * @param $items
     * @param $file
     * @return int
     */
    public function write($items, $file) {

        $xml = new DOMDocument();
        $xml->formatOutput = true;
        $channel = $xml->createElement('channel');
        foreach($items as &$i) {
            $item = $this->createItem($xml, $i);
            $channel->appendChild($item);
        }

        $xml->appendChild($channel);
        return $xml->save($file);
    }

    /**
     *
     * @return number
     */
    protected function doReverseOrder(){

        // Sort items to be ordered date ascending
        usort($this->items, function($a, $b) {
            if ($a['date'] == $b['date']) return 0;
            return ($a['date'] < $b['date']) ? -1 : 1;
        });
    }

    /**
     * Returns an XML element value
     *
     * @param String $document
     * @param String $tagName
     * @return String
     */
    protected function getElementValue($document,$tagName) {
        $elements = $document->getElementsByTagName($tagName);
        if ($elements->length == 0) return false;
        return $elements->item(0)->nodeValue;
    }

}