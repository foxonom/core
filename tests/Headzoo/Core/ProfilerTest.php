<?php
use Headzoo\Core\Profiler;

/**
 * @coversDefaultClass Headzoo\Core\Profiler
 */
class ProfilerTest
    extends PHPUnit_Framework_TestCase
{
    /**
     * The profiler
     * @var Profiler
     */
    protected $profiler;
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        Profiler::enabled(true);
        $this->profiler = new Profiler(); 
    }
    
    /**
     * @covers ::enabled
     */
    public function testEnabled()
    {
        $this->assertTrue(Profiler::enabled());
        $this->assertFalse(Profiler::enabled(false));
        $this->assertFalse(Profiler::enabled());
    }
    
    /**
     * @covers ::factory
     */
    public function testFactory()
    {
        $profiler = Profiler::factory();
        $this->assertInstanceOf(Profiler::class, $profiler);
        $this->assertFalse($profiler->isStarted());

        $profiler = Profiler::factory(false);
        $this->assertInstanceOf(Profiler::class, $profiler);
        $this->assertFalse($profiler->isStarted());
        
        $profiler = Profiler::factory(true);
        $this->assertTrue($profiler->isStarted());
        
        $profiler = Profiler::factory("profile1");
        $this->assertTrue($profiler->isStarted("profile1"));
    }
    
    /**
     * @covers ::start
     */
    public function testStart()
    {
        $this->assertFalse($this->profiler->isStarted());
        $this->assertFalse($this->profiler->isStarted("profile1"));
        
        $time = microtime(true);
        $this->assertGreaterThanOrEqual($time, $this->profiler->start());
        $this->assertGreaterThanOrEqual($time, $this->profiler->start("profile1"));
        $this->assertGreaterThanOrEqual($time, $this->profiler->start("profile2"));
        $this->assertTrue($this->profiler->isStarted());
        $this->assertTrue($this->profiler->isStarted("profile1"));
    }

    /**
     * @covers ::start
     * @expectedException Headzoo\Core\Exceptions\ProfilingException
     */
    public function testStart_Logic1()
    {
        $this->profiler->start();
        $this->profiler->start();
    }

    /**
     * @covers ::start
     * @expectedException Headzoo\Core\Exceptions\ProfilingException
     */
    public function testStart_Logic2()
    {
        $this->profiler->start("profile1");
        $this->profiler->start("profile1");
    }

    /**
     * @covers ::stop
     */
    public function testStop()
    {
        $this->profiler->start();
        usleep(100);
        $this->assertGreaterThan(0, $this->profiler->stop(false));
        
        $e = null;
        try {
            $this->profiler->stop();
        } catch (Headzoo\Core\Exceptions\ProfilingException $e) {}
        $this->assertNotNull($e);

        $this->profiler->start("profile1");
        usleep(100);
        $this->assertGreaterThan(0, $this->profiler->stop("profile1", false));

        $this->profiler->start("profile1");
        usleep(100);
        $this->profiler->start("profile2");
        usleep(100);
        
        $profile2 = $this->profiler->stop("profile2", false);
        $profile1 = $this->profiler->stop("profile1", false);
        $this->assertGreaterThan(0, $profile1);
        $this->assertGreaterThan($profile2, $profile1);
        
        Profiler::enabled(false);
        $this->assertFalse($this->profiler->start());
        usleep(100);
        $this->assertFalse($this->profiler->stop());
    }

    /**
     * @covers ::stop
     */
    public function testStop_Output_Default()
    {
        $this->expectOutputRegex("~^Time for profile 'default': [\\d.]+" . PHP_EOL . "$~");
        $this->profiler->start();
        usleep(100);
        $this->profiler->stop(true);
    }

    /**
     * @covers ::stop
     */
    public function testStop_Output_Profile1()
    {
        $this->expectOutputRegex("~^Time for profile 'profile1': [\\d.]+" . PHP_EOL . "$~");
        $this->profiler->start("profile1");
        usleep(100);
        $this->profiler->stop("profile1", true);
    }
}
