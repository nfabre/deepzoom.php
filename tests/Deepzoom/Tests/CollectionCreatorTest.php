<?php
namespace Deepzoom\Tests;

use Deepzoom;

use Deepzoom\ImageAdapter\GdThumb;
use Deepzoom\Tests\Fixtures\ImageAdapter\Stub;
use Deepzoom\CollectionCreator;
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
class CollectionCreatorTest extends \PHPUnit_Framework_TestCase
{
	static public function setUpBeforeClass()
    {
    }

    public function setUp()
    {
        $this->path = __DIR__.'/Fixtures/';
    	if (file_exists($this->path . 'collection.xml')) {
    		unlink($this->path . 'collection.xml');
        }
    }
    
	protected function tearDown()
    {
        if (file_exists($this->path . 'collection.xml')) {
            unlink($this->path . 'collection.xml');
        }
    }
    
    public function testConstructor()
    {
        $collectionCreator = new CollectionCreator(new Descriptor(),new GdThumb(),10,'png',3);
        $this->assertAttributeType('Deepzoom\DescriptorInterface','_descriptor',$collectionCreator, '__construct() takes the tile size as its first argument'); 
        $this->assertAttributeType('Deepzoom\ImageAdapter\ImageAdapterInterface','_imageAdapter',$collectionCreator, '__construct() takes the tile size as its first argument'); 
        $this->assertAttributeEquals(10,'_tileSize',$collectionCreator, '__construct() takes the tile size as its first argument'); 
        $this->assertAttributeEquals('png','_tileFormat',$collectionCreator, '__construct() takes the tile size as its first argument'); 
        $this->assertAttributeEquals(3,'_maxLevel',$collectionCreator, '__construct() takes the tile size as its first argument'); 
    }
    /*
    public function testCreate() {
    	//$images = array($this->path.'hlegius.xml',$this->path.'hlegius.xml');
    	$images = array($this->path.'hlegius.xml');
        $collectionCreator = new CollectionCreator(new Descriptor(),new Stub(),254,'jpg',8);
        $collectionCreator->create($images,$this->path.'collection.xml');  
        
        $this->assertFileExists($this->path.'collection.xml','-->create() Creates Deep Zoom image from source file and saves it to destination');
        $xml = simplexml_load_file($this->path.'collection.xml');
        
        $this->assertNotNull($xml['MaxLevel']);
        $this->assertEquals(8,(int)$xml['MaxLevel']);
        $this->assertNotNull($xml['TileSize']);
        $this->assertEquals(254,(int)$xml['TileSize']);
        $this->assertNotNull($xml['Format']);
        $this->assertEquals('jpg',(string)$xml['Format']);
        $this->assertNotNull($xml['NextItemId']);
        $this->assertEquals(count($images),(int)$xml['NextItemId']);
        $this->assertNotNull($xml->Items);
        
        $this->assertNotNull($xml->Items->I);
        $this->assertEquals(count($images),$xml->Items->I->count());
        $cpt = 0;
        foreach($xml->Items->I as $i) {
            $this->assertNotNull($i['Id']);
            $this->assertEquals($cpt,(int)$i['Id']);	
            $this->assertNotNull($i['N']);
            $this->assertEquals($cpt,(int)$i['N']);  
            $this->assertSame((int)$i['N'],(int)$i['Id']);
            $this->assertNotNull($i['Source']);
            $this->assertStringEndsWith($images[$cpt],(string)$i['Source']);
            $this->assertNotNull($i->Size);
            $this->assertNotNull($i->Size['Width']);
            $this->assertGreaterThan(0,$i->Size['Width']);
            $this->assertNotNull($i->Size['Height']);
            $this->assertGreaterThan(0,$i->Size['Height']);
            $cpt++;
        }
    }*/
}