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

use Deepzoom\ImageAdapter\Imagick;
use Deepzoom\Exception as dzException;
use Deepzoom\StreamWrapper\File;

/**
 * Testing Image Creator
 *
 * @package    Deepzoom
 * @subpackage Test_ImageAdapter
 * @author     Nicolas Fabre <nicolas.fabre@gmail.com>
 */
class ImagickTest extends \PHPUnit_Framework_TestCase
{
	static public function setUpBeforeClass()
    {
    }

    public function setUp()
    {
    	if (!class_exists('Imagick')) {
            $this->markTestSkipped('Imagick is not installed');
         }
         $this->path = __DIR__.'/../Fixtures/';
    }
    
	protected function tearDown()
    {
    	if(file_exists(DEEPZOOM_TESTSUITE_DESTINATION_PATH.'hlegius.save.jpg')) {
    		unlink(DEEPZOOM_TESTSUITE_DESTINATION_PATH.'hlegius.save.jpg');
    	}
    }
    
	public function testConstructor()
    {
        $imageCreator = new Imagick();
        $this->assertInstanceOf('Deepzoom\ImageAdapter\ImageAdapterInterface',$imageCreator, '__construct()'); 
        $this->assertInstanceOf('Deepzoom\ImageAdapter\Imagick',$imageCreator, '__construct()'); 
        $this->assertInstanceOf('\Imagick',$imageCreator, '__construct()'); 
    }
    
    public function testSetSource() {
    	$imageCreator = new Imagick();	
    	$imageCreator->setSource($this->path.'hlegius.jpg');
    	$this->assertStringEndsWith('hlegius.jpg',$imageCreator->getImageFilename(),'setSource()  Set image path');
    }
    
    public function testResize() {
    	$imageCreator = new Imagick();	
    	$imageCreator->setSource($this->path.'hlegius.jpg');
    	$return = $imageCreator->resizePx(100,100);
    	$this->assertInstanceOf('Deepzoom\ImageAdapter\ImageAdapterInterface',$return, 'resize() Resizes an image to be no larger than $width or $height'); 
    	$this->assertInternalType('array',$return->getDimensions(),'resize() Resizes an image to be no larger than $width or $height'); 
    	// preserve the proportions
    	$this->assertEquals(array('width' => 100, 'height' => 75),$return->getDimensions(),'resize() Resizes an image to be no larger than $width or $height');
    }
    
    public function testGetDimensions() {
    	$imageCreator = new Imagick();	
    	$imageCreator->setSource($this->path.'hlegius.jpg');
    	$dimensions = $imageCreator->getDimensions();	
    	$this->assertInternalType('array',$dimensions,'->getDimensions() Returns image dimensions');
    	$this->assertArrayHasKey('height',$dimensions,'->getDimensions() Returns image dimensions');
    	$this->assertArrayHasKey('width',$dimensions,'->getDimensions() Returns image dimensions');
    	$this->assertEquals(array('width' => 2048, 'height' => 1536),$dimensions,'->getDimensions() Returns image dimensions');
    	
    }
    
     public function testSave() {
    	$imageCreator = new Imagick();	
    	$imageCreator->setStreamWrapper(new File());
    	$imageCreator->setSource($this->path.'hlegius.jpg');
     	$imageCreator->save(DEEPZOOM_TESTSUITE_DESTINATION_PATH.'hlegius.save.jpg');
     	$this->assertFileExists(DEEPZOOM_TESTSUITE_DESTINATION_PATH.'hlegius.save.jpg');
     }
     
      public function testCrop() {
      	$imageCreator = new Imagick();	
    	$imageCreator->setSource($this->path.'hlegius.jpg');
    	$return = $imageCreator->crop(50,50,100,100);
		$this->assertInstanceOf('Deepzoom\ImageAdapter\ImageAdapterInterface',$return, 'crop() Cropping function that crops an image using $startX and $startY as the upper-left hand corner'); 
    	$this->assertEquals(array('width' => 100, 'height' => 100),$return->getDimensions(),'crop() Cropping function that crops an image using $startX and $startY as the upper-left hand corner'); 
      }
      
      public function testGetStreamWrapper() {
      	$imageCreator = new Imagick();   
      	$imageCreator->setStreamWrapper(new File());
      	$this->assertInstanceOf('Deepzoom\StreamWrapper\File', $imageCreator->getStreamWrapper());
      }
}