<?php
use Headzoo\Core\Validator;

/**
 * @coversDefaultClass Headzoo\Core\Validator
 */
class ValidatorTest
    extends PHPUnit_Framework_TestCase
{
    /**
     * The test fixture
     * @var Validator
     */
    protected $validator;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->validator = new Validator();
    }

    /**
     * @covers ::validateRequired
     */
    public function testValidateRequired()
    {
        $values = [
            "name"   => "headzoo",
            "job"    => "circus animal",
            "age"    => 38,
            "gender" => "male"
        ];
        $required = [
            "name",
            "age",
            "gender"
        ];
        
        $this->validator->validateRequired($values, $required);
    }

    /**
     * @covers ::validateRequired
     */
    public function testValidateRequired_Empty()
    {
        $values = [
            "name"   => "headzoo",
            "job"    => "circus animal",
            "age"    => null,
            "gender" => "male"
        ];
        $required = [
            "name",
            "age",
            "gender"
        ];

        $this->validator->validateRequired($values, $required, true);
    }

    /**
     * @covers ::validateRequired
     * @expectedException Headzoo\Core\Exceptions\ValidationFailedException
     */
    public function testValidateRequired_Invalid()
    {
        $values = [
            "name"   => "headzoo",
            "job"    => "circus animal"
        ];
        $required = [
            "name",
            "age",
            "gender"
        ];

        $this->validator->validateRequired($values, $required);
    }

    /**
     * @covers ::validateRequired
     * @expectedException Headzoo\Core\Exceptions\ValidationFailedException
     */
    public function testValidateRequired_Invalid_Empty()
    {
        $values = [
            "name"   => "headzoo",
            "job"    => "circus animal",
            "gender" => "male",
            "age"    => null
        ];
        $required = [
            "name",
            "age",
            "gender"
        ];

        $this->validator->validateRequired($values, $required);
    }
}
