<?php
use Headzoo\Core\Comparator;

class ComparatorTest
    extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Headzoo\Core\Comparator::compare
     */
    public function testCompare()
    {
        $this->assertEquals(0, Comparator::compare(5, 5));
        $this->assertEquals(-1, Comparator::compare(4, 5));
        $this->assertEquals(1, Comparator::compare(6, 5));
    }
    
    /**
     * @covers Headzoo\Core\Comparator::isEquals
     */
    public function testIsEquals()
    {
        $this->assertTrue(Comparator::isEquals(5, 5));
        $this->assertTrue(Comparator::isEquals(5, "5"));
        $this->assertFalse(Comparator::isEquals(4, 5));
    }

    /**
     * @covers Headzoo\Core\Comparator::isNotEquals
     */
    public function testIsNotEquals()
    {
        $this->assertFalse(Comparator::isNotEquals(5, 5));
        $this->assertFalse(Comparator::isNotEquals(5, "5"));
        $this->assertTrue(Comparator::isNotEquals(4, 5));
    }

    /**
     * @covers Headzoo\Core\Comparator::isStrictlyEquals
     */
    public function testIsStrictlyEquals()
    {
        $this->assertTrue(Comparator::isStrictlyEquals(5, 5));
        $this->assertFalse(Comparator::isStrictlyEquals(5, "5"));
        $this->assertFalse(Comparator::isStrictlyEquals(4, 5));
    }

    /**
     * @covers Headzoo\Core\Comparator::isStrictlyNotEquals
     */
    public function testIsStrictlyNotEquals()
    {
        $this->assertFalse(Comparator::isStrictlyNotEquals(5, 5));
        $this->assertTrue(Comparator::isStrictlyNotEquals(5, "5"));
        $this->assertTrue(Comparator::isStrictlyNotEquals(4, 5));
    }

    /**
     * @covers Headzoo\Core\Comparator::isLessThan
     */
    public function testIsLessThan()
    {
        $this->assertFalse(Comparator::isLessThan(5, 5));
        $this->assertFalse(Comparator::isLessThan(5, "5"));
        $this->assertTrue(Comparator::isLessThan(4, 5));
    }

    /**
     * @covers Headzoo\Core\Comparator::isLessThanOrEquals
     */
    public function testIsLessThanOrEquals()
    {
        $this->assertTrue(Comparator::isLessThanOrEquals(5, 5));
        $this->assertTrue(Comparator::isLessThanOrEquals(5, "5"));
        $this->assertTrue(Comparator::isLessThanOrEquals(4, 5));
    }

    /**
     * @covers Headzoo\Core\Comparator::isGreaterThan
     */
    public function testIsGreaterThan()
    {
        $this->assertFalse(Comparator::isGreaterThan(5, 5));
        $this->assertFalse(Comparator::isGreaterThan(5, "5"));
        $this->assertTrue(Comparator::isGreaterThan(6, 5));
    }

    /**
     * @covers Headzoo\Core\Comparator::isGreaterThanOrEquals
     */
    public function testIsGreaterThanOrEquals()
    {
        $this->assertTrue(Comparator::isGreaterThanOrEquals(5, 5));
        $this->assertTrue(Comparator::isGreaterThanOrEquals(5, "5"));
        $this->assertTrue(Comparator::isGreaterThanOrEquals(6, 5));
        $this->assertFalse(Comparator::isGreaterThanOrEquals(4, 5));
    }

    /**
     * @covers Headzoo\Core\Comparator::isInstanceOf
     */
    public function testIsInstanceOf()
    {
        $this->assertTrue(Comparator::isInstanceOf($this, ComparatorTest::class));
        $this->assertFalse(Comparator::isInstanceOf($this, Comparator::class));
    }
}
