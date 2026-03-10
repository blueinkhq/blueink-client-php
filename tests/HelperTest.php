<?php

use PHPUnit\Framework\TestCase;
use Blueink\ClientSDK\Helper;

/**
 * @covers \Blueink\ClientSDK\Helper
 */
class HelperTest extends TestCase
{
    /**
     * Test generateKey creates a key with correct format
     */
    public function testGenerateKeyFormat()
    {
        $key = Helper::generateKey('test', 5);
        $this->assertStringStartsWith('test_', $key);
        $this->assertEquals(10, strlen($key)); // 'test_' (5) + 5 chars
    }

    /**
     * Test generateKey with different lengths
     */
    public function testGenerateKeyDifferentLengths()
    {
        $key3 = Helper::generateKey('bundle', 3);
        $this->assertStringStartsWith('bundle_', $key3);
        $this->assertEquals(10, strlen($key3)); // 'bundle_' (7) + 3 chars

        $key10 = Helper::generateKey('doc', 10);
        $this->assertStringStartsWith('doc_', $key10);
        $this->assertEquals(14, strlen($key10)); // 'doc_' (4) + 10 chars
    }

    /**
     * Test generateKey produces different keys
     */
    public function testGenerateKeyUniqueness()
    {
        $key1 = Helper::generateKey('packet', 5);
        $key2 = Helper::generateKey('packet', 5);
        $this->assertNotEquals($key1, $key2);
    }

    /**
     * Test mergeAdditionalData with empty arrays
     */
    public function testMergeAdditionalDataEmpty()
    {
        $result = Helper::mergeAdditionalData([], []);
        $this->assertEquals([], $result);
    }

    /**
     * Test mergeAdditionalData merges arrays correctly
     */
    public function testMergeAdditionalDataMerge()
    {
        $data = ['key1' => 'value1', 'key2' => 'value2'];
        $additional = ['key3' => 'value3', 'key4' => 'value4'];
        $result = Helper::mergeAdditionalData($data, $additional);
        
        $expected = [
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'value3',
            'key4' => 'value4'
        ];
        $this->assertEquals($expected, $result);
    }

    /**
     * Test mergeAdditionalData overwrites existing keys
     */
    public function testMergeAdditionalDataOverwrite()
    {
        $data = ['key1' => 'original', 'key2' => 'value2'];
        $additional = ['key1' => 'overwritten'];
        $result = Helper::mergeAdditionalData($data, $additional);
        
        $this->assertEquals('overwritten', $result['key1']);
        $this->assertEquals('value2', $result['key2']);
    }

    /**
     * Test mergeAdditionalData throws exception for non-array data
     */
    public function testMergeAdditionalDataThrowsExceptionForNonArrayData()
    {
        $this->expectException(\TypeError::class);
        Helper::mergeAdditionalData('not an array', []);
    }

    /**
     * Test mergeAdditionalData throws exception for non-array additional_data
     */
    public function testMergeAdditionalDataThrowsExceptionForNonArrayAdditional()
    {
        $this->expectException(\TypeError::class);
        Helper::mergeAdditionalData([], 'not an array');
    }

    /**
     * Test removeNullProperties removes null values
     */
    public function testRemoveNullPropertiesRemovesNulls()
    {
        $obj = (object)[
            'name' => 'John',
            'email' => null,
            'phone' => '123-456-7890',
            'address' => null
        ];
        
        $result = Helper::removeNullProperties($obj);
        
        $this->assertEquals('John', $result->name);
        $this->assertEquals('123-456-7890', $result->phone);
        $this->assertFalse(property_exists($result, 'email'));
        $this->assertFalse(property_exists($result, 'address'));
    }

    /**
     * Test removeNullProperties with all null properties
     */
    public function testRemoveNullPropertiesAllNull()
    {
        $obj = (object)[
            'field1' => null,
            'field2' => null,
            'field3' => null
        ];
        
        $result = Helper::removeNullProperties($obj);
        
        $this->assertIsObject($result);
        $this->assertEquals(0, count((array)$result));
    }

    /**
     * Test removeNullProperties with no null properties
     */
    public function testRemoveNullPropertiesNoNulls()
    {
        $obj = (object)[
            'name' => 'Jane',
            'email' => 'jane@example.com',
            'phone' => '987-654-3210'
        ];
        
        $result = Helper::removeNullProperties($obj);
        
        $this->assertEquals('Jane', $result->name);
        $this->assertEquals('jane@example.com', $result->email);
        $this->assertEquals('987-654-3210', $result->phone);
    }

    /**
     * Test removeNullProperties with null object
     */
    public function testRemoveNullPropertiesNullObject()
    {
        $result = Helper::removeNullProperties(null);
        $this->assertNull($result);
    }
}

