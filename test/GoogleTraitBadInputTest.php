<?php

class GooglePolylineBadInput
{
    use emcconville\Polyline\GoogleTrait;
}

class GoogleTraitBadInputTest extends PHPUnit_Framework_TestCase
{
    protected $object = NULL;
    public function setUp()
    {
        $this->object = new GooglePolylineBadInput();
    }
    
    /**
     * @covers \emcconville\Polyline\GoogleTrait::encodePoints
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testBadInputEncode()
    {
        $this->object->encodePoints('I\'m not an array!');
    }
    
    /**
     * @covers \emcconville\Polyline\GoogleTrait::decodeString
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testBadInputDecode()
    {
        $fail = $this->object->decodeString(0xFF6633);
    }
}