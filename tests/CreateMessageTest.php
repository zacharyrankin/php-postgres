<?php
namespace Postgres\Tests;

class CreateMessageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider tokenizeMessageProvider
     */
    public function testTokenizeMessage($msg, $expected)
    {
        $tokens = \Postgres\tokenizeMessage($msg);
        $this->assertEquals($expected, $tokens);
    }

    public function tokenizeMessageProvider()
    {
        return [
            [
                "Hello World",
                [
                    ['type' => 'unknown', 'value' => 'Hello World']
                ]
            ],
            [
                "3::int16",
                [
                    ['type' => 'int16', 'value' => '3::int16', 'number' => '3']
                ]
            ],
            [
                "3::int16 6::int16",
                [
                    ['type' => 'int16', 'value' => '3::int16', 'number' => '3'],
                    ['type' => 'whitespace', 'value' => ' '],
                    ['type' => 'int16', 'value' => '6::int16', 'number' => '6'],
                ]
            ],
            [
                "1::int16  1::int16",
                [
                    ['type' => 'int16', 'value' => '1::int16', 'number' => '1'],
                    ['type' => 'whitespace', 'value' => '  '],
                    ['type' => 'int16', 'value' => '1::int16', 'number' => '1'],
                ]
            ],
            [
                "100::int32",
                [
                    ['type' => 'int32', 'value' => '100::int32', 'number' => '100'],
                ]
            ],
            [
                "99::int32 180::int16",
                [
                    ['type' => 'int32', 'value' => '99::int32', 'number' => '99'],
                    ['type' => 'whitespace', 'value' => ' '],
                    ['type' => 'int16', 'value' => '180::int16', 'number' => '180'],
                ]
            ],
            [
                '"SELECT 1"::string',
                [
                    ['type' => 'string', 'value' => '"SELECT 1"::string', 'string' => 'SELECT 1'],
                ]
            ],
            [
                '"SELECT * FROM "table""::string',
                [
                    [
                        'type'   => 'string',
                        'value'  => '"SELECT * FROM "table""::string',
                        'string' => 'SELECT * FROM "table"',
                    ],
                ]
            ],
            [
                'Q::code LENGTH "SELECT 1"::string \0',
                [
                    [
                        'type'  => 'code',
                        'value' => 'Q::code',
                        'code'  => 'Q',
                    ],
                    ['type' => 'whitespace', 'value' => ' '],
                    [
                        'type'  => 'const',
                        'value' => 'LENGTH',
                    ],
                    ['type' => 'whitespace', 'value' => ' '],
                    [
                        'type'   => 'string',
                        'value'  => '"SELECT 1"::string',
                        'string' => 'SELECT 1',
                    ],
                    ['type' => 'whitespace', 'value' => ' '],
                    [
                        'type'  => 'const',
                        'value' => '\0',
                    ],
                ]
            ],
        ];
    }

    /**
     * @dataProvider createMessageProvider
     */
    public function testCreateMessage($msg, $expected)
    {
        $protocol_msg = \Postgres\createMessage($msg);
        $this->assertEquals($expected, $protocol_msg);
    }

    public function createMessageProvider()
    {
        return [
            [
                'Q::code LENGTH "SELECT 1"::string \0',
                'Q' . pack('N', 13) . "SELECT 1\0"
            ],
            [
                'LENGTH 3::int16 0::int16 \0',
                pack('N', 9) . pack('n', 3) . pack('n', 0) . "\0"
            ],
            [
                'LENGTH 3::int16 0::int16 "user\0postgres\0database\0postgres\0"::string \0',
                pack('N', 41) . pack('n', 3) . pack('n', 0) . "user\0postgres\0database\0postgres\0\0"
            ],
            [
                'Q::code LENGTH "SELECT 1"::string \0',
                'Q' . pack('N', 13) . "SELECT 1\0"
            ]
        ];
    }
}
