<?php

require_once('Lib/Parser.php');

class BrightCoveParser extends Parser {

    /**
     * @return array|bool
     */
    public function parse() {

        echo 'BrightCoveParser: Parsing File<br>';

        // Ensure we have content
        if (empty($this->input)) {
            echo 'BrightCoveParser: No Content<br>';
            return false;
        }

        // Load xml
        $doc = new DOMDocument();
        if (!$doc->loadXML($this->input)) {
            $err = libxml_get_last_error();
            if ($err === false) {
                echo 'BrightCoveParser: Could not load XML into DOMDocument<br>';
            } else {
                echo 'BrightCoveParser'.$err->message.'<br>';
            }
            return false;
        }

        return $this->parseDocument($doc);
    }

    protected function parseDocument(DOMDocument $doc) {

        // Get BC items
        $items = $doc->getElementsByTagName('item');

        // BC details
        $details = array('title', 'description');

        foreach($items as $key => $item) {

            // Grab Item Details
            foreach($details as $d){
                $this->items[$key][$d] = self::getElementValue($item, $d);
            }

            $date = self::getElementValue($item, 'pubDate');
            $timestamp = strtotime($date);

            $this->items[$key]['pubDate'] = $timestamp;

            $source_video = false;
            $highest_quality = 0;

            $ids = $item->getElementsByTagNameNS('http://www.brightcove.tv/link','titleid');
            foreach($ids as $index => $id) {
                if($index > 0){
                    break;
                }
                $this->items[$key]['id'] = $id->nodeValue;
            }

            $videos = $item->getElementsByTagNameNS('http://search.yahoo.com/mrss/','content');
            foreach($videos as $v) {

                if($v->hasAttribute('bitrate')) {
                    if($v->getAttribute('bitrate') > $highest_quality){

                        // Store video
                        if($v->hasAttribute('url')) {
                            $source_video = $v->getAttribute('url');
                            $highest_quality = $v->getAttribute('bitrate');
                        }else{
                            echo 'Could not get video url';
                        }
                    }
                }
            }

            // Thumbnails
            $thumbnails = $item->getElementsByTagNameNS('http://search.yahoo.com/mrss/','thumbnail');
            foreach($thumbnails as $t) {
                if($t->hasAttribute('url')) {
                    $this->items[$key]['thumbnails'][] = $t->getAttribute('url');
                }
            }

            if($source_video === false) {
                echo 'Error locating a source video';
            }else{
                $this->items[$key]['video'] = $source_video;
            }

        }

        return $this->items;
    }

    /**
     *
     * @return number
     */
    public function orderDesc(){

        // Sort items to be ordered date ascending
        usort($this->items, function($a, $b) {
            if ($a['pubDate'] == $b['pubDate']) return 0;
            return ($a['pubDate'] < $b['pubDate']) ? -1 : 1;
        });
    }

}