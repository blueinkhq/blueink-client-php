<?php
namespace Blueink\ClientSDK\Tests;

use Blueink\ClientSDK\BundleHelper;
use Blueink\ClientSDK\TemplateRef;
use PHPUnit\Framework\TestCase;

/**
 * Exercises the Python-parity convenience methods on BundleHelper:
 *   addSigner, addField, addAutoPlacement, addDocumentTemplate,
 *   assignRole, setValue, addDocumentByHTML, setEnvelopeTemplate,
 *   addEnvelopeTemplateFieldValue, asData, asDataForEnvelopeTemplate.
 *
 * @covers \Blueink\ClientSDK\BundleHelper
 * @covers \Blueink\ClientSDK\Helper
 */
class BundleHelperParityTest extends TestCase
{
    public function testAddSignerRequiresEmailOrPhone(): void
    {
        $bh = new BundleHelper([]);
        $this->expectException(\InvalidArgumentException::class);
        $bh->addSigner(name: 'No Contact');
    }

    public function testAddSignerRegistersPacketAndReturnsKey(): void
    {
        $bh = new BundleHelper([]);
        $key = $bh->addSigner(name: 'Jane', email: 'jane@example.com');

        $this->assertStringStartsWith('packet_', $key);
        $data = $bh->asData();
        $this->assertCount(1, $data['packets']);
        $this->assertSame('jane@example.com', $data['packets'][0]['email']);
        $this->assertSame('Jane', $data['packets'][0]['name']);
    }

    public function testAddFieldAttachesFieldToDocumentWithEditors(): void
    {
        $bh = new BundleHelper([]);
        $signer = $bh->addSigner(name: 'A', email: 'a@x.com');
        $doc = $bh->addDocumentByURL('https://example.com/doc.pdf');

        $field_key = $bh->addField(
            document_key: $doc, x: 1, y: 2, w: 3, h: 4, p: 5,
            kind: 'sig', editors: [$signer], label: 'Sign Here'
        );

        $this->assertStringStartsWith('field_', $field_key);
        $data = $bh->asData();
        $field = $data['documents'][0]['fields'][0];
        $this->assertSame('sig', $field['kind']);
        $this->assertSame('Sign Here', $field['label']);
        $this->assertSame([$signer], $field['editors']);
    }

    public function testAddFieldRejectsUnknownDocument(): void
    {
        $bh = new BundleHelper([]);
        $this->expectException(\RuntimeException::class);
        $bh->addField('nope', 1, 2, 3, 4, 1, 'sig');
    }

    public function testAddAutoPlacementSerializesCorrectly(): void
    {
        $bh = new BundleHelper([]);
        $signer = $bh->addSigner(name: 'A', email: 'a@x.com');
        $doc = $bh->addDocumentByURL('https://example.com/doc.pdf');

        $bh->addAutoPlacement($doc, 'sig', 'Signature', 20, 5, -5, 2, [$signer]);

        $data = $bh->asData();
        $ap = $data['documents'][0]['auto_placements'][0];
        $this->assertSame('sig', $ap['kind']);
        $this->assertSame('Signature', $ap['search']);
        $this->assertSame(20, $ap['w']);
        $this->assertSame(-5, $ap['offset_x']);
        $this->assertSame(2, $ap['offset_y']);
        $this->assertSame([$signer], $ap['editors']);
    }

    public function testAddDocumentTemplateBuildsTemplateRefWithAssignmentsAndValues(): void
    {
        $bh = new BundleHelper([]);
        $signer = $bh->addSigner(name: 'A', email: 'a@x.com', key: 'sgn-1');
        $tpl = $bh->addDocumentTemplate(
            'tmpl-123',
            ['signer' => 'sgn-1'],
            ['agree' => true]
        );

        $this->assertStringStartsWith('tmpl_', $tpl);
        $data = $bh->asData();
        $doc = $data['documents'][0];
        $this->assertSame('tmpl-123', $doc['template_id']);
        $this->assertSame('signer', $doc['assignments'][0]['role']);
        $this->assertSame('sgn-1', $doc['assignments'][0]['signer']);
        $this->assertSame('agree', $doc['field_values'][0]['key']);
        $this->assertTrue($doc['field_values'][0]['initial_value']);
    }

    public function testAssignRoleAndSetValueAppendToTemplateRef(): void
    {
        $bh = new BundleHelper([]);
        $signer = $bh->addSigner(name: 'A', email: 'a@x.com', key: 'sgn-1');
        $tpl = $bh->addDocumentTemplate('tmpl-123', [], []);

        $bh->assignRole($tpl, 'sgn-1', 'signer');
        $bh->setValue($tpl, 'agree', 'yes');

        $data = $bh->asData();
        $doc = $data['documents'][0];
        $this->assertSame('signer', $doc['assignments'][0]['role']);
        $this->assertSame('agree', $doc['field_values'][0]['key']);
        $this->assertSame('yes', $doc['field_values'][0]['initial_value']);
    }

    public function testAssignRoleRejectsNonTemplateDocument(): void
    {
        $bh = new BundleHelper([]);
        $bh->addSigner(name: 'A', email: 'a@x.com', key: 'sgn-1');
        $doc = $bh->addDocumentByURL('https://example.com/doc.pdf');

        $this->expectException(\RuntimeException::class);
        $bh->assignRole($doc, 'sgn-1', 'signer');
    }

    public function testAddDocumentByHTMLEmitsFileHTML(): void
    {
        $bh = new BundleHelper([]);
        $bh->addDocumentByHTML('<p>Hi</p>');

        $data = $bh->asData();
        $this->assertSame('<p>Hi</p>', $data['documents'][0]['file_html']);
    }

    public function testEnvelopeTemplateFlowSerializesAsExpected(): void
    {
        $bh = new BundleHelper(['label' => 'Env', 'is_test' => true]);
        $bh->addSigner(name: 'A', email: 'a@x.com', key: 'sgn-1');
        $bh->setEnvelopeTemplate('T-xyz', ['company' => 'ACME']);
        $bh->addEnvelopeTemplateFieldValue('amount', 99);

        $data = $bh->asDataForEnvelopeTemplate();
        $this->assertSame('T-xyz', $data['envelope_template']['template_id']);
        $this->assertCount(2, $data['envelope_template']['field_values']);
        $this->assertSame('Env', $data['label']);
        $this->assertTrue($data['is_test']);
        $this->assertSame('a@x.com', $data['packets'][0]['email']);
    }
}
