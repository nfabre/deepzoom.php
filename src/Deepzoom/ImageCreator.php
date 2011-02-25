<?php

/*
 * This file is part of the Deepzoom.php package.
 *
 * (c) Nicolas Fabre <nicolas.fabre@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deepzoom;

use Deepzoom\ImageAdapter\ImageAdapterInterface;
use Deepzoom\StreamWrapper\StreamWrapperInterface;

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
             	return $image->resizePx($dimension['width'],$dimension['height']);
            }
        } else throw new \InvalidArgumentException('Invalid pyramid level');
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
        $imageFile = $this->_streamWrapper->ensure($dirName.DIRECTORY_SEPARATOR.$imageName.'_files');
       
        foreach (range(0,$this->_descriptor->getNumLevels() - 1) as $level) {
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