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
	public function resizePx($width,$height);
	
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