<?php
namespace Deepzoom\Tests\Fixtures\ImageAdapter; 

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
	public function resize($width,$height) {
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
	
	public function newImage($width,$height) {
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