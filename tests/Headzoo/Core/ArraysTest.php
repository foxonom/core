<?php
use Headzoo\Core\Arrays;
use Headzoo\Core\Strings;

/**
 * @coversDefaultClass Headzoo\Core\Arrays
 */
class ArraysTest
    extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::remove
     */
    public function testRemove()
    {
        $array = [
            "headzoo",
            "joe",
            "sam"
        ];
        $this->assertEquals(0, Arrays::remove($array, "amy"));
        
        $this->assertEquals(1, Arrays::remove($array, "headzoo"));
        $this->assertEquals(
            ["joe", "sam"],
            $array
        );

        $array = [
            "headzoo",
            "joe",
            "sam"
        ];
        $this->assertEquals(1, Arrays::remove($array, "headzoo", false, true));
        $this->assertEquals(
            [1 => "joe", "sam"],
            $array
        );

        $array = [
            "headzoo",
            "headzoo",
            "joe",
            "sam",
            "headzoo",
            "joe"
        ];
        $this->assertEquals(3, Arrays::remove($array, "headzoo"));
        $this->assertEquals(
            ["joe", "sam", "joe"],
            $array
        );
    }
    
    /**
     * @covers ::containsKeyValue
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
     * @covers ::column
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
     * @covers ::columnFilter
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
     * @covers ::join
     */
    public function testJoin()
    {
        $arr = [
            "headzoo",
            "joe",
            "sam"
        ];
        $this->assertEquals(
            "'headzoo', 'joe', 'sam'", 
            Arrays::join($arr, ", ", 'Headzoo\Core\Strings::quote')
        );
        $this->assertEquals(
            "'headzoo', 'joe', 'sam'",
            Arrays::join($arr, 'Headzoo\Core\Strings::quote')
        );
        $this->assertEquals(
            "'headzoo', 'joe', 'sam'",
            Arrays::join($arr, [new Strings(), "quote"])
        );
        $this->assertEquals(
            "'headzoo', 'joe', 'sam'",
            Arrays::join($arr, function($str) { return Strings::quote($str); })
        );
    }

    /**
     * @covers ::conjunct
     */
    public function testConjunct()
    {
        $arr = [
            "headzoo",
            "joe",
            "sam"
        ];
        $this->assertEquals(
            "'headzoo', 'joe', or 'sam'",
            Arrays::conjunct($arr, "or", 'Headzoo\Core\Strings::quote')
        );
        $this->assertEquals(
            "'headzoo', 'joe', " . Arrays::DEFAULT_CONJUNCTION . " 'sam'",
            Arrays::conjunct($arr, 'Headzoo\Core\Strings::quote')
        );
        $this->assertEquals(
            "'headzoo', 'joe', " . Arrays::DEFAULT_CONJUNCTION . " 'sam'",
            Arrays::conjunct($arr, [new Strings(), "quote"])
        );
        $this->assertEquals(
            "'headzoo', 'joe', " . Arrays::DEFAULT_CONJUNCTION . " 'sam'",
            Arrays::conjunct($arr, function($str) { return Strings::quote($str); })
        );
        
        $arr = [
            "headzoo"
        ];
        $this->assertEquals(
            "'headzoo'",
            Arrays::conjunct($arr, "and", 'Headzoo\Core\Strings::quote')
        );
    }

    /**
     * @covers ::findString
     * @dataProvider providerFindString
     */
    public function testFindString($needle, $reverse, $expected)
    {
        if (stripos($expected, "Framework_Error") !== false) {
            $this->setExpectedException($expected);
        }
        $arr = [
            "headzoo",
            "joe",
            "sam",
            "sam",
            "666",
            "headzoo",
            3.14,
            "joe"
        ];
        $this->assertEquals(
            $expected, 
            Arrays::findString($arr, $needle, $reverse)
        );
    }

    /**
     * Data provider for testFindString
     * 
     * @return array
     */
    public function providerFindString()
    {
        return [
            ["headzoo",                 false,  0],
            ["headzoo",                 true,   5],
            ["joe",                     false,  1],
            ["sam",                     false,  2],
            ["SAM",                     true,   3],
            ["JOE",                     false,  1],
            ["666",                     false,  4],
            [666,                       false,  4],
            [666,                       true,   4],
            [3.14,                      false,  6],
            ["3.14",                    false,  6],
            ["amy",                     false,  false],
            [null,                      false,  false],
            [["amy"],                   false,  "PHPUnit_Framework_Error_Warning"],
            [new stdClass(),            false,  "PHPUnit_Framework_Error_Warning"],
            [fopen("php://stdin", "r"), false,  "PHPUnit_Framework_Error_Warning"]
        ];
    }
}
