<?php
namespace Deepzoom\Tests;

use Deepzoom\Descriptor;
use Deepzoom\Exception as dzException;

/**
* Deep Zoom Tools
*
* Copyright (c) 2008-2010, OpenZoom <http://openzoom.org/>
* Copyright (c) 2008-2010, Nicolas Fabre <nicolas.fabre@gmail.com>
* All rights reserved.
*
* Redistribution and use in source and binary forms, with or without modification,
* are permitted provided that the following conditions are met:
*
* 1. Redistributions of source code must retain the above copyright notice,
* this list of conditions and the following disclaimer.
*
* 2. Redistributions in binary form must reproduce the above copyright
* notice, this list of conditions and the following disclaimer in the
* documentation and/or other materials provided with the distribution.
*
* 3. Neither the name of OpenZoom nor the names of its contributors may be used
* to endorse or promote products derived from this software without
* specific prior written permission.
*
* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
* ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
* WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
* DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
* ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
* (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
* LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
* ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
* (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
* SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

/**
 * Testing Descriptor
 *
 * @package    Deepzoom
 * @subpackage Test_Deepzoom
 * @author     Nicolas Fabre <nicolas.fabre@gmail.com>
 */
class DescriptorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->path = __DIR__.'/Fixtures/';
    }

	public function testConstructor()
    {
        $descriptor = new Descriptor(10,20,256,0,'png');
        $this->assertEquals(10, $descriptor->getWidth(), '__construct() takes the image with as its first argument');
        $this->assertEquals(20, $descriptor->getHeight(), '__construct() takes the image with as its first argument');
        $this->assertEquals(256, $descriptor->getTileSize(), '__construct() takes the tile size as its first argument');
        $this->assertEquals(0, $descriptor->getTileOverlap(), '__construct() takes the tile overlap as its first argument');
        $this->assertEquals('png', $descriptor->getTileFormat(), '__construct() takes the tile format as its first argument');
        
    }
    
    public function testOpen()
    {
    	$descriptor = new Descriptor();
    	$descriptor->open($this->path.'model1.xml');
    	$this->assertTrue(true);
    }
    
	public function testOpenFail()
    {
    	$descriptor = new Descriptor();
    	try {
    		$descriptor->open($this->path.'fail.xml');
    	}catch (dzException $e) {
    		$this->assertStringStartsWith('File not found',$e->getMessage());
    	}
    }
    
	public function testSave()
    {
    	$descriptor = new Descriptor();
    	$descriptor->open($this->path.'model1.xml');
    	$descriptor->save($this->path.'model1-sav.xml');
    	$this->assertFileEquals($this->path.'model1.xml',$this->path.'model1-sav.xml');
    }
    
    public function testDump()
    {
    	$method = new \ReflectionMethod(
          'Deepzoom\Descriptor', 'dump'
        );
        $method->setAccessible(TRUE);
    	$this->assertEquals(
          $this->_getDumpDescriptor(), $method->invoke(new Descriptor)
        );
    }   
    
    public function testSetGetWidth()
    {
        $descriptor = new Descriptor();
        $descriptor->setWidth(100);
        $this->assertEquals(100, $descriptor->getWidth(), '->setWidth() sets the width of the image');
    }
    
    public function testSetGetHeight()
    {
        $descriptor = new Descriptor();
        $descriptor->setHeight(100);
        $this->assertEquals(100, $descriptor->getHeight(), '->setHeight() sets the height of the image');
    }    
    
    public function testSetGetTileSize()
    {
        $descriptor = new Descriptor();
        $descriptor->setTileSize(289);
        $this->assertEquals(289, $descriptor->getTileSize(), '->setTileSize() sets the size of the tile');
    }

    
    public function testSetGetTileOverlap()
    {
        $descriptor = new Descriptor();
        $descriptor->setTileOverlap(0);
        $this->assertEquals(0, $descriptor->getTileOverlap(), '->setTileOverlap() sets the overlap of the tile');
    }    

    
    public function testSetGetTileFormat()
    {
        $descriptor = new Descriptor();
        $descriptor->setTileFormat('png');
        $this->assertEquals('png', $descriptor->getTileFormat(), '->setTileFormat() sets the format of the tile');
    }
    
    public function testGetNumLevels(){
    	 $descriptor = new Descriptor(2048,1536);	
    	 $this->assertEquals(12,$descriptor->getNumLevels(), '->getNumLevels() gets the number of levels in the pyramid');
    	 $this->assertGreaterThan(0,$descriptor->getNumLevels(), '->getNumLevels() gets the number of levels in the pyramid');
    }
    
	public function testGetScale(){
    	 $descriptor = new Descriptor(2048,1536);	
    	 foreach (range(0,11) as $level) {
    	 	$this->assertLessThanOrEqual(1,$descriptor->getScale($level), '->getScale() gets the scale of a pyramid level');
    	 }
    	 
    }
    public function testGetScaleException(){
    	 $descriptor = new Descriptor(2048,1536);
    	 try {
    		$descriptor->getScale($descriptor->getNumLevels());
    		$this->fail('Exception fail');
    	 }catch (dzException $e) {
    		$this->assertStringStartsWith('Invalid pyramid level',$e->getMessage());
    	 } 
    }
    
	public function testGetDimensionException(){
    	 $descriptor = new Descriptor(2048,1536);
    	 try {
    		$descriptor->getDimension($descriptor->getNumLevels());
    		$this->fail('Exception fail');
    	 }catch (dzException $e) {
    		$this->assertStringStartsWith('Invalid pyramid level',$e->getMessage());
    	 } 
    }
    
	public function testGetDimension(){
    	 $descriptor = new Descriptor(2048,1536);
		foreach (range(0,11) as $level) {
			$this->assertEquals(array('width', 'height'), array_keys($descriptor->getDimension($level)), '->getDimension() gets the dimension of a pyramid level');
    	 	$this->assertType('array',$descriptor->getDimension($level), '->getDimension() gets the dimension of a pyramid level');
    	 }
    }
    
	public function testGetNumTilesException(){
    	 $descriptor = new Descriptor(2048,1536);
    	 try {
    		$descriptor->getNumTiles($descriptor->getNumLevels());
    		$this->fail('Exception fail');
    	 }catch (dzException $e) {
    		$this->assertStringStartsWith('Invalid pyramid level',$e->getMessage());
    	 } 
    }
    
	public function testGetNumTiles(){
    	 $descriptor = new Descriptor(2048,1536);
    	 foreach (range(0,11) as $level) {
    	 	$this->assertEquals(array('columns', 'rows'), array_keys($descriptor->getNumTiles($level)), '->getNumTiles() gets the number of tiles in a pyramid level');
    	 	$this->assertType('array',$descriptor->getNumTiles($level), '->getNumTiles() gets the number of tiles in a pyramid level');
			$tiles = $descriptor->getNumTiles($level);
    	 	$this->assertGreaterThan(0,$tiles['columns'], '->getNumTiles() gets the number of tiles in a pyramid level');	
			$this->assertGreaterThan(0,$tiles['rows'], '->getNumTiles() gets the number of tiles in a pyramid level');
    	 }
    }
    
	public function testGetTileBoundsException(){
    	 $descriptor = new Descriptor(2048,1536);
    	 try {
    		$descriptor->getTileBounds($descriptor->getNumLevels(),0,0);
    		$this->fail('Exception fail');
    	 }catch (dzException $e) {
    		$this->assertStringStartsWith('Invalid pyramid level',$e->getMessage());
    	 } 
    }
    
    public function testGetTileBounds() {
    	$descriptor = new Descriptor(2048,1536);
    	foreach (range(0,11) as $level) {
    		$this->assertEquals(array('x', 'y','width', 'height'), array_keys($descriptor->getTileBounds($level,1,1)), '->getTileBounds() gets the bounding box of the tile in a pyramid level');
    	}	
    }
        
    protected function _getDumpDescriptor(){
        return <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<Image TileSize="254" Overlap="1" Format="jpg" xmlns="http://schemas.microsoft.com/deepzoom/2008">
	<Size Width="" Height=""/>
</Image>
EOF;
    }    	
}