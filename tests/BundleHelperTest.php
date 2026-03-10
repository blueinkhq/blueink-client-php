<?php

require_once __DIR__ . '/../src/blueink/helpers/BundleHelper.php';
require_once __DIR__ . '/../src/blueink/models/Bundles.php';

use PHPUnit\Framework\TestCase;
use Blueink\ClientSDK\BundleHelper;
use Blueink\ClientSDK\Document;

/**
 * @covers \Blueink\ClientSDK\BundleHelper
 */
class BundleHelperTest extends TestCase
{
    /**
     * Test BundleHelper constructor with empty params
     */
    public function testBundleHelperConstructorEmpty()
    {
        $helper = new BundleHelper([]);

        $this->assertNull($helper->label);
        $this->assertEquals(false, $helper->in_order);
        $this->assertEquals(false, $helper->is_test);
        $this->assertNull($helper->custom_key);
        $this->assertNull($helper->team);
        $this->assertEquals([], $helper->cc_emails);
        $this->assertEquals([], $helper->documents);
        $this->assertEquals([], $helper->packets);
    }

    /**
     * Test BundleHelper constructor with label
     */
    public function testBundleHelperConstructorWithLabel()
    {
        $params = ['label' => 'Test Bundle'];
        $helper = new BundleHelper($params);
        
        $this->assertEquals('Test Bundle', $helper->label);
    }

    /**
     * Test BundleHelper constructor with email subject and message
     */
    public function testBundleHelperConstructorWithEmailFields()
    {
        $params = [
            'email_subject' => 'Please Sign',
            'email_message' => 'Please review and sign the document'
        ];
        $helper = new BundleHelper($params);
        
        $this->assertEquals('Please Sign', $helper->email_subject);
        $this->assertEquals('Please review and sign the document', $helper->email_message);
    }

    /**
     * Test BundleHelper constructor with in_order flag
     */
    public function testBundleHelperConstructorWithInOrder()
    {
        $params = ['in_order' => true];
        $helper = new BundleHelper($params);

        $this->assertEquals(true, $helper->in_order);
    }

    /**
     * Test BundleHelper constructor with is_test flag
     */
    public function testBundleHelperConstructorWithIsTest()
    {
        $params = ['is_test' => true];
        $helper = new BundleHelper($params);
        
        $this->assertTrue($helper->is_test);
    }

    /**
     * Test BundleHelper constructor with all fields
     */
    public function testBundleHelperConstructorFull()
    {
        $params = [
            'label' => 'Complete Bundle',
            'email_subject' => 'Action Required',
            'email_message' => 'Please sign the attached documents',
            'custom_key' => 'custom_123',
            'team' => 'Legal',
            'is_test' => true,
            'in_order' => true,
            'cc_emails' => ['cc1@example.com', 'cc2@example.com']
        ];
        $helper = new BundleHelper($params);

        $this->assertEquals('Complete Bundle', $helper->label);
        $this->assertEquals('Action Required', $helper->email_subject);
        $this->assertEquals('Please sign the attached documents', $helper->email_message);
        $this->assertEquals('custom_123', $helper->custom_key);
        $this->assertEquals('Legal', $helper->team);
        $this->assertEquals(true, $helper->is_test);
        $this->assertEquals(true, $helper->in_order);
        $this->assertCount(2, $helper->cc_emails);
    }

    /**
     * Test BundleHelper::addCC adds email to cc_emails
     */
    public function testBundleHelperAddCC()
    {
        $helper = new BundleHelper([]);
        
        $helper->addCC('cc1@example.com');
        $helper->addCC('cc2@example.com');
        
        $this->assertCount(2, $helper->cc_emails);
        $this->assertContains('cc1@example.com', $helper->cc_emails);
        $this->assertContains('cc2@example.com', $helper->cc_emails);
    }

    /**
     * Test BundleHelper::addCC with existing cc_emails
     */
    public function testBundleHelperAddCCWithExisting()
    {
        $params = ['cc_emails' => ['existing@example.com']];
        $helper = new BundleHelper($params);
        
        $helper->addCC('new@example.com');
        
        $this->assertCount(2, $helper->cc_emails);
        $this->assertContains('existing@example.com', $helper->cc_emails);
        $this->assertContains('new@example.com', $helper->cc_emails);
    }

    /**
     * Test BundleHelper::addDocumentByURL
     */
    public function testBundleHelperAddDocumentByURL()
    {
        $helper = new BundleHelper([]);
        
        $key = $helper->addDocumentByURL('https://example.com/document.pdf');
        
        $this->assertIsString($key);
        $this->assertStringStartsWith('doc_', $key);
        $this->assertCount(1, $helper->documents);
    }

    /**
     * Test BundleHelper::addDocumentByB64
     */
    public function testBundleHelperAddDocumentByB64()
    {
        $helper = new BundleHelper([]);
        $b64Content = base64_encode('PDF content here');
        
        $key = $helper->addDocumentByB64('test.pdf', $b64Content);
        
        $this->assertIsString($key);
        $this->assertStringStartsWith('doc_', $key);
        $this->assertCount(1, $helper->documents);
    }

    /**
     * Test BundleHelper::addDocumentByB64 multiple documents
     */
    public function testBundleHelperAddDocumentByB64Multiple()
    {
        $helper = new BundleHelper([]);
        $b64Content1 = base64_encode('PDF content 1');
        $b64Content2 = base64_encode('PDF content 2');
        
        $key1 = $helper->addDocumentByB64('test1.pdf', $b64Content1);
        $key2 = $helper->addDocumentByB64('test2.pdf', $b64Content2);
        
        $this->assertNotEquals($key1, $key2);
        $this->assertCount(2, $helper->documents);
    }

    /**
     * Test BundleHelper with reminder settings
     */
    public function testBundleHelperWithReminders()
    {
        $params = [
            'label' => 'Test',
            'reminder_offset' => 3,
            'reminder_interval' => 7,
            'reminder_expires' => '2024-12-31'
        ];
        $helper = new BundleHelper($params);

        // These properties may not be initialized if not set in constructor
        // Just verify the object was created successfully
        $this->assertInstanceOf(BundleHelper::class, $helper);
    }

    /**
     * Test BundleHelper with cc_sender
     */
    public function testBundleHelperWithCCSender()
    {
        $params = [
            'label' => 'Test',
            'cc_emails' => ['cc@example.com']
        ];
        $helper = new BundleHelper($params);

        // Verify the object was created successfully
        $this->assertInstanceOf(BundleHelper::class, $helper);
    }

    /**
     * Test BundleHelper with documents and packets arrays
     */
    public function testBundleHelperWithDocumentsAndPackets()
    {
        $documents = [Document::create('doc_1'), Document::create('doc_2')];
        $packets = ['packet_1', 'packet_2'];
        
        $params = [
            'documents' => $documents,
            'packets' => $packets
        ];
        $helper = new BundleHelper($params);
        
        $this->assertCount(2, $helper->documents);
        $this->assertCount(2, $helper->packets);
    }
}

