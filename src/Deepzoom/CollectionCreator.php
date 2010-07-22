<?php
namespace Deepzoom;

use Deepzoom\ImageAdapter;
use Deepzoom\ImageAdapter\ImageAdapterInterface;
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
 * Collection Creator
 *
 * @package    Deepzoom
 * @author     Nicolas Fabre <nicolas.fabre@gmail.com>
 */
class CollectionCreator extends AbstractCreator {
	const NS_DEEPZOOM = 'http://schemas.microsoft.com/deepzoom/2009';

    /**
     * Max level
     * 
     * @var int
     */ 
    protected $_maxLevel; 
    	
    /**
     * 
     * @var Deepzoom\DescriptorInterface
     */
    protected $_descriptor;

    /**
     * 
     * @var Deepzoom\ImageAdapter\ImageAdapterInterface
     */
    protected $_imageAdapter;

    /**
     * Constructor
     * 
     * @param Deepzoom\DescriptorInterface $descriptor
     * @param Deepzoom\ImageAdapter\ImageAdapterInterface $adapter
     */
	public function __construct(DescriptorInterface $descriptor, ImageAdapterInterface  $adapter, $tileSize=254, $tileFormat='jpg',$maxLevel = 8) {
	    $this->_descriptor = $descriptor;
        $this->_imageAdapter = $adapter;
        $this->_tileSize = (int) $tileSize;
        $this->_tileFormat = $tileFormat;
        $this->_maxLevel = $maxLevel;
	}
	
	/**
	 * Creates a Deep Zoom collection from a list of images
	 * 
	 * @param array $images
	 * @param string $destination
	 */
	public function create(array $images,$destination) {
	   $this->createPyramid($images,$destination);	
	   $this->createDescriptor($images,$destination);	
	}
	
	/**
	 * Creates a Deep Zoom collection pyramid from a list of images.
	 *
	 * @param array $images
     * @param string $destination
	 */
	protected function createPyramid(array $images,$destination) {
        $pyramidPath = dirname($destination); 		
		$this->_ensure($pyramidPath.'_files');
		
		for($level=0;$level<=$this->_maxLevel;$level++) {
           $levelSize = pow(2,$level);
           $levelPath = $pyramidPath + DIRECTORY_SEPARATOR + $level; 
           $this->_ensure($levelPath);
            for($i=0;$i<sizeof($images);$i++) {
            	$path = $images[$i];
            	$this->_descriptor->open($path);
            	$tilePosition = $this->getTilePosition($i,$level);
            	$tilePath = $levelPath . sprintf("/%s_%s.%s",$tilePosition['column'], $tilePosition['row'], $this->_tileFormat);
            	
                if(!file_exists($tilePath)) {
                	$this->_imageAdapter->newImage($this->_tileSize,$this->_tileSize);
                	$this->_imageAdapter->save($tilePath, $this->_tileFormat);
                }
                //$tileIimage = $this->_imageAdapter->setSource($tilePath);
                $sourcePath = dirname($path) + "_files/" + $level. sprintf("/%s_%s.%s",0, 0, $this->_descriptor->getTileFormat());
                //$sourceImage = $this->_imageAdapter->setSource($sourcePath);
                
                $imagesPerTile = floor($this->_tileSize / $levelSize);
                $position = $this->getPosition($i);  
                //$coordX = ($position['column'] % $imagesPerTile) * $levelSize;    	
                //$coordY = ($position['row'] % $imagesPerTile) * $levelSize;    	
               var_Dump(array($level,$position));
            }
		}
	}
	
	/**
	 * Creates a Deep Zoom collection descriptor from a list of images.
	 * 
	 * @param array $images
	 * @param string $destination
	 */
	protected function createDescriptor(array $images,$destination) {
	    $dom = new \DOMDocument('1.0', 'utf-8');
	    $collection = $dom->createElementNS(self::NS_DEEPZOOM, "Collection");
	    $dom->appendChild($collection);
	    $collection->setAttribute('xmlns',self::NS_DEEPZOOM);
	    $collection->setAttribute('MaxLevel',$this->_maxLevel);
	    $collection->setAttribute('TileSize',$this->_tileSize);
	    $collection->setAttribute('Format',$this->_tileFormat);
	    
	    $items = $dom->createElementNS(self::NS_DEEPZOOM, "Items");
	    
	    $nextItemId = 0;
        foreach ($images as $_path) {
        	$this->_descriptor->open($_path);
        	
        	$item = $dom->createElementNS(self::NS_DEEPZOOM, "I");
        	
        	$item->setAttribute("Id", $nextItemId);
            $item->setAttribute("N", $nextItemId);
            $item->setAttribute("Source", $_path);
            
            $size = $dom->createElementNS(self::NS_DEEPZOOM, "Size");
            
            $size->setAttribute("Width", $this->_descriptor->getWidth());
            $size->setAttribute("Height", $this->_descriptor->getHeight());
            $item->appendChild($size);
            
            $items->appendChild($item);
		    $nextItemId++;    
		}
		$collection->appendChild($items);
		$collection->setAttribute('NextItemId',$nextItemId);
		$dom->save($destination);
	}
	
	/**
	 * 
	 * @param int $zOrder
	 * @param int $level
	 * 
	 * @return array
	 */
	protected function getTilePosition($zOrder, $level){
	   $levelSize = pow(2,$level);	
	   $position = $this->getPosition($zOrder);
	   
	   $tileXPosition = floor($position['column'] * $levelSize) / $this->_tileSize;
	   $tileYPosition = floor($position['row'] * $levelSize) / $this->_tileSize;
	   
	   return array('column' => $tileXPosition, 'row' => $tileYPosition);
	}
	
	/**
	 * Returns position (column, row) from given Z-order (Morton number.)
	 * 
	 * @param int $zOrder
	 * 
	 * @return array
	 */
	protected function getPosition($zOrder) {
	   $column = 0;   	
	   $row = 0;   
	   foreach(range(0,32,2) as $i) {
	       $offset = $i / 2;
	       // column
	       $columnOffset = $i;
	       $columnMask = 1 << $columnOffset;
	       $columnValue = ($zOrder & $columnMask) >> $columnOffset;
	       $column |= $columnValue << $offset;
	       // row    
	       $rowOffset = $i + 1;
           $rowMask = 1 << $rowOffset;
           $rowValue = ($zOrder & $rowMask) >> $rowOffset;
           $row |= $rowValue << $offset	;
	   }	
	   
	   return array('column' => (int)$column,'row' => (int)$row);
	}
	
    /**
     * Returns the Z-order (Morton number) from given position.
     * 
     * @param int $column
     * @param int $row
     */
	protected function getZOrder($column, $row){
	   $zOrder = 0;
	   for($i=0;$i<32;$i++) {
	       $zOrder |= ($column & 1 << $i) << $i | ($row & 1 << $i) << ($i + 1);	
	   }	
	   
	   return $zOrder;
	}
}