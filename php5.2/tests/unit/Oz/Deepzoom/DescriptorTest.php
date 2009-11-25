<?php
require_once 'Oz/Deepzoom/Descriptor.php';

/**
 * Oz_Deepzoom_Descriptor test case.
 */
class Oz_Deepzoom_DescriptorTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * @var Oz_Deepzoom_Descriptor
	 */
	private $_descriptor;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
        $this->sourceDescriptor = $this->getFile('source/model_hlegius.xml');
        $this->destDescriptor = $this->getFile('dest/hlegius.xml','w');
        
        $xml = simplexml_load_file($this->sourceDescriptor);
        $this->width = (int)$xml->Size["Width"];
        $this->height = (int)$xml->Size["Height"];
        $this->tileSize = (string)$xml["TileSize"];
        $this->tileOverlap	= (string)$xml["Overlap"];
        $this->tileFormat	= (string)$xml["Format"];
        
		$this->_descriptor = new Oz_Deepzoom_Descriptor($this->width,$this->height,
		                          $this->tileSize,$this->tileOverlap,$this->tileFormat);
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		$this->_descriptor = null;
	}
	
	/**
	 * Tests new Oz_Deepzoom_Descriptor()
	 */
	public function test__construct() {
		$this->assertEquals($this->width,$this->_descriptor->width); 		
        $this->assertEquals($this->height,$this->_descriptor->height); 		
        $this->assertEquals($this->tileSize,$this->_descriptor->tileSize); 		
        $this->assertEquals($this->tileOverlap,$this->_descriptor->tileOverlap); 		
        $this->assertEquals($this->tileFormat,$this->_descriptor->tileFormat); 		
	
	}
	
	/**
	 * Tests Oz_Deepzoom_Descriptor->open()
	 */
	public function testOpen() {
		
		$this->_descriptor->open($this->sourceDescriptor);
		
		$this->assertEquals($this->width,$this->_descriptor->width); 		
        $this->assertEquals($this->height,$this->_descriptor->height); 		
        $this->assertEquals($this->tileSize,$this->_descriptor->tileSize); 		
        $this->assertEquals($this->tileOverlap,$this->_descriptor->tileOverlap); 		
        $this->assertEquals($this->tileFormat,$this->_descriptor->tileFormat); 	
	}
	
	/**
	 * Tests Oz_Deepzoom_Descriptor->save()
	 */
	public function testSave() {
		$this->_descriptor->save($this->destDescriptor);
		$this->assertXmlFileEqualsXmlFile($this->destDescriptor,$this->sourceDescriptor);
	}
	
	/**
	 * Tests Oz_Deepzoom_Descriptor->numLevels()
	 */
	public function testNumLevels() {
		$levels = $this->_descriptor->numLevels();
        $this->assertGreaterThan(0,$levels);  
	}
	
	/**
	 * Tests Oz_Deepzoom_Descriptor->getScale()
	 */
	public function testGetScale() {
		try {
          $scale = $this->_descriptor->getScale(10);
          $this->assertTrue(is_float($scale));
          $this->assertGreaterThan(0,$scale);          
        } catch(Gr_Deepzoom_Exception $e) {
            $this->fail();
        	return;
        }
	}
	
	public function testInvalidScale() {
		try {
			$this->_descriptor->getScale(-1);
		}catch(Oz_Deepzoom_Exception $e) {
			 $this->assertTrue('Invalid pyramid level (scale)' === $e->getMessage());
			 return;
		}
		$this->fail();
        return;
	}
	
	/**
	 * Tests Oz_Deepzoom_Descriptor->getDimension()
	 */
	public function testGetDimension() {
		list($width,$height) = $this->_descriptor->getDimension(11);
        $this->assertEquals($this->width,$width);	   
        $this->assertEquals($this->height,$height);
	}
	
	/**
	 *	Tests Oz_Deepzoom_Descriptor->getDimension() 
	 */
	public function testInvalidDimension() {
		try {
			$this->_descriptor->getDimension(-1);
		}catch(Oz_Deepzoom_Exception $e) {
			 $this->assertTrue('Invalid pyramid level (dimension)' === $e->getMessage());
			return;
		}
		$this->fail();
        return;
	}
		
	/**
	 * Tests Oz_Deepzoom_Descriptor->getNumTiles()
	 */
	public function testGetNumTiles() {
		$numTiles = $this->_descriptor->getNumTiles(11);
		$this->assertType('array',$numTiles);
		$this->assertEquals(
          		array(9,7),
          		$numTiles
        );
	}
	
	/**
	 * Tests Oz_Deepzoom_Descriptor->getNumTiles()
	 */
	public function testInvalidGetNumTiles() {
		try {
		$this->_descriptor->getNumTiles(-1);
		}catch(Oz_Deepzoom_Exception $e) {
			 $this->assertTrue('Invalid pyramid level (NumTiles)' === $e->getMessage());
			return;
		}
		$this->fail();
        return;
	}	
	
	/**
	 * Tests Oz_Deepzoom_Descriptor->getTileBounds()
	 */
	public function testGetTileBounds() {
		
		try {
			$tileBounds = $this->_descriptor->getTileBounds(11,0,0);
			// Check type
			$this->assertType('array',$tileBounds);
			
			$this->assertEquals(
          		array(0,0,255,255),
          		$tileBounds
          	);
		}catch(Oz_Deepzoom_Exception $e) {
			$this->fail();
			return;
		}
	}
	
	/**
	 * Tests Oz_Deepzoom_Descriptor->getTileBounds()
	 */
	public function testInvalidGetTileBounds() {
		try {
		$this->_descriptor->getTileBounds(-1,0,0);
		}catch(Oz_Deepzoom_Exception $e) {
			 $this->assertTrue('Invalid pyramid level (TileBounds)' === $e->getMessage());
			return;
		}
		$this->fail();
        return;
	}	
	
	protected function getFile($filename)
    {
        return 
          dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR .
          '_files' . DIRECTORY_SEPARATOR . $filename
        ;
    }
}

