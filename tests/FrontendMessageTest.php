<?php

namespace Postgres\Tests;

use PHPUnit\Framework\TestCase;
use Postgres\FrontendMessage;

class FrontendMessageTest extends TestCase
{
    public function testCastsToStringAndEndsInNUL()
    {
        $msg = new FrontendMessage();
        $this->assertEquals("", "{$msg}");
    }
    
    public function testWritingMessageIdentifier()
    {
        $msg = new FrontendMessage();
        $msg->writeIdent('Q');
        $this->assertEquals("Q", "{$msg}");
    }

    public function testWritingNUL()
    {
        $msg = new FrontendMessage();
        $msg->writeNUL();
        $this->assertEquals("\0", "{$msg}");
    }

    public function testWritingInt32()
    {
        $msg = new FrontendMessage();
        $msg->writeInt32(2147483647);
        $this->assertTrue(strlen($msg) === 4);
        $arr = unpack('Nint', $msg);
        $this->assertEquals(2147483647, $arr['int']);
    }

    public function testWritingInt16()
    {
        $msg = new FrontendMessage();
        $msg->writeInt16(3);
        $this->assertTrue(strlen($msg) === 2);
        $arr = unpack('nint', $msg);
        $this->assertEquals(3, $arr['int']);
    }

    public function testWritingString()
    {
        $msg = new FrontendMessage();
        $msg->writeString("hello world");
        $this->assertEquals("hello world", "{$msg}");
    }

    public function testWritingStartupMessage()
    {
        $msg = new FrontendMessage();
        $msg->writeInt16(3);                // 2
        $msg->writeInt16(0);                // 2
        $msg->writeString('user');          // 4
        $msg->writeNUL();                   // 1
        $msg->writeString('zacharyrankin'); // 13
        $msg->writeNUL();                   // 1
        $msg->writeString('database');      // 8
        $msg->writeNUL();                   // 1
        $msg->writeString('postgres');      // 8
        $msg->writeNUL();                   // 1
        $msg->writeNUL();                   // 1
        $this->assertEquals(42, strlen($msg));
    }
}
