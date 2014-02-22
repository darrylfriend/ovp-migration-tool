<?php
/**
 * Created by PhpStorm.
 * User: darryl
 * Date: 22/02/2014
 * Time: 12:02
 */

abstract class XmlWriter {

    protected $fileName = '';
    protected $items = array();

    public function setFileName($name) {
        $this->fileName = $name;
    }

    public function setItems($items) {
        if(!is_array($items)) {
            return false;
        }
        $this->items = $items;
    }

    public function write() {

        $xml = new DOMDocument();
        $xml->formatOutput = true;
        $channel = $xml->createElement('channel');
        foreach($this->items as &$i) {
            $item = $this->createItem($xml, $i);
            $channel->appendChild($item);
        }

        $xml->appendChild($channel);
        return $xml->save($this->file);
    }

    abstract function createItem($xml, $i);
}