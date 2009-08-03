<?php

require 'SchemaExtractor.php';

class SchemaExtractorTest extends PHPUnit_Framework_TestCase {
    /**
     * @expectedException InvalidArgumentException
     */
    public function testDuckTyping() {
        new SchemaExtractor(new StdClass);
    }

    public function testBasicUsage() {
        $result = SchemaExtractor(new BasicUsageDescriptorStub)->getXmlDataset();
        $this->assertXmlStringEqualsXmlFile('./fixtures/basic-usage.xml', $result);
    }

    /**
     * Tests that the extractor generates <null/> elements for SQL NULL fields.
     */
    public function testWorksWithNullColumns() {
        $result = SchemaExtractor(new NullColumnsDescriptorStub)->getXmlDataset();
        $this->assertXmlStringEqualsXmlFile('./fixtures/null-columns.xml', $result);
    }

    /**
     * Tests that consecutive calls to getXmlDataset() and saveXmlDataset()
     * don't interfere due to inner object state.
     *
     * This test method is dependent up the vfsStream library in order to mock
     * the filesystem.
     *
     * @see http://www.phpunit.de/manual/current/en/test-doubles.html#test-doubles.mocking-the-filesystem
     */
    public function testConsecutiveCallsToGetAndSaveXmlDatasetDonNotInterfere() {
        if (! $this->loadVfsStreamClass()) {
            $this->fail('This test method is dependent up the vfsStream library.');
        }

        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamFile('schema.xml'));
        $xmlSavePath = vfsStream::url('schema.xml');

        $extractor = SchemaExtractor(new BasicUsageDescriptorStub);
        $result    = $extractor->getXmlDataset();
        $extractor->saveXmlDataset($xmlSavePath);

        $this->assertXmlStringEqualsXmlFile('./fixtures/basic-usage.xml', $result);
        $this->assertXmlFileEqualsXmlFile('./fixtures/basic-usage.xml', $xmlSavePath);
    }

    /**
     * Tries to load the vfsStream library. Returns TRUE if operation was
     * successful, FALSE otherwise.
     *
     * @return boolean
     */
    protected function loadVfsStreamClass() {
        @include_once 'vfsStream/vfsStream.php';

        if (class_exists('vfsStream', false)) {
            return true;
        }

        return false;
    }
}

class BasicUsageDescriptorStub {
    public function getTableColumns($table) {
        switch ($table) {
            case 'foo':
                return array('xxx', 'yyy', 'zzz');
            case 'bar':
                return array('aaa', 'bbb', 'ccc');
        }
    }

    public function getTableValues($table) {
        switch ($table) {
            case 'foo':
                return array(
                    array('xxx_1', 'yyy_1', 'zzz_1'),
                    array('xxx_2', 'yyy_2', 'zzz_2'),
                );
            case 'bar':
                return array(
                    array('aaa_1', 'bbb_1', 'ccc_1'),
                    array('aaa_2', 'bbb_2', 'ccc_2'),
                );
        }
    }

    public function getTables() {
        return array('foo', 'bar');
    }
}

class NullColumnsDescriptorStub {
    public function getTableColumns($table) {
        switch ($table) {
            case 'foo':
                return array('xxx', 'yyy', 'zzz');
            case 'bar':
                return array('aaa', 'bbb', 'ccc');
        }
    }

    public function getTableValues($table) {
        switch ($table) {
            case 'foo':
                return array(
                    array('xxx_1', 'yyy_1', 'zzz_1'),
                    array('xxx_2', 'yyy_2', null),
                );
            case 'bar':
                return array(
                    array('aaa_1', 'bbb_1', 'ccc_1'),
                    array('aaa_2', null, 'ccc_2'),
                );
        }
    }

    public function getTables() {
        return array('foo', 'bar');
    }
}
