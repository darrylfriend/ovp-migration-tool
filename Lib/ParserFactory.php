<?php

require_once('Lib/Parser.php');
require_once('Lib/Parsers/BrightCoveParser.php');
require_once('Lib/Parsers/KalturaParser.php');

class ParserFactory {

    /**
     * Determine which parser is required for this file
     *
     *  - Brightcove
     * 	- Kaltura
     * 	- Todo, bring in support for other online video platforms
     *
     * @param $filename
     * @return bool
     * @throws \RuntimeException
     */
    public function getParser($filename) {

        if (!file_exists($filename)) {
            echo 'ParseFactory: File does not exist: '.$filename.'<br>';
            return false;
        }

        // Todo - Possibly may need to rewrite this if csv or similar outputs are provided by other providers
        $ext =  strtolower($this->findFileExtension($filename));
        if ($ext !== 'xml') {
            echo 'ParseFactory: Unsupported file type: '.$ext.'<br>';
            return false;
        }

        // Get file contents
        $input = Parser::getFileContents($filename);

        // Identify file type
        $type = false;
        if (strpos($input,'</generator>') !== false && strpos($input,'http://www.brightcove.com/?v=1.0') !== false) {
            $type = 'BrightCove';
        } elseif (false) {
            // Todo - Add support for Kaltura
            $type = 'Kaltura';
        } else {
            // Todo - Bring in SUPPORT for other providers
            echo 'ParseFactory: Unsupported file import type: '.$filename.'<br>';
            return false;
        }

        // Load the appropriate parser
        if ($type != false) {
            $obj = $this->loadParserInstance($type);
            if ($obj) {
                // Set input content to parse
                $obj->setInput($input);
            }
        } else {
            echo 'ParseFactory: Could not get parser instance: '.$type.'<br>';
            return false;
        }

        return $obj;
    }

    public function loadParserInstance($type) {

        $className = $type.'Parser';
        echo 'ParseFactory: Looking for classname - '.$className.'<br>';

        if (class_exists($className)) {
            echo 'ParseFactory: Found class name: '.$className.'<br>';
            return new $className();
        } else {
            echo 'ParseFactory: Could not find parser of type: '.$type.'<br>';
            return false;
        }
    }

    protected function findFileExtension ($filename) {
        $filename = explode('.',$filename);
        $length = count($filename);
        $extension = $filename[$length-1];
        return $extension;
    }
}