<?php
use Headzoo\Core\Comparator;

class ComparatorTest
    extends PHPUnit_Framework_TestCase
{
    /**
     * The test fixture
     * @var Comparator
     */
    protected $fixture;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->fixture = new Comparator();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Headzoo\Core\Comparator::compare
     */
    public function testCompare()
    {
        $this->assertEquals(0, $this->fixture->compare(5, 5));
        $this->assertEquals(-1, $this->fixture->compare(4, 5));
        $this->assertEquals(1, $this->fixture->compare(6, 5));
    }
    
    /**
     * @covers Headzoo\Core\Comparator::isEquals
     */
    public function testIsEquals()
    {
        $this->assertTrue($this->fixture->isEquals(5, 5));
        $this->assertTrue($this->fixture->isEquals(5, "5"));
        $this->assertFalse($this->fixture->isEquals(4, 5));
    }

    /**
     * @covers Headzoo\Core\Comparator::isNotEquals
     */
    public function testIsNotEquals()
    {
        $this->assertFalse($this->fixture->isNotEquals(5, 5));
        $this->assertFalse($this->fixture->isNotEquals(5, "5"));
        $this->assertTrue($this->fixture->isNotEquals(4, 5));
    }

    /**
     * @covers Headzoo\Core\Comparator::isStrictlyEquals
     */
    public function testIsStrictlyEquals()
    {
        $this->assertTrue($this->fixture->isStrictlyEquals(5, 5));
        $this->assertFalse($this->fixture->isStrictlyEquals(5, "5"));
        $this->assertFalse($this->fixture->isStrictlyEquals(4, 5));
    }

    /**
     * @covers Headzoo\Core\Comparator::isStrictlyNotEquals
     */
    public function testIsStrictlyNotEquals()
    {
        $this->assertFalse($this->fixture->isStrictlyNotEquals(5, 5));
        $this->assertTrue($this->fixture->isStrictlyNotEquals(5, "5"));
        $this->assertTrue($this->fixture->isStrictlyNotEquals(4, 5));
    }

    /**
     * @covers Headzoo\Core\Comparator::isLessThan
     */
    public function testIsLessThan()
    {
        $this->assertFalse($this->fixture->isLessThan(5, 5));
        $this->assertFalse($this->fixture->isLessThan(5, "5"));
        $this->assertTrue($this->fixture->isLessThan(4, 5));
    }

    /**
     * @covers Headzoo\Core\Comparator::isLessThanOrEquals
     */
    public function testIsLessThanOrEquals()
    {
        $this->assertTrue($this->fixture->isLessThanOrEquals(5, 5));
        $this->assertTrue($this->fixture->isLessThanOrEquals(5, "5"));
        $this->assertTrue($this->fixture->isLessThanOrEquals(4, 5));
    }

    /**
     * @covers Headzoo\Core\Comparator::isGreaterThan
     */
    public function testIsGreaterThan()
    {
        $this->assertFalse($this->fixture->isGreaterThan(5, 5));
        $this->assertFalse($this->fixture->isGreaterThan(5, "5"));
        $this->assertTrue($this->fixture->isGreaterThan(6, 5));
    }

    /**
     * @covers Headzoo\Core\Comparator::isGreaterThanOrEquals
     */
    public function testIsGreaterThanOrEquals()
    {
        $this->assertTrue($this->fixture->isGreaterThanOrEquals(5, 5));
        $this->assertTrue($this->fixture->isGreaterThanOrEquals(5, "5"));
        $this->assertTrue($this->fixture->isGreaterThanOrEquals(6, 5));
        $this->assertFalse($this->fixture->isGreaterThanOrEquals(4, 5));
    }

    /**
     * @covers Headzoo\Core\Comparator::isInstanceOf
     */
    public function testIsInstanceOf()
    {
        $this->assertTrue($this->fixture->isInstanceOf($this, ComparatorTest::class));
        $this->assertFalse($this->fixture->isInstanceOf($this, Comparator::class));
    }
}
