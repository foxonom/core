<?php
use Headzoo\Utilities\Arrays;

class ArraysTest
    extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Headzoo\Utilities\Arrays::containsKeyValue
     */
    public function testContainsKeyValue()
    {
        $arr = [
            "admins" => [
                "headzoo" => "sean@headzoo.io",
                "joe"     => "joe@headzoo.io"
            ],
            "mods" => [
                "sam"     => "sam@headzoo.io"
            ]
        ];
        $this->assertTrue(
            Arrays::containsKeyValue($arr, "headzoo", "sean@headzoo.io")
        );
        
        $arr = [
            "headzoo" => "sean@headzoo.io",
            "joe"     => "joe@headzoo.io",
            "sam"     => "sam@headzoo.io"
        ];
        $this->assertTrue(
            Arrays::containsKeyValue($arr, "headzoo", "sean@headzoo.io", false)
        );
        $this->assertFalse(
            Arrays::containsKeyValue($arr, "headzoo", "joe@headzoo.io", false)
        );
    }

    /**
     * @covers Headzoo\Utilities\Arrays::column
     */
    public function testColumn()
    {
        
        $arr = [
            0 => [
                "username" => "headzoo",
                "email"    => "sean@headzoo.io"
            ],
            1 => [
                "username" => "joe",
                "email"    => "joe@headzoo.io"
            ]
        ];
        $this->assertEquals(
            ["headzoo", "joe"],
            Arrays::column($arr, "username")
        );
    }

    /**
     * @covers Headzoo\Utilities\Arrays::columnFilter
     */
    public function testColumnFilter()
    {
        $arr = [
            0 => [
                "username" => "headzoo",
                "email"    => "sean@headzoo.io",
                "admin"    => true
            ],
            1 => [
                "username" => "joe",
                "email"    => "joe@headzoo.io",
                "admin"    => false
            ],
            2 => [
                "username" => "sam",
                "email"    => "sam@headzoo.io",
                "admin"    => true
            ]
        ];
        $this->assertEquals(
            ["headzoo", "sam"],
            Arrays::columnFilter($arr, "username", function($element) { return $element["admin"]; })
        );
    }

    /**
     * @covers Headzoo\Utilities\Arrays::join
     */
    public function testJoin()
    {
        $arr = [
            "HEADZOO",
            "JOE",
            "SAM"
        ];
        $this->assertEquals(
            "headzoo, joe, sam", 
            Arrays::join($arr, ", ", "strtolower")
        );
    }
}
