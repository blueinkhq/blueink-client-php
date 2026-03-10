<?php

require_once __DIR__ . '/../src/blueink/helpers/PersonHelper.php';

use PHPUnit\Framework\TestCase;
use Blueink\ClientSDK\PersonHelper;

/**
 * @covers \Blueink\ClientSDK\PersonHelper
 */
class PersonHelperTest extends TestCase
{
    /**
     * Test PersonHelper constructor with empty params
     */
    public function testPersonHelperConstructorEmpty()
    {
        $helper = new PersonHelper([]);

        // Just verify the object was created
        $this->assertInstanceOf(PersonHelper::class, $helper);
    }

    /**
     * Test PersonHelper constructor with name
     */
    public function testPersonHelperConstructorWithName()
    {
        $params = ['name' => 'John Doe'];
        $helper = new PersonHelper($params);

        $this->assertEquals('John Doe', $helper->name);
    }

    /**
     * Test PersonHelper constructor with metadata
     */
    public function testPersonHelperConstructorWithMetadata()
    {
        $metadata = ['employee_id' => '12345', 'department' => 'Sales'];
        $params = ['metadata' => $metadata];
        $helper = new PersonHelper($params);

        $this->assertEquals($metadata, $helper->metadata);
    }

    /**
     * Test PersonHelper constructor with phones
     */
    public function testPersonHelperConstructorWithPhones()
    {
        $phones = ['555-1234', '555-5678'];
        $params = ['phones' => $phones];
        $helper = new PersonHelper($params);

        $this->assertEquals($phones, $helper->phones);
    }

    /**
     * Test PersonHelper constructor with emails
     */
    public function testPersonHelperConstructorWithEmails()
    {
        $emails = ['john@example.com', 'john.doe@example.com'];
        $params = ['emails' => $emails];
        $helper = new PersonHelper($params);

        $this->assertEquals($emails, $helper->emails);
    }

    /**
     * Test PersonHelper constructor with all fields
     */
    public function testPersonHelperConstructorFull()
    {
        $params = [
            'name' => 'Jane Smith',
            'metadata' => ['role' => 'Manager'],
            'phones' => ['555-9999'],
            'emails' => ['jane@example.com']
        ];
        $helper = new PersonHelper($params);

        $this->assertEquals('Jane Smith', $helper->name);
        $this->assertEquals(['role' => 'Manager'], $helper->metadata);
        $this->assertEquals(['555-9999'], $helper->phones);
        $this->assertEquals(['jane@example.com'], $helper->emails);
    }

    /**
     * Test PersonHelper::addPhone adds phone to phones array
     */
    public function testPersonHelperAddPhone()
    {
        $helper = new PersonHelper([]);
        
        $helper->addPhone('555-1234');
        $helper->addPhone('555-5678');
        
        $this->assertCount(2, $helper->phones);
        $this->assertContains('555-1234', $helper->phones);
        $this->assertContains('555-5678', $helper->phones);
    }

    /**
     * Test PersonHelper::addPhone with existing phones
     */
    public function testPersonHelperAddPhoneWithExisting()
    {
        $helper = new PersonHelper([]);
        $helper->phones = ['555-0000'];

        $helper->addPhone('555-1111');

        $this->assertCount(2, $helper->phones);
        $this->assertContains('555-0000', $helper->phones);
        $this->assertContains('555-1111', $helper->phones);
    }

    /**
     * Test PersonHelper::setPhones replaces phones
     */
    public function testPersonHelperSetPhones()
    {
        $params = ['phones' => ['555-0000', '555-1111']];
        $helper = new PersonHelper($params);
        
        $newPhones = ['555-9999', '555-8888'];
        $helper->setPhones($newPhones);
        
        $this->assertEquals($newPhones, $helper->phones);
        $this->assertCount(2, $helper->phones);
    }

    /**
     * Test PersonHelper::getPhones returns phones
     */
    public function testPersonHelperGetPhones()
    {
        $helper = new PersonHelper([]);
        $phones = ['555-1234', '555-5678'];
        $helper->phones = $phones;

        $result = $helper->getPhones();

        $this->assertEquals($phones, $result);
    }

    /**
     * Test PersonHelper::addEmail adds email to emails array
     */
    public function testPersonHelperAddEmail()
    {
        $helper = new PersonHelper([]);
        
        $helper->addEmail('john@example.com');
        $helper->addEmail('john.doe@example.com');
        
        $this->assertCount(2, $helper->emails);
        $this->assertContains('john@example.com', $helper->emails);
        $this->assertContains('john.doe@example.com', $helper->emails);
    }

    /**
     * Test PersonHelper::addEmail with existing emails
     */
    public function testPersonHelperAddEmailWithExisting()
    {
        $helper = new PersonHelper([]);
        $helper->emails = ['existing@example.com'];

        $helper->addEmail('new@example.com');

        $this->assertCount(2, $helper->emails);
        $this->assertContains('existing@example.com', $helper->emails);
        $this->assertContains('new@example.com', $helper->emails);
    }

    /**
     * Test PersonHelper::setEmails replaces emails
     */
    public function testPersonHelperSetEmails()
    {
        $helper = new PersonHelper([]);
        $helper->emails = ['old1@example.com', 'old2@example.com'];

        $newEmails = ['new1@example.com', 'new2@example.com'];
        $helper->setEmails($newEmails);

        $this->assertEquals($newEmails, $helper->emails);
        $this->assertCount(2, $helper->emails);
    }

    /**
     * Test PersonHelper::getEmails returns emails
     */
    public function testPersonHelperGetEmails()
    {
        $helper = new PersonHelper([]);
        $emails = ['john@example.com', 'john.doe@example.com'];
        $helper->emails = $emails;

        $result = $helper->getEmails();

        $this->assertEquals($emails, $result);
    }

    /**
     * Test PersonHelper::setMetadata sets metadata
     */
    public function testPersonHelperSetMetadata()
    {
        $helper = new PersonHelper([]);
        
        $metadata = ['role' => 'Manager', 'department' => 'Engineering'];
        $helper->setMetadata($metadata);
        
        $this->assertEquals($metadata, $helper->metadata);
    }

    /**
     * Test PersonHelper::set_name sets name
     */
    public function testPersonHelperSetName()
    {
        $helper = new PersonHelper([]);
        
        $helper->set_name('Alice Johnson');
        
        $this->assertEquals('Alice Johnson', $helper->name);
    }

    /**
     * Test PersonHelper with multiple phones and emails
     */
    public function testPersonHelperMultipleContactChannels()
    {
        $helper = new PersonHelper([]);
        
        $helper->addPhone('555-1111');
        $helper->addPhone('555-2222');
        $helper->addPhone('555-3333');
        
        $helper->addEmail('email1@example.com');
        $helper->addEmail('email2@example.com');
        
        $this->assertCount(3, $helper->phones);
        $this->assertCount(2, $helper->emails);
    }
}

