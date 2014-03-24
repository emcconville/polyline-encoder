<?php

namespace emcconville\Polyline\BingTrait;

class BingTraitTest extends \PHPUnit_Framework_TestCase
{
    protected $object = NULL;
    protected $encoded = 'vx1vilihnM6hR7mEl2Q';
    protected $points = array( // Numbers rounded
							array(35.89431, -110.72522),
							array(35.89393, -110.72578),
							array(35.89374, -110.72606),
							array(35.89337, -110.72662)
						);

    public function setUp()
    {
        $this->object = $this->getObjectForTrait(__NAMESPACE__);
    }
    
    /**
     * @covers \emcconville\Polyline\BingTrait::encodePoints
     */
    public function testEncode()
    {
        $expected = $this->encoded;
        $actual   = $this->object->encodePoints($this->points);
        $this->assertEquals($expected,$actual);
    }


    /**
     * @covers \emcconville\Polyline\BingTrait::decodeString
     */
    public function testDecode()
    {
        $expected = $this->object->decodeString($this->encoded);
        $actual   = $this->points;
        $this->assertEquals($expected,$actual);
    }

}
