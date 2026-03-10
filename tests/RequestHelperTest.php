<?php

require_once __DIR__ . '/../src/blueink/helpers/RequestHelper.php';

use PHPUnit\Framework\TestCase;
use Blueink\ClientSDK\Pagination;
use Blueink\ClientSDK\RequestHelper;

/**
 * @covers \Blueink\ClientSDK\Pagination
 * @covers \Blueink\ClientSDK\RequestHelper
 */
class RequestHelperTest extends TestCase
{
    /**
     * Test Pagination constructor parses header correctly
     */
    public function testPaginationConstructor()
    {
        $pagination = new Pagination('1,10,20,200');
        
        $this->assertEquals(1, $pagination->page_number);
        $this->assertEquals(10, $pagination->total_pages);
        $this->assertEquals(20, $pagination->per_page);
        $this->assertEquals(200, $pagination->total_results);
    }

    /**
     * Test Pagination constructor with different values
     */
    public function testPaginationConstructorDifferentValues()
    {
        $pagination = new Pagination('5,50,10,500');
        
        $this->assertEquals(5, $pagination->page_number);
        $this->assertEquals(50, $pagination->total_pages);
        $this->assertEquals(10, $pagination->per_page);
        $this->assertEquals(500, $pagination->total_results);
    }

    /**
     * Test Pagination::paginationAsString returns formatted string
     */
    public function testPaginationAsString()
    {
        $pagination = new Pagination('1,10,20,200');
        $result = $pagination->paginationAsString();
        
        $this->assertStringContainsString('page_number: 1', $result);
        $this->assertStringContainsString('per_page:20', $result);
        $this->assertStringContainsString('total_pages:10', $result);
        $this->assertStringContainsString('total_results: 200', $result);
    }

    /**
     * Test Pagination with large numbers
     */
    public function testPaginationLargeNumbers()
    {
        $pagination = new Pagination('100,1000,50,50000');
        
        $this->assertEquals(100, $pagination->page_number);
        $this->assertEquals(1000, $pagination->total_pages);
        $this->assertEquals(50, $pagination->per_page);
        $this->assertEquals(50000, $pagination->total_results);
    }

    /**
     * Test RequestHelper constructor requires private_api_key
     */
    public function testRequestHelperConstructorRequiresKey()
    {
        $this->expectException(\TypeError::class);
        new RequestHelper(null);
    }

    /**
     * Test RequestHelper constructor with valid key
     */
    public function testRequestHelperConstructorWithKey()
    {
        $helper = new RequestHelper('test_api_key_123');
        
        $this->assertInstanceOf(RequestHelper::class, $helper);
    }

    /**
     * Test RequestHelper constructor with raise_exceptions flag
     */
    public function testRequestHelperConstructorWithRaiseExceptions()
    {
        $helper = new RequestHelper('test_api_key_123', false);
        
        $this->assertInstanceOf(RequestHelper::class, $helper);
    }

    /**
     * Test RequestHelper constructor with raise_exceptions true
     */
    public function testRequestHelperConstructorWithRaiseExceptionsTrue()
    {
        $helper = new RequestHelper('test_api_key_123', true);
        
        $this->assertInstanceOf(RequestHelper::class, $helper);
    }

    /**
     * Test RequestHelper with empty string key
     */
    public function testRequestHelperConstructorEmptyKey()
    {
        // Empty string is technically valid for the constructor
        $helper = new RequestHelper('');
        $this->assertInstanceOf(RequestHelper::class, $helper);
    }

    /**
     * Test RequestHelper with whitespace key
     */
    public function testRequestHelperConstructorWhitespaceKey()
    {
        // Whitespace string is technically valid for the constructor
        $helper = new RequestHelper('   ');
        $this->assertInstanceOf(RequestHelper::class, $helper);
    }

    /**
     * Test Pagination with edge case values
     */
    public function testPaginationEdgeCaseValues()
    {
        $pagination = new Pagination('1,1,1,1');
        
        $this->assertEquals(1, $pagination->page_number);
        $this->assertEquals(1, $pagination->total_pages);
        $this->assertEquals(1, $pagination->per_page);
        $this->assertEquals(1, $pagination->total_results);
    }

    /**
     * Test Pagination with zero values
     */
    public function testPaginationZeroValues()
    {
        $pagination = new Pagination('0,0,0,0');
        
        $this->assertEquals(0, $pagination->page_number);
        $this->assertEquals(0, $pagination->total_pages);
        $this->assertEquals(0, $pagination->per_page);
        $this->assertEquals(0, $pagination->total_results);
    }

    /**
     * Test RequestHelper with various key formats
     */
    public function testRequestHelperVariousKeyFormats()
    {
        $keys = [
            'simple_key',
            'key-with-dashes',
            'key_with_underscores',
            'UPPERCASE_KEY',
            'MixedCaseKey123',
            'key.with.dots'
        ];
        
        foreach ($keys as $key) {
            $helper = new RequestHelper($key);
            $this->assertInstanceOf(RequestHelper::class, $helper);
        }
    }

    /**
     * Test Pagination paginationAsString format consistency
     */
    public function testPaginationAsStringFormat()
    {
        $pagination = new Pagination('2,20,15,300');
        $result = $pagination->paginationAsString();
        
        // Verify the format is consistent
        $this->assertMatchesRegularExpression('/page_number: \d+/', $result);
        $this->assertMatchesRegularExpression('/per_page:\d+/', $result);
        $this->assertMatchesRegularExpression('/total_pages:\d+/', $result);
        $this->assertMatchesRegularExpression('/total_results: \d+/', $result);
    }
}

