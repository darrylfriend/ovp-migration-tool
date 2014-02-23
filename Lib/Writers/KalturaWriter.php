<?php

require_once('Lib/Writer.php');

class KalturaWriter extends Writer {

    /**
     * Write the contents to a file
     *
     * @return int|mixed
     */
    public function write() {

        echo 'KalturaWriter: Starting document creation<br>';

        $xml = new DOMDocument();
        if ($this->formatXml) {
            $xml->formatOutput = true;
        }

        echo 'KalturaWriter: Writing to doc<br>';
        $channel = $xml->createElement('channel');

        // Loop over items writing them to the doc
        foreach($this->items as &$i) {
            $item = $this->createItem($xml, $i);
            $channel->appendChild($item);
        }

        // Append the XML to the doc
        $xml->appendChild($channel);
        echo 'KalturaWriter: Doc writing complete<br>';

        // Save the XML document
        return $xml->save($this->fileName);
    }

    /**
     * Create an XML element
     *
     * @param $xml
     * @param $i
     * @return mixed
     */
    public function createItem($xml, $i) {

        // Item
        $item = $xml->createElement('item');

        // Action
        $child = $xml->createElement('action');
        $child->nodeValue = 'add';
        $item->appendChild($child);

        // Type
        $child = $xml->createElement('type');
        $child->nodeValue = '1';
        $item->appendChild($child);

        // Name
        $child = $xml->createElement('name');
        $child->nodeValue = $i['title'];
        $item->appendChild($child);

        // Description
        $child = $xml->createElement('description');
        $child->nodeValue = $i['description'];
        $item->appendChild($child);

        // Tags
        if (isset($i['tags']) && !empty($i['tags'])) {
            $child = $xml->createElement('tags');
            foreach($i['tags'] as $t) {
                $tag = $xml->createElement('tag');
                $tag->nodeValue = $t;
                $child->appendChild($tag);
            }
            $item->appendChild($child);
        }

        // Categories
        if (isset($i['category']) && !empty($i['category'])) {
            $child = $xml->createElement('categories');
            foreach($i['category'] as $t) {
                $tag = $xml->createElement('category');
                $tag->nodeValue = $t;
                $child->appendChild($tag);
            }
            $item->appendChild($child);
        }

        // Media Type
        $child = $xml->createElement('media');
        $grandChild = $xml->createElement('mediaType');
        $grandChild->nodeValue = '1';
        $child->appendChild($grandChild);
        $item->appendChild($child);

        // Content Assets
        $child = $xml->createElement('contentAssets');
        $grandChild = $xml->createElement('content');
        $greatGrandChild = $xml->createElement('urlContentResource');

        // URL attribute
        $urlAttribute = $xml->createAttribute('url');
        $urlAttribute->value = $i['video'];
        $greatGrandChild->appendChild($urlAttribute);
        $grandChild->appendChild($greatGrandChild);
        $child->appendChild($grandChild);
        $item->appendChild($child);

        // Thumbnail
        $child = $xml->createElement('thumbnails');
        $default = true;
        foreach($i['thumbnails'] as $key => $t) {

            $grandChild = $xml->createElement('thumbnail');

            // Set default if first
            if($default === true) {
                $defaultAttribute = $xml->createAttribute('isDefault');
                $defaultAttribute->value = 'true';
                $grandChild->appendChild($defaultAttribute);
                $default = false;
            }

            $greatGrandChild = $xml->createElement('urlContentResource');

            // Thumbnail URL attribute
            $urlAttribute = $xml->createAttribute('url');
            $urlAttribute->value = $t;
            $greatGrandChild->appendChild($urlAttribute);
            $grandChild->appendChild($greatGrandChild);
            $child->appendChild($grandChild);
        }
        $item->appendChild($child);

        // Todo - Include start date on output
        /*$child = $xml->createElement('startDate');
        $child->nodeValue = $i['pubDate'];
        $item->appendChild($child);

        // Todo - Include end date on output
        $child = $xml->createElement('endDate');
        $child->nodeValue = date('Y-m-d H:i:s',$i['pubDate']);*/

        $item->appendChild($child);

        return $item;
    }

}