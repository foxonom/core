<?php
use Headzoo\Core\Strings;

/**
 * @coversDefaultClass Headzoo\Core\Strings
 * @requires extension mbstring
 */
class StringsTest
    extends PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        Strings::$__mbstring_extension_name = "mbstring";
        Strings::setUseMultiByte(false);
        Strings::setCharacterSet(null);
    }
    
    /**
     * @covers ::setUseMultiByte
     */
    public function testSetUseMultiByte()
    {
        Strings::setUseMultiByte(true);
        Strings::setUseMultiByte(false);
        $this->assertTrue(true);
    }

    /**
     * @covers ::setUseMultiByte
     * @expectedException Headzoo\Core\Exceptions\RuntimeException
     */
    public function testSetUseMultiByte_RuntimeException()
    {
        Strings::$__mbstring_extension_name = "mbstring-test";
        Strings::setUseMultiByte(true);
    }

    /**
     * @covers ::setCharacterSet
     */
    public function testSetCharacterSet()
    {
        Strings::setCharacterSet("ISO-8859-10");
        Strings::setCharacterSet("UTF-8");
        Strings::setCharacterSet("UTF8");
        Strings::setCharacterSet("utf8");
        Strings::setCharacterSet(null);
        $this->assertTrue(true);
    }

    /**
     * @covers ::setCharacterSet
     * @expectedException Headzoo\Core\Exceptions\InvalidArgumentException
     */
    public function testSetCharacterSet_InvalidArgumentException()
    {
        Strings::setCharacterSet("UTF-88");
    }
    
    /**
     * @covers ::quote
     */
    public function testQuote()
    {
        $this->assertEquals(
            "'Ticking away the moments that make up a dull day'",
            Strings::quote("Ticking away the moments that make up a dull day")
        );
        $this->assertEquals(
            "`You fritter and waste the hours in an offhand way`",
            Strings::quote("You fritter and waste the hours in an offhand way", "`")
        );
    }
    
    /**
     * @covers ::random
     */
    public function testRandom()
    {
        $actual = Strings::random(
            10, 
            Strings::CHARS_LOWER | Strings::CHARS_UPPER | Strings::CHARS_NUMBERS | Strings::CHARS_PUNCTUATION
        );
        $this->assertTrue(strlen($actual) == 10);
    }

    /**
     * @covers ::startsWith
     */
    public function testStartsWith()
    {
        $this->assertTrue(Strings::startsWith("But Gollum, and the evil one", "But"));
        $this->assertFalse(Strings::startsWith("But Gollum, and the evil one", "Gollum"));

        Strings::setUseMultiByte(true);
        $this->assertTrue(Strings::startsWith("But Gollum, and the evil one", "But"));
        $this->assertFalse(Strings::startsWith("But Gollum, and the evil one", "Gollum"));
    }

    /**
     * @covers ::endsWith
     */
    public function testEndsWith()
    {
        $this->assertTrue(Strings::endsWith("But Gollum, and the evil one", "one"));
        $this->assertFalse(Strings::endsWith("But Gollum, and the evil one", "evil"));

        Strings::setUseMultiByte(true);
        $this->assertTrue(Strings::endsWith("But Gollum, and the evil one", "one"));
        $this->assertFalse(Strings::endsWith("But Gollum, and the evil one", "evil"));
    }

    /**
     * @covers ::length
     */
    public function testLength()
    {
        $this->assertEquals(
            6,
            Strings::length("Gollum")
        );
        
        Strings::setUseMultiByte(true);
        $this->assertEquals(
            6,
            Strings::length("Gollum")
        );
    }

    /**
     * @covers ::chars
     */
    public function testChars()
    {
        $this->assertEquals(
            ["G", "o", "l", "l", "u", "m"],
            Strings::chars("Gollum")
        );
        
        Strings::setUseMultiByte(true);
        $this->assertEquals(
            ["G", "o", "l", "l", "u", "m"],
            Strings::chars("Gollum")
        );
    }

    /**
     * @covers ::startsUpper
     */
    public function testStartsUpper()
    {
        $this->assertTrue(Strings::startsUpper("Gollum"));
        $this->assertFalse(Strings::startsUpper("gollum"));
        
        Strings::setUseMultiByte(true);
        $this->assertTrue(Strings::startsUpper("Gollum"));
        $this->assertFalse(Strings::startsUpper("gollum"));
    }

    /**
     * @covers ::startsLower
     */
    public function testStartsLower()
    {
        $this->assertTrue(Strings::startsLower("gollum"));
        $this->assertFalse(Strings::startsLower("Gollum"));

        Strings::setUseMultiByte(true);
        $this->assertTrue(Strings::startsLower("gollum"));
        $this->assertFalse(Strings::startsLower("Gollum"));
    }

    /**
     * @covers ::camelCaseToUnderscore
     */
    public function testCamelCaseToUnderscore()
    {
        $this->assertEquals(
            "camel_case_string",
            Strings::camelCaseToUnderscore("CamelCaseString")
        );
        $this->assertEquals(
            "camel_case_string",
            Strings::camelCaseToUnderscore("Ca_melCa_seSt_ring")
        );
        $this->assertEquals(
            "camel_case_string",
            Strings::camelCaseToUnderscore("Camel_Case_String")
        );
        $this->assertEquals(
            "camel_case_string",
            Strings::camelCaseToUnderscore("Camel-Case-String")
        );
        $this->assertEquals(
            "mary_had_a_little_lamb",
            Strings::camelCaseToUnderscore("MaryHadALittleLamb")
        );
        
        Strings::setUseMultiByte(true);
        $this->assertEquals(
            "camel_case_string",
            Strings::camelCaseToUnderscore("CamelCaseString")
        );
        $this->assertEquals(
            "camel_case_string",
            Strings::camelCaseToUnderscore("Ca_melCa_seSt_ring")
        );
        $this->assertEquals(
            "camel_case_string",
            Strings::camelCaseToUnderscore("Camel_Case_String")
        );
        $this->assertEquals(
            "camel_case_string",
            Strings::camelCaseToUnderscore("Camel-Case-String")
        );
        $this->assertEquals(
            "mary_had_a_little_lamb",
            Strings::camelCaseToUnderscore("MaryHadALittleLamb")
        );
    }

    /**
     * @covers ::underscoreToCamelCase
     */
    public function testTransformUnderscoreToCamelCase()
    {
        $this->assertEquals(
            "CamelCaseString",
            Strings::underscoreToCamelCase("camel_case_string")
        );
        $this->assertEquals(
            "CamelCaseString",
            Strings::underscoreToCamelCase("Camel_Case_String")
        );
        $this->assertEquals(
            "CamelCaseString",
            Strings::underscoreToCamelCase("Camel-Case-String")
        );
        $this->assertEquals(
            "CamelCaseString",
            Strings::underscoreToCamelCase("Camel__Case__String")
        );

        Strings::setUseMultiByte(true);
        $this->assertEquals(
            "CamelCaseString",
            Strings::underscoreToCamelCase("camel_case_string")
        );
        $this->assertEquals(
            "CamelCaseString",
            Strings::underscoreToCamelCase("Camel_Case_String")
        );
        $this->assertEquals(
            "CamelCaseString",
            Strings::underscoreToCamelCase("Camel-Case-String")
        );
        $this->assertEquals(
            "CamelCaseString",
            Strings::underscoreToCamelCase("Camel__Case__String")
        );
    }

    /**
     * @covers ::transformCase
     * @dataProvider providerTransform
     */
    public function testTransform($str, $case, $expected)
    {
        $orig = $str;
        Strings::transform($str, $case);
        $this->assertEquals(
            $expected,
            $str
        );

        Strings::setUseMultiByte(true);
        Strings::transform($orig, $case);
        $this->assertEquals(
            $expected,
            $orig
        );
    }

    /**
     * Data provider for testTransform
     * 
     * @return array
     */
    public function providerTransform()
    {
        return [
            ["But Gollum, and the evil one",     Strings::TR_LOWER,      "but gollum, and the evil one"],
            ["Leaves are falling all around",    Strings::TR_UPPER,      "LEAVES ARE FALLING ALL AROUND"],
            ["It's timE I waS on my waY",        Strings::TR_TITLE,      "It's Time I Was On My Way"],
            ["sometimes I grow so tired",        Strings::TR_UC_FIRST,   "Sometimes I grow so tired"],
            ["But Gollum, and the evil one",     Strings::TR_LC_FIRST,   "but Gollum, and the evil one"],
            ["MaryHadALittleLamb",               Strings::TR_UNDERSCORE, "mary_had_a_little_lamb"],
            ["mary_had_a_little_lamb",           Strings::TR_CAMEL_CASE, "MaryHadALittleLamb"]
        ];
    }
}
