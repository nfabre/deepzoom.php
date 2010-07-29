<?php
namespace Deepzoom\Tests;

use Deepzoom;

use Deepzoom\ImageAdapter\GdThumb;
use Deepzoom\StreamWrapper\File;
use Deepzoom\Tests\Fixtures\ImageAdapter\Stub;
use Deepzoom\Tests\Fixtures\StreamWrapper\Stub as streamStub;
use Deepzoom\ImageCreator;
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
 * Testing Image Creator
 *
 * @package    Deepzoom
 * @subpackage Test_Deepzoom
 * @author     Nicolas Fabre <nicolas.fabre@gmail.com>
 */
class ImageCreatorTest extends \PHPUnit_Framework_TestCase
{
	static public function setUpBeforeClass()
    {
    }

    public function setUp()
    {
        $this->path = __DIR__.'/Fixtures/';
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
        $imageCreator = new ImageCreator(new streamStub(),new Descriptor(new streamStub()),new GdThumb(),10,0.5,'png');
        $this->assertAttributeType('Deepzoom\DescriptorInterface','_descriptor',$imageCreator, '__construct() takes the tile size as its first argument'); 
        $this->assertAttributeType('Deepzoom\ImageAdapter\ImageAdapterInterface','_imageAdapter',$imageCreator, '__construct() takes the tile size as its first argument'); 
        $this->assertAttributeEquals(10,'_tileSize',$imageCreator, '__construct() takes the tile size as its first argument'); 
        $this->assertAttributeEquals(0,'_tileOverlap',$imageCreator, '__construct() takes the tile size as its first argument'); 
        $this->assertAttributeEquals('png','_tileFormat',$imageCreator, '__construct() takes the tile size as its first argument'); 
    }
    
 	public function testGetTiles()
    {
    	$stub = $this->_createMockDescriptor();
    	$imageCreator = new ImageCreator(new streamStub(),$stub,new GdThumb(),10,0.5,'png');
    	$tiles = $imageCreator->getTiles(10);
     	$this->assertType('array',$tiles, '->getTiles() Iterator for all tiles in the given level');
        $this->assertEquals(100,sizeof($tiles), '->getTiles() Iterator for all tiles in the given level');
    }
    
    public function testGetImageException() {
    	$imageCreator = new ImageCreator(new streamStub(),$this->_createMockDescriptor(),new GdThumb(),10,0.5,'png');
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
    	$imageCreator = new ImageCreator(new streamStub(),$stub,$this->_createMockGdThumb(),10,0.5,'png');
    	$this->assertTrue($imageCreator->getImage(8) instanceof GdThumb);
    	$stub = $this->_createMockDescriptor();
    	$stub->expects($this->any())
                 ->method('getDimension')
                 ->will($this->returnValue(array('width' => 500,'height' => 500)));	
    	$imageCreator = new ImageCreator(new streamStub(),$stub,$this->_createMockGdThumb(),10,0.5,'png');
    	$this->assertTrue($imageCreator->getImage(8) instanceof GdThumb);
    }
    
    public function testGetDescriptor() {
        $imageCreator = new ImageCreator(new streamStub(),new Descriptor(new streamStub()),new GdThumb());
        $this->assertType('Deepzoom\DescriptorInterface',$imageCreator->getDescriptor(), '__construct() takes the tile size as its first argument'); 
        
    }
    
    public function testCreate() {
    	$imageCreator = new ImageCreator(new streamStub(),new Descriptor(new streamStub()),new Stub(),254,1,'jpg');
    	$imageCreator->create($this->path.'hlegius.jpg',$this->path.'hlegius.xml');  
    	$this->assertFileExists($this->path.'hlegius.xml','-->create() Creates Deep Zoom image from source file and saves it to destination');
    }
    
    public function testCreateMillion() {
        $imageCreator = new ImageCreator(new streamStub(),new Descriptor(new streamStub()),new Stub(50000,50000),254,1,'jpg');	
        $imageCreator->create($this->path.'hlegius.jpg',$this->path.'hlegius.xml');  
    }
    
    public function testClamp()
    {
    	$imageCreator = new ImageCreator(new streamStub(),new Descriptor(new streamStub()),new GdThumb(),10,0.5,'png');
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
    	$imageCreator = new ImageCreator(new streamStub(),new Descriptor(new streamStub()),new GdThumb(),10,0.5,'png');
    	$this->assertFalse(file_exists(__DIR__ . '/path'));
    	$method = new \ReflectionMethod(
          'Deepzoom\ImageCreator', '_ensure'
        );
        $method->setAccessible(TRUE);
    	$method->invoke($imageCreator,__DIR__ . '/path');
    	$this->assertTrue(file_exists(__DIR__ . '/path'));
    }
    
    protected function _createMockDescriptor() {
    	$stub = $this->getMock('Deepzoom\Descriptor',array(), array(),'',false,false,true );
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
    
     protected function _createMockGdThumb() {
     	$stub = $this->getMock('Deepzoom\ImageAdapter\GdThumb',array(), array(),'',true,false,true );	
     	$stub->expects($this->any())
                 ->method('__clone')
                 ->will($this->returnValue($this));	
        $stub->expects($this->any())
                 ->method('resize')
                 ->will($this->returnValue(new GdThumb()));	
                 ;         
     	return $stub;
     }
}