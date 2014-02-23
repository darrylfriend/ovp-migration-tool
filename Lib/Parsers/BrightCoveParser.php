<?php

/**
 * Created by PhpStorm.
 * User: darryl
 * Date: 22/02/2014
 * Time: 11:31
 */

class BrightCoveParser extends Parser {

    public function parse($file) {

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

        if($this->reverseOrder){
            $this->doReverseOrder();
        }

        return $this->items;
    }
}