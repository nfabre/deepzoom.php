<?php
namespace Deepzoom\Tests\ImageAdapter;

use Deepzoom\ImageAdapter\GdThumb;
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
 * Testing Image Creator
 *
 * @package    Deepzoom
 * @subpackage Test_ImageAdapter
 * @author     Nicolas Fabre <nicolas.fabre@gmail.com>
 */
class GdThumbTest extends \PHPUnit_Framework_TestCase
{
	static public function setUpBeforeClass()
    {
    }

    public function setUp()
    {
      $this->path = __DIR__.'/../Fixtures/';
    }
    
	protected function tearDown()
    {
        
    }
    
	public function testConstructor()
    {
        $imageCreator = new GdThumb();
        $this->assertType('Deepzoom\ImageAdapter\ImageAdapterInterface',$imageCreator, '__construct()'); 
        $this->assertType('Deepzoom\ImageAdapter\GdThumb',$imageCreator, '__construct()'); 
        $this->assertType('\GdThumb',$imageCreator, '__construct()'); 
    }
    
    public function testSetSource() {
    	$imageCreator = new GdThumb();	
    	$imageCreator->setSource($this->path.'hlegius.jpg');
    	$this->assertAttributeEquals($this->path.'hlegius.jpg','_source',$imageCreator,'setSource()  Set image path');
    	$this->assertAttributeType('array','currentDimensions',$imageCreator,'setSource()  Set image path');
    	$this->assertAttributeType('resource','oldImage',$imageCreator,'setSource()  Set image path');
    }
    
    public function testResize() {
    	$imageCreator = new GdThumb();	
    	$imageCreator->setSource($this->path.'hlegius.jpg');
    	$return = $imageCreator->resize(100,100);
    	$this->assertType('Deepzoom\ImageAdapter\ImageAdapterInterface',$return, 'resize() Resizes an image to be no larger than $width or $height'); 
    	$this->assertAttributeType('array','currentDimensions',$return,'resize() Resizes an image to be no larger than $width or $height'); 
    	// preserve the proportions
    	$this->assertAttributeEquals(array('width' => 100, 'height' => 74),'currentDimensions',$return,'resize() Resizes an image to be no larger than $width or $height');
    	$this->assertAttributeType('resource','oldImage',$return,'resize() Resizes an image to be no larger than $width or $height');
    }
    
    public function testGetDimensions() {
    	$imageCreator = new GdThumb();	
    	$imageCreator->setSource($this->path.'hlegius.jpg');
    	$dimensions = $imageCreator->getDimensions();	
    	$this->assertType('array',$dimensions,'->getDimensions() Returns image dimensions');
    	$this->assertArrayHasKey('height',$dimensions,'->getDimensions() Returns image dimensions');
    	$this->assertArrayHasKey('width',$dimensions,'->getDimensions() Returns image dimensions');
    	$this->assertEquals(array('width' => 2048, 'height' => 1536),$dimensions,'->getDimensions() Returns image dimensions');
    	
    }
    
     public function testSave() {
    	$imageCreator = new GdThumb();	
    	$imageCreator->setSource($this->path.'hlegius.jpg');
     	$imageCreator->save($this->path.'hlegius.save.jpg');
     	$this->assertFileExists($this->path.'hlegius.save.jpg');
     }
     
      public function testCrop() {
      	$imageCreator = new GdThumb();	
    	$imageCreator->setSource($this->path.'hlegius.jpg');
    	$return = $imageCreator->crop(50,50,100,100);
		$this->assertType('Deepzoom\ImageAdapter\ImageAdapterInterface',$return, 'crop() Cropping function that crops an image using $startX and $startY as the upper-left hand corner'); 
    	$this->assertAttributeType('array','currentDimensions',$return,'crop() Cropping function that crops an image using $startX and $startY as the upper-left hand corner'); 
    	$this->assertAttributeEquals(array('width' => 100, 'height' => 100),'currentDimensions',$return,'crop() Cropping function that crops an image using $startX and $startY as the upper-left hand corner'); 
    	$this->assertAttributeType('resource','oldImage',$return,'crop() Cropping function that crops an image using $startX and $startY as the upper-left hand corner'); 
      }
}