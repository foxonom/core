<?php
use Headzoo\Core\Objects;
use Headzoo\Core\Strings;

/**
 * @coversDefaultClass Headzoo\Core\Objects
 */
class ObjectsTest
    extends PHPUnit_Framework_TestCase
    implements ObjectTestInterface
{
    /**
     * @covers ::getFullName
     * @dataProvider providerGetFullName
     */
    public function testGetFullName($class, $expected)
    {
        $this->assertEquals(
            $expected,
            Objects::getFullName($class)
        );
    }

    /**
     * @covers ::isObject
     * @dataProvider providerIsObject
     */
    public function testIsObject($obj, $expected)
    {
        $this->assertEquals(
            $expected,
            Objects::isObject($obj)
        );
    }
    
    /**
     * @covers ::isInstance
     * @dataProvider providerIsInstance
     */
    public function testIsInstance($obj, $class, $expected)
    {
        $this->assertEquals(
            $expected,
            Objects::isInstance($obj, $class)
        );
    }

    /**
     * @covers ::isInstance
     * @dataProvider providerIsInstance_Array
     */
    public function testIsInstance_Array($objs, $class, $expected)
    {
        $this->assertEquals(
            $expected,
            Objects::isInstance($objs, $class)
        );
    }
    
    /**
     * @covers ::equals
     */
    public function testEquals()
    {
        $obj_a = new ObjectEqualsTest();
        $obj_b = new ObjectEqualsTest();
        $this->assertTrue(Objects::equals($obj_a, $obj_b));
        $this->assertTrue(Objects::equals($obj_a, $obj_a));
        $this->assertTrue(Objects::equals($obj_a, $obj_b, true));
        $this->assertTrue(Objects::equals($obj_a, $obj_a, true));
        $this->assertFalse(Objects::equals($obj_a, $this));
        
        $obj_a = new ObjectEqualsTest();
        $obj_a->name = "Sean";
        $obj_a->job  = "Circus Freak";
        $obj_a->setAge(38);
        $obj_a->setLocation("NYC");
        
        $obj_b = new ObjectEqualsTest();
        $obj_b->name = "Sean";
        $obj_b->job  = "Circus Freak";
        $obj_b->setAge(38);
        $obj_b->setLocation("The Moon");
        
        $this->assertTrue(Objects::equals($obj_a, $obj_b));
        $this->assertTrue(Objects::equals($obj_a, $obj_a));
        $this->assertTrue(Objects::equals($obj_a, $obj_a, true));
        $this->assertFalse(Objects::equals($obj_a, $obj_b, true));
        
        
        $obj_a = new ObjectEqualsTest();
        $obj_a->name = "Sean";
        $obj_b = new ObjectEqualsTest();
        $obj_b->name = "Joe";
        $this->assertFalse(Objects::equals($obj_a, $obj_b));
        $this->assertFalse(Objects::equals($obj_a, $obj_b, true));

        
        $obj_a = new ObjectEqualsTest();
        $obj_a->name = 0;
        $obj_b = new ObjectEqualsTest();
        $obj_b->name = false;
        $this->assertFalse(Objects::equals($obj_a, $obj_b));
        $this->assertFalse(Objects::equals($obj_a, $obj_b, true));
    }

    /**
     * @covers ::merge
     */
    public function testMerge()
    {
        $obj_a = new stdClass();
        $obj_a->name = "Sean";
        $obj_b = new stdClass();
        $obj_b->job = "Circus Freak";
        
        $actual = Objects::merge($obj_a, $obj_b);
        $this->assertSame($obj_a, $actual);
        $this->assertNotSame($obj_b, $actual);
        $this->assertEquals("Sean", $actual->name);
        $this->assertEquals("Circus Freak", $actual->job);

        // -----------------------------

        $obj_a = new stdClass();
        $obj_a->name = "Sean";
        $obj_b = new stdClass();
        $obj_b->job = "Circus Freak";
        $obj_c = new stdClass();
        $obj_c->job = "Space Buccaneer";
        
        $actual = Objects::merge($obj_a, $obj_b, $obj_c);
        $this->assertEquals("Sean", $actual->name);
        $this->assertEquals("Space Buccaneer", $actual->job);

        // -----------------------------
        
        $obj_a = new stdClass();
        $obj_a->name = "Sean";
        $obj_a->job = "Programmer";
        
        $obj_b = new stdClass();
        $obj_b->name = "Joe";
        $obj_b->job = "Circus Freak";

        $actual = Objects::merge($obj_a, $obj_b);
        $this->assertEquals("Joe", $actual->name);
        $this->assertEquals("Circus Freak", $actual->job);

        // -----------------------------
        
        $obj_a = new ObjectEqualsTest();
        $obj_a->name = "Sean";
        $obj_a->job = "Programmer";
        $obj_a->setAge(38);
        
        $obj_b = new ObjectEqualsTest();
        $obj_b->name = "Joe";
        $obj_b->job  = "Circus Freak";
        $obj_b->setAge(23);

        $actual = Objects::merge($obj_a, $obj_b);
        $this->assertEquals("Joe", $actual->name);
        $this->assertEquals("Circus Freak", $actual->job);
        $this->assertEquals(38, $actual->getAge());

        // -----------------------------

        $obj_a = new stdClass();
        $obj_a->name = "Sean";

        $obj_b = new ObjectEqualsTest();
        $obj_b->name = "Joe";
        $obj_b->job  = "Circus Freak";
        
        $actual = Objects::merge($obj_a, $obj_b);
        $this->assertSame($obj_a, $actual);
        $this->assertEquals("Joe", $actual->name);
        $this->assertEquals("Circus Freak", $actual->job);

        // -----------------------------

        $obj_a = new ObjectEqualsTest();
        $obj_a->name = "Sean";
        
        $obj_b = new ObjectEqualsTest();
        $obj_b->job = "Circus Freak";
        
        $actual = Objects::merge(new stdClass(), $obj_a, $obj_b);
        $this->assertInstanceOf(stdClass::class, $actual);
        $this->assertEquals("Sean", $actual->name);
        $this->assertEquals("Circus Freak", $actual->job);

        // -----------------------------

        $obj_a = new ObjectEqualsTest();
        $obj_a->job = "Programmer";

        $obj_b = new ObjectEqualsTest();
        $obj_b->name = "Joe";
        $obj_b->job = "Circus Freak";

        $actual = Objects::merge($obj_a, $obj_b);
        $this->assertEquals("Joe", $actual->name);
        $this->assertEquals("Circus Freak", $actual->job);
    }

    /**
     * @covers ::merge
     * @expectedException Headzoo\Core\Exceptions\InvalidArgumentException
     */
    public function testMerge_Invalid()
    {
        $obj_a = 42;
        $obj_b = new stdClass();
        /** @noinspection PhpParamsInspection */
        Objects::merge($obj_a, $obj_b);
    }

    /**
     * @covers ::merge
     * @expectedException Headzoo\Core\Exceptions\InvalidArgumentException
     */
    public function testMerge_Invalid2()
    {
        $obj_a = new stdClass();
        $obj_b = 42;
        /** @noinspection PhpParamsInspection */
        Objects::merge($obj_a, $obj_b);
    }
    
    /**
     * Data provider for testIsInstance
     * 
     * @return array
     */
    public function providerIsInstance()
    {
        $obj = new stdClass();
        
        return [
            [$obj,                  new stdClass(),             true],
            [$obj,                  $obj,                       true],
            [$obj,                  'stdClass',                 true],
            [$this,                 ObjectsTest::class,         true],
            [$obj,                  '\stdClass',                true],
            [$this,                 ObjectTestInterface::class, true],
            [$obj,                  $this,                      false],
            [$obj,                  ObjectsTest::class,         false],
            [$this,                 'stdClass',                 false],
            [$obj,                  ObjectTestInterface::class, false],
            [42,                    'stdClass',                 false]
        ];
    }

    /**
     * Data provider for testIsInstance_Array
     *
     * @return array
     */
    public function providerIsInstance_Array()
    {
        $obj = new stdClass();
        return [
            [
                [$obj, $obj, new stdClass()],
                $obj,
                true
            ],
            [
                [$this, new ObjectsTest()],
                $this,
                true
            ],
            [
                [$this, new ObjectsTest()],
                ObjectsTest::class,
                true
            ],
            [
                [
                    [$this, new ObjectsTest()],
                    [$this]
                ],
                ObjectsTest::class,
                true
            ],
            [
                [$this, new stdClass()],
                $this,
                false
            ],
            [
                [$this, new stdClass()],
                stdClass::class,
                false
            ],
            [
                [42, new stdClass()],
                stdClass::class,
                false
            ]
        ];
    }

    /**
     * Data provider for testGetFullName
     * 
     * @return array
     */
    public function providerGetFullName()
    {
        return [
            [$this,             'ObjectsTest'],
            ['ObjectsTest',     'ObjectsTest'],
            ['\ObjectsTest\\',  'ObjectsTest'],
            [new Strings(),     'Headzoo\Core\Strings']
        ];
    }
    
    /**
     * Data provider for testIsObject
     *
     * @return array
     */
    public function providerIsObject()
    {
        return [
            [new stdClass(),    true],
            [$this,             true],
            [
                [$this, new stdClass()],
                true
            ],
            [ObjectsTest::class, false],
            ['stdClass',         false],
            [
                [$this, ObjectsTest::class],
                false
            ]
        ];
    }
}

interface ObjectTestInterface {}

class ObjectEqualsTest
{
    public $name;
    
    public $job;
    
    protected $age;
    
    private $location;
    
    public function setAge($age)
    {
        $this->age = $age;
    }
    
    public function getAge()
    {
        return $this->age;
    }
    
    public function setLocation($location)
    {
        $this->location = $location;
    }
    
    public function getLocation()
    {
        return $this->location;
    }
}
