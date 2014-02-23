<?php

require_once('Lib/Writer.php');
require_once('Lib/Writers/BrightCoveWriter.php');
require_once('Lib/Writers/KalturaWriter.php');

class WriterFactory {

    public function loadWriterInstance($type) {
        $className = $type.'Writer';
        echo 'WriterFactory: Looking for classname - '.$className.'<br>';
        if (class_exists($className)) {
            return new $className();
        } else {
            echo 'ParseFactory: Could not find writer of type: '.$type.'<br>';
            return false;
        }
    }
    
}