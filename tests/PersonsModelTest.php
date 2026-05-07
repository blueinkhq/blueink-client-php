<?php

require_once __DIR__ . '/../src/blueink/models/Persons.php';

use Blueink\ClientSDK\ContactChannel;
use Blueink\ClientSDK\Person;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Blueink\ClientSDK\ContactChannel
 * @covers \Blueink\ClientSDK\Person
 */
class PersonsModelTest extends TestCase
{
    /**
     * Test ContactChannel constructor with empty params
     */
    public function testContactChannelConstructorEmpty()
    {
        $channel = new ContactChannel([]);

        $this->assertNull($channel->email);
        $this->assertNull($channel->phone);
        $this->assertNull($channel->kind);
    }

    /**
     * Test ContactChannel constructor with email
     */
    public function testContactChannelConstructorWithEmail()
    {
        $params = ['email' => 'john@example.com'];
        $channel = new ContactChannel($params);

        $this->assertEquals('john@example.com', $channel->email);
        $this->assertNull($channel->phone);
        $this->assertNull($channel->kind);
    }

    /**
     * Test ContactChannel constructor with phone
     */
    public function testContactChannelConstructorWithPhone()
    {
        $params = ['phone' => '555-1234'];
        $channel = new ContactChannel($params);

        $this->assertEquals('555-1234', $channel->phone);
        $this->assertNull($channel->email);
        $this->assertNull($channel->kind);
    }

    /**
     * Test ContactChannel constructor with all fields
     */
    public function testContactChannelConstructorFull()
    {
        $params = [
            'email' => 'jane@example.com',
            'phone' => '555-5678',
            'kind' => 'em',
        ];
        $channel = new ContactChannel($params);

        $this->assertEquals('jane@example.com', $channel->email);
        $this->assertEquals('555-5678', $channel->phone);
        $this->assertEquals('em', $channel->kind);
    }

    /**
     * Test ContactChannel constructor with null params
     */
    public function testContactChannelConstructorNullParams()
    {
        $channel = new ContactChannel(null);

        $this->assertNull($channel->email);
        $this->assertNull($channel->phone);
        $this->assertNull($channel->kind);
    }

    /**
     * Test Person constructor with empty params
     */
    public function testPersonConstructorEmpty()
    {
        $person = new Person([]);

        $this->assertNull($person->name);
        $this->assertNull($person->metadata);
        $this->assertNull($person->channel);
    }

    /**
     * Test Person constructor with name
     */
    public function testPersonConstructorWithName()
    {
        $params = ['name' => 'John Doe'];
        $person = new Person($params);

        $this->assertEquals('John Doe', $person->name);
        $this->assertNull($person->metadata);
        $this->assertNull($person->channel);
    }

    /**
     * Test Person constructor with metadata
     */
    public function testPersonConstructorWithMetadata()
    {
        $metadata = ['employee_id' => '12345', 'department' => 'Sales'];
        $params = ['metadata' => $metadata];
        $person = new Person($params);

        $this->assertEquals($metadata, $person->metadata);
        $this->assertNull($person->name);
        $this->assertNull($person->channel);
    }

    /**
     * Test Person constructor with channel
     */
    public function testPersonConstructorWithChannel()
    {
        $channel = new ContactChannel(['email' => 'john@example.com']);
        $params = ['channel' => $channel];
        $person = new Person($params);

        $this->assertInstanceOf(ContactChannel::class, $person->channel);
        $this->assertEquals('john@example.com', $person->channel->email);
    }

    /**
     * Test Person constructor with all fields
     */
    public function testPersonConstructorFull()
    {
        $metadata = ['role' => 'Manager'];
        $channel = new ContactChannel(['email' => 'jane@example.com', 'phone' => '555-9999']);
        $params = [
            'name' => 'Jane Smith',
            'metadata' => $metadata,
            'channel' => $channel,
        ];
        $person = new Person($params);

        $this->assertEquals('Jane Smith', $person->name);
        $this->assertEquals($metadata, $person->metadata);
        $this->assertInstanceOf(ContactChannel::class, $person->channel);
        $this->assertEquals('jane@example.com', $person->channel->email);
        $this->assertEquals('555-9999', $person->channel->phone);
    }

    /**
     * Test Person constructor with null params
     */
    public function testPersonConstructorNullParams()
    {
        $person = new Person(null);

        $this->assertNull($person->name);
        $this->assertNull($person->metadata);
        $this->assertNull($person->channel);
    }

    /**
     * Test Person with multiple metadata fields
     */
    public function testPersonMultipleMetadata()
    {
        $metadata = [
            'employee_id' => '12345',
            'department' => 'Engineering',
            'location' => 'New York',
            'start_date' => '2023-01-15',
        ];
        $params = ['name' => 'Bob Johnson', 'metadata' => $metadata];
        $person = new Person($params);

        $this->assertCount(4, $person->metadata);
        $this->assertEquals('Engineering', $person->metadata['department']);
        $this->assertEquals('New York', $person->metadata['location']);
    }

    /**
     * Test ContactChannel with different kinds
     */
    public function testContactChannelDifferentKinds()
    {
        $emailChannel = new ContactChannel(['email' => 'test@example.com', 'kind' => 'em']);
        $phoneChannel = new ContactChannel(['phone' => '555-1234', 'kind' => 'mp']);

        $this->assertEquals('em', $emailChannel->kind);
        $this->assertEquals('mp', $phoneChannel->kind);
    }
}
