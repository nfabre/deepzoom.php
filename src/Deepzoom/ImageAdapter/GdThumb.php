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

require_once __DIR__.'/../../vendor/phpthumb/ThumbBase.inc.php';
require_once __DIR__.'/../../vendor/phpthumb/GdThumb.inc.php';

use Deepzoom\StreamWrapper\StreamWrapperInterface;

/**
 * PhpThumb Adapter 
 *
 * @package    Deepzoom
 * @subpackage ImageAdapter
 * @author     Nicolas Fabre <nicolas.fabre@gmail.com>
 */
class GdThumb extends \GdThumb implements ImageAdapterInterface {

	/**
	 * @var $_source string
	 */
	protected $_source;
	
	/**
	 * @var Deepzoom\StreamWrapper\StreamWrapperInterface $_streamWrapper
	 */
	protected $_streamWrapper;
	
	public function __construct () {
	}
	
	/**
	 * Set image path
	 * 
	 * @param string $source
	 */
	public function setSource($source) {
		$this->_source = $source;
		parent::__construct($source);
		return $this;
	}
 	
	/**
	 * Resizes an image to be no larger than $width or $height
	 * 
	 * @param int $width
	 * @param int $height
	 */
	public function resizePx($width, $height) {
		parent::resize($width,$height);
		return $this;	
	}
	/**
	 * Returns image dimensions
	 * 
	 * return array
	 */
	public function getDimensions() {
		return $this->getCurrentDimensions();
	}
	
	/**
     * Saves an image
     * 
     * @param string $destination
     * @param string $format
     */
    public function save($destination, $format=null) {
    	$tmp = './'.$this->getUniqId().'.'.$this->getFormat();
		parent::save($tmp , $format);
		$this->getStreamWrapper()->putContents($destination,file_get_contents($tmp));
		unlink($tmp);
		return $this;	
	}
	
	/**
	 * Cropping function that crops an image using $startX and $startY as the upper-left hand corner
	 *  
	 * @param int $startX
	 * @param int $startY
	 * @param int $width
	 * @param int $height
	 */
	public function crop ($startX, $startY, $width, $height)
	{
		parent::crop($startX, $startY, $width, $height);
		return $this;
	}
	
	/**
     * Calculates the new image dimensions
     * 
     * These calculations are based on both the provided dimensions and $this->maxWidth and $this->maxHeight
     * 
     * @param int $width
     * @param int $height
     */
	protected function calcImageSize ($width, $height) {
		parent::calcImageSize ($width, $height);
		
		if($this->newDimensions['newWidth'] == 0) {
		  $this->newDimensions['newWidth'] = 1;	
		}
		
	    if($this->newDimensions['newHeight'] == 0) {
          $this->newDimensions['newHeight'] = 1; 
        }
        return $this->newDimensions;
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
    public function getStreamWrapper() {
        return $this->_streamWrapper;
    }
    
    protected function getUniqId() {
    	return uniqid('tmp_', true);
    }
}