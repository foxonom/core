<?php
use Headzoo\Core\ErrorTypes;

/**
 * @coversDefaultClass
 */
class ErrorTypesTest
    extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::isValid
     */
    public function testIsValid()
    {
        $this->assertTrue(ErrorTypes::isValid(E_ERROR));
        $this->assertTrue(ErrorTypes::isValid(E_ALL));
        $this->assertFalse(ErrorTypes::isValid(0));
        $this->assertFalse(ErrorTypes::isValid("foo"));
        $this->assertFalse(ErrorTypes::isValid(null));
    }

    /**
     * @covers ::types
     */
    public function testTypes()
    {
        $this->assertNotEmpty(ErrorTypes::types());
        $this->assertContains(
            E_ERROR,
            ErrorTypes::types()
        );
        $this->assertContains(
            E_RECOVERABLE_ERROR,
            ErrorTypes::types()
        );
    }
}
