<?php
use Headzoo\Core\AbstractEnum;

/**
 * @coversDefaultClass Headzoo\Core\AbstractEnum
 */
class ConstantsEnumTest
    extends PHPUnit_Framework_TestCase
{
    /**
     * Test fixture
     * @var DaysEnum
     */
    protected $day;
    
    /**
     * Test fixture
     * @var HolidaysEnum
     */
    protected $holiday;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->day = new DaysEnum();
        $this->holiday = new HolidaysEnum();
    }

    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        new DaysEnum("SUNDAY");
        new DaysEnum("sunday");
        new HolidaysEnum("CHRISTMAS");
        
        $day = DaysEnum::SUNDAY();
        $this->assertInstanceOf(
            DaysEnum::class,
            $day
        );
        $day = DaysEnum::sunday();
        $this->assertInstanceOf(
            DaysEnum::class,
            $day
        );
        $holiday = HolidaysEnum::CHRISTMAS();
        $this->assertInstanceOf(
            HolidaysEnum::class,
            $holiday
        );

        $day = new DaysEnum("FRIDAY");
        new DaysEnum($day);
        new DaysEnum(DaysEnum::FRIDAY());
    }

    /**
     * @covers ::__construct
     * @expectedException Exceptions\UndefinedConstantException
     */
    public function testConstruct_UndefinedConstant_NoDefault()
    {
        new PetsEnum();
    }

    /**
     * @covers ::__construct
     * @expectedException Exceptions\UndefinedConstantException
     */
    public function testConstruct_UndefinedConstant_Invalid()
    {
        new DaysEnum("LUNCHDAY");
    }

    /**
     * @covers ::__construct
     * @expectedException Exceptions\LogicException
     */
    public function testConstruct_Logic()
    {
        new FoodsEnum();
    }

    /**
     * @covers ::__construct
     * @expectedException Exceptions\InvalidArgumentException
     */
    public function testConstruct_NotInstanceOf()
    {
        new DaysEnum(new OtherDaysEnum());
    }

    /**
     * @covers ::value
     */
    public function testValue()
    {
        $this->assertEquals(
            DaysEnum::SUNDAY,
            $this->day->value()
        );
        $this->assertEquals(
            HolidaysEnum::CHRISTMAS,
            $this->holiday->value()
        );
        $this->assertEquals(
            DaysEnum::SUNDAY,
            $this->day
        );
        $this->assertEquals(
            HolidaysEnum::CHRISTMAS,
            $this->holiday
        );
    }

    /**
     * @covers ::value
     */
    public function testEquals()
    {
        $this->assertEquals(
            DaysEnum::SUNDAY(),
            $this->day
        );
        $this->assertNotEquals(
            DaysEnum::MONDAY(),
            $this->day
        );
        $this->assertEquals(
            DaysEnum::SUNDAY,
            $this->day
        );
        $this->assertNotEquals(
            DaysEnum::MONDAY,
            $this->day
        );
        
        $day1 = new DaysEnum(DaysEnum::MONDAY);
        $day2 = new DaysEnum(DaysEnum::MONDAY);
        $day3 = new DaysEnum(DaysEnum::TUESDAY);
        $this->assertTrue($day1->equals($day2));
        $this->assertTrue($day2->equals($day1));
        $this->assertFalse($day1->equals($day3));
        
        switch($day1) {
            case DaysEnum::SUNDAY:
                $this->assertTrue(false);
                break;
            case DaysEnum::MONDAY:
                $this->assertTrue(true);
                break;
            default:
                $this->assertTrue(false);
                break;
        }
    }

    /**
     * @covers ::__invoke
     */
    public function testCopy()
    {
        $day = new DaysEnum(DaysEnum::MONDAY);
        $temp = $day;
        $this->assertSame($temp, $day);
        $this->assertTrue($temp === $day);
        $this->assertTrue($day->equals($temp));
        
        $temp = $day();
        $this->assertNotSame($temp, $day);
        $this->assertFalse($temp === $day);
        $this->assertTrue($day->equals($temp));
        
        $temp = $day(DaysEnum::FRIDAY);
        $this->assertNotSame($temp, $day);
        $this->assertFalse($day->equals($temp));
    }
}

class DaysEnum
    extends AbstractEnum
{
    const SUNDAY    = "SUNDAY";
    const MONDAY    = "MONDAY";
    const TUESDAY   = "TUESDAY";
    const WEDNESDAY = "WEDNESDAY";
    const THURSDAY  = "THURSDAY";
    const FRIDAY    = "FRIDAY";
    const SATURDAY  = "SATURDAY";
    const __DEFAULT = self::SUNDAY;
}

class OtherDaysEnum
    extends AbstractEnum
{
    const SUNDAY    = "SUNDAY";
    const MONDAY    = "MONDAY";
    const TUESDAY   = "TUESDAY";
    const WEDNESDAY = "WEDNESDAY";
    const THURSDAY  = "THURSDAY";
    const FRIDAY    = "FRIDAY";
    const SATURDAY  = "SATURDAY";
    const __DEFAULT = self::SUNDAY;
}

class HolidaysEnum
    extends AbstractEnum
{
    const CHRISTMAS = "CHRISTMAS";
    const EASTER    = "EASTER";
    const NEW_YEARS = "NEW_YEARS";
    const __DEFAULT = self::CHRISTMAS;
}

class PetsEnum
    extends AbstractEnum
{
    const DOG  = "DOG";
    const CAT  = "CAT";
    const BIRD = "BIRD";
    const FISH = "FISH";
}


class FoodsEnum
    extends AbstractEnum
{
    const PIZZA        = "PIZZA";
    const HAMBURGER    = "HAMBURGER";
    const HOT_DOG      = "HOT_DOG";
    const FRENCH_FRIES = "FRIES";
    const __DEFAULT    = self::PIZZA;
}