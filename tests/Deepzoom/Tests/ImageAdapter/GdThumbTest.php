<?php

/*
 * This file is part of the Deepzoom.php package.
 *
 * (c) Nicolas Fabre <nicolas.fabre@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deepzoom\Tests\ImageAdapter;

use Deepzoom\ImageAdapter\GdThumb;
use Deepzoom\Exception as dzException;
use Deepzoom\StreamWrapper\File;

/**
 * Testing Image Creator
 *
 * @package    Deepzoom
 * @subpackage Test_ImageAdapter
 * @author     Nicolas Fabre <nicolas.fabre@gmail.com>
 */
class GdThumbTest extends \PHPUnit_Framework_TestCase
{
	public function setUp() {
		$this->path = __DIR__ . '/../Fixtures/';
		if (!function_exists('gd_info')) {
			$this->markTestSkipped('Extension GD not loaded');
		}
	}
    
	protected function tearDown() {
    	if(file_exists(DEEPZOOM_TESTSUITE_DESTINATION_PATH.'hlegius.save.jpg')) {
    		unlink(DEEPZOOM_TESTSUITE_DESTINATION_PATH.'hlegius.save.jpg');
    	}
    }
    
	public function testConstructor()
    {
        $imageCreator = new GdThumb();
        $this->assertInstanceOf('Deepzoom\ImageAdapter\ImageAdapterInterface',$imageCreator, '__construct()'); 
        $this->assertInstanceOf('Deepzoom\ImageAdapter\GdThumb',$imageCreator, '__construct()'); 
        $this->assertInstanceOf('\GdThumb',$imageCreator, '__construct()'); 
    }
    
    public function testSetSource() {
    	$imageCreator = new GdThumb();	
    	$imageCreator->setSource($this->path.'hlegius.jpg');
    	$this->assertAttributeEquals($this->path.'hlegius.jpg','_source',$imageCreator,'setSource()  Set image path');
    	$this->assertAttributeInternalType('array','currentDimensions',$imageCreator,'setSource()  Set image path');
    	$this->assertAttributeInternalType('resource','oldImage',$imageCreator,'setSource()  Set image path');
    }
    
    public function testResize() {
    	$imageCreator = new GdThumb();	
    	$imageCreator->setSource($this->path.'hlegius.jpg');
    	$return = $imageCreator->resizePx(100,100);
    	$this->assertInstanceOf('Deepzoom\ImageAdapter\ImageAdapterInterface',$return, 'resize() Resizes an image to be no larger than $width or $height'); 
    	$this->assertAttributeInternalType('array','currentDimensions',$return,'resize() Resizes an image to be no larger than $width or $height'); 
    	// preserve the proportions
    	$this->assertAttributeEquals(array('width' => 100, 'height' => 74),'currentDimensions',$return,'resize() Resizes an image to be no larger than $width or $height');
    	$this->assertAttributeInternalType('resource','oldImage',$return,'resize() Resizes an image to be no larger than $width or $height');
    }
    
    public function testGetDimensions() {
    	$imageCreator = new GdThumb();	
    	$imageCreator->setSource($this->path.'hlegius.jpg');
    	$dimensions = $imageCreator->getDimensions();	
    	$this->assertInternalType('array',$dimensions,'->getDimensions() Returns image dimensions');
    	$this->assertArrayHasKey('height',$dimensions,'->getDimensions() Returns image dimensions');
    	$this->assertArrayHasKey('width',$dimensions,'->getDimensions() Returns image dimensions');
    	$this->assertEquals(array('width' => 2048, 'height' => 1536),$dimensions,'->getDimensions() Returns image dimensions');
    	
    }
    
     public function testSave() {
    	$imageCreator = new GdThumb();	
    	$imageCreator->setStreamWrapper(new File());
    	$imageCreator->setSource($this->path.'hlegius.jpg');
     	$imageCreator->save(DEEPZOOM_TESTSUITE_DESTINATION_PATH.'hlegius.save.jpg');
     	$this->assertFileExists(DEEPZOOM_TESTSUITE_DESTINATION_PATH.'hlegius.save.jpg');
     }
     
      public function testCrop() {
      	$imageCreator = new GdThumb();	
    	$imageCreator->setSource($this->path.'hlegius.jpg');
    	$return = $imageCreator->crop(50,50,100,100);
		$this->assertInstanceOf('Deepzoom\ImageAdapter\ImageAdapterInterface',$return, 'crop() Cropping function that crops an image using $startX and $startY as the upper-left hand corner'); 
    	$this->assertAttributeInternalType('array','currentDimensions',$return,'crop() Cropping function that crops an image using $startX and $startY as the upper-left hand corner'); 
    	$this->assertAttributeEquals(array('width' => 100, 'height' => 100),'currentDimensions',$return,'crop() Cropping function that crops an image using $startX and $startY as the upper-left hand corner'); 
    	$this->assertAttributeInternalType('resource','oldImage',$return,'crop() Cropping function that crops an image using $startX and $startY as the upper-left hand corner'); 
      }
      
     public function testCalcImageSize() {
        $imageCreator = new GdThumb();  
        $imageCreator->setSource($this->path.'hlegius.jpg');
        $method = new \ReflectionMethod(
          'Deepzoom\ImageAdapter\GdThumb', 'calcImageSize'
        );
        $method->setAccessible(TRUE);
        $result = $method->invoke($imageCreator,0,100);
        $this->assertInternalType('array',$result,'calcImageSize() These calculations are based on both the provided dimensions and $this->maxWidth and $this->maxHeight'); 
        $this->assertEquals(array('newWidth' => 1, 'newHeight' => 100),$result,'calcImageSize() These calculations are based on both the provided dimensions and $this->maxWidth and $this->maxHeight'); 
		$result = $method->invoke($imageCreator,0,0);
        $this->assertInternalType('array',$result,'calcImageSize() These calculations are based on both the provided dimensions and $this->maxWidth and $this->maxHeight'); 
        $this->assertEquals(array('newWidth' => 1, 'newHeight' => 1),$result,'calcImageSize() These calculations are based on both the provided dimensions and $this->maxWidth and $this->maxHeight'); 
        
     }
      
      public function testGetStreamWrapper() {
        $imageCreator = new GdThumb();   
        $imageCreator->setStreamWrapper(new File());
        $this->assertAttributeInstanceOf('Deepzoom\StreamWrapper\File','_streamWrapper',$imageCreator);
        $this->assertInstanceOf('Deepzoom\StreamWrapper\File', $imageCreator->getStreamWrapper());
      }
}