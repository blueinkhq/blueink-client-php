<?php

require_once __DIR__ . '/../src/blueink/models/Bundles.php';
require_once __DIR__ . '/../src/blueink/helpers/Helper.php';

use Blueink\ClientSDK\Bundle;
use Blueink\ClientSDK\Document;
use Blueink\ClientSDK\Field;
use Blueink\ClientSDK\Packet;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Blueink\ClientSDK\Field
 * @covers \Blueink\ClientSDK\Packet
 * @covers \Blueink\ClientSDK\Document
 * @covers \Blueink\ClientSDK\Bundle
 */
class BundlesModelTest extends TestCase
{
    /**
     * Test Field constructor with required parameters
     */
    public function testFieldConstructor()
    {
        $params = [
            'kind' => 'signature',
            'key' => 'field_abc123',
            'x' => 10,
            'y' => 20,
            'w' => 100,
            'h' => 50,
            'label' => 'Sign Here',
            'page' => 1,
        ];

        $field = new Field($params);

        $this->assertEquals('signature', $field->kind);
        $this->assertEquals('field_abc123', $field->key);
        $this->assertEquals(10, $field->x);
        $this->assertEquals(20, $field->y);
        $this->assertEquals(100, $field->w);
        $this->assertEquals(50, $field->h);
        $this->assertEquals('Sign Here', $field->label);
        $this->assertEquals(1, $field->page);
    }

    /**
     * Test Field::create generates key if not provided
     */
    public function testFieldCreateGeneratesKey()
    {
        $field = Field::create(10, 20, 100, 50, 1, 'signature', null, []);

        $this->assertStringStartsWith('field_', $field->key);
        $this->assertEquals(10, $field->x);
        $this->assertEquals(20, $field->y);
    }

    /**
     * Test Field::create with provided key
     */
    public function testFieldCreateWithKey()
    {
        $field = Field::create(10, 20, 100, 50, 1, 'signature', 'custom_key', []);

        $this->assertEquals('custom_key', $field->key);
    }

    /**
     * Test Field::addEditor adds editor to field
     */
    public function testFieldAddEditor()
    {
        $field = new Field([
            'kind' => 'signature',
            'key' => 'field_1',
            'x' => 10,
            'y' => 20,
            'w' => 100,
            'h' => 50,
        ]);

        $field->addEditor('editor1@example.com');
        $field->addEditor('editor2@example.com');

        $this->assertCount(2, $field->editors);
        $this->assertContains('editor1@example.com', $field->editors);
        $this->assertContains('editor2@example.com', $field->editors);
    }

    /**
     * Test Packet constructor requires key
     */
    public function testPacketConstructorRequiresKey()
    {
        $this->expectException(\ErrorException::class);
        new Packet([]);
    }

    /**
     * Test Packet constructor with key
     */
    public function testPacketConstructor()
    {
        $params = [
            'key' => 'packet_123',
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '555-1234',
        ];

        $packet = new Packet($params);

        $this->assertEquals('packet_123', $packet->key);
        $this->assertEquals('John Doe', $packet->name);
        $this->assertEquals('john@example.com', $packet->email);
        $this->assertEquals('555-1234', $packet->phone);
    }

    /**
     * Test Packet::create generates key if not provided
     */
    public function testPacketCreateGeneratesKey()
    {
        $packet = Packet::create();

        $this->assertStringStartsWith('packet_', $packet->key);
    }

    /**
     * Test Packet::create with provided key
     */
    public function testPacketCreateWithKey()
    {
        $packet = Packet::create('custom_packet_key', 'Jane Doe');

        $this->assertEquals('custom_packet_key', $packet->key);
        $this->assertEquals('Jane Doe', $packet->name);
    }

    /**
     * Test Document constructor requires key
     */
    public function testDocumentConstructorRequiresKey()
    {
        $this->expectException(\ErrorException::class);
        new Document([]);
    }

    /**
     * Test Document constructor with key
     */
    public function testDocumentConstructor()
    {
        $params = [
            'key' => 'doc_123',
            'file_url' => 'https://example.com/file.pdf',
            'parse_tags' => true,
        ];

        $document = new Document($params);

        $this->assertEquals('doc_123', $document->key);
        $this->assertEquals('https://example.com/file.pdf', $document->file_url);
    }

    /**
     * Test Document::create generates key if not provided
     */
    public function testDocumentCreateGeneratesKey()
    {
        $document = Document::create();

        $this->assertStringStartsWith('doc_', $document->key);
    }

    /**
     * Test Document::addField adds field to document
     */
    public function testDocumentAddField()
    {
        $document = new Document(['key' => 'doc_test']);
        $field = new Field([
            'kind' => 'signature',
            'key' => 'field_1',
            'x' => 10,
            'y' => 20,
            'w' => 100,
            'h' => 50,
        ]);

        $document->addField($field);

        $this->assertCount(1, $document->fields);
        $this->assertContains($field, $document->fields);
    }

    /**
     * Test Bundle constructor requires packets and documents
     */
    public function testBundleConstructorRequiresPackets()
    {
        $this->expectException(\ErrorException::class);
        new Bundle(['documents' => []]);
    }

    /**
     * Test Bundle constructor requires documents
     */
    public function testBundleConstructorRequiresDocuments()
    {
        $this->expectException(\ErrorException::class);
        new Bundle(['packets' => []]);
    }

    /**
     * Test Bundle::create with packets and documents
     */
    public function testBundleCreate()
    {
        $packets = [Packet::create('packet_1')];
        $documents = [Document::create('doc_1')];

        $bundle = Bundle::create($packets, $documents, ['label' => 'Test Bundle']);

        $this->assertCount(1, $bundle->packets);
        $this->assertCount(1, $bundle->documents);
        $this->assertEquals('Test Bundle', $bundle->label);
    }

    /**
     * Test Bundle::addPacket adds packet to bundle
     */
    public function testBundleAddPacket()
    {
        $packet1 = new Packet(['key' => 'packet_1']);
        $doc1 = new Document(['key' => 'doc_1']);
        $packets = [$packet1];
        $documents = [$doc1];
        $bundle = Bundle::create($packets, $documents);

        $newPacket = new Packet(['key' => 'packet_2']);
        $bundle->addPacket($newPacket);

        $this->assertCount(2, $bundle->packets);
    }

    /**
     * Test Bundle::addDocument adds document to bundle
     */
    public function testBundleAddDocument()
    {
        $packet1 = new Packet(['key' => 'packet_1']);
        $doc1 = new Document(['key' => 'doc_1']);
        $packets = [$packet1];
        $documents = [$doc1];
        $bundle = Bundle::create($packets, $documents);

        $newDocument = new Document(['key' => 'doc_2']);
        $bundle->addDocument($newDocument);

        $this->assertCount(2, $bundle->documents);
    }
}
