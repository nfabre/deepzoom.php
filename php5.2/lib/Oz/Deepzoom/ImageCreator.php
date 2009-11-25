<?php
/**
* Deep Zoom Tools
*
* Copyright (c) 2008-2009, OpenZoom <http://openzoom.org/>
* Copyright (c) 2008-2009, Nicolas Fabre <nicolas.fabre@gmail.com>
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
 * @see Thumbnail
 */
require 'thumbnail.inc.php';
require 'Oz/Deepzoom/Descriptor.php';


/**
 * Creates Deep Zoom images
 *
 * @category   Oz
 * @package    Oz_Deepzoom
 * @author     Nicolas Fabre <nicolas.fabre@gmail.com>
 */
class Oz_Deepzoom_ImageCreator {
	
	/**
	 * @var string
	 */
    protected $_tileSize;
    /**
     * @var float
     */
    protected $_tileOverlap;
    protected $_tileFormat;
    protected $_imageQuality;
    protected $_resizeFilter;
    /**
     * @var Oz_Deepzoom_Image
     */
    protected $_image;
    
    /**
     * @var Oz_Deepzoom_Descriptor
     */
	protected $_descriptor;
	
    /**
     * Constructor
     *
     * @param int $tileSize
     * @param int $tileOverlap
     * @param string $tileFormat
     * @param float $imageQuality
     * @param int $resizeFilter
     */
	public function __construct($tileSize=254, $tileOverlap=1, $tileFormat="jpg", $imageQuality=0.95, $resizeFilter = null) {
        $this->_tileSize = (int) $tileSize;
        $this->_tileFormat = $tileFormat;
        $this->_tileOverlap  = $this->_clamp((int)$tileOverlap,0,1);
        $this->_resizeFilter = $resizeFilter;		
	}
	
	/**
	 * Returns the bitmap image at the given level
	 *
	 * @param int $level
	 * 
	 * @return Gr_Image
	 */
	public function getImage($level) {
        if(0 <= $level and $level < $this->_descriptor->numLevels()) {
            list($width, $height) = $this->_descriptor->getDimension($level);
            
            // don't transform to what we already have
            if($this->_descriptor->width == $width and $this->_descriptor->height == $height) {
            	return $this->_image;	
            }
            else {
            	$image = clone $this->_image;
            	$image->resize($width,$height);
            	return $image;
            }
        } else new Oz_Deepzoom_Exception('Invalid pyramid level');
	}
	
	/**
	 * Iterator for all tiles in the given level. Returns (column, row) of a tile.
	 *
	 * @param int $level
	 * 
	 * @return array
	 */
	public function tiles($level) {
		list($columns, $rows) = $this->_descriptor->getNumTiles($level);
		$yield = array();
		foreach (range(0, $columns - 1) as $column) {
		  foreach (range(0, $rows - 1) as $row) {
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
	 */
	public function  create($source,$destination) {
        $this->_image = new Thumbnail($source);
        list($width, $height) = $this->_image->size();
        $this->_descriptor = new Oz_Deepzoom_Descriptor($width,$height,$this->_tileSize,$this->_tileOverlap,$this->_tileFormat);	
        $aImage = pathinfo($destination); 
        /**
         * @todo secure varaibles
         */
        $imageName = $aImage['filename'];
        $dirName = $aImage['dirname'];
        $imageFile = $this->_ensure($dirName.DIRECTORY_SEPARATOR.$imageName.'_files');
        foreach (range(9,$this->_descriptor->numLevels() - 1) as $level) {
        	$levelDir = $this->_ensure($imageFile.DIRECTORY_SEPARATOR.$level);
        	$levelImage = $this->getImage($level);
        	$tiles = $this->tiles($level);
        	$format = $this->_descriptor->tileFormat;
        	foreach ($tiles as $_tile) {
                list($column, $row) = $_tile;
                list($x,$y,$x2,$y2) = $this->_descriptor->getTileBounds($level,$column,$row);
                $cropLevelImage = clone $levelImage;
                $cropLevelImage->crop($x,$y,$x2,$y2);
                $format = $this->_descriptor->tileFormat;
                $tilePath = $levelDir.DIRECTORY_SEPARATOR.sprintf('%s_%s.%s',$column,$row,$format);
                $cropLevelImage->save($tilePath);
                unset($cropLevelImage);
        	}
        	unset($levelImage);
        }
        $this->_descriptor->save($destination);
	}

	/**
	 * 
	 *
	 * @param int $val
	 * @param int $min
	 * @param int $max
	 * 
	 * @return int
	 */
	protected function _clamp($val, $min, $max) {
		if($val < $min) {
		    return $min;
		}elseif($val > $max) {
			return $max;
		}
		return $val;
	}
	
	/**
	 * Create directory if not exist
	 *
	 * @param string $pathname
	 * 
	 * @return string
	 */
	protected function _ensure($pathname) {
		if(!file_exists($pathname)) {
		   mkdir($pathname);
		}
		return $pathname;
	}
}
