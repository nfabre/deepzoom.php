<?php

/*
 * This file is part of the Deepzoom.php package.
 *
 * (c) Nicolas Fabre <nicolas.fabre@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deepzoom\Tests;

use Deepzoom\Exception as dzException;

use Deepzoom;

use Deepzoom\Descriptor;
use Deepzoom\Tests\Fixtures\StreamWrapper\Stub as streamStub;
use Deepzoom\StreamWrapper\File;
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
        $this->path = __DIR__.DEEPZOOM_TESTSUITE_FIXTURES_PATH;
    }

	public function testConstructor()
    {
        $descriptor = new Descriptor(new File(),10,20,256,0,'png');
        $this->assertEquals(10, $descriptor->getWidth(), '__construct() takes the image with as its first argument');
        $this->assertEquals(20, $descriptor->getHeight(), '__construct() takes the image with as its first argument');
        $this->assertEquals(256, $descriptor->getTileSize(), '__construct() takes the tile size as its first argument');
        $this->assertEquals(0, $descriptor->getTileOverlap(), '__construct() takes the tile overlap as its first argument');
        $this->assertEquals('png', $descriptor->getTileFormat(), '__construct() takes the tile format as its first argument');
        $this->assertAttributeInstanceOf('Deepzoom\StreamWrapper\StreamWrapperInterface','_streamWrapper',$descriptor, '__construct() takes the tile size as its first argument');
    }
    
    public function testOpenFile()
    {
    	$descriptor = new Descriptor($this->_getMockFileForOpenTest());
    	$descriptor->open($this->path.'model1.xml');
    	$this->assertTrue(true);
    }
    
	public function testOpenFileButInvalidXml()
    {
    	$descriptor = new Descriptor($this->_getMockFileForOpenTestButInvalidXml());
    	try {
    		$descriptor->open($this->path.'model1.xml');
    		$this->fail('No exception caught');
    	}catch (dzException $e) {
    		$this->assertStringStartsWith('Invalid Xml',$e->getMessage());
    	}
    }

	public function testOpenFileButDeepzoomException()
    {
    	$descriptor = new Descriptor($this->_getMockFileForOpenTestButFail());
    	try {
    		$descriptor->open($this->path.'model1.xml');
    		$this->fail('No exception caught');
    	}catch (\InvalidArgumentException $e) {
    		$this->assertStringStartsWith('File not found',$e->getMessage());
    	}
    }
    
    public function testDump()
    {
    	$descriptor = new Descriptor(new File());
    	$this->assertEquals($this->getDumpDescriptor(), $descriptor->dump());
    }  
    
 	public function testSetterAndGetterForWidthAttribute()
    {
        $descriptor = new Descriptor(new File());
        $descriptor->setWidth(100);
        $this->assertAttributeInternalType('int', '_width', $descriptor);
        $this->assertAttributeEquals(100, '_width', $descriptor);
        $this->assertEquals(100, $descriptor->getWidth(), '->setWidth() sets the width of the image');
    }
    
    public function testSetterAndGetterForHeightAttribute()
    {
        $descriptor = new Descriptor(new File());
        $descriptor->setHeight(100);
        $this->assertAttributeInternalType('int', '_height', $descriptor);
        $this->assertAttributeEquals(100, '_height', $descriptor);
        $this->assertEquals(100, $descriptor->getHeight(), '->setHeight() sets the height of the image');
    }    
    
	public function testSetterAndGetterForTileSizeAttribute()
    {
        $descriptor = new Descriptor(new File());
        $descriptor->setTileSize(289);
        $this->assertAttributeInternalType('int', '_tileSize', $descriptor);
        $this->assertAttributeEquals(289, '_tileSize', $descriptor);
        $this->assertEquals(289, $descriptor->getTileSize(), '->setTileSize() sets the size of the tile');
    }
    
    public function testSetterAndGetterForTileOverlapAttribute()
    {
        $descriptor = new Descriptor(new File());
        $descriptor->setTileOverlap(0);
        $this->assertAttributeInternalType('int', '_tileOverlap', $descriptor);
        $this->assertAttributeEquals(0, '_tileOverlap', $descriptor);
        $this->assertEquals(0, $descriptor->getTileOverlap(), '->setTileOverlap() sets the overlap of the tile');
    }    
    
	public function testSetterAndGetterForTileFormatAttribute()
    {
        $descriptor = new Descriptor(new File());
        $descriptor->setTileFormat('png');
        $this->assertAttributeInternalType('string', '_tileFormat', $descriptor);
        $this->assertAttributeEquals('png', '_tileFormat', $descriptor);
        $this->assertEquals('png', $descriptor->getTileFormat(), '->setTileFormat() sets the format of the tile');
    }
    
	public function testGetNumLevels(){
    	 $descriptor = new Descriptor(new streamStub(),2048,1536);	
    	 $this->assertGreaterThan(0,$descriptor->getNumLevels(), '->getNumLevels() gets the number of levels in the pyramid');
	 	 $this->assertEquals(12,$descriptor->getNumLevels(), '->getNumLevels() gets the number of levels in the pyramid');
	}
	
	public function testGetScale(){
    	 $descriptor = new Descriptor(new streamStub(),2048,1536);	
    	 foreach (range(0,11) as $level) {
    	 	$this->assertLessThanOrEqual(1,$descriptor->getScale($level), '->getScale() gets the scale of a pyramid level');
    	 }
    }
    
	public function testGetScaleButInvalidArgumentException(){
    	 $descriptor = new Descriptor(new streamStub(),2048,1536);
    	 try {
    		$descriptor->getScale($descriptor->getNumLevels());
    		$this->fail('Exception fail');
    	 }catch (\InvalidArgumentException $e) {
    		$this->assertStringStartsWith('Invalid pyramid level',$e->getMessage());
    	 } 
    }
    
	public function testSetterAndGetterForStreamWrapperAttribute(){
         $descriptor = new Descriptor(new streamStub());  
         $file = new File();
         $descriptor->setStreamWrapper($file);
         $this->assertAttributeInstanceOf('Deepzoom\StreamWrapper\StreamWrapperInterface', '_streamWrapper', $descriptor);
         $this->assertInstanceOf('Deepzoom\StreamWrapper\StreamWrapperInterface',$descriptor->getStreamWrapper());
         $this->assertInstanceOf('Deepzoom\StreamWrapper\File',$descriptor->getStreamWrapper());
         $this->assertSame($file,$descriptor->getStreamWrapper());
    }
    
	public function testGetDimensionButInvalidArgumentException(){
    	 $descriptor = new Descriptor(new streamStub(),2048,1536);
    	 try {
    		$descriptor->getDimension($descriptor->getNumLevels());
    		$this->fail('Exception fail');
    	 }catch (\InvalidArgumentException $e) {
    		$this->assertStringStartsWith('Invalid pyramid level',$e->getMessage());
    	 } 
    }
    
	public function testGetDimension(){
    	 $descriptor = new Descriptor(new streamStub(),2048,1536);
		foreach (range(0,11) as $level) {
			$this->assertEquals(array('width', 'height'), array_keys($descriptor->getDimension($level)), '->getDimension() gets the dimension of a pyramid level');
    	 	$this->assertInternalType('array',$descriptor->getDimension($level), '->getDimension() gets the dimension of a pyramid level');
    	 }
    }
    
	public function testGetNumTilesButInvalidArgumentException(){
    	 $descriptor = new Descriptor(new streamStub(),2048,1536);
    	 try {
    		$descriptor->getNumTiles($descriptor->getNumLevels());
    		$this->fail('Exception fail');
    	 }catch (\InvalidArgumentException $e) {
    		$this->assertStringStartsWith('Invalid pyramid level',$e->getMessage());
    	 } 
    }
    
	public function testGetNumTiles(){
    	 $descriptor = new Descriptor(new streamStub(),2048,1536);
    	 foreach (range(0,11) as $level) {
    	 	$this->assertEquals(array('columns', 'rows'), array_keys($descriptor->getNumTiles($level)), '->getNumTiles() gets the number of tiles in a pyramid level');
    	 	$this->assertInternalType('array',$descriptor->getNumTiles($level), '->getNumTiles() gets the number of tiles in a pyramid level');
			$tiles = $descriptor->getNumTiles($level);
    	 	$this->assertGreaterThan(0,$tiles['columns'], '->getNumTiles() gets the number of tiles in a pyramid level');	
			$this->assertGreaterThan(0,$tiles['rows'], '->getNumTiles() gets the number of tiles in a pyramid level');
    	 }
    }
    
	public function testGetTileBoundsButInvalidArgumentException(){
    	 $descriptor = new Descriptor(new streamStub(),2048,1536);
    	 try {
    		$descriptor->getTileBounds($descriptor->getNumLevels(),0,0);
    		$this->fail('Exception fail');
    	 }catch (\InvalidArgumentException $e) {
    		$this->assertStringStartsWith('Invalid pyramid level',$e->getMessage());
    	 } 
    }
    
    public function testGetTileBounds() {
    	$descriptor = new Descriptor(new streamStub(),2048,1536);
    	foreach (range(0,11) as $level) {
    		$this->assertEquals(array('x', 'y','width', 'height'), array_keys($descriptor->getTileBounds($level,1,1)), '->getTileBounds() gets the bounding box of the tile in a pyramid level');
    	}	
    }
    
	public function testSaveFile()
    {
    	$descriptor = new Descriptor(new File());
    	$descriptor->open($this->path.'model1.xml');
    	$descriptor->save(DEEPZOOM_TESTSUITE_DESTINATION_PATH.'model1-sav.xml');
    	$this->assertFileEquals($this->path.'model1.xml',DEEPZOOM_TESTSUITE_DESTINATION_PATH.'model1-sav.xml');
    }
    
    public function getDumpDescriptor(){
        return <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<Image TileSize="254" Overlap="1" Format="jpg" xmlns="http://schemas.microsoft.com/deepzoom/2008">
	<Size Width="" Height=""/>
</Image>
EOF;
    }    

    public function getInvalidDescriptor(){
        return <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
EOF;
    }    
    
    protected function _getMockFileForOpenTest() {
    	$file = $this->getMock('Deepzoom\\StreamWrapper\\File',array(),array(),'',false);	
    	$file->expects($this->once())
				   ->method('exists')
				   ->with($this->isType('string'))
				   ->will($this->returnValue(true));
		$file->expects($this->once())
		           ->method('getContents')
				   ->with($this->isType('string'))
				   ->will($this->returnCallback(array($this,'getDumpDescriptor')));		   
		return $file;			   
    }
    
	protected function _getMockFileForOpenTestButInvalidXml() {
    	$file = $this->getMock('Deepzoom\\StreamWrapper\\File',array(),array(),'',false);	
    	$file->expects($this->once())
				   ->method('exists')
				   ->with($this->isType('string'))
				   ->will($this->returnValue(true));
		$file->expects($this->once())
		           ->method('getContents')
				   ->with($this->isType('string'))
				   ->will($this->returnCallback(array($this,'getInvalidDescriptor')));		   
		return $file;			   
    }
    
 	protected function _getMockFileForOpenTestButFail() {
    	$file = $this->getMock('Deepzoom\\StreamWrapper\\File',array(),array(),'',false);	
    	$file->expects($this->once())
				   ->method('exists')
				   ->with($this->isType('string'))
				   ->will($this->returnValue(false));
		$file->expects($this->never())
		           ->method('getContents');
		return $file;			   
    }
}