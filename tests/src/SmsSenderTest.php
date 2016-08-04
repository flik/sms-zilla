<?php
namespace SmsSender;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-08-02 at 10:25:21.
 */
class SmsSenderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SmsSender
     */
    protected $object;

    private $messageText = "Message content with ĄŻŹĆŚĘŁÓŃążźćśęłóń";

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        
        $this->object = new SmsSender(new \SmsSender\Adapter\MockGateway());
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers SmsSender\SmsSender::setText
     * @expectedException \InvalidArgumentException
     */
    public function testSetText()
    {
        $retVal = $this->object->setText('');
        $this->assertEquals($this->messageText, $retVal->getText());
    }

    /**
     * @covers SmsSender\SmsSender::getText
     */
    public function testGetText()
    {
        $retVal = $this->object->setText($this->messageText);
        $this->assertEquals($this->messageText, $retVal->getText());
    }

    /**
     * @covers SmsSender\SmsSender::setMessage
     */
    public function testSetMessage()
    {
        $message = new MessageModel();
        $message->setText($this->messageText);
        $this->object->setMessage($message);
        $this->assertAttributeEquals($this->messageText, 'text', $message);
    }

    /**
     * @covers SmsSender\SmsSender::getMessage
     */
    public function testGetMessage()
    {
        $message = new MessageModel();
        $message->setText($this->messageText);
        $message->addRecipient('654789321');
        $testMsg = clone $message;
        $this->object->setMessage($message);
        $this->assertEquals($testMsg, $this->object->getMessage());
    }

    /**
     * @covers SmsSender\SmsSender::setRecipient
     */
    public function testSetRecipient()
    {
        $this->object->setRecipient('654789321');
        $textVal = $this->object->getRecipients();
        $expected = array('48654789321');
        $this->assertEquals($expected, $textVal);
        $this->object->setRecipient('48147258369');
        $textVal = $this->object->getRecipients();
        $expected = array('48654789321', '48147258369');
        $this->assertEquals($expected, $textVal);
    }

    /**
     * @covers SmsSender\SmsSender::getRecipients
     * @todo   Implement testGetRecipients().
     */
    public function testGetRecipients()
    {
        $phones = ['504324567', '48504324431', '504324561', '48000000000'];
        $phonesExpected = ['48504324567', '48504324431', '48504324561', '48000000000'];
        $this->object->setRecipient($phones);
        $testVal = $this->object->getRecipients();
        $this->assertEquals($phonesExpected, $testVal);
    }

    /**
     * @covers SmsSender\SmsSender::send
     * @todo   Implement testSend().
     */
    public function testSend()
    {
        $message = new MessageModel();
        $message->setText($this->messageText);
        $this->object->setMessage($message);
        $result = $this->object->send();
        $this->assertTrue($result);
        $messages = $this->object->getAdapter()->getSentMessages();
        $this->assertCount(1, $messages);
        
        $this->assertEquals($message, $messages[0]);
    }

    /**
     * @covers SmsSender\SmsSender::getAdapter
     * @todo   Implement testGetAdapter().
     */
    public function testGetAdapter()
    {
        $expected = new \SmsSender\Adapter\MockGateway();
        $result = $this->object->getAdapter();
        $this->assertEquals($expected, $result);
    }
}