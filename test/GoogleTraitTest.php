<?php

class GooglePolyline
{
    use emcconville\Polyline\GoogleTrait;
}

class GoogleTraitTest extends PHPUnit_Framework_TestCase
{
    protected $object = NULL;
    protected $encoded = '}`c~FlyquOnAE?`B@|HBpGJ?@pI';
    protected $points = array(
							array(41.79999,-87.58695),
							array(41.79959,-87.58692),
							array(41.79959,-87.58741),
							array(41.79958,-87.58900),
							array(41.79956,-87.59037),
							array(41.79950,-87.59037),
							array(41.79949,-87.59206)
						);

    public function setUp()
    {
        $this->object = new GooglePolyline();
        if(is_null($this->object))
            $this->markTestSkipped('Trait not loaded');
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
