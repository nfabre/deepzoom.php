<?php
namespace Deepzoom;

use Deepzoom\ImageAdapter\ImageAdapterInterface;
use Deepzoom\StreamWrapper\StreamWrapperInterface;

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
 * Image Creator, generate pyramid
 *
 * @package    Deepzoom
 * @author     Nicolas Fabre <nicolas.fabre@gmail.com>
 */
class ImageCreator extends AbstractCreator {
    /**
     * Tile Overlap 
     * 
     * @var float
     */ 
    protected $_tileOverlap; 
    
    /**
     *
     * @var Deepzoom\StreamWrapper\StreamWrapperInterface
     */
    protected $_streamWrapper;
    
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
     * @param Deepzoom\StreamWrapper\StreamWrapperInterface $streamWrapper
     * @param int $tileSize
     * @param int $tileOverlap
     * @param string $tileFormat
     */
	public function __construct(StreamWrapperInterface $streamWrapper,DescriptorInterface $descriptor, ImageAdapterInterface  $adapter, $tileSize=254, $tileOverlap=1, $tileFormat='jpg')
    {
        $this->_streamWrapper = $streamWrapper;
        $this->_descriptor = $descriptor;
        $this->_imageAdapter = $adapter;
        $this->_tileSize = (int) $tileSize;
        $this->_tileFormat = $tileFormat;
        $this->_tileOverlap = $this->_clamp((int)$tileOverlap,0,1);
    }
    
    /**
     * Returns the bitmap image at the given level
     *
     * @param int $level
     * 
     * @return ?
     * @throw Deepzoom\Exception check pyramid level
     */ 
    public function getImage($level) {
    	if(0 <= $level && $level < $this->_descriptor->getNumLevels()) {
            $dimension = $this->_descriptor->getDimension($level);
            // don't transform to what we already have
            if($this->_descriptor->getWidth() == $dimension['width'] and $this->_descriptor->getHeight() == $dimension['height']) {
            	return $this->_imageAdapter;
            }
            else {
            	$image = clone $this->_imageAdapter;
             	return $image->resize($dimension['width'],$dimension['height']);
            }
        } else throw new Exception('Invalid pyramid level');
    } 
     
    /**
     * Iterator for all tiles in the given level. Returns (column, row) of a tile.
     *
     * @param int $level
     * 
     * @return array
     * @throw Deepzoom\Exception check pyramid level
     */ 
    public function getTiles($level) {
		$tiles = $this->_descriptor->getNumTiles($level);
		$yield = array();
		foreach (range(0, $tiles['columns'] - 1) as $column) {
			foreach (range(0, $tiles['rows'] - 1) as $row) {
		    	$yield[] = array($column,$row);
			}
		}
		return $yield;
    } 
     
    /**
     * Creates Deep Zoom image from source file and saves it to destination
     *
     * @param string $source
     * @param string $destination
     * @throw Deepzoom\Exception check source existe and destination is writable
     */ 
    public function  create($source,$destination) {
    	$this->_imageAdapter->setStreamWrapper($this->_streamWrapper)->setSource($source);
        $dimensions = $this->_imageAdapter->getDimensions();
        $this->_descriptor->setWidth($dimensions['width'])
        				  ->setHeight($dimensions['height'])
        				  ->setTileSize($this->_tileSize)
        				  ->setTileOverlap($this->_tileOverlap)
        				  ->setTileFormat($this->_tileFormat);
        $aImage = $this->_streamWrapper->getPathInfo($destination);
        /**
		* @todo secure variables
		*/
        $imageName = $aImage['filename'];
        $dirName = $aImage['dirname'];
        //$imageFile = $this->_ensure($dirName.DIRECTORY_SEPARATOR.$imageName.'_files');
        $imageFile = $this->_streamWrapper->ensure($dirName.DIRECTORY_SEPARATOR.$imageName.'_files');
       
        foreach (range(0,$this->_descriptor->getNumLevels() - 1) as $level) {
	         //$levelDir = $this->_ensure($imageFile.DIRECTORY_SEPARATOR.$level);
	         $levelDir = $this->_streamWrapper->ensure($imageFile.DIRECTORY_SEPARATOR.$level);
	         
	         $levelImage = $this->getImage($level);
	         $format = $this->_descriptor->getTileFormat();
            
	         $tiles = $this->_descriptor->getNumTiles($level);
             foreach (range(0, $tiles['columns'] - 1) as $column) {
                foreach (range(0, $tiles['rows'] - 1) as $row) {
                    $bounds = $this->_descriptor->getTileBounds($level,$column,$row);
                    $cropLevelImage = clone $levelImage;
                    $cropLevelImage->crop($bounds['x'],$bounds['y'],$bounds['width'],$bounds['height']);
                    $tilePath = $levelDir.DIRECTORY_SEPARATOR.sprintf('%s_%s.%s',$column,$row,$format);
                    $cropLevelImage->save($tilePath);
                    unset($cropLevelImage);
                }
             }
	         unset($levelImage);
        }
        $this->_descriptor->save($destination);
    } 
    
    /**
     * 
     */
    public function getDescriptor() {
    	return $this->_descriptor;
    }
}