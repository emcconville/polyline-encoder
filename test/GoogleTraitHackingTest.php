<?php

class GooglePolylineHacking
{
    use emcconville\Polyline\GoogleTrait;
    private function polylinePrecision()
    {
        return 6;
    }
    private function polylineTupleSize()
    {
        return 3;
    }
}

class GoogleTraitHackingTest extends PHPUnit_Framework_TestCase
{
    protected $object = NULL;
    protected $encoded = 'AQ_seKA@_seKA@_seKA@_seKA@_seKA@_seKA@_seKA@_seKA@_seK';
    protected $points = array(
							array(0.000001,0.000009,0.200000),
                            array(0.000002,0.000008,0.400000),
                            array(0.000003,0.000007,0.600000),
                            array(0.000004,0.000006,0.800000),
                            array(0.000005,0.000005,1.000000),
                            array(0.000006,0.000004,1.200000),
                            array(0.000007,0.000003,1.400000),
                            array(0.000008,0.000002,1.600000),
                            array(0.000009,0.000001,1.800000)
						);

    public function setUp()
    {
        $this->object = new GooglePolylineHacking();
    }
    
    /**
     * @covers \emcconville\Polyline\GoogleTrait::encodePoints
     */
    public function testEncode()
    {
        $expected = $this->encoded;
        $actual   = $this->object->encodePoints($this->points);
        $this->assertEquals($expected,$actual);
    }


    /**
     * @covers \emcconville\Polyline\GoogleTrait::decodeString
     */
    public function testDecode()
    {
        $expected = $this->object->decodeString($this->encoded);
        $actual   = $this->points;
        $this->assertEquals($expected,$actual);
    }

}
