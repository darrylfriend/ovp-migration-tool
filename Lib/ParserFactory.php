<?php
namespace \lib;

/**
 * Created by PhpStorm.
 * User: darryl
 * Date: 22/02/2014
 * Time: 11:56
 */

class ParserFactory {

    /**
     * Determine which parser is required for this file
     *
     *  - Brightcove
     * 	- Kaltura
     * 	- XXX - Todo, bring in support for other online video platforms
     *
     * @param $file
     * @return bool
     * @throws RuntimeException
     */
    public function getParser($file) {

        list($protocol, $filename) = explode('://',$file);

        if ($protocol !== 'file' || !file_exists($filename)) {
            throw new \RuntimeException('File does not exist: '.$file);
        }

        // Only XML files are know for import, export.
        // XXX - Possibly may need to rewrite this if options for csv or similar are provided by other providers
        $ext =  strtolower(findFileExtension($filename));
        if ($ext !== 'xml') {
            throw new \RuntimeException('Unsupported file type: '.$ext);
        }

        // Get file contents
        $input = Parser::getFileContents($filename);

        // Identify file type
        $type = false;
        if (strpos($input,'</generator>') !== false && strpos($input,'http://www.brightcove.com/?v=1.0') !== false) {
            $type = 'brightcove';
        } elseif (false) {
            // - XXX
            // Add support for Kaltura
            $type = 'kaltura';
        } else {
            // XXX - Bring in SUPPORT for other providers
            throw new \RuntimeException('Unsupported file import type: '.$filename);
        }

        // Load the appropriate parser
        if ($type != false) {
            $obj = $this->loadParserInstance($type);
            if ($obj) {
                // Set file but don't reload
                $obj->setFile($file, false);
                $obj->setInput($input);
            }
        } else {
            $obj = false;
        }

        return $obj;
    }

    public function loadParserInstance($type) {
        $className = __NAMESPACE__.'\\Parsers\\'.camelize($type).'Parser';
        if (class_exists($className)) {
            return new $className();
        } else {
            throw new \RuntimeException('Could not find parser of type: '.$type);
        }
    }
}