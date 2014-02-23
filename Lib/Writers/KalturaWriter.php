<?php

/**
 * Created by PhpStorm.
 * User: darryl
 * Date: 22/02/2014
 * Time: 11:31
 */

class KalturaWriter extends XmlWriter {

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
        $child = $xml->createElement('tags');
        foreach($i['tags'] as $t) {
            $tag = $xml->createElement('tag');
            $tag->nodeValue = $t;
            $child->appendChild($tag);
        }
        $item->appendChild($child);

        // Categories
        $child = $xml->createElement('categories');
        foreach($i['category'] as $t) {
            $tag = $xml->createElement('category');
            $tag->nodeValue = $t;
            $child->appendChild($tag);
        }
        $item->appendChild($child);

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

        // StartDate
        /*$child = $xml->createElement('startDate');
        $child->nodeValue = $i['pubDate'];
        $item->appendChild($child);

        // EndDate
        $child = $xml->createElement('endDate');
        $child->nodeValue = date('Y-m-d H:i:s',$i['pubDate']);*/


        $item->appendChild($child);

        return $item;
    }

}