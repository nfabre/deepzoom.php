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

use Deepzoom\Tests\Fixtures\ImageAdapter\Stub as imageStub;

use Deepzoom;

use Deepzoom\ImageCreator;
use Deepzoom\Descriptor;
use Deepzoom\Tests\Fixtures\StreamWrapper\Stub as streamStub;
use Deepzoom\StreamWrapper\File;
use Deepzoom\ImageAdapter\GdThumb;

/**
 * Testing ImageCreator
 *
 * @package    Deepzoom
 * @subpackage Test_Deepzoom
 * @author     Nicolas Fabre <nicolas.fabre@gmail.com>
 */
class ImageCreatorTest extends \PHPUnit_Framework_TestCase {

	public function setUp() {
		$this->path = __DIR__.DEEPZOOM_TESTSUITE_FIXTURES_PATH;			
	}
	
	public function testGetTiles() {

		$imageCreator = new ImageCreator(new File,$this->_getMockDescriptorForGetNumTiles() , new GdThumb);
		$yield = $imageCreator->getTiles(10);
		$this->assertInternalType('array', $yield);
		$this->assertEquals(100, count($yield));
	}
	
	
	public function testGetImage() {
		$img = new GdThumb;
		$descriptor = $this->_getMockDescriptor();
		$descriptor->expects($this->atLeastOnce())
        		   ->method('getWidth')  
        		   ->will($this->returnValue(100));		   
        $descriptor->expects($this->atLeastOnce())
        		   ->method('getHeight')  
        		   ->will($this->returnValue(100));			  
        		   
		$imageCreator = new ImageCreator(new File,$descriptor , $img);
		$image = $imageCreator->getImage(10);
		$this->assertInstanceOf('Deepzoom\ImageAdapter\GdThumb', $image);
		$this->assertSame($img, $image);			
	}
	
	public function testGetImageWithResizing() {
		$img = $this->_getMockImage();
		
		$descriptor = $this->_getMockDescriptor();
		$descriptor->expects($this->atLeastOnce())
        		   ->method('getWidth')  
        		   ->will($this->returnValue(200));		   
        		   	
		$imageCreator = new ImageCreator(new File, $descriptor, $img);
		$image = $imageCreator->getImage(10);
		$this->assertInstanceOf('Deepzoom\ImageAdapter\GdThumb', $image);
		$this->assertNotSame($img, $image);			
	}
	
	public function testGetImageButInvalidArgumentException() {
		try {
			$imageCreator = new ImageCreator(new File, $this->_getMockDescriptorButInvalidArgumentException(), new GdThumb);	
			$imageCreator->getImage(10);
		}catch (\InvalidArgumentException $e) {
    		$this->assertStringStartsWith('Invalid pyramid level',$e->getMessage());
    	}
	}
	
	public function testCreate() {
		$imageCreator = new ImageCreator(new File, new Descriptor(new File), new imageStub());	
		$imageCreator->create($this->path.'hlegius.jpg', DEEPZOOM_TESTSUITE_DESTINATION_PATH.'hlegius.xml');
		$this->assertFileExists(DEEPZOOM_TESTSUITE_DESTINATION_PATH.'hlegius.xml');
	}
	
	public function testGetDescriptor() {
		$descriptor = new Descriptor(new File);
		$imageCreator = new ImageCreator(new File,$descriptor , new GdThumb);
		$this->assertInstanceOf('Deepzoom\Descriptor',$imageCreator->getDescriptor());
		$this->assertSame($descriptor, $imageCreator->getDescriptor());				
	}
	
	public function testClamp() {
		$class = new \ReflectionClass('Deepzoom\ImageCreator');
	    $method = $class->getMethod('_clamp');
	    $method->setAccessible(true);
		$imageCreator = new ImageCreator(new File,new Descriptor(new File) , new GdThumb);
	    $this->assertEquals(0, $method->invokeArgs($imageCreator, array(-1,0,1)));
	    $this->assertEquals(0, $method->invokeArgs($imageCreator, array(0,0,1)));
	    $this->assertEquals(1, $method->invokeArgs($imageCreator, array(2,0,1)));
	    $this->assertEquals(1, $method->invokeArgs($imageCreator, array(1,0,1)));
	}
	
	protected function _getMockDescriptorButInvalidArgumentException() {
		// Create a Mock Object for the Deepzoom\Descriptor class
		$descriptor = $this->getMock('Deepzoom\\Descriptor',array('getNumLevels'),array(),'',false);	
		$descriptor->expects($this->atLeastOnce())
                   ->method('getNumLevels')
                   ->will($this->returnValue(1));
                   
    	return $descriptor;               
	}
	
	protected function _getMockDescriptorForGetNumTiles() {
		// Create a Mock Object for the Deepzoom\Descriptor class
		$descriptor = $this->getMock('Deepzoom\\Descriptor',array(),array(),'',false);	
		$descriptor->expects($this->once())
				   ->method('getNumTiles')
				   ->with($this->equalTo(10))
				   ->will($this->returnValue(array('columns'=>10,'rows' => 10)));
		return $descriptor;   
	}
	
	protected function _getMockDescriptor() {
		// Create a Mock Object for the Deepzoom\Descriptor class
		$descriptor = $this->getMock('Deepzoom\\Descriptor',array(),array(),'',false);	
		$descriptor->expects($this->atLeastOnce())
                   ->method('getNumLevels')
                   ->will($this->returnValue(20));
        $descriptor->expects($this->atLeastOnce())       
        		   ->method('getDimension')  
        		   ->with($this->equalTo(10))
        		   ->will($this->returnValue(array('width'=>100,'height' => 100))); 
        		   
    	return $descriptor;               
	}
	
	protected function _getMockImage() {
		$image = $this->getMock('Deepzoom\\ImageAdapter\\GdThumb',array('resizePx'),array(),'',false);	
		$image->expects($this->atLeastOnce())       
        		   ->method('resizePx')  
        		   ->with($this->equalTo(100),$this->equalTo(100))
        		   ->will($this->returnValue(new GdThumb())); 
        		   
		return $image;
	}
}