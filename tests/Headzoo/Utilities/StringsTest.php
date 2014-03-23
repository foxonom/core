<?php
use Headzoo\Utilities\Strings;

class StringsTest
    extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Headzoo\Utilities\Strings::random
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
 * @covers Headzoo\Utilities\Strings::transformCamelCaseToUnderscore
 */
    public function testTransformCamelCaseToUnderscore()
    {
        $this->assertEquals(
            "camel_case_string",
            Strings::transformCamelCaseToUnderscore("CamelCaseString")
        );
        $this->assertEquals(
            "ca_mel_ca_se_st_ring",
            Strings::transformCamelCaseToUnderscore("Ca_melCa_seSt_ring")
        );
        $this->assertEquals(
            "camel_case_string",
            Strings::transformCamelCaseToUnderscore("Camel_Case_String")
        );
    }

    /**
     * @covers Headzoo\Utilities\Strings::transformUnderscoreToCamelCase
     */
    public function testTransformUnderscoreToCamelCase()
    {
        $this->assertEquals(
            "CamelCaseString",
            Strings::transformUnderscoreToCamelCase("camel_case_string")
        );
        $this->assertEquals(
            "CamelCaseString",
            Strings::transformUnderscoreToCamelCase("Camel_Case_String")
        );
    }
}
