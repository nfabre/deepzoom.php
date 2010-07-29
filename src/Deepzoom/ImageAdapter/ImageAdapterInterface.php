<?php
namespace Deepzoom\ImageAdapter; 

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
 * ImageAdapter Interface 
 *
 * @package    Deepzoom
 * @subpackage ImageAdapter
 * @author     Nicolas Fabre <nicolas.fabre@gmail.com>
 */
interface ImageAdapterInterface {

	/**
	 * Resizes an image to be no larger than $width or $heigh
	 * 
	 * @param int $width
	 * @param int $height
	 */
	public function resize($width,$height);
	
	/**
	 * Returns image dimensions
	 * 
	 * return array
	 */
	public function getDimensions();
	
	/**
	 * Cropping function that crops an image using $startX and $startY as the upper-left hand corner
	 *  
	 * @param int $startX
	 * @param int $startY
	 * @param int $width
	 * @param int $height
	 */
	public function crop($startX,$startY,$width,$height);
	
	/**
	 * Saves an image
	 * 
	 * @param string $destination
	 * @param string $format
	 */
	public function save($destination, $format=null);
	
	/**
	 * Image path
	 * 
	 * @param strung $destination
	 */
	public function setSource($path);
	
	/**
     * Sets the stream wrapper
     *
     * @param \Deepzoom\StreamWrapper\StreamWrapperInterface $streamWrapper
     */
    public function setStreamWrapper(StreamWrapperInterface $streamWrapper);
    
    /**
     * Gets the stream wrapper
     *
     * @return \Deepzoom\StreamWrapper\StreamWrapperInterface stream wrapper
     */
    public function getStreamWrapper();
}