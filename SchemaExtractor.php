<?php
/**
 * @author  Ionut G. Stan
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

/**
 * Class to generate XML structures (as string or saved on the filesystem)
 * conforming to PHPUnit's XML data sets. Useful for generating fixtures
 * when testing database related functionality. For some usage examples see
 * the associated test file.
 */
class SchemaExtractor {
    /**
     * @var  object $descriptor
     */
    private $desc;

    /**
     * @var DOMDocument
     */
    private $dom;
    
    /**
     * Flag to mark that the $descriptor has been already exported into a DOM
     * structure. Needed so that consecutive calls to getXmlDataset() and
     * saveXmlDataset() do no interfere.
     *
     * @var boolean
     */
    private $dataProcessed = false;

    /**
     * The descriptor object must implement three methods:
     *      - getTableColumns($tableName)
     *      - getTableValues($tableName)
     *      - getTables()
     *
     * @param  object $descriptor
     * @throws InvalidArgumentException if $descriptor does not conform the
     *         duck interface.
     */
    public function __construct($descriptor) {
        $this->setDescriptor($descriptor);
        $this->setDomDocument();
    }

    /**
     * The descriptor object must implement three methods:
     *      - getTableColumns($tableName)
     *      - getTableValues($tableName)
     *      - getTables()
     *
     * @param  object $descriptor
     * @throws InvalidArgumentException if $descriptor does not conform the
     *         duck interface.
     * @return void
     */
    protected function setDescriptor($descriptor) {
        $methods = array('getTableColumns', 'getTableValues', 'getTables');

        foreach ($methods as $method) {
            if (! is_callable(array($descriptor, $method))) {
                throw new InvalidArgumentException(
                    "Descriptor object must implement method: $method"
                );
            }
        }
        $this->desc = $descriptor;
    }

    /**
     * Initializes the internal DOMDocument object
     *
     * @return void
     */
    public function setDomDocument() {
        $this->dom = new DOMDocument;
        $this->dom->formatOutput = true;
        $this->dom->loadXML('<dataset/>');
    }

    /**
     * @see    http://www.phpunit.de/manual/current/en/database.html#database.datasets.xml
     * @return string An XML representation conforming to the structure
     *         expected by PHPUnit's database testing framework
     */
    public function getXmlDataset() {
        if ($this->dataProcessed) {
            return $this->dom->saveXML();
        }

        foreach ($this->desc->getTables() as $table) {
            $this->buildTable($table);
        }

        $this->dataProcessed = true;
        return $this->dom->saveXML();
    }

    /**
     * @see    http://www.phpunit.de/manual/current/en/database.html#database.datasets.xml
     * @param  string $filePath
     * @return void   May issues an warning if unable to write to file
     */
    public function saveXmlDataset($filePath) {
        if ($this->dataProcessed) {
            return $this->dom->save($filePath);
        }

        foreach ($this->desc->getTables() as $table) {
            $this->buildTable($table);
        }

        $this->dataProcessed = true;
        return $this->dom->save($filePath);
    }

    /**
     * @param  string $tableName
     * @return void
     */
    protected function buildTable($tableName) {
        $domTable = $this->dom->createElement('table');
        $domTable->setAttribute('name', $tableName);

        $this->buildColumns($domTable, $tableName);
        $this->buildRows($domTable, $tableName);

        $this->dom->documentElement->appendChild($domTable);
    }

    /**
     * @param  DOMElement $domTable
     * @param  string     $tableName
     * @return void
     */
    protected function buildColumns($domTable, $tableName) {
        foreach ($this->desc->getTableColumns($tableName) as $column) {
            $domColumn = $this->dom->createElement('column');
            $domColumn->appendChild($this->dom->createTextNode($column));

            $domTable->appendChild($domColumn);
        }
    }

    /**
     * @param  DOMElement $domTable
     * @param  string     $tableName
     * @return void
     */
    protected function buildRows($domTable, $tableName) {
        foreach ($this->desc->getTableValues($tableName) as $row) {
            $domRow = $this->dom->createElement('row');

            $this->buildRow($domRow, $row);

            $domTable->appendChild($domRow);
        }
    }

    /**
     * @param  DOMElement $domRow
     * @param  iterable   $row
     * @return void
     */
    protected function buildRow($domRow, $row) {
        foreach ($row as $value) {
            $domValue = $this->getFieldValue($value);
            $domRow->appendChild($domValue);
        }
    }

    /**
     * PHPUnit represents SQL NULL values with a custum <null/> element.
     *
     * @see    http://www.phpunit.de/manual/current/en/database.html#database.tables.xmldataset.elements
     * @param  null|string $value
     * @return DOMElement
     */
    protected function getFieldValue($value) {
        if ($value === null) {
            $domValue = $this->dom->createElement('null');
        } else {
            $domValue = $this->dom->createElement('value');
            $domValue->appendChild($this->dom->createTextNode($value));
        }

        return $domValue;
    }
}

/**
 * This is a convenience method for avoiding things like:
 *
 *      $extractor = new SchemaExtractor($descriptor);
 *      $extractor->getXmlDataset();
 *
 * Using this function we can write the above like this:
 *
 *      $extractor = SchemaExtractor($descriptor)->getXmlDataset();
 *
 * The descriptor object must implement three methods:
 *      - getTableColumns($tableName)
 *      - getTableValues($tableName)
 *      - getTables()
 *
 * @param  object $descriptor
 * @throws InvalidArgumentException if $descriptor does not conform the
 *         duck interface.
 * @return SchemaExtractor
 */
function SchemaExtractor($descriptor) {
    return new SchemaExtractor($descriptor);
}
