<?php

/*
 * This file is part of the Deepzoom.php package.
 *
 * (c) Nicolas Fabre <nicolas.fabre@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deepzoom\Tests\Fixtures\ImageAdapter; 

use Deepzoom\ImageAdapter\ImageAdapterInterface;
use Deepzoom\StreamWrapper\StreamWrapperInterface;

/**
 *  Stub for ImageAdapter
 *
 * @package    Deepzoom
 * @subpackage Test_ImageAdapter
 * @author     Nicolas Fabre <nicolas.fabre@gmail.com>
 */
class Stub implements ImageAdapterInterface {
	protected $_width;
	protected $_height;
	protected $_streamWrapper;
	
	public function __construct($width=null,$height=null) {
	   $this->_width = $width;
       $this->_height = $height;	
	}
	/**
	 * Resizes an image to be no larger than $width or $heigh
	 * 
	 * @param int $width
	 * @param int $heigh
	 */
	public function resizePx($width,$height) {
		$this->_width = $width;
		$this->_height = $height;
		return $this;
	}
	
	public function setSource($img) {
		if(empty($this->_width)) {
		  list($width,$height) = getimagesize($img);
		  $this->_width = $width;
		  $this->_height = $height;
		}
		return $this;
	}
	
	
	/**
	 * Returns image dimensions
	 * 
	 * return array
	 */
	public function getDimensions(){
		return array('width' => $this->_width, 'height' => $this->_height);
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
		// do some calculations
		$cropWidth	= ($this->_width < $width) ? $this->_width : $width;
		$cropHeight = ($this->_height < $height) ? $this->_height : $height;
		
		$this->_width = $cropWidth;
		$this->_height = $cropHeight;
		return $this;
	}
	
	/**
	 * Saves an image
	 * 
	 * @param string $destination
	 */
	public function save($destination, $format=null){
		return true;
	}
	
	/**
     * Sets the stream wrapper
     *
     * @param \Deepzoom\StreamWrapper\StreamWrapperInterface $streamWrapper
     */
    public function setStreamWrapper(StreamWrapperInterface $streamWrapper) {
        $this->_streamWrapper = $streamWrapper;
        
        return $this;   
    }
    
    /**
     * Gets the stream wrapper
     *
     * @return \Deepzoom\StreamWrapper\StreamWrapperInterface stream wrapper
     */
    public function getStreamWrapper()
    {
        return $this->_streamWrapper;
    }
}