<?php

require('Lib/ParserFactory.php');
require('Lib/WriterFactory.php');

class Migrate {

    const BRIGHTCOVE = 'BrightCove';
    const KALTURA = 'Kaltura';

    protected $inputFile = null;
    protected $items = array();

    protected $outputFileName = 'migrationOutput.xml';
    protected $outputType = null;
    public $formatXml = false;

    protected $limit = false;
    protected $orderDesc = true;

    private $parserFactory = null;
    private $writerFactory = null;

    /**
     * Instantiate the parser and writer factories
     */
    public function __construct() {
        $this->parserFactory = new ParserFactory();
        $this->writerFactory = new WriterFactory();
    }

    /**
     * Set Input File Name
     *
     * @param $file
     */
    public function setFile($file) {
        $this->inputFile = $file;
    }

    /**
     * Set the output file type
     *
     * @param $type
     * @return bool
     */
    public function setOutputType($type) {

        switch ($type) {
            case self::BRIGHTCOVE:
            case self::KALTURA:
                $this->outputType = $type;
                break;
            default:
                echo 'Migrate: Invalid Type<br>';
                return false;
        }
    }

    /**
     * Sets the name of the output file
     *
     * @param $name
     */
    public function setOutputFileName($name) {
        $this->outputFileName = $name;
    }

    /**
     * Order the parsed items by date descending
     * @param $bool
     * @return bool
     */
    public function setOrderDesc($bool) {

        if (!is_bool($bool)) {
            echo 'Migrate: Could not set order desc<br>';
            return false;
        }

        $this->orderDesc = $bool;
    }
    /**
     * Set the number of input items to write to the output file
     *
     * @param $limit
     * @return bool
     */
    public function setLimit($limit) {

        // Validate we have an int
        if (!is_int($limit)) {
            echo 'Migrate: Could not set limit<br>';
            return false;
        }
        $this->limit = $limit;
    }

    /**
     * Performs the migrate function.
     *
     * 1) Will perform a Parse
     * 2) Validate contents of the parse
     * 3) Write the contents to the requested output type
     *
     * @return bool
     */
    public function go() {

        echo 'Migrate: Executing Migrate<br>';

        // Validate we have a file
        if (!$this->inputFile) {
            echo 'Migrate: No input file specified<br>';
            return false;
        }

        echo 'Migrate: Exported File - '.$this->inputFile.'<br>';

        // Parse
        $this->parse();

        if (empty($this->items)) {
            echo 'Migrate: No items found<br>';
            return false;
        }

        // Write
        $this->write();
    }

    /**
     * Parses the file.
     * Uses the parserFactory to get the correct parser and parse the file
     *
     * @return bool
     */
    protected function parse() {

        echo 'Migrate: Executing Parse:'.$this->inputFile.'<br>';
        $parser = $this->parserFactory->getParser($this->inputFile);

        if (!$parser) {
            echo 'Migrate: Could not parse file<br>';
            return false;
        }

        // Parse all items
        $this->items = $parser->parse();

        // Order the items if required
        if ($this->orderDesc) {
            $parser->orderDesc();
        }

        echo 'Migrate: Found '.count($this->items).' item(s)<br>';

        // Limit items returned if required
        if ($this->limit) {
            $this->items = array_slice($this->items, 0, $this->limit);
            echo 'Migrate: Limited returned results to: '.count($this->items).' item(s)<br>';
        }

        return true;
    }

    /**
     * Writes the parsed content to a file
     * Uses the writerFactory to get the correct writer and write the file
     *
     * @return bool
     */
    protected function write() {

        if (empty($this->items)) {
            echo 'Migrate: No items found to write<br>';
            return false;
        }

        if (!$this->outputType) {
            echo 'Migrate: No output type set<br>';
            return false;
        }

        // Write the file
        $writer = $this->writerFactory->loadWriterInstance($this->outputType);
        $writer->setItems($this->items);
        $writer->setFileName($this->outputFileName);
        $writer->setFormatXml($this->formatXml);

        $result = $writer->write();
        if ($result) {
            echo 'Migrate: File created - '.$this->outputFileName.'<br>';
        }
    }
}
