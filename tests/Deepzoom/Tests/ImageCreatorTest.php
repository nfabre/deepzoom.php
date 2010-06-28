<?php

/*
 */

namespace Deepzoom\Tests;

use Deepzoom;

use Deepzoom\ImageAdapter\PhpThumb;
use Deepzoom\ImageCreator;
use Deepzoom\Descriptor;
use Deepzoom\Exception as dzException;

class ImageCreatorTest extends \PHPUnit_Framework_TestCase
{
	static public function setUpBeforeClass()
    {
    }

    public function setUp()
    {
        $this->path = __DIR__.'/../../fixtures/Deepzoom/ImageCreator/';
    	if (file_exists(__DIR__ . '/path')) {
            rmdir(__DIR__. '/path');
        }
    }
    
	protected function tearDown()
    {
        if (file_exists(__DIR__ . '/path')) {
            rmdir(__DIR__. '/path');
        }
    }
    
	public function testConstructor()
    {
        $imageCreator = new ImageCreator(new Descriptor(),new PhpThumb(),10,0.5,'png');
        $this->assertAttributeType('Deepzoom\DescriptorInterface','_descriptor',$imageCreator, '__construct() takes the tile size as its first argument'); 
        $this->assertAttributeType('Deepzoom\ImageAdapter\ImageAdapterInterface','_imageAdapter',$imageCreator, '__construct() takes the tile size as its first argument'); 
        $this->assertAttributeEquals(10,'_tileSize',$imageCreator, '__construct() takes the tile size as its first argument'); 
        $this->assertAttributeEquals(0,'_tileOverlap',$imageCreator, '__construct() takes the tile size as its first argument'); 
        $this->assertAttributeEquals('png','_tileFormat',$imageCreator, '__construct() takes the tile size as its first argument'); 
    }
    
 	public function testGetTiles()
    {
    	$stub = $this->_createMockDescriptor();
    	$imageCreator = new ImageCreator($stub,new PhpThumb(),10,0.5,'png');
    	$tiles = $imageCreator->getTiles(10);
     	$this->assertType('array',$tiles, '->getTiles() Iterator for all tiles in the given level');
        $this->assertEquals(100,sizeof($tiles), '->getTiles() Iterator for all tiles in the given level');
    }
    
    public function testGetImageException() {
    	$imageCreator = new ImageCreator($this->_createMockDescriptor(),new PhpThumb(),10,0.5,'png');
    	try {
    		$imageCreator->getImage(-1);
    		$this->fail('Excepion fail');
    	} catch(dzException $e) {
    		$this->assertStringStartsWith('Invalid pyramid level',$e->getMessage());	
    		try {
    			$imageCreator->getImage(700);
    			$this->fail('Excepion fail');
    		} catch(dzException $e) {	
    			$this->assertStringStartsWith('Invalid pyramid level',$e->getMessage());	
    		}
    	}	
    }
    
    public function testGetImage() {
    	$stub = $this->_createMockDescriptor();
    	$stub->expects($this->any())
                 ->method('getDimension')
                 ->will($this->returnValue(array('width' => 800,'height' => 800)));	
    	$imageCreator = new ImageCreator($stub,$this->_createMockPhpThumb(),10,0.5,'png');
    	$this->assertTrue($imageCreator->getImage(8) instanceof PhpThumb);
    	$stub = $this->_createMockDescriptor();
    	$stub->expects($this->any())
                 ->method('getDimension')
                 ->will($this->returnValue(array('width' => 500,'height' => 500)));	
    	$imageCreator = new ImageCreator($stub,$this->_createMockPhpThumb(),10,0.5,'png');
    	$this->assertTrue($imageCreator->getImage(8) instanceof PhpThumb);
    }
    
    
    public function testCreate() {
    	$stub = $this->_createMockDescriptor();
    	$stub->expects($this->any())
                 ->method('getDimension')
                 ->will($this->returnValue(array('width' => 800,'height' => 800)));	
    	$imageCreator = new ImageCreator(new Descriptor(),$this->_createMockPhpThumb(),10,0.5,'png');
    	$imageCreator->create($this->path.'hlegius.jpg',$this->path.'hlegius.xml');            	 	
    }
    
    public function testClamp()
    {
    	$imageCreator = new ImageCreator(new Descriptor(),new PhpThumb(),10,0.5,'png');
     	$method = new \ReflectionMethod(
          'Deepzoom\ImageCreator', '_clamp'
        );
 
        $method->setAccessible(TRUE);
 
        $this->assertEquals(
        	0, $method->invoke($imageCreator,-2,0,1),'->_clamp() Ensures that $val is between the limits set by $min and $max.'
        );
        $this->assertEquals(
        	1, $method->invoke($imageCreator,5,0,1),'->_clamp() Ensures that $val is between the limits set by $min and $max.'
        );
        $this->assertEquals(
        	0.5, $method->invoke($imageCreator,0.5,0,1),'->_clamp() Ensures that $val is between the limits set by $min and $max.'
        );	
    }
    
    public function testEnsure()
    {
    	$imageCreator = new ImageCreator(new Descriptor(),new PhpThumb(),10,0.5,'png');
    	$this->assertFalse(file_exists(__DIR__ . '/path'));
    	$method = new \ReflectionMethod(
          'Deepzoom\ImageCreator', '_ensure'
        );
        $method->setAccessible(TRUE);
    	$method->invoke($imageCreator,__DIR__ . '/path');
    	$this->assertTrue(file_exists(__DIR__ . '/path'));
    }
    
    protected function _createMockDescriptor() {
    	$stub = $this->getMock('Deepzoom\Descriptor');
        $stub->expects($this->any())
        	  ->method('getNumLevels')
              ->will($this->returnValue(10));
         $stub->expects($this->any())
        	  ->method('getWidth')
              ->will($this->returnValue(500));
        $stub->expects($this->any())
        	  ->method('getHeight')
              ->will($this->returnValue(500));      
        $stub->expects($this->any())
                 ->method('getNumTiles')
                 ->will($this->returnValue(array('columns' => 10,'rows' => 10)));     
                    
    	return $stub;    
    }
    
     protected function _createMockPhpThumb() {
     	$stub = $this->getMock('Deepzoom\ImageAdapter\PhpThumb',array(), array(),'',TRUE,false,true );	
     	$stub->expects($this->any())
                 ->method('__clone')
                 ->will($this->returnValue($this));	
        $stub->expects($this->any())
                 ->method('resize')
                 ->will($this->returnValue(new PhpThumb()));	
                 ;         
     	return $stub;
     }
}