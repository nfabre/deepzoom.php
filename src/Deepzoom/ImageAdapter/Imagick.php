<?php

/*
 * This file is part of the Deepzoom.php package.
 *
 * (c) Nicolas Fabre <nicolas.fabre@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deepzoom\ImageAdapter; 

use Deepzoom\StreamWrapper\StreamWrapperInterface;

/**
 * Imagick Adapter 
 *
 * @package    Deepzoom
 * @subpackage ImageAdapter
 * @author     Nicolas Fabre <nicolas.fabre@gmail.com>
 */
class Imagick extends \Imagick implements ImageAdapterInterface {
    
    /**
     * @var Deepzoom\StreamWrapper\StreamWrapperInterface $_streamWrapper
     */
    protected static $_streamWrapper;
    
	/**
	 * Returns image dimensions
	 * 
	 * return array
	 */
	public function getDimensions() {
		return array('width' => $this->getImageWidth(),'height' =>$this->getImageHeight());	
	}
	
	/**
	 * Cropping function that crops an image using $startX and $startY as the upper-left hand corner
	 *  
	 * @param int $startX
	 * @param int $startY
	 * @param int $width
	 * @param int $height
	 */
	public function crop($startX,$startY,$width,$height){
		$this->cropimage($width,$height,$startX,$startY);
		return $this;	
	}
	
	/**
     * Saves an image
     * 
     * @param string $destination
     * @param string $format
     */
    public function save($destination, $format=null) {
		$this->writeimage($destination);
		return $this;
	}
	
	/**
	 * Image path
	 * 
	 * @param strung $destination
	 */
	public function setSource($path){
		$this->readImage($path);
		return $this;
	}
	
    /**
     * Sets the stream wrapper
     *
     * @param \Deepzoom\StreamWrapper\StreamWrapperInterface $streamWrapper
     */
    public function setStreamWrapper(StreamWrapperInterface $streamWrapper) {
        self::$_streamWrapper = $streamWrapper;
        return $this;   
    }
    
    /**
     * Gets the stream wrapper
     *
     * @return \Deepzoom\StreamWrapper\StreamWrapperInterface stream wrapper
     */
    public function getStreamWrapper() {
        return self::$_streamWrapper;
    }
    
	/**
	 * Resizes an image to be no larger than $width or $height
	 * 
	 * @param int $width
	 * @param int $height
	 */
	public function resizePx($width, $height) {
		$this->scaleImage($width,$height,true);
		return $this;	
	}
}