<?php
namespace Deepzoom\Tests\ImageAdapter;

use Deepzoom\ImageAdapter\Imagine;
use Deepzoom\Exception as dzException;
use Deepzoom\StreamWrapper\File;

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
 * Testing Image Creator
 *
 * @package    Deepzoom
 * @subpackage Test_ImageAdapter
 * @author     Nicolas Fabre <nicolas.fabre@gmail.com>
 */
class ImagineTest extends \PHPUnit_Framework_TestCase
{
	static public function setUpBeforeClass()
    {
    }

    public function setUp()
    {
      $this->path = __DIR__.'/../Fixtures/';
      $this->markTestSkipped('Work in progress');	
    }
    
	public function testConstructor()
    {
        $imageCreator = new Imagine();
        $this->assertInstanceOf('Deepzoom\ImageAdapter\ImageAdapterInterface',$imageCreator, '__construct()'); 
        $this->assertInstanceOf('Deepzoom\ImageAdapter\Imagine',$imageCreator, '__construct()'); 
    }
    
    public function testSetSource() {
    	$imageCreator = new Imagine();	
    	$imageCreator->setSource($this->path.'hlegius.jpg');
    	$this->assertStringEndsWith('hlegius.jpg',$imageCreator->getImageFilename(),'setSource()  Set image path');
    }
    
    public function testResize() {
    	$imageCreator = new Imagine();	
    	$imageCreator->setSource($this->path.'hlegius.jpg');
    	$return = $imageCreator->resize(100,100);
    	$this->assertInstanceOf('Deepzoom\ImageAdapter\ImageAdapterInterface',$return, 'resize() Resizes an image to be no larger than $width or $height'); 
    	$this->assertInternalType('array',$return->getDimensions(),'resize() Resizes an image to be no larger than $width or $height'); 
    	// preserve the proportions
    	$this->assertEquals(array('width' => 100, 'height' => 75),$return->getDimensions(),'resize() Resizes an image to be no larger than $width or $height');
    }
    
    public function testGetDimensions() {
    	$imageCreator = new Imagine();	
    	$imageCreator->setSource($this->path.'hlegius.jpg');
    	$dimensions = $imageCreator->getDimensions();	
    	$this->assertInternalType('array',$dimensions,'->getDimensions() Returns image dimensions');
    	$this->assertArrayHasKey('height',$dimensions,'->getDimensions() Returns image dimensions');
    	$this->assertArrayHasKey('width',$dimensions,'->getDimensions() Returns image dimensions');
    	$this->assertEquals(array('width' => 2048, 'height' => 1536),$dimensions,'->getDimensions() Returns image dimensions');
    	
    }
    
     public function testSave() {
     	unlink($this->path.'hlegius.save.jpg');
    	$imageCreator = new Imagine();	
    	$imageCreator->setStreamWrapper(new File());
    	$imageCreator->setSource($this->path.'hlegius.jpg');
     	$imageCreator->save($this->path.'hlegius.save.jpg');
     	$this->assertFileExists($this->path.'hlegius.save.jpg');
     }
     
      public function testCrop() {
      	$imageCreator = new Imagine();	
    	$imageCreator->setSource($this->path.'hlegius.jpg');
    	$return = $imageCreator->crop(50,50,100,100);
		$this->assertInstanceOf('Deepzoom\ImageAdapter\ImageAdapterInterface',$return, 'crop() Cropping function that crops an image using $startX and $startY as the upper-left hand corner'); 
    	$this->assertEquals(array('width' => 100, 'height' => 100),$return->getDimensions(),'crop() Cropping function that crops an image using $startX and $startY as the upper-left hand corner'); 
      }
      
      public function testNewImage() {
        $imageCreator = new Imagine();  
        $imageCreator->setStreamWrapper(new File());
        //$imageCreator->newImage(50,50);
     }
     
      
      public function testGetStreamWrapper() {
        $imageCreator = new Imagine();   
        $imageCreator->setStreamWrapper(new File());
        $this->assertAttributeInstanceOf('Deepzoom\StreamWrapper\File','_streamWrapper',$imageCreator);
        $this->assertInstanceOf('Deepzoom\StreamWrapper\File', $imageCreator->getStreamWrapper());
      }
}